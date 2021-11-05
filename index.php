<?php
set_include_path("./src");

require_once('src/Router.php');
require_once('/users/21901956/private/mysql_config.php');
include_once 'model/CarStorageMySQL.php';
include_once 'model/AccountStorageMySQL.php';

$router = new Router();
$dsn = 'mysql:host='. MYSQL_HOST.';dbname=' . MYSQL_DB . ';charset=utf8mb4';
$user = MYSQL_USER;
$pass = MYSQL_PASSWORD;
$bd = new PDO($dsn, $user, $pass);
$carStorageSQL = new CarStorageMySQL($bd);
$accountStorageSQL = new AccountStorageMySQL($bd);
$router->main($carStorageSQL,$accountStorageSQL);
?>
