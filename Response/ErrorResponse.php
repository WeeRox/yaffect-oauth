<?php
namespace Response;

class ErrorResponse
{
  private $response = array();

  private static function init()
  {
    http_response_code(400);
    header("Cache-Control: no-store");
    header("Pragma: no-cache");
    header("Content-Type: application/json");
  }

  public static function invalidRequest()
  {
    self::init();
    $response['error'] = 'invalid_request';
    echo json_encode($response);
  }

  public static function invalidClient()
  {
    self::init();
    http_response_code(401);
    $response['error'] = 'invalid_client';

    header("WWW-Authenticate: Basic");

    echo json_encode($response);
  }

  public static function invalidGrant()
  {
    self::init();
    $response['error'] = 'invalid_grant';
    echo json_encode($response);
  }

  public static function unauthorizedClient()
  {
    self::init();
    $response['error'] = 'unauthorized_client';
    echo json_encode($response);
  }

  public static function unsupportedGrantType($grantType)
  {
    self::init();
    $response['error'] = 'unsupported_grant_type';
    $response['error_description'] = "Grant type '$grantType' is unsupported. Supported grant types are 'password'.";
    echo json_encode($response);
  }

  public static function invalidScope()
  {
    self::init();
    $response['error'] = 'invalid_scope';
    echo json_encode($response);
  }

  /* Below are custom error reponses */

  // The user didn't provide a correct pair of username and password
  public static function unauthenticatedUser()
  {
    self::init();
    http_response_code(401); // 401 Unauthorized
    $response['error'] = 'unauthenticated_user';
    $response['error_description'] = "Username and/or password are incorrect. Try again with a different pair of username and password.";

    header("WWW-Authenticate: Newauth");

    echo json_encode($response);
  }
}

?>
