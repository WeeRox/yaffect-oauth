<?php
namespace Grant;

use Response\ErrorResponse;

class ResourceOwnerPasswordCredentialsGrant
{

  public $client;

  public static function respond()
  {
    if (self::validateRequest() && self::validateClient() && self::validateUser()) {
      //TODO: return access token (bearer response)
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
    return true;
  }

  private static function validateClient()
  {
    echo "validateClient<br />";

    // check for unsupported auth methods
    if (!empty($_SERVER['HTTP_AUTHORIZATION']) && (explode(" ", $_SERVER['HTTP_AUTHORIZATION'])[0] != 'Basic')) {
      ErrorResponse::invalidClient();
      return false;
    }

    // check if client want to authenticate using header or body
    if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
      $clientId = $_SERVER['PHP_AUTH_USER'];
      $clientSecret = $_SERVER['PHP_AUTH_PW'];
    } else if (!empty($_POST['client_id'])) {
      $clientId = $_POST['client_id'];
      if (empty($_POST['client_secret'])) {
        $clientSecret = NULL;
      } else {
        $clientSecret = $_POST['client_secret'];
      }
    }

    echo "clientId: " . $clientId . "<br />";
    echo "clientSecret: '" . $clientSecret . "'<br />";

    // TODO: get client info from database
    // TODO: if client id weren't found, return error invalid_client
    // TODO: check whether the client were issued a client secret and if so check that it matches the one in the request
  }

  private static function validateUser()
  {

  }
}
?>
