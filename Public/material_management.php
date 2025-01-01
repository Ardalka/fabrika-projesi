<?php
require_once '../Classes/Material.php';
require_once '../Core/DB.php';
require_once '../Classes/Navbar.php';

session_start();

$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$navbar = new Navbar($userRole);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$material = new Material();
$materials = $material->getAllMaterials();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add'])) {
            $material->addMaterial($_POST['name'], $_POST['cost'], $_POST['stock']);
        } elseif (isset($_POST['update'])) {
            $material->updateMaterial($_POST['id'], $_POST['name'], $_POST['cost'], $_POST['stock']);
        } elseif (isset($_POST['addStock'])) {
            $quantity = $_POST['quantity'];
            $materialId = $_POST['id'];
            $totalCost = $material->purchaseMaterial($materialId, $quantity);

            // Başarı mesajını oturum değişkenine kaydet
            $_SESSION['successMessage'] = "Stok başarıyla eklendi. Harcanan toplam tutar: " . number_format($totalCost, 2) . " TL.";
        }
    } catch (Exception $e) {
        // Hata mesajını oturum değişkenine kaydet
        $_SESSION['errorMessage'] = $e->getMessage();
    }

    header('Location: material_management.php');
    exit();
}

// Oturumdan mesajları al ve sıfırla
$successMessage = isset($_SESSION['successMessage']) ? $_SESSION['successMessage'] : null;
$errorMessage = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : null;
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

    <!-- Başarı ve Hata Mesajları -->
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
                <?php foreach ($materials as $mat): ?>
                    <tr>
                        <td><?= htmlspecialchars($mat['MaterialID']) ?></td>
                        <td><?= htmlspecialchars($mat['MaterialName']) ?></td>
                        <td><?= number_format($mat['CostPerUnit'], 2) ?></td>
                        <td><?= htmlspecialchars($mat['Stock']) ?></td>
                        <td>
                            <form action="" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?= $mat['MaterialID'] ?>">
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
