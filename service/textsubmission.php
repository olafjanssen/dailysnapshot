<?php
require_once('../lib/logging.php');
require_once('../lib/config.php');
require_once('../lib/state.php');
require_once('../lib/canvasAPI.php');

if (isset($_POST['submission'])) {

  $submitted = submitTextAssignment(State::courseId(), State::assignmentId(), $_POST['submission']);

  echo json_encode($submitted);

  // remove cache
  $files = glob('cache/'.md5(State::courseId() . State::assignmentId() . State::canvasDomain()).'-*'); // get all file names
  foreach($files as $file){ // iterate files
    if(is_file($file))
      unlink($file); // delete file
  }
}
