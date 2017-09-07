<?php
require_once('../../lib/logging.php');
require_once('../../lib/config.php');
require_once('../../lib/state.php');

$text = $_POST['id'] . "\n";

file_put_contents(State::getKey(), $text, FILE_APPEND | LOCK_EX);
