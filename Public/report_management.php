<?php
require_once '../Core/DB.php';
require_once '../Classes/Navbar.php';

session_start();

$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$navbar = new Navbar($userRole);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card img {
            height: 150px;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
        }
        .card-title {
            text-align: center;
            color: #007bff;
        }
    </style>
</head>
<body>
<?php $navbar->render(); ?>
<div class="container">
    <h1 class="text-center text-primary">Rapor Yönetimi</h1>
    <div class="row mt-5">
        <div class="col-md-4">
            <div class="card">
                <a href="sales_report.php" class="text-decoration-none">
                    <img src="../images/sales_report.jpg" class="card-img-top" alt="Satış Raporu">
                    <div class="card-body">
                        <h5 class="card-title">Satış Raporu</h5>
                        <p class="card-text text-center text-muted">Günlük satış verilerini ve kâr/zarar analizi.</p>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <a href="product_report.php" class="text-decoration-none">
                    <img src="../images/product_report.jpg" class="card-img-top" alt="Parça Raporu">
                    <div class="card-body">
                        <h5 class="card-title">Parça Raporu</h5>
                        <p class="card-text text-center text-muted">Günlük satılan parça bilgileri ve grafikler.</p>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <a href="material_report.php" class="text-decoration-none">
                    <img src="../images/material_report.jpg" class="card-img-top" alt="Malzeme Raporu">
                    <div class="card-body">
                        <h5 class="card-title">Malzeme Raporu</h5>
                        <p class="card-text text-center text-muted">Günlük satın alınan malzeme bilgileri ve grafikler.</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
