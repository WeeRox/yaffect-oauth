<?php
$config = include 'config.php';

if ($config['debug']) {
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
} else {
	error_reporting(0);
	ini_set('display_errors', 'Off');
}

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
use Grant\ResourceOwnerPasswordCredentialsGrant;
use Grant\RefreshTokenGrant;
use Database\Database;

if (empty($_POST['grant_type'])) {
  ErrorResponse::invalidRequest();
} else {
  switch ($_POST['grant_type']) {
    case 'password':
      ResourceOwnerPasswordCredentialsGrant::respond();
      break;
    case 'refresh_token':
      RefreshTokenGrant::respond();
      break;
    default:
      ErrorResponse::unsupportedGrantType($_POST['grant_type']);
      break;
  }
}
?>
