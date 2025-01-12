<?php
require_once '../Classes/Product.php';
require_once '../Classes/Machine.php';
require_once '../Classes/SalesManager.php';
require_once '../Core/DB.php';
require_once '../Classes/Navbar.php';
require_once '../Classes/Balance.php';

session_start();

$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$navbar = new Navbar($userRole);

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = new DB();
$balance = new Balance($db);
$salesManager = new SalesManager($db, $balance);

// Manuel olarak tanımlanan ürünler ve makineler
$products = [
    new Product(1, $db), // Kaput
    new Product(2, $db), // Ayna
    new Product(3, $db),  // Direksiyon
    new Product(4, $db), // Lastik
    new Product(5, $db), // Far
    new Product(6, $db), // Kapı
    new Product(7, $db), // Tavan
    new Product(8, $db)  // Koltuk
];

$machines = [
    new Machine(1, $db, $balance), // Yavaş Makine
    new Machine(2, $db, $balance)  // Hızlı Makine
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product'];
    $machineId = $_POST['machine'];
    $quantity = $_POST['quantity'];

    try {
        // Seçilen ürün ve makineyi bul
        $selectedProduct = null;
        $selectedMachine = null;

        foreach ($products as $product) {
            if ($product->getId() == $productId) {
                $selectedProduct = $product;
                break;
            }
        }

        foreach ($machines as $machine) {
            if ($machine->getId() == $machineId) {
                $selectedMachine = $machine;
                break;
            }
        }

        if (!$selectedProduct || !$selectedMachine) {
            throw new Exception("Seçilen ürün veya makine bulunamadı.");
        }

        // Satışı kaydet
        $salesManager->recordSale($_SESSION['user_id'], $selectedProduct, $selectedMachine, $quantity);
        $successMessage = "Sipariş verildi.";
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
    <title>Ürün Satışı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php $navbar->render(); ?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h1 class="text-center text-primary mb-4">Ürün Satışı</h1>

            <?php if (isset($successMessage)): ?>
                <div class="alert alert-success text-center"><?= htmlspecialchars($successMessage) ?></div>
            <?php elseif (isset($errorMessage)): ?>
                <div class="alert alert-danger text-center"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>

            <form action="" method="POST" class="bg-light p-4 rounded shadow">
                <div class="mb-3">
                    <label for="product" class="form-label">Ürün Seçin</label>
                    <select id="product" name="product" class="form-select" required>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= $product->getId() ?>"><?= htmlspecialchars($product->getName()) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="machine" class="form-label">Makine Seçin</label>
                    <select id="machine" name="machine" class="form-select" required>
                        <?php foreach ($machines as $machine): ?>
                            <option value="<?= $machine->getId() ?>">
                                <?= htmlspecialchars($machine->getName()) ?> - Üretim Süresi: <?= htmlspecialchars($machine->getProductionRate()) ?> saat
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Miktar</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" min="1" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success btn-lg">Sipariş Ver</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
