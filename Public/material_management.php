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
    if (isset($_POST['add'])) {
        $material->addMaterial($_POST['name'], $_POST['cost'], $_POST['stock']);
    } elseif (isset($_POST['update'])) {
        $material->updateMaterial($_POST['id'], $_POST['name'], $_POST['cost'], $_POST['stock']);
    } elseif (isset($_POST['addStock'])) {
        $material->purchaseMaterial($_POST['id'], $_POST['quantity']);
    }
    header('Location: material_management.php');
    exit();
}
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
<div class="container mt-4">
    <h1>Malzeme Yönetimi</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Malzeme Adı</th>
                <th>Birim Maliyet</th>
                <th>Stok</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($materials as $mat): ?>
                <tr>
                    <td><?= $mat['MaterialID'] ?></td>
                    <td><?= $mat['MaterialName'] ?></td>
                    <td><?= $mat['CostPerUnit'] ?></td>
                    <td><?= $mat['Stock'] ?></td>
                    <td>
                        <form action="" method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $mat['MaterialID'] ?>">
                        </form>
                        <form action="" method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $mat['MaterialID'] ?>">
                            <input type="number" name="quantity" class="form-control form-control-sm d-inline" style="width: 100px;" min="1" placeholder="Miktar">
                            <button type="submit" name="addStock" class="btn btn-success btn-sm">Stok Ekle</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
