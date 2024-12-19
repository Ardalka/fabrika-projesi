<?php
require_once '../Core/Config.php';
require_once '../Core/DB.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $db = new DB();
    $user = $db->fetch(
        "SELECT * FROM Users WHERE UserName = :username",
        [':username' => $username]
    );

    if ($user && hash_equals($user['PasswordHash'], hash('sha256', $password))) {
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['role'] = $user['Role'];
        header('Location: index.php');
        exit();
    } else {
        echo "Geçersiz kullanıcı adı veya şifre.";
    }
}
?>
