<?php
session_start();

if (!isset($_SESSION['kullaniciID'])) {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Navbar</title>
<link rel="stylesheet" href="index.css">
</head>
<body>

<div class="navbar">
    <div class="navbar-left">
        <a href="index.php">Ana Sayfa</a>
        <a href="satis.php">Satış</a>
        <a href="rapor.php">Raporlar</a>
        <a href="kullanici.php">Kullanıcılar</a>
        <a href="makineler.php">Makineler</a>
        <a href="stoklar.php">Stoklar</a>
        <a href="malzeme_ekle.php">Malzemeler</a>
    </div>

    <div class="navbar-right">
        <a href="#profil">Kullanıcı Adı</a>
        <a href="#cikis" class="logout">Çıkış Yap</a>
    </div>
</div>

<div class="card-container">
    <div class="card" onclick="showDetails(1)">
        <h2>Fabrika</h2>
        <p>Fabrika hakkında daha fazla bilgi almak istiyorsanız tıklayın.</p>
    </div>
    <div class="card" onclick="showDetails(2)">
        <h2>Makine</h2>
        <p>Makine hakkında daha fazla bilgi almak istiyorsanız tıklayın.</p>
    </div>
    <div class="card" onclick="showDetails(3)">
        <h2>Üretim</h2>
        <p>Üretim hakkında daha fazla bilgi almak istiyorsanız tıklayın.</p>
    </div>
</div>

<!-- Detay kartı: Burada içerik gösterilecek -->
<div class="detail-card" id="detail-card">
    <button class="back-button" onclick="hideDetails()">Geri</button>
    <div id="detail-content"></div>
</div>
<script src="index.js"></script>
</body>
</html>
