<?php
session_start();

$user = "root";
$pass = "";
$db_name = "adv_login";
$host = "localhost";

$db = new PDO('mysql:host='.$host.';dbname='.$db_name.';charset=utf8', $user, $pass);

require_once("conf/library.php");
require_once("conf/ClassLogin.php");

$login = new Login();
?>