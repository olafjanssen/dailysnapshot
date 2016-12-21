<?php
require_once('../lib/logging.php');
require_once '../lib/state.php';
require_once '../lib/canvasAPI.php';

header('Content-type: application/json');

$submissions = listAssignmentsSubmissionHistory(State::courseId(), State::assignmentId(), $_GET['user']);
$json = json_encode($submissions);

echo $json;
