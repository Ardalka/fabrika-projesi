<?php
require_once '../Core/DB.php';
require_once '../Classes/Balance.php';
require_once '../Classes/Navbar.php';

session_start();

// Admin yetkisi kontrolü
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
if ($userRole !== 'admin') {
    header('Location: login.php');
    exit();
}

// Navbar oluştur
$navbar = new Navbar($userRole);

// Bakiye bilgisi
$balance = new Balance();
$currentBalance = $balance->getBalance();

// Geçmiş hareketleri al
$balanceHistory = $balance->getBalanceHistory();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bakiye Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php $navbar->render(); ?>
<div class="container mt-4">
    <h1>Fabrika Bakiyesi</h1>
    <p><strong>Mevcut Bakiye:</strong> <?= number_format($currentBalance, 2) ?> TL</p>

    <h2>Bakiye Geçmişi</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tarih</th>
                <th>İşlem Türü</th>
                <th>Tutar</th>
                <th>Açıklama</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($balanceHistory as $history): ?>
                <tr>
                    <td><?= $history['TransactionDate'] ?></td>
                    <td><?= ucfirst($history['TransactionType']) ?></td>
                    <td><?= number_format($history['Amount'], 2) ?> TL</td>
                    <td><?= $history['Description'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>