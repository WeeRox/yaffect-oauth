<?php
namespace Response;

class ErrorResponse
{
  private $response = array();
  function __construct()
  {
    http_response_code(400);
    header("Cache-Control: no-store");
    header("Pragma: no-cache");
    header("Content-Type: application/json");
  }

  static function invalidRequest()
  {

  }

  static function invalidClient()
  {

  }

  static function invalidGrant()
  {

  }

  static function unauthorizedClient()
  {

  }

  static function unsupportedGrantType($grantType)
  {
    $response['error'] = 'unsupported_grant_type';
    $response['error_description'] = "Grant type '$grantType' is unsupported. Supported grant types are 'password'.";
    echo json_encode($response);
  }

  static function invalidScope()
  {

  }
}

?>
