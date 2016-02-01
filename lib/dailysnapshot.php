<?php
/**
 * Created by IntelliJ IDEA.
 *
 * User: olafjanssen
 * Date: 01/12/15
 * Time: 13:25
 */
require_once('config.php');

$submissions = Array(); //listAssignmentsSubmissionHistory(Config::courseId(), Config::assignmentId(), 'all');


//GET https://<canvas-install-url>/login/oauth2/auth?client_id=XXX&response_type=code&redirect_uri=https://example.com/oauth_complete&state=YYY

// TODO generate a random state and check
