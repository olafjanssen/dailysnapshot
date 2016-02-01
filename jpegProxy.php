<?php
// PHP Proxy for jpeg images inclusing EXIF orientation correction
error_reporting(E_ALL);
ini_set('display_errors', 1);

$path = $_GET['path'];

$session = curl_init($path);

curl_setopt($session, CURLOPT_URL, $path);
curl_setopt($session, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);

$data = curl_redirect_exec($session, $count);

// correct for the EXIF orientation
$fileName = "data://image/jpeg;base64," . base64_encode($data);

$image = imagecreatefromjpeg($fileName);
//$image = imagerotate($image, array_values([0, 0, 0, 180, 0, 0, -90, 0, 90])[@exif_read_data($fileName)['Orientation'] ?: 0], 0);

// output the corrected jpeg
$mime = curl_getinfo($session, CURLINFO_CONTENT_TYPE);

header("Content-Type: ". $mime);
imagejpeg($image);

// clean-up
imagedestroy($image);
curl_close($session);


function curl_redirect_exec($ch, &$redirects, $curlopt_header = false) {
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $data = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if ($http_code == 301 || $http_code == 302) {
    list($header) = explode("\r\n\r\n", $data, 2);
    $matches = array();
    preg_match('/(Location:|URI:)(.*?)\n/', $header, $matches);
    $url = trim(array_pop($matches));
    $url_parsed = parse_url($url);
    if (isset($url_parsed)) {
      curl_setopt($ch, CURLOPT_URL, $url);
      $redirects++;
      return curl_redirect_exec($ch, $redirects);
    }
  }
  if ($curlopt_header)
    return $data;
  else {
    list(,$body) = explode("\r\n\r\n", $data, 2);
    return $body;
  }
}

