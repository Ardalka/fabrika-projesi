<?php
require_once '../Core/DB.php';
session_start();
$db = new DB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $db->fetch("SELECT * FROM Users WHERE Email = :email", [':email' => $email]);

    if ($user && password_verify($password, $user['Password'])) {
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['role'] = $user['Role'];
        header('Location: index.php');
        exit();
    } else {
        echo "Geçersiz e-posta veya şifre.";
    }
}
?>
