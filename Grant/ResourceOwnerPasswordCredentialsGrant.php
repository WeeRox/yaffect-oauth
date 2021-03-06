<?php
namespace Grant;

use Response\ErrorResponse;
use Response\BearerResponse;
use Database\Database;

class ResourceOwnerPasswordCredentialsGrant
{

  private static $database;

  public static function respond()
  {
    self::$database = new Database();

    // do a 'hard' check since scope can be empty
    if (($scope = self::validateRequest()) !== false && ($clientId = self::validateClient()) && ($userId = self::validateUser())) {
      BearerResponse::respond($clientId, $userId, $scope);
    }
  }

  private static function validateRequest()
  {
    // check that the client sent some auth info in either the header or the body
    if (!empty($_SERVER['HTTP_AUTHORIZATION']) || !empty($_POST['client_id'])) {
      // check that the client didn't send multiple credentials
      if (!empty($_SERVER['HTTP_AUTHORIZATION']) && !empty($_POST['client_id'])) {
        ErrorResponse::invalidRequest();
        return false;
      }
    } else {
      ErrorResponse::invalidRequest();
      return false;
    }

    // check that all required parameters is present
    if (empty($_POST['username']) || empty($_POST['password'])) {
      ErrorResponse::invalidRequest();
      return false;
    }

    if (isset($_POST['scope'])) {
      return $_POST['scope'];
    } else {
      return '';
    }
  }

  private static function validateClient()
  {
    // check for unsupported auth methods
    if (!empty($_SERVER['HTTP_AUTHORIZATION']) && (explode(" ", $_SERVER['HTTP_AUTHORIZATION'])[0] != 'Basic')) {
      ErrorResponse::invalidClient();
      return false;
    }

    // check if client want to authenticate using header or body
    if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
      $clientId = $_SERVER['PHP_AUTH_USER'];

      if (empty($_SERVER['PHP_AUTH_PW'])) {
        $clientSecret = NULL;
      } else {
        $clientSecret = $_SERVER['PHP_AUTH_PW'];
      }
    } else if (!empty($_POST['client_id'])) {
      $clientId = $_POST['client_id'];

      if (empty($_POST['client_secret'])) {
        $clientSecret = NULL;
      } else {
        $clientSecret = $_POST['client_secret'];
      }
    }

    if ($result = self::$database->query("SELECT * FROM clients WHERE client_id = UNHEX('" . self::$database->base64url2hex($clientId) . "');")) {
      // check if client id exist in database
      if ($result->num_rows != 1) {
        ErrorResponse::invalidClient();
        return false;
      }

      $row = $result->fetch_assoc();

      // make sure that the client is authorized to use this grant type
      if ($row['grant_type'] != "password") {
        ErrorResponse::unauthorizedClient();
        return false;
      }

      // check if client secret has been issued to the client
      if (!is_null($row['client_secret'])) {
        // check that the client secrets matches
        if ($clientSecret !== self::$database->hex2base64url(bin2hex($row['client_secret']))) {
          ErrorResponse::invalidClient();
          return false;
        }
      }

      $result->close();
    } else {
      // TODO: database error
      return false;
    }

    return $clientId;
  }

  private static function validateUser()
  {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($result = self::$database->query("SELECT * FROM users WHERE username = '$username'")) {

      // check if username exist in database
      if ($result->num_rows != 1) {
        ErrorResponse::unauthenticatedUser();
        return false;
      }

      $row = $result->fetch_assoc();

      if (!password_verify($password, $row['password'])) {
        ErrorResponse::unauthenticatedUser();
        return false;
      }

      $userId = self::$database->hex2base64url(bin2hex($row['user_id']));

      $result->close();
    } else {
      // TODO: database error
      return false;
    }

    return $userId;
  }
}
?>
