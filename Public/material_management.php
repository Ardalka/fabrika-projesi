<?php
require_once '../Classes/Material.php';
require_once '../Core/DB.php';
require_once '../Classes/Navbar.php';
require_once '../Classes/Balance.php';

session_start();

$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$navbar = new Navbar($userRole);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$db = new DB();
$balance = new Balance();

// Manuel olarak tanımlanmış materyal nesneleri
$materialObjects = [
    new Material(1, $db, $balance), // Çelik
    new Material(2, $db, $balance), // Alüminyum
    new Material(3, $db, $balance), // Bakır
    new Material(4, $db, $balance), // Plastik
    new Material(5, $db, $balance)  // Cam
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['addStock'])) {
            $materialId = $_POST['id'];
            $quantity = $_POST['quantity'];
            
            foreach ($materialObjects as $material) {
                if ($material->getId() == $materialId) {
                    $totalCost = $material->purchaseMaterial($quantity);
                    $_SESSION['successMessage'] = "Stok başarıyla eklendi. Harcanan toplam tutar: " . number_format($totalCost, 2) . " TL.";
                    break;
                }
            }
        }
    } catch (Exception $e) {
        $_SESSION['errorMessage'] = $e->getMessage();
    }

    header('Location: material_management.php');
    exit();
}

$successMessage = $_SESSION['successMessage'] ?? null;
$errorMessage = $_SESSION['errorMessage'] ?? null;
unset($_SESSION['successMessage'], $_SESSION['errorMessage']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Malzeme Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php $navbar->render(); ?>
<div class="container mt-5">
    <h1 class="text-center text-primary mb-4">Malzeme Yönetimi</h1>

    <?php if ($successMessage): ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($successMessage) ?></div>
    <?php elseif ($errorMessage): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Malzeme Adı</th>
                    <th>Birim Maliyet (TL)</th>
                    <th>Stok</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materialObjects as $material): ?>
                    <tr>
                        <td><?= htmlspecialchars($material->getId()) ?></td>
                        <td><?= htmlspecialchars($material->getName()) ?></td>
                        <td><?= number_format($material->getCostPerUnit(), 2) ?></td>
                        <td><?= htmlspecialchars($material->getStock()) ?></td>
                        <td>
                            <form action="" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?= $material->getId() ?>">
                                <div class="input-group">
                                    <input type="number" name="quantity" class="form-control form-control-sm" style="width: 100px;" min="1" placeholder="Miktar" required>
                                    <button type="submit" name="addStock" class="btn btn-success btn-sm">Stok Ekle</button>
                                </div>
                            </form>
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
