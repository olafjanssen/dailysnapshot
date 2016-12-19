<?php
// do Oauth2 check
require_once('lib/config.php');
require_once('lib/state.php');

State::fromInitialQuery();

if (!State::courseId() || !State::canvasDomain()) {
  header('HTTP/1.1 403 Forbidden');
  echo 'Connect to this LTI using your Canvas course.';
  exit();
}

if (!State::refreshToken()) {
  // log in
  $uri = 'https://' . State::canvasDomain() . '/login/oauth2/auth?client_id=' . urlencode(Config::clientId(State::canvasDomain())) . '&response_type=code&redirect_uri=' . urlencode(Config::oauthCallbackURI()) . '&state=' . State::oauthState();
  header('Location: ' . $uri);
} else {
  // refresh the access token
  $data = array('client_id' => Config::clientId(State::canvasDomain()),
    'redirect_uri' => urlencode($uri),
    'client_secret' => rawurlencode(Config::clientSecret(State::canvasDomain())),
    'refresh_token' => State::refreshToken(),
    'grant_type' => 'refresh_token');

  $ch = curl_init('https://' . State::canvasDomain() . '/login/oauth2/token');
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($ch);
  curl_close($ch);
  $result = json_decode($result, true);
  State::setAccessToken($result['access_token']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Digital Dummy</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
  <meta name="apple-mobile-web-app-capable" content="yes">

  <link rel="stylesheet" href="bower_components/normalize-css/normalize.css">
  <link rel="stylesheet" href="bower_components/css-modal/build/modal.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/toastr/toastr.min.css">
  <link rel="apple-touch-icon" sizes="144x144" href="img/apple-icon-144x144.png">
  <link rel="icon" type="image/png" href="img/digitaldummy.png">
  <link rel="stylesheet" href="css/dailysnapshot.css">
  <link rel="stylesheet" href="css/pong.css">
  <link rel="stylesheet" href="bower_components/trumbowyg/dist/ui/trumbowyg.min.css">

  <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="bower_components/toastr/toastr.min.js"></script>
  <script src='bower_components/fastclick/lib/fastclick.js'></script>
  <script src="js/upload.js?v1.2"></script>
</head>
<body>
<header>
  <h1>Digital Dummy</h1>
  <h2>You've moved mountains today!</h2>
</header>


<form enctype="multipart/form-data" id="upload-form" style="display: block;">
  <div id="file-upload-wrapper">
    <i class="fa fa-upload"></i>
    <span>media</span>
    <input name="file" type="file" id="file-upload"/>
  </div>
  <a id="text-upload-wrapper" href="#modal-text">
    <i class="fa fa-plus-square"></i>
    <span>text</span>
  </a>
  <progress></progress>
</form>

<section class="modal--show" id="modal-text" tabindex="-1"
         role="dialog" aria-labelledby="modal-label" aria-hidden="true">

  <div class="modal-inner">
    <form id="text-upload-form">
      <div class="modal-content">
        <div id="submission-text" placeholder="Type your submission text here."></div>
        <button type="submit" id="text-submit">submit text</button>
      </div>
    </form>
  </div>

  <a href="#!" class="modal-close" title="Close this modal" data-close="Close"
     data-dismiss="modal">?</a>
</section>

<a id="view-dummy-link" href="index.php">view your dummy</a>

<script>
  if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
      FastClick.attach(document.body);
    }, false);
  }

  $("a").click(function (event) {
    event.preventDefault();
    window.location = $(this).attr("href");
  });
</script>
<script src="bower_components/css-modal/modal.js"></script>
<script src="bower_components/trumbowyg/dist/trumbowyg.min.js"></script>
<script>$('#submission-text').trumbowyg({
    mobile: true,
    tablet: true,
    fullscreenable: false,
      btns: ['viewHTML',
        '|', 'formatting',
        '|', 'btnGrp-design',
        '|', 'link',
        '|', 'btnGrp-justify',
        '|', 'btnGrp-lists']
    }
  );</script>
</body>
</html>
