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
    <style>
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
<?php $navbar->render(); ?>
<div class="container mt-4">
    <h1 class="text-center">Parçalar</h1>
    <div class="row">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="<?= htmlspecialchars($product['ImagePath'] ?? 'Pics/default.jpg') ?>" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($product['ProductName']) ?>">
                    <div class="card-body">
                        <h5 class="card-title text-center"><?= htmlspecialchars($product['ProductName']) ?></h5>
                        <p class="card-text text-muted text-center">
                            <?= htmlspecialchars($product['Description'] ?? 'Açıklama bulunmamaktadır.') ?>
                        </p>
                        <h6>Malzemeler:</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Malzeme</th>
                                    <th>Miktar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productMaterials as $material): ?>
                                    <?php if ($material['ProductID'] == $product['ProductID']): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($material['MaterialName']) ?></td>
                                            <td><?= $material['Quantity'] ?> adet</td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white mt-5">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-4">
                <h5>İletişim</h5>
                <p>Adres: Fabrika Mah. 123 Sok. No:45</p>
                <p>Telefon: +90 555 555 5555</p>
                <p>Email: info@fabrikaprojesi.com</p>
            </div>
            <div class="col-md-4 text-center">
                <h5>Sosyal Medya</h5>
                <a href="#" class="text-white me-2">Facebook</a>
                <a href="#" class="text-white me-2">Twitter</a>
                <a href="#" class="text-white">LinkedIn</a>
            </div>
            <div class="col-md-4 text-end">
                <h5>Hızlı Linkler</h5>
                <a href="index.php" class="text-white d-block">Ana Sayfa</a>
                <a href="product_showcase.php" class="text-white d-block">Ürünler</a>
                <a href="contact.php" class="text-white d-block">İletişim</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
