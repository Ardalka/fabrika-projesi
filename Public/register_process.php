<?php
require_once '../Core/Config.php';
require_once '../Core/DB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $db = new DB();

    // Kullanıcı adı kontrolü
    $existingUser = $db->fetch(
        "SELECT * FROM Users WHERE UserName = :username",
        [':username' => $username]
    );

    if ($existingUser) {
        echo "Bu kullanıcı adı zaten alınmış.";
    } else {
        // Kullanıcı ekle
        $db->execute(
            "INSERT INTO Users (UserName, PasswordHash, Role) VALUES (:username, :password, 'user')",
            [
                ':username' => $username,
                ':password' => hash('sha256', $password)
            ]
        );
        header('Location: login.php');
        exit();
    }
}
?>
