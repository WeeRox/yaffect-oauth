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
    // TODO: check that the client didn't send multiple credentials

    // Check that all required parameters is present
    if (empty($_POST['username']) || empty($_POST['password'])) {
      ErrorResponse::invalidRequest();
      return false;
    }
    return true;
  }

  private static function validateClient()
  {
    if (explode(" ", $_SERVER['HTTP_AUTHORIZATION'])[0] == 'Basic') {
      if (!empty($_SERVER['PHP_AUTH_USER'])) {
        // TODO: get client info from database
        // TODO: if client id weren't found, return error invalid_client
        // TODO: check whether the client were issued a client secret and if so check that it matches the one in the request
      } else {
        // no id were passed in the authorization header
        ErrorResponse::invalidClient();
        return false;
      }
    } else {
      // the authentication type weren't Basic
      ErrorResponse::invalidClient();
      return false;
    }
  }

  private static function validateUser()
  {

  }
}
?>
