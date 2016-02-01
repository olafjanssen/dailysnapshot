<?php
/**
 * Created by IntelliJ IDEA.
 * User: olafjanssen
 * Date: 10/12/15
 * Time: 20:30
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'lib/state.php';
State::verify();

require_once 'lib/canvasAPI.php';

header('Content-type: application/json');

// find the correct assignment
if (!State::assignmentId()) {
  $daily = searchAssignment(State::courseId(), 'Daily');

  if (count($daily)>0) {
    State::setAssignmentId($daily[0]->id);
  }
}

$submissions = listAssignmentsSubmissionHistory(State::courseId(), State::assignmentId(), 'all');
echo json_encode($submissions);
