<?php
namespace Token;

use Database\Database;

class AccessToken
{
  public static function generateAccessToken($clientId, $userId, $expiresIn, $scope)
  {
    $database = new Database();

    $accessToken_hex = $database->uuid2hex($database->generateUUIDv4());
    $accessToken_base64url = $database->hex2base64url($accessToken_hex);
    $clientId_hex = $database->base64url2hex($clientId);
    $userId_hex = $database->base64url2hex($userId);

    if ($database->query("INSERT INTO access_tokens (access_token, client_id, user_id, expires, scope) VALUES (UNHEX('$accessToken_hex'), UNHEX('$clientId_hex'), UNHEX('$userId_hex'), DATE_ADD(UTC_TIMESTAMP(), INTERVAL $expiresIn SECOND), '$scope');")) {
      return $accessToken_base64url;
    } else {
      // TODO: database error
      return false;
    }
  }
}
?>
