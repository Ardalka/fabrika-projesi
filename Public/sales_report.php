<?php
require_once '../Core/DB.php';
require_once '../Classes/Navbar.php';
require_once '../Classes/Report.php';

session_start();

$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$navbar = new Navbar($userRole);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$db = new DB();
$report = new Report($db);
$selectedDate = $_POST['selectedDate'] ?? null;

// Satış raporu verileri
$salesData = [];
$totalProfitLoss = 0; // Toplam kar/zarar
if ($selectedDate) {
    $salesData = $report->getDailySales($selectedDate);
    $totalProfitLoss = array_reduce($salesData, function ($carry, $sale) {
        return $carry + $sale['ProfitLoss'];
    }, 0);
}

// Satış verilerini tarih ve saat sırasına göre sıralama
usort($salesData, function ($a, $b) {
    return strtotime($a['SaleDate']) - strtotime($b['SaleDate']);
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satış Raporu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .report-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            background-color: #f9f9f9;
            margin-top: 20px;
        }
        .report-card h4 {
            text-align: center;
            color: #007bff;
        }
        .highlight {
            background-color: #e7f1ff;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .total-profit-loss {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<?php $navbar->render(); ?>
<div class="container mt-5">
    <h1 class="text-center text-primary">Satış Raporu</h1>
    <form method="POST" class="mt-4">
        <div class="row mb-3">
            <label for="selectedDate" class="form-label">Tarih Seçin:</label>
            <input type="date" id="selectedDate" name="selectedDate" class="form-control" value="<?= htmlspecialchars($selectedDate ?? '') ?>" required>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Raporu Göster</button>
        </div>
    </form>

    <?php if ($selectedDate): ?>
        <div class="mt-5">
            <h3 class="text-center">Seçilen Tarih: <span class="highlight"><?= htmlspecialchars($selectedDate) ?></span></h3>
            
            <div class="report-card">
                <h4>Günlük Satış Verileri</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Satış ID</th>
                            <th>Tarih</th>
                            <th>Ürün</th>
                            <th>Miktar</th>
                            <th>Toplam Fiyat (TL)</th>
                            <th>Kâr/Zarar (TL)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($salesData as $sale): ?>
                            <tr>
                                <td><?= htmlspecialchars($sale['SaleID']) ?></td>
                                <td><?= htmlspecialchars($sale['SaleDate']) ?></td>
                                <td><?= htmlspecialchars($sale['ProductName']) ?></td>
                                <td><?= htmlspecialchars($sale['Quantity']) ?></td>
                                <td><?= number_format($sale['TotalPrice'], 2) ?></td>
                                <td class="<?= $sale['ProfitLoss'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= number_format($sale['ProfitLoss'], 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="total-profit-loss <?= $totalProfitLoss >= 0 ? 'text-success' : 'text-danger' ?>">
                    Toplam Kâr/Zarar: <?= number_format($totalProfitLoss, 2) ?> TL
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
