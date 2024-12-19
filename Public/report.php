<?php
require_once '../Classes/Report.php';
require_once '../Core/DB.php';
require_once '../Classes/Navbar.php';

session_start();

$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$navbar = new Navbar($userRole);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$report = new Report();

$carbonFootprint = $report->getCarbonFootprintByMachine();
$energyConsumption = $report->getEnergyConsumptionByMachine();
$mostSoldProduct = $report->getMostSoldProduct();
$salesByProduct = $report->getSalesByProduct();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raporlama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
</head>
<body>
<?php $navbar->render(); ?>
    <h1>Fabrika Raporları</h1>

    <h2>Karbon Ayak İzi (Makine Bazında)</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Makine Adı</th>
                <th>Toplam Karbon Ayak İzi (kg CO2)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($carbonFootprint as $data): ?>
                <tr>
                    <td><?= $data['MachineName'] ?></td>
                    <td><?= $data['TotalCarbon'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Enerji Tüketimi (Makine Bazında)</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Makine Adı</th>
                <th>Toplam Enerji Tüketimi (kWh)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($energyConsumption as $data): ?>
                <tr>
                    <td><?= $data['MachineName'] ?></td>
                    <td><?= $data['TotalEnergy'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>En Çok Satılan Ürün</h2>
    <p>Ürün: <?= $mostSoldProduct['ProductName'] ?> (Satış Adedi: <?= $mostSoldProduct['TotalSold'] ?>)</p>

    <h2>Ürün Satışları (Grafik)</h2>
    <canvas id="salesChart" width="400" height="200"></canvas>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesData = {
            labels: <?= json_encode(array_column($salesByProduct, 'ProductName')) ?>,
            datasets: [{
                label: 'Satış Adedi',
                data: <?= json_encode(array_column($salesByProduct, 'TotalSold')) ?>,
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545'],
                borderColor: ['#0056b3', '#1e7e34', '#d39e00', '#bd2130'],
                borderWidth: 1
            }]
        };
        new Chart(ctx, {
            type: 'bar',
            data: salesData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    
</body>
</html>
