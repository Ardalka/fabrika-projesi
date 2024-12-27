<?php
require_once '../Classes/Navbar.php';
session_start();

// Kullanıcı rolünü belirle
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : null;
$navbar = new Navbar($userRole);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php $navbar->render(); ?>

    <div class="container mt-4">
        <h1>Hoş Geldiniz!</h1>
        <?php if ($userRole === null): ?>
            <p>Giriş yaparak fabrika yönetim sistemine erişebilirsiniz.</p>
            <a href="login.php" class="btn btn-primary">Giriş Yap</a>
            <a href="register.php" class="btn btn-secondary">Kayıt Ol</a>
        <?php else: ?>
            <p>Fabrika yönetim sistemine hoş geldiniz, <?= $userRole === 'admin' ? 'Admin' : 'Kullanıcı' ?>!</p>
            <p>Menüden bir seçenek belirleyin.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
