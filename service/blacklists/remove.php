<?php
require_once('../../lib/logging.php');
require_once('../../lib/config.php');
require_once('../../lib/state.php');

$text = $_POST['id'] . "\n";
$list = State::getKey();

$contents = file_get_contents($list, LOCK_EX);
$contents = str_replace($text, '', $contents);
file_put_contents($list, $contents, LOCK_EX);
