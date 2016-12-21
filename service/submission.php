<?php
require_once('../lib/logging.php');
require_once('../lib/config.php');
require_once('../lib/state.php');
require_once('../lib/canvasAPI.php');
require_once('../lib/exif.php');

/**
 * Function to rotate JPGs based on their EXIF orientation tag; ignores other files.
 *
 * @param $filename string path to file to rotate
 */
function autoRotate($filename) {

  $exif = read_exif_data_raw($filename, false);
  // Only look at valid JPG files
  if (!$exif['ValidJpeg'] ) {
    return $jpgFile;
  }

  $orientation = $exif['IFD0']['Orientation'];

  // Fix Orientation if needed
  switch($orientation) {
    case "Upsidedown":
      $image   = imagecreatefromjpeg($filename);
      $image = imagerotate($image, 180, 0);
      imagejpeg($image, $filename, 90);
      break;
    case "90 deg CCW":
      $image   = imagecreatefromjpeg($filename);
      $image = imagerotate($image, -90, 0);
      imagejpeg($image, $filename, 90);
      break;
    case "90 deg CW":
      $image   = imagecreatefromjpeg($filename);
      $image = imagerotate($image, 90, 0);
      imagejpeg($image, $filename, 90);
      break;
  }
}

$fileIds = [];
foreach ($_FILES as $key => $file) {
  autoRotate($file['tmp_name']);
  $res = uploadSubmissionFile(State::courseId(), State::assignmentId(), $file['name'], $file['type'], $file['size']);
  $res = uploadSubmissionData($res['upload_url'], $res['upload_params'], $file['tmp_name']);
  $fileIds[] = $res['id'];
}

$submitted =  submitAssignment(State::courseId(), State::assignmentId(), $fileIds);

echo json_encode($submitted);
