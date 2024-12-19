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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maintenance'])) {
    $machineId = $_POST['machineId'];
    $machine->performMaintenance($machineId);
    header('Location: machine_management.php');
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
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Makine Adı</th>
                <th>Toplam Çalışma Süresi (saat)</th>
                <th>Toplam Elektrik Tüketimi (kWh)</th>
                <th>Toplam Karbon Ayak İzi (kg CO2)</th>
                <th>Sağlık Durumu (%)</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($machines as $mach): ?>
                <tr>
                    <td><?= $mach['MachineName'] ?></td>
                    <td><?= $mach['TotalWorkTime'] ?></td>
                    <td><?= $mach['TotalEnergyUsed'] ?></td>
                    <td><?= $mach['TotalCarbonProduced'] ?></td>
                    <td><?= $mach['Health'] ?>%</td>
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