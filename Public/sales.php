<?php
require_once '../Classes/Product.php';
require_once '../Classes/Machine.php';
require_once '../Classes/SalesManager.php';
require_once '../Core/DB.php';
require_once '../Classes/Navbar.php';
session_start();

$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$navbar = new Navbar($userRole);

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$product = new Product();
$machine = new Machine();
$salesManager = new SalesManager();

$products = $product->getAllProducts();
$machines = $machine->getAllMachines();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product'];
    $machineId = $_POST['machine'];
    $quantity = $_POST['quantity'];

    // Makine ve ürün bilgilerini al
    $selectedMachine = $machine->getMachineById($machineId);
    $productionTime = $selectedMachine['ProductionRate'] * $quantity;
    $energyUsed = $selectedMachine['EnergyConsumption'] * $quantity;
    $carbonProduced = $selectedMachine['CarbonFootprint'] * $quantity;

    try {
        // Satışı kaydet
        $salesManager->recordSale($_SESSION['user_id'], $productId, $machineId, $quantity, $productionTime, $energyUsed, $carbonProduced);
        $successMessage = "Sipariş verildi";
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satış Sayfası</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php $navbar->render(); ?>
<div class="container mt-4">
    <h1>Ürün Satışı</h1>

    <?php if (isset($successMessage)): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
    <?php elseif (isset($errorMessage)): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="mb-3">
            <label for="product" class="form-label">Ürün Seçin:</label>
            <select id="product" name="product" class="form-select" required>
                <?php foreach ($products as $prod): ?>
                    <option value="<?= $prod['ProductID'] ?>"><?= htmlspecialchars($prod['ProductName']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="machine" class="form-label">Makine Seçin:</label>
            <select id="machine" name="machine" class="form-select" required>
                <?php foreach ($machines as $mach): ?>
                    <option value="<?= $mach['MachineID'] ?>">
                        <?= htmlspecialchars($mach['MachineName']) ?> - Üretim Süresi: <?= htmlspecialchars($mach['ProductionRate']) ?> saat
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Miktar:</label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="1" required>
        </div>

        <button type="submit" class="btn btn-primary">Sipariş Ver</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
