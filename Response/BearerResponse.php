<?php
namespace Response;

use Token\AccessToken;

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

  public static function respond($clientId, $userId, $expiresIn=3600, $scope='', $refreshToken=True)
  {
    init();

    $accessToken = AccessToken::generateAccessToken($clientId, $userId, $expiresIn, $scope);

    if ($accessToken === false) {
      // TODO: return error
      return false;
    }

    $response['access_token'] = $accessToken;
    $response['token_type'] = "Bearer";
    $response['expires_in'] = $expiresIn;

    if ($refreshToken) {
      $refreshToken = RefreshToken::generateRefreshToken($clientId, $userId, $scope);
      $response['refresh_token'] = $refreshToken;
    }

    echo json_encode($response);
  }
}
?>
