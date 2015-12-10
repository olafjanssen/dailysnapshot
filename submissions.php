<?php
/**
 * Created by IntelliJ IDEA.
 * User: olafjanssen
 * Date: 10/12/15
 * Time: 20:30
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'lib/canvasAPI.php';
require_once('lib/dailysnapshot.php');

header('Content-type: application/json');
echo json_encode($submissions);
