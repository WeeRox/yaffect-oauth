<?php
namespace Token;

use Database\Database;

class RefreshToken
{
  public static function generateRefreshToken($client_id, $user_id, $scope)
  {
    $database = new Database();

    $refresh_token_hex = $database->uuid2hex($database->generateUUIDv4());
    $refresh_token_base64url = $database->hex2base64url($refresh_token_hex);

    // TODO: convert from base64 and unhex ids
    if ($database->query("INSERT INTO refresh_tokens (refresh_token, client_id, user_id, scope) VALUES (UNHEX('$access_token_hex'), $client_id, $user_id, $scope);")) {
      return $refresh_token_base64url;
    } else {
      // TODO: database error
      return false;
    }
  }
}
?>
