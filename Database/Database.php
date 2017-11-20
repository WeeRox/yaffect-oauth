<?php
namespace Database;

use MySQLi;

class Database
{

  private static $connection;

  function __construct()
  {
    if (!isset(self::$connection)) {
      $config = include __DIR__ . "/../config.php";

      self::$connection = new mysqli($config['hostname'], $config['username'], $config['password'], $config['database']);
    }

    if (self::$connection === false) {
      echo "database error";
      // TODO: handle error
    }
  }

  public function query($query)
  {
    $result = self::$connection->query($query);
    return $result;
  }
}
?>
