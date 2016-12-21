<?php
/**
 * Enable logging of errors to a temporary log files.
 */
error_reporting(E_ALL);
ini_set("log_errors", 1);

$date = new DateTime();
ini_set("error_log", dirname(__FILE__) . "/../tmp/error-".$date->format('Y-m-d').".log");
