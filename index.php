<?php
set_include_path("./src");

require_once("Router.php");
require_once('/users/21901956/private/mysql_config.php');
include_once 'model/CarStorageMySQL.php';

/*
 * Cette page est simplement le point d'arrivée de l'internaute
 * sur notre site. On se contente de créer un routeur
 * et de lancer son main.
 */
$router = new Router();
//$animalStorage = new AnimalStorageFile($_SERVER['TMPDIR'].'/test');
//$animalStorage->reinit();
$dsn = 'mysql:host='. MYSQL_HOST.';dbname=' . MYSQL_DB . ';charset=utf8mb4';
$user = MYSQL_USER;
$pass = MYSQL_PASSWORD;
$bd = new PDO($dsn, $user, $pass);
$carStorageSQL = new CarStorageMySQL($bd);
$router->main($carStorageSQL);
?>
