<?php
session_start();
require_once '../Core/DB.php'; // Veritabanı bağlantı dosyanız.

$db = new DB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['Username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Şifre eşleşim kontrolü
    if ($password !== $confirm_password) {
        header('Location: register.php?error=Passwords do not match');
        exit();
    }

    // Aynı email ile kayıt kontrolü
    $existingUser = $db->fetch("SELECT * FROM Users WHERE Email = ?", [$email]);
    if ($existingUser) {
        header('Location: register.php?error=Email already exists');
        exit();
    }

    // Şifreyi hashleyerek kayıt et
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $db->execute(
        "INSERT INTO Users (Username, Email, Password, Role) VALUES (?, ?, ?, ?)",
        [$username, $email, $hashedPassword, 'user'] // Varsayılan rol "user"
    );

    // Başarı mesajı
    header('Location: register.php?success=Registration successful! You can now login.');
    exit();
} else {
    header('Location: register.php');
    exit();
}
