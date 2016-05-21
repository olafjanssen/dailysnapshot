<?php
/**
 * Created by IntelliJ IDEA.
 * User: olafjanssen
 * Date: 10/12/15
 * Time: 20:30
 */

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once '../lib/state.php';
require_once '../lib/canvasAPI.php';

header('Content-type: application/json');

// find the correct assignment
$daily = searchAssignment(State::courseId(), 'Digital Dummy');
if (count($daily) > 0) {
  State::setAssignmentId($daily[0]->id);
} else {
  // create assignment
  $description = urlencode('<p>Upload directly from your smartphone using <a href="' . State::createUploadLink() . '">' . State::createUploadLink() . '</a>. Bookmark it or put it on your home screen!</p>');
  $data = 'assignment[name]=' . urlencode('Digital Dummy') . '&assignment[points_possible]=1&assignment[grading_type]=pass_fail' .
    '&assignment[submission_types][]=online_upload&assignment[published]=true&assignment[description]=' . $description;

  $assignmentId = createGenericAssignment(State::courseId(), $data);
  State::setAssignmentId($assignmentId);
}

$passphrase = State::refreshToken();
$fileHash = 'cache/' . md5(State::courseId() . State::assignmentId() . State::canvasDomain()) . '-' . md5(State::refreshToken());

if (file_exists($fileHash)) {
  if (time() - filemtime($fileHash) < 8 * 3600) {
    $data = decrypt_file($fileHash, $passphrase);
    echo $data;
    exit();
  }
}

$submissions = listAssignmentsSubmissionHistory(State::courseId(), State::assignmentId(), 'all');
$json = json_encode($submissions);

// store encrypted cached file
encrypt_file($json, $fileHash, $passphrase, true);

echo $json;

function encrypt_file($source,$destination,$passphrase,$stream=NULL) {
  // $source can be a local file...
  if($stream) {
    $contents = $source;
    // OR $source can be a stream if the third argument ($stream flag) exists.
  }else{
    $handle = fopen($source, "rb");
    $contents = fread($handle, filesize($source));
    fclose($handle);
  }

  $iv = substr(md5("\x1B\x3C\x58".$passphrase, true), 0, 8);
  $key = substr(md5("\x2D\xFC\xD8".$passphrase, true) . md5("\x2D\xFC\xD9".$passphrase, true), 0, 24);
  $opts = array('iv'=>$iv, 'key'=>$key);
  $fp = fopen($destination, 'wb') or die("Could not open file for writing.");
  stream_filter_append($fp, 'mcrypt.tripledes', STREAM_FILTER_WRITE, $opts);
  fwrite($fp, $contents) or die("Could not write to file.");
  fclose($fp);

}

function decrypt_file($file,$passphrase) {
  $iv = substr(md5("\x1B\x3C\x58".$passphrase, true), 0, 8);
  $key = substr(md5("\x2D\xFC\xD8".$passphrase, true) .
    md5("\x2D\xFC\xD9".$passphrase, true), 0, 24);
  $opts = array('iv'=>$iv, 'key'=>$key);
  $fp = fopen($file, 'rb');
  stream_filter_append($fp, 'mdecrypt.tripledes', STREAM_FILTER_READ, $opts);
  $data = trim(stream_get_contents($fp));
  fclose($fp);
  return $data;
}
