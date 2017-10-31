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
?>
