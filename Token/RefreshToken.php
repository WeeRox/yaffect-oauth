<?php
namespace Token;

use Database\Database;

class RefreshToken
{
  public static function generateRefreshToken($clientId, $userId, $scope)
  {
    $database = new Database();

    $refreshToken_hex = $database->uuid2hex($database->generateUUIDv4());
    $refreshToken_base64url = $database->hex2base64url($refreshToken_hex);
    $clientId_hex = $database->base64url2hex($clientId);
    $userId_hex = $database->base64url2hex($userId);

    if ($database->query("INSERT INTO refresh_tokens (refresh_token, client_id, user_id, scope) VALUES (UNHEX('$refreshToken_hex'), UNHEX('$clientId_hex'), UNHEX('$userId_hex'), '$scope');")) {
      return $refreshToken_base64url;
    } else {
      // TODO: database error
      return false;
    }
  }
}
?>
