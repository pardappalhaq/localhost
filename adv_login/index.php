<?php
require("conf/conn.php");

$login_status = $login->cek_login();
if($login_status){
    // Bawa ke halaman home
    header("location:home.php");
    exit();
} else {
    // Include form log in jika belum log in
    include "login.php";
}
?>