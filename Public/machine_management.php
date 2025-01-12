<?php
require_once '../Classes/Machine.php';
require_once '../Classes/Navbar.php';
require_once '../Core/DB.php';
require_once '../Classes/Balance.php';

session_start();

// Admin yetkisi kontrolü
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
if ($userRole !== 'admin') {
    header('Location: login.php');
    exit();
}

$navbar = new Navbar($userRole);
$db = new DB();
$balance = new Balance();

// Manuel olarak tanımlanan makineler
$machineObjects = [
    new Machine(1, $db, $balance), // Yavaş Makine
    new Machine(2, $db, $balance)  // Hızlı Makine
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maintenance'])) {
    $machineId = $_POST['machineId'];
    try {
        foreach ($machineObjects as $machine) {
            if ($machine->getId() == $machineId) {
                $result = $machine->performMaintenance();
                $_SESSION['successMessage'] = $result;
                break;
            }
        }
    } catch (Exception $e) {
        $_SESSION['errorMessage'] = $e->getMessage();
    }
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
<div class="container mt-5">
    <h1 class="text-center text-primary mb-4">Makine Yönetimi</h1>

    <?php if (isset($_SESSION['successMessage'])): ?>
        <div class="alert alert-success text-center"><?= $_SESSION['successMessage'] ?></div>
        <?php unset($_SESSION['successMessage']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['errorMessage'])): ?>
        <div class="alert alert-danger text-center"><?= $_SESSION['errorMessage'] ?></div>
        <?php unset($_SESSION['errorMessage']); ?>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-primary">
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
                <?php foreach ($machineObjects as $machine): ?>
                    <?php 
                        $stats = $machine->getMachineStats();
                        $maintenanceCost = (100 - $machine->getHealth()) * $machine->getMaintenanceCostPerUnit();
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($machine->getName()) ?></td>
                        <td><?= number_format($stats['TotalWorkTime'], 2) ?> saat</td>
                        <td><?= number_format($stats['TotalEnergyUsed'], 2) ?> kWh</td>
                        <td><?= number_format($stats['TotalCarbonProduced'], 2) ?> kg CO2</td>
                        <td>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar <?= $machine->getHealth() > 50 ? 'bg-success' : ($machine->getHealth() > 20 ? 'bg-warning' : 'bg-danger') ?>"
                                    role="progressbar" style="width: <?= $machine->getHealth() ?>%;"
                                    aria-valuenow="<?= $machine->getHealth() ?>" aria-valuemin="0" aria-valuemax="100">
                                    <?= htmlspecialchars($machine->getHealth()) ?>%
                                </div>
                            </div>
                        </td>
                        <td><?= $machine->getHealth() < 100 ? number_format($maintenanceCost, 2) : '-' ?></td>
                        <td>
                            <?php if ($machine->getHealth() < 100): ?>
                                <form method="POST">
                                    <input type="hidden" name="machineId" value="<?= $machine->getId() ?>">
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
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
