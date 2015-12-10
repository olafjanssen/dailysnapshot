<?php
// PHP Proxy for jpeg images inclusing EXIF orientation correction

error_reporting(E_ALL);
ini_set('display_errors', 1);

$path = $_GET['path'];

$session = curl_init($path);

curl_setopt($session, CURLOPT_HEADER, false);
curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
curl_setopt($session, CURLOPT_FOLLOWLOCATION, true);

// TODO find a SSL worthy solution
curl_setopt($session, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);


$data = curl_exec($session);

// correct for the EXIF orientation
$fileName = "data://image/jpeg;base64," . base64_encode($data);
$image   = imagecreatefromjpeg($fileName);
$image = imagerotate($image, array_values([0, 0, 0, 180, 0, 0, -90, 0, 90])[@exif_read_data($fileName)['Orientation'] ?: 0], 0);

// output the corrected jpeg
$mime = curl_getinfo($session, CURLINFO_CONTENT_TYPE);
header("Content-Type: ". $mime);
imagejpeg($image);

// clean-up
imagedestroy($image);
curl_close($session);


