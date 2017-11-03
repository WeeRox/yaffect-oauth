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
  }

  public static function invalidClient()
  {
    self::init();
  }

  public static function invalidGrant()
  {
    self::init();
  }

  public static function unauthorizedClient()
  {
    self::init();
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
  }
}

?>
