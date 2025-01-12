<?php
require_once '../Classes/Product.php';
require_once '../Core/DB.php';
require_once '../Classes/Navbar.php';

session_start();

$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$navbar = new Navbar($userRole);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$db = new DB();

// Manuel olarak tanımlanmış ürün nesneleri
$productObjects = [
    new Product(1, $db), // Kaput
    new Product(2, $db), // Ayna
    new Product(3, $db), // Direksiyon
    new Product(4, $db), // Lastik
    new Product(5, $db), // Far
    new Product(6, $db), // Kapı
    new Product(7, $db), // Tavan
    new Product(8, $db)  // Koltuk
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update'])) {
            $productId = $_POST['id'];
            $name = $_POST['name'];
            $imagePath = $_POST['imagePath'];
            $description = $_POST['description'];
            
            foreach ($productObjects as $product) {
                if ($product->getId() == $productId) {
                    $product->updateProduct($name, $imagePath, $description);
                    break;
                }
            }
        }
    } catch (Exception $e) {
        $_SESSION['errorMessage'] = $e->getMessage();
    }

    header('Location: product_management.php');
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
    <title>Ürün Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php $navbar->render(); ?>
<div class="container mt-5">
    <h1 class="text-center text-primary mb-4">Ürün Yönetimi</h1>

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
                    <th>Ürün Adı</th>
                    <th>Resim</th>
                    <th>Açıklama</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productObjects as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product->getId()) ?></td>
                        <td><?= htmlspecialchars($product->getName()) ?></td>
                        <td><img src="<?= htmlspecialchars($product->getImagePath()) ?>" alt="Ürün Resmi" width="50"></td>
                        <td><?= htmlspecialchars($product->getDescription()) ?></td>
                        <td>
                            <form action="" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?= $product->getId() ?>">
                                <input type="text" name="name" value="<?= htmlspecialchars($product->getName()) ?>" required>
                                <input type="text" name="imagePath" value="<?= htmlspecialchars($product->getImagePath()) ?>" required>
                                <input type="text" name="description" value="<?= htmlspecialchars($product->getDescription()) ?>" required>
                                <button type="submit" name="update" class="btn btn-primary btn-sm">Güncelle</button>
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
