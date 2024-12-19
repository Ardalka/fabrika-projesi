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

$product = new Product();
$products = $product->getAllProducts();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $product->addProduct($_POST['name']);
    } elseif (isset($_POST['update'])) {
        $product->updateProduct($_POST['id'], $_POST['name']);
    } elseif (isset($_POST['delete'])) {
        $product->deleteProduct($_POST['id']);
    }
    header('Location: product_management.php');
    exit();
}
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
    <h1>Ürün Yönetimi</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ürün Adı</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $prod): ?>
                <tr>
                    <td><?= $prod['ProductID'] ?></td>
                    <td><?= $prod['ProductName'] ?></td>
                    <td>
                        <form action="" method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $prod['ProductID'] ?>">
                            <button type="submit" name="delete">Sil</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Yeni Ürün Ekle</h2>
    <form action="" method="POST">
        <label for="name">Ürün Adı:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <button type="submit" name="add">Ekle</button>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
