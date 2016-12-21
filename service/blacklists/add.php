<?php
error_reporting(E_ALL);
ini_set("log_errors", 1);
ini_set("error_log", "../../tmp/index-error.log");

require_once('../../lib/config.php');
require_once('../../lib/state.php');

$text = $_POST['id'] . "\n";

file_put_contents(State::getKey(), $text, FILE_APPEND | LOCK_EX);
