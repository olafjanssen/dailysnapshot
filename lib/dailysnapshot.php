<?php
/**
 * Created by IntelliJ IDEA.
 *
 * User: olafjanssen
 * Date: 01/12/15
 * Time: 13:25
 */
require_once('config.php');

$submissions = listAssignmentsSubmissionHistory(Config::courseId(), Config::assignmentId(), 'all');
