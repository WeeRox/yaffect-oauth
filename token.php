<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

use Response\ErrorResponse;
use Grant\ResourceOwnerPasswordCredentialsGrant;
use Database\Database;

// Register an autoloader for classes.
// The namespace will correspond to folder structure
spl_autoload_register(function ($class)
{
  $file = str_replace('\\', '/', $class) . '.php';
  if (file_exists($file)) {
    include $file;
  }
});

if (empty($_POST['grant_type'])) {
  ErrorResponse::invalidRequest();
} else {
  switch ($_POST['grant_type']) {
    case 'password':
      ResourceOwnerPasswordCredentialsGrant::respond();
      break;
    default:
      ErrorResponse::unsupportedGrantType($_POST['grant_type']);
      break;
  }
}
?>
