<?php
require_once 'db.php'; // Veritabanı bağlantısı

// Mesaj değişkeni
$registerMessage = '';

// Kayıt işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isim = $_POST['isim'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Şifreyi düz metin olarak kaydet
    $query = "INSERT INTO kullanicilar (KullaniciAdi, Sifre, Eposta, KayitTarihi, YetkiSeviyesi)
              VALUES (:isim, :sifre, :email, NOW(), 0)";
    $stmt = $pdo->prepare($query);

    try {
        $stmt->execute([
            ':isim' => $isim,
            ':sifre' => $password, // Şifre düz metin olarak kaydediliyor
            ':email' => $email
        ]);
        $registerMessage = "Kayıt başarılı! <a href='login.php'>Giriş yap</a>";
    } catch (PDOException $e) {
        $registerMessage = "Kayıt sırasında bir hata oluştu: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
</head>
<body>
    <h1>Kayıt Ol</h1>
    <form method="POST" action="">
        <label for="isim">İsim:</label>
        <input type="text" name="isim" id="isim" required><br><br>
        
        <label for="email">E-posta:</label>
        <input type="email" name="email" id="email" required><br><br>
        
        <label for="password">Şifre:</label>
        <input type="password" name="password" id="password" required><br><br>
        
        <button type="submit">Kayıt Ol</button>
    </form>
    <p style="color: green;"><?= $registerMessage ?></p>

    <!-- Hesabım Var Butonu -->
    <p>Hesabınız var mı? <a href="login.php">Giriş yap</a></p>
</body>
</html>
