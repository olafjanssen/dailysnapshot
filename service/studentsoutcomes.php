<?php
/**
 * Created by IntelliJ IDEA.
 * User: olafjanssen
 * Date: 10/12/15
 * Time: 20:30
 */

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

foreach ($outcomeResults->outcome_results as $result) {
  $outcomeId = $result->id;
  
}


$outcomeResults->users = $gradableStudents;
$outcomeGroups->outcome_groups = $outcomeGroups;

echo json_encode($outcomeResults);

