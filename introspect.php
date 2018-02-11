<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Register an autoloader for classes.
// The namespace will correspond to folder structure
spl_autoload_register(function ($class)
{
  $file = str_replace('\\', '/', $class) . '.php';
  if (file_exists($file)) {
    include $file;
  }
});

use Response\ErrorResponse;
use Response\IntrospectionResponse;
use Database\Database;

// Check that the request contains authentication info
if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
  // No authentication was included
  ErrorResponse::invalidClient();
} else {
  $auth_type = explode(" ", $_SERVER['HTTP_AUTHORIZATION'])[0];
  if ($auth_type === "Basic") {
    $clientId = $_SERVER['PHP_AUTH_USER'];

    if (empty($_SERVER['PHP_AUTH_PW'])) {
      $clientSecret = NULL;
    } else {
      $clientSecret = $_SERVER['PHP_AUTH_PW'];
    }

    // Check that all required parameters exist in request
    if (empty($_POST['token'])) {
      ErrorResponse::invalidRequest();
      return;
    }

    $token = $_POST['token'];

    $database = new Database();

    // Check if client exists in database
    if ($result = $database->query("SELECT * FROM clients WHERE client_id = UNHEX('" . $database->base64url2hex($clientId) . "');")) {
      // Check if client id exist in database
      if ($result->num_rows != 1) {
        ErrorResponse::invalidClient();
        return;
      }

      $row = $result->fetch_assoc();

      // Check that the client is authorized to use the introspection endpoint (e.g. the client is a resource server)
      if ($row['grant_type'] != 'introspect') {
        ErrorResponse::invalidClient();
        return;
      }

      // Check if client secret has been issued to the client
      if (!is_null($row['client_secret'])) {
        // Check that the client secrets matches
        if ($clientSecret !== $database->hex2base64url(bin2hex($row['client_secret']))) {
          ErrorResponse::invalidClient();
          return;
        }
      }

      $result->close();
    } else {
      // TODO: database error
      return;
    }

    // Check if request contains token type hint parameter
    if (!empty($_POST['token_type_hint'])) {
      if ($_POST['token_type_hint'] === 'access_token') {
        $tokenInfo = checkForAccessToken($token);

        // If the token didn't exist in the access tokens, check in the refresh tokens
        if ($tokenInfo === false) {
          $tokenInfo = checkForRefreshToken($token);
        }

        // The token weren't an access token nor a refresh token
        if ($tokenInfo === false) {
          IntrospectionResponse::invalidToken();
        } else {
          IntrospectionResponse::tokenInfo($tokenInfo);
        }
      } else if ($_POST['token_type_hint'] === 'refresh_token') {
        $tokenInfo = checkForRefreshToken($token);

        // If the token didn't exist in the refresh tokens, check in the access tokens
        if ($tokenInfo === false) {
          $tokenInfo = checkForAccessToken($token);
        }

        // The token weren't a refresh token nor an access token
        if ($tokenInfo === false) {
          IntrospectionResponse::invalidToken();
        } else {
          IntrospectionResponse::tokenInfo($tokenInfo);
        }
      } else {
        // The token type hint contains a unsupported value
        ErrorResponse::invalidRequest();
      }
    } else {
      // Start with looking in the access tokens
      $tokenInfo = checkForAccessToken($token);

      // If the token didn't exist in the access tokens, check in the refresh tokens
      if ($tokenInfo === false) {
        $tokenInfo = checkForRefreshToken($token);
      }

      // The token weren't an access token nor a refresh token
      if ($tokenInfo === false) {
        IntrospectionResponse::invalidToken();
      } else {
        IntrospectionResponse::tokenInfo($tokenInfo);
      }
    }
  } else {
    // Unsupported authentication method
    ErrorResponse::invalidClient();
  }
}

function checkForAccessToken($token)
{
  $database = new Database();

  if ($result = $database->query("SELECT *, TIMESTAMPDIFF(SECOND, UTC_TIMESTAMP(), expires) AS expired FROM access_tokens WHERE access_token = UNHEX('" . $database->base64url2hex($token) . "');")) {
    if ($result->num_rows == 0) {
      return false;
    }

    $row = $result->fetch_assoc();

    // TODO: convert row to array
    $tokenInfo = array();

    if ($row['expired'] > 0) {
      $tokenInfo['active'] = true;
    } else {
      $tokenInfo['active'] = false;
    }

    $tokenInfo['scope'] = $row['scope'];
    $tokenInfo['client_id'] = $database->hex2base64url(bin2hex($row['client_id']));
    $tokenInfo['user_id'] = $database->hex2base64url(bin2hex($row['user_id']));

    $result->close();
  }

  return $tokenInfo;
}

function checkForRefreshToken($token)
{
  $database = new Database();

  if ($result = $database->query("SELECT * FROM refresh_tokens WHERE refresh_token = UNHEX('" . $database->base64url2hex($token) . "');")) {
    if ($result->num_rows == 0) {
      return false;
    }

    $row = $result->fetch_assoc();

    // TODO: convert row to array
    $tokenInfo = array();

    $tokenInfo['active'] = true;
    $tokenInfo['scope'] = $row['scope'];
    $tokenInfo['client_id'] = $database->hex2base64url(bin2hex($row['client_id']));
    $tokenInfo['user_id'] = $database->hex2base64url(bin2hex($row['user_id']));

    $result->close();
  }

  return $tokenInfo;
}
?>
