<?php
require_once '../Classes/Navbar.php';
session_start();

$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : null;
$navbar = new Navbar($userRole);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hakkımızda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .about-section {
            padding: 50px;
            text-align: center;
            background-color: #f4f4f4;
            margin-top: 50px;
        }
        .about-section h1 {
            font-size: 2.5rem;
            color: #007bff;
        }
        .about-section p {
            font-size: 1.2rem;
            line-height: 1.8;
        }
        .contact-card {
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #fff;
            max-width: 600px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .contact-card img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 20px;
        }
        .contact-card h5 {
            margin: 0;
            font-size: 1.5rem;
            color: #007bff;
        }
        .contact-card p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
<?php $navbar->render(); ?>
<div class="about-section">
    <h1>Hakkımızda</h1>
    <p>
    ProdTrack, fabrikaların üretim süreçlerini dijitalleştirerek daha verimli ve sürdürülebilir bir üretim deneyimi sunmayı hedefleyen yenilikçi bir platformdur.
     Malzeme ve makine yönetimi, satış işlemleri, stok takibi ve ayrıntılı raporlama gibi birçok özelliği bir araya getirerek kullanıcıların kaynaklarını en verimli şekilde kullanmalarını sağlar.
      Kullanıcı dostu arayüzü ve modern teknolojileri sayesinde, işletmelerin operasyonel maliyetlerini düşürürken üretkenliği artırır.
     ProdTrack, çevre dostu yaklaşımları ve esnek yapısı ile endüstriyel yönetimde fark yaratır.
    </p>
</div>
<div class="container">
    <div class="contact-card">
        <img src="../images/omer.jpeg" alt="Ömer Faruk İlhan">
        <div>
            <h5>Ömer Faruk İlhan</h5>
            <p>Email: 231613041@cbu.edu.tr</p>
            <p>Telefon: 0531 253 84 69</p>
        </div>
    </div>
    <div class="contact-card">
        <img src="../images/arda.jpeg" alt="Arda İlktuğ">
        <div>
            <h5>Arda İlktuğ</h5>
            <p>Email: 231613023@ogr.cbu.edu.tr</p>
            <p>Telefon: 0543 543 20 05</p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
