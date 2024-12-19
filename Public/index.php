<?php
session_start();
require_once '../Classes/Navbar.php';

// Kullanıcının rolüne göre Navbar'ı oluştur
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$navbar = new Navbar($userRole);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fabrika Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php $navbar->render(); ?>
    <div class="container mt-5">
        <h1 class="text-center text-primary">Fabrikaya Hoş Geldiniz</h1>
        <p class="text-center">Fabrikamızın sürdürülebilirliği ve çevre dostu yaklaşımı hakkında daha fazla bilgi edinin.</p>
        <div class="text-center">
            <a href="sales.php" class="btn btn-primary">Satış Sayfasına Git</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
