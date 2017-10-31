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

//Include config for MySQL server
$config = include "config.php";

//Create a connection to the MySQL server
$db = new mysqli($config['hostname'], $config['username'], $config['password'], $config['database']);

$db->close();
?>
