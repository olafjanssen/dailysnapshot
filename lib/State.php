<?php

/**
 * Created by IntelliJ IDEA.
 * User: olafjanssen
 * Date: 01/02/16
 * Time: 21:17
 */
class State {

  public static function verify() {
    session_start();
    self::courseId();
    self::canvasDomain();
    self::accessToken();
    self::assignmentId();
  }

  private static function ensure($postKey, $sessionKey) {
    if (array_key_exists($postKey, $_POST)) {
      $_SESSION[$sessionKey] = $_POST[$postKey];
    }
    return array_key_exists($sessionKey, $_SESSION)? $_SESSION[$sessionKey] : null;
  }

  public static function courseId() {
    return self::ensure('custom_canvas_course_id', 'courseId');
  }

  public static function canvasDomain() {
    return self::ensure('custom_canvas_api_domain', 'canvasDomain');
  }

  public static function accessToken() {
    return self::ensure('access_token', 'accessToken');
  }

  public static function refreshToken() {
    return self::ensure('refresh_token', 'refreshToken');
  }

  public static function setAccessToken($accessToken) {
    $_SESSION['accessToken'] = $accessToken;
  }

  public static function setRefreshToken($refreshToken) {
    $_SESSION['refreshToken'] = $refreshToken;
  }

  public static function oauthState() {
    if (!array_key_exists('oauthState', $_SESSION)) {
      // generate a relatively random and cheap string for oauth2 state check
      $_SESSION['oauthState'] = substr(md5(rand()), 0, 7);
    }
    return $_SESSION['oauthState'];
  }

  public static function assignmentId() {
    return '16929';
  }

}
