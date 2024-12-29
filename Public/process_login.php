<?php
session_start();
require_once '../Core/DB.php'; // Veritabanı bağlantı dosyanız.

$db = new DB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Kullanıcıyı veritabanında kontrol et
    $user = $db->fetch("SELECT * FROM Users WHERE Email = ?", [$email]);

    if ($user && password_verify($password, $user['Password'])) {
        // Giriş başarılı, kullanıcı bilgilerini oturuma kaydet
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['role'] = $user['Role']; // Kullanıcı rolü varsa kaydet
        header('Location: index.php'); // Ana sayfaya yönlendir
        exit();
    } else {
        // Giriş başarısız, hata mesajı ile geri yönlendir
        header('Location: login.php?error=Invalid email or password');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
