<?php
/**
 * Created by IntelliJ IDEA.
 * User: olafjanssen
 * Date: 03/02/16
 * Time: 13:45
 */

require_once('../lib/config.php');
require_once('../lib/state.php');
require_once('../lib/canvasAPI.php');


if (isset($_POST['submission']) && isset($_POST['user'])) {
  $submitted = submitAssignmentComment(State::courseId(), State::assignmentId(), $_POST['user'], $_POST['submission']);

  echo json_encode($submitted);

}
