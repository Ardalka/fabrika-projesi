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

// Karbon Ayak İzi Vergisi hesaplama
$carbonTaxDetails = $balance->calculateCarbonTax();
$totalCarbon = $carbonTaxDetails['totalCarbon'];
$carbonTax = $carbonTaxDetails['carbonTax'];

// Elektrik Faturası hesaplama
$electricityDetails = $balance->calculateElectricityBill();
$totalEnergy = $electricityDetails['totalEnergy'];
$electricityBill = $electricityDetails['electricityBill'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['payCarbonTax'])) {
        try {
            $message = $balance->payCarbonTax($carbonTax);
            $_SESSION['success_message'] = $message; // Başarı mesajını sakla
            header('Location: balance_management.php'); // Sayfayı yeniden yükle
            exit();
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
        }
    }

    if (isset($_POST['payElectricityBill'])) {
        try {
            $message = $balance->payElectricityBill($electricityBill);
            $_SESSION['success_message'] = $message; // Başarı mesajını sakla
            header('Location: balance_management.php'); // Sayfayı yeniden yükle
            exit();
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>{$e->getMessage()}</div>";
        }
    }
}
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

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success_message'] ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <p><strong>Mevcut Bakiye:</strong> <?= number_format($currentBalance, 2) ?> TL</p>

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Karbon Ayak İzi Vergisi</h5>
            <p>Toplam Karbon Ayak İzi: <strong><?= number_format($totalCarbon, 2) ?> kg CO2</strong></p>
            <p>Ödenecek Vergi: <strong><?= number_format($carbonTax, 2) ?> TL</strong></p>
            <form action="" method="POST">
                <button type="submit" name="payCarbonTax" class="btn btn-danger">Vergiyi Öde</button>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Elektrik Faturası</h5>
            <p>Toplam Elektrik Tüketimi: <strong><?= number_format($totalEnergy, 2) ?> kWh</strong></p>
            <p>Ödenecek Tutar: <strong><?= number_format($electricityBill, 2) ?> TL</strong></p>
            <form action="" method="POST">
                <button type="submit" name="payElectricityBill" class="btn btn-warning">Faturayı Öde</button>
            </form>
        </div>
    </div>

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
                    <td><?= htmlspecialchars($history['TransactionDate']) ?></td>
                    <td><?= ucfirst($history['TransactionType'] === 'tax' ? 'Vergi' : ($history['TransactionType'] === 'electricity' ? 'Elektrik Faturası' : $history['TransactionType'])) ?></td>
                    <td><?= number_format($history['Amount'], 2) ?> TL</td>
                    <td><?= htmlspecialchars($history['Description']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
