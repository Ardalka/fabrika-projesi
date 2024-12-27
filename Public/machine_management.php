<?php
require_once '../Classes/Machine.php';
require_once '../Classes/Navbar.php';
session_start();

// Admin yetkisi kontrolü
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
if ($userRole !== 'admin') {
    header('Location: login.php');
    exit();
}

// Navbar ve Machine sınıfını oluştur
$navbar = new Navbar($userRole);
$machine = new Machine();
$machines = $machine->getAllMachines();

$machineStats = [];
foreach ($machines as $mach) {
    $machineStats[$mach['MachineID']] = $machine->getMachineStats($mach['MachineID']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maintenance'])) {
    $machineId = $_POST['machineId'];
    try {
        $result = $machine->performMaintenance($machineId);
        $_SESSION['successMessage'] = $result; // Mesajı oturumda sakla
    } catch (Exception $e) {
        $_SESSION['errorMessage'] = $e->getMessage(); // Hata mesajını oturumda sakla
    }
    header('Location: machine_management.php'); // Sayfayı yenile
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makine Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php $navbar->render(); ?>
<div class="container mt-4">
    <h1>Makine Yönetimi</h1>

    <?php if (isset($_SESSION['successMessage'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['successMessage'] ?>
        </div>
        <?php unset($_SESSION['successMessage']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['errorMessage'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['errorMessage'] ?>
        </div>
        <?php unset($_SESSION['errorMessage']); ?>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Makine Adı</th>
                <th>Toplam Çalışma Süresi (saat)</th>
                <th>Toplam Elektrik Tüketimi (kWh)</th>
                <th>Toplam Karbon Ayak İzi (kg CO2)</th>
                <th>Sağlık Durumu (%)</th>
                <th>Bakım Maliyeti (TL)</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($machines as $mach): ?>
                <?php 
                    $stats = $machineStats[$mach['MachineID']] ?? null;
                    $healthDeficit = 100 - $mach['Health'];
                    $maintenanceCost = $healthDeficit * $mach['MaintenanceCostPerUnit'];
                ?>
                <tr>
                    <td><?= htmlspecialchars($mach['MachineName']) ?></td>
                    <td><?= $stats['TotalWorkTime'] ?? 0 ?></td>
                    <td><?= $stats['TotalEnergyUsed'] ?? 0 ?></td>
                    <td><?= $stats['TotalCarbonProduced'] ?? 0 ?></td>
                    <td><?= htmlspecialchars($mach['Health']) ?>%</td>
                    <td><?= $mach['Health'] < 100 ? number_format($maintenanceCost, 2) : '-' ?></td>
                    <td>
                        <?php if ($mach['Health'] < 100): ?>
                            <form method="POST">
                                <input type="hidden" name="machineId" value="<?= $mach['MachineID'] ?>">
                                <button type="submit" name="maintenance" class="btn btn-warning">Bakım Yap</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-success" disabled>Sağlık Tam</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
