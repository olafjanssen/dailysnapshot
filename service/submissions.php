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

$submissions = listAssignmentsSubmissionHistory(State::courseId(), State::assignmentId(), 'all');
$json = json_encode($submissions);

echo $json;
