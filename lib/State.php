<?php
session_start();

/**
 * Created by IntelliJ IDEA.
 * User: olafjanssen
 * Date: 01/02/16
 * Time: 21:17
 *
 * Store in session: courseId, canvasDomain, access_token, assignment_id
 * Store in cookie: refresh_token
 */
class State {

  public static function fromInitialPost() {
    if (array_key_exists('custom_canvas_course_id', $_POST)) {
      $_SESSION['courseId'] = $_POST['custom_canvas_course_id'];
      $_SESSION['canvasDomain'] = $_POST['custom_canvas_api_domain'];
      $_SESSION['oauthState'] = base64_encode(substr(md5(rand()), 0, 7) . ',index.php');
    }
  }

  public static function createUploadLink() {
    return Config::baseURI() . '/upload.php?q=' . base64_encode(self::courseId() . ',' . self::assignmentId() . ',' . self::canvasDomain());
  }

  public static function fromInitialQuery() {
    if (array_key_exists('q', $_GET)) {
      $data = explode(',', base64_decode($_GET['q']), 3);
      $_SESSION['courseId'] = $data[0];
      $_SESSION['assignmentId'] = $data[1];
      $_SESSION['canvasDomain'] = $data[2];
      $_SESSION['oauthState'] = base64_encode(substr(md5(rand()), 0, 7) . ',upload.php?q=' . $_GET['q']);
    }
  }

  public static function courseId() {
    return array_key_exists('courseId', $_SESSION) ? $_SESSION['courseId'] : null;
  }

  public static function canvasDomain() {
    return array_key_exists('canvasDomain', $_SESSION) ? $_SESSION['canvasDomain'] : null;
  }

  public static function accessToken() {
    return array_key_exists('access_token', $_SESSION) ? $_SESSION['access_token'] : null;
  }

  public static function setAccessToken($accessToken) {
    $_SESSION['access_token'] = $accessToken;
  }

  public static function getKey() {
    return sha1(self::courseId() . self::canvasDomain());
  }

  public static function refreshToken() {
    return array_key_exists(sha1(self::courseId() . self::canvasDomain()), $_COOKIE) ? $_COOKIE[sha1(self::courseId() . self::canvasDomain())] : null;
  }

  public static function setRefreshToken($refreshToken) {
    setcookie(sha1(self::courseId() . self::canvasDomain()), $refreshToken, time() + 3600 * 24 * 30, '/', Config::baseDomain(), true, true);
  }

  public static function oauthStateUri() {
    if (array_key_exists('oauthState', $_SESSION)) {
      var_dump(base64_decode($_SESSION['oauthState']));
      $data = explode(',', base64_decode($_SESSION['oauthState']), 2);
      return $data[1];
    }
    return null;
  }

  public static function oauthState() {
    return array_key_exists('oauthState', $_SESSION) ? $_SESSION['oauthState'] : null;
  }

  public static function assignmentId() {
    return array_key_exists('assignmentId', $_SESSION) ? $_SESSION['assignmentId'] : null;
  }

  public static function setAssignmentId($assignmentId) {
    $_SESSION['assignmentId'] = $assignmentId;
  }

}
