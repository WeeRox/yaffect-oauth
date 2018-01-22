<?php
namespace Token;

use Database\Database;

class AccessToken
{
  public static function generateAccessToken($clientId, $userId, $expires, $scope)
  {
    $database = new Database();

    $accessToken_hex = $database->uuid2hex($database->generateUUIDv4());
    $accessToken_base64url = $database->hex2base64url($accessToken_hex);

    // TODO: convert from base64 and unhex ids
    if ($database->query("INSERT INTO access_tokens (access_token, client_id, user_id, expires, scope) VALUES (UNHEX('$accessToken_hex'), $clientId, $userId, $expires, $scope);")) {
      return $accessToken_base64url;
    } else {
      // TODO: database error
      return false;
    }
  }
}
?>
