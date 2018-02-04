<?php
namespace Response;

use Token\AccessToken;
use Token\RefreshToken;

class BearerResponse
{
  private $response = array();

  private static function init()
  {
    http_response_code(200);
    header("Cache-Control: no-store");
    header("Pragma: no-cache");
    header("Content-Type: application/json; charset=UTF-8");
  }

  // if refreshToken is true, generate new refreshToken
  // if refreshToken is false, do not generate refreshToken
  // if refreshToken is a string, send that refresh token in the response
  public static function respond($clientId, $userId, $scope='', $refreshToken=true, $expiresIn=3600)
  {
    self::init();

    $accessToken = AccessToken::generateAccessToken($clientId, $userId, $expiresIn, $scope);

    // if access token is false, something went wrong
    if ($accessToken !== false) {
      $response['access_token'] = $accessToken;
      $response['token_type'] = "Bearer";
      $response['expires_in'] = $expiresIn;

      if (is_string($refreshToken)) {
        $response['refresh_token'] = $refreshToken;
      } else if ($refreshToken) {
        $refreshToken = RefreshToken::generateRefreshToken($clientId, $userId, $scope);
        $response['refresh_token'] = $refreshToken;
      }

      echo json_encode($response);
    }
  }
}
?>
