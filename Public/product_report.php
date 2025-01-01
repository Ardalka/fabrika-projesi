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

// Parça raporu verileri
$productSalesData = [];
$mostSoldProduct = null;
if ($selectedDate) {
    $productSalesData = $report->getProductSalesData($selectedDate);
    $mostSoldProduct = $report->getMostSoldProduct($selectedDate);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parça Raporu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 300px;
            height: 300px;
            margin: 0 auto;
        }
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
    </style>
</head>
<body>
<?php $navbar->render(); ?>
<div class="container mt-5">
    <h1 class="text-center text-primary">Parça Raporu</h1>
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
                <h4>Günlük Satılan Parçalar</h4>
                <div class="chart-container">
                    <canvas id="productSalesChart"></canvas>
                </div>
            </div>

            <?php if ($mostSoldProduct): ?>
                <div class="report-card">
                    <h4>En Çok Satılan Parça</h4>
                    <p class="text-center highlight">
                        <?= htmlspecialchars($mostSoldProduct['ProductName']) ?> (<?= $mostSoldProduct['TotalSold'] ?> adet)
                    </p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
const productSalesData = <?= json_encode($productSalesData) ?>;

const productLabels = productSalesData.map(item => item.ProductName);
const productValues = productSalesData.map(item => item.TotalSold);

new Chart(document.getElementById('productSalesChart'), {
    type: 'pie',
    data: {
        labels: productLabels,
        datasets: [{
            data: productValues,
            backgroundColor: productLabels.map(() => `#${Math.floor(Math.random()*16777215).toString(16)}`),
        }]
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
