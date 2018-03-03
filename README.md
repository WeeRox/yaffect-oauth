# yaffect-oauth
This is the OAuth 2 server for the [Yaffect Android application](https://github.com/WeeRox/yaffect-android).

## Getting started

### Prerequisites
#### Apache
This project needs a web server to function. The project have only been tested with [Apache 2.4](https://httpd.apache.org/), but feel free to test with other web servers!

This is the VirtualHost configuration that I'm using for this project:
```apache
<VirtualHost *:80>
  DocumentRoot "/path/to/directory"
  <Directory "/path/to/directory">
    AllowOverride All
  </Directory>
</VirtualHost>
```

#### Database
This project requires MySQL-compatible databases, e.g. [MySQL](https://www.mysql.com/) or [MariaDB](https://mariadb.org/).

#### PHP
This project have been developed using [PHP 7.2](http://php.net/), but might work with other versions of PHP. 
If you are compiling PHP from source, make sure to use the [--with-mysqli](http://php.net/manual/en/mysqli.installation.php) flag. 

### Install
Clone this repostiory into a directory of your choice and point to that directory in your `httpd-vhosts.conf` file. 

Add a file named `config.php` in the root directory of the repository containing the information about the server, for example:
```php
<?php
return array(
  'hostname' => '[hostname]',
  'username' => '[username]',
  'password' => '[password]',
  'database' => '[database-name]',
  'debug' => true
);
?>
```
If `debug` is set to true the server will output error messages. 
