<?php
require_once '../Core/DB.php';
require_once '../Classes/Navbar.php';

session_start();

// Navbar
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$navbar = new Navbar($userRole);

$db = new DB();

// Tüm ürünleri ve malzemeleri al
$products = $db->fetchAll("SELECT * FROM Products");
$productMaterials = $db->fetchAll("
    SELECT p.ProductID, m.MaterialName, pm.Quantity 
    FROM ProductMaterials pm
    JOIN Materials m ON pm.MaterialID = m.MaterialID
    JOIN Products p ON pm.ProductID = p.ProductID
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parça Tanıtımı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php $navbar->render(); ?>
<div class="container mt-4">
    <h1>Parça Tanıtımı</h1>
    <div class="row">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="<?= htmlspecialchars($product['ImagePath'] ?? 'Pics/default.jpg') ?>" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($product['ProductName']) ?>" 
                         style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['ProductName']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($product['Description'] ?? 'Açıklama bulunmamaktadır.') ?></p>
                        <h6>Malzemeler:</h6>
                        <ul>
                            <?php foreach ($productMaterials as $material): ?>
                                <?php if ($material['ProductID'] == $product['ProductID']): ?>
                                    <li><?= htmlspecialchars($material['MaterialName']) ?>: <?= $material['Quantity'] ?> adet</li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
