<?php
require_once('../lib/logging.php');
require_once '../lib/state.php';
require_once '../lib/canvasAPI.php';

header('Content-type: application/json');

$gradableStudents = listGradableStudents(State::courseId(), State::assignmentId());
$outcomeGroups = getOutcomeResults(State::courseId());


$userIds = array();
foreach ($gradableStudents as $student) {
  array_push($userIds, $student->id);
}

$outcomeResults = getOutcomeResults(State::courseId(), $userIds);

$outcomeIds = array();
foreach ($outcomeResults->outcome_results as $result) {
  array_push($outcomeIds, $result->links->learning_outcome);
}

$outcomeIds = array_unique($outcomeIds);
$outcomes = array();
foreach ($outcomeIds as $outcomeId){
  $outcome = getOutcome($outcomeId);
  array_push($outcomes, $outcome);
}


$outcomeResults->users = $gradableStudents;
$outcomeResults->outcomes = $outcomes;

echo json_encode($outcomeResults);

