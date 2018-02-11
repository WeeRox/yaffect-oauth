<?php
namespace Response;

class IntrospectionResponse
{
  private $response = array();

  private static function init()
  {
    http_response_code(200);
    header("Content-Type: application/json; charset=UTF-8");
  }

  public static function invalidToken()
  {
    self::init();
    $response['active'] = false;
    echo json_encode($response);
  }

  // $tokenInfo is an array
  public static function tokenInfo($tokenInfo)
  {
    self::init();
    echo json_encode($tokenInfo);
  }
}
?>
