<?
// PHP Image cache for jpeg images
$path = $_GET['path'];

$hashPath = 'cache/' . sha1($path);
header("Content-Type: image/jpeg");

if (file_exists($hashPath)) {
  readfile($hashPath);
} else {
  $data = file_get_contents($path);

  var_dump($data);

  $fileName = "data://image/jpeg;base64," . base64_encode($data);
  $image = imagecreatefromjpeg($fileName);

//$image = imagerotate($image, array_values([0, 0, 0, 180, 0, 0, -90, 0, 90])[@exif_read_data($fileName)['Orientation'] ?: 0], 0);

  $newwidth = 640;
  $newheight = imagesy($image) * $newwidth / imagesx($image);
  $thumb = imagecreatetruecolor($newwidth, $newheight);
  imagecopyresized($thumb, $image, 0, 0, 0, 0, $newwidth, $newheight, imagesx($image), imagesy($image));
  imagejpeg($thumb, $hashPath);
  imagejpeg($thumb);
}
