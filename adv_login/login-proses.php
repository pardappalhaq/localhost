<?php
include "conf/conn.php";

if(!$login->cek_salah_login()){
    // Kalau user salah login melebihi batas (5 kali), proses langsung berhenti
    create_alert("error", "Mohon maaf Anda tidak dapat login lagi karena kesalahan login Anda terlalu banyak. Hubungi Administrator untuk informasi lebih lanjut", "index.php");
}

if(isset($_POST['btn'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Step 1: Cek apakah username ada di tabel
    $cek = $db->query("SELECT * FROM tb_admin WHERE user = ".$db->quote($username));
    if($cek->rowCount() > 0){
        // Username ada, tangkap password yg ada di database
        $row = $cek->fetch();
        $password_db = $row['pass'];

        if(password_verify($password, $password_db)){
            // Password sudah cocok
            $expired = 0;
            if(isset($_POST['remember'])){
                if($_POST['remember'] == 1){
                    $expired = '+1 year'; // 1 tahun
                }
            }
            // Pencatatan token dilakukan di sini
            $login->true_login($username, $expired);
            create_alert("success", "Log In Berhasil", "index.php");
        } else {
            // Password tidak cocok, catat kesalahan login
            $login->salah_login_action($username);
            create_alert("error", "Username atau password tersebut salah", "index.php");
        }
    } else {
        // Username tidak ditemukan, catat kesalahan login
        $login->salah_login_action($username);
        create_alert("error", "Username atau password tersebut tidak terdaftar", "index.php");
    }
}
?>