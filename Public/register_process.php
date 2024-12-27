<?php
require_once '../Core/DB.php';
$db = new DB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    try {
        $db->execute(
            "INSERT INTO Users (Username, Email, Password, Role) VALUES (:username, :email, :password, :role)",
            [
                ':username' => $username,
                ':email' => $email,
                ':password' => $password,
                ':role' => $role
            ]
        );
        header('Location: login.php');
        exit();
    } catch (PDOException $e) {
        // Eğer email benzersiz değilse bir hata göster
        if ($e->getCode() === '23000') {
            echo "Bu e-posta adresi zaten kullanılıyor.";
        } else {
            echo "Bir hata oluştu: " . $e->getMessage();
        }
    }
}
?>
