<?php
/**
 * Created by IntelliJ IDEA.
 * User: olafjanssen
 * Date: 03/02/16
 * Time: 13:45
 */

require_once('lib/config.php');
require_once('lib/state.php');
require_once('lib/canvasAPI.php');


$fileIds = [];
foreach ($_FILES as $key => $file) {

  $res = uploadSubmissionFile(State::courseId(), State::assignmentId(), $file['name'], $file['type'], $file['size']);
  $res = uploadSubmissionData($res['upload_url'], $res['upload_params'], $file['tmp_name']);
  $fileIds[] = $res['id'];
}

$submitted =  submitAssignment(State::courseId(), State::assignmentId(), $fileIds);

echo json_encode($submitted);
