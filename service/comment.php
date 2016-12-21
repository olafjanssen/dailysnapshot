<?php
require_once('../lib/logging.php');
require_once('../lib/config.php');
require_once('../lib/state.php');
require_once('../lib/canvasAPI.php');


if (isset($_POST['submission']) && isset($_POST['user'])) {
  $submitted = submitAssignmentComment(State::courseId(), State::assignmentId(), $_POST['user'], $_POST['submission']);

  echo json_encode($submitted);

}
