<?php
require_once 'db.php'; // Veritabanı bağlantısı

$loginMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Kullanıcıyı veritabanında kontrol et
    $query = "SELECT * FROM kullanicilar WHERE Eposta = :email";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':email' => $email]);
    $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($kullanici) {
        // Şifre kontrolü
        if ($password === $kullanici['Sifre']) { // Şifre düz metinse
            session_start();
            $_SESSION['kullaniciID'] = $kullanici['KullaniciID'];
            $_SESSION['yetkiSeviyesi'] = $kullanici['YetkiSeviyesi'];

            // Başarılı giriş yaptıktan sonra yönlendir
            header("Location: index.php");
            exit(); // Yönlendirmeden sonra PHP'nin çalışmasını durdur
        } else {
            $loginMessage = "Hatalı şifre!";
        }
    } else {
        $loginMessage = "Kullanıcı bulunamadı!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
</head>
<body>
    <h1>Giriş Yap</h1>
    <form method="POST" action="">
        <label for="email">E-posta:</label>
        <input type="email" name="email" id="email" required><br><br>
        
        <label for="password">Şifre:</label>
        <input type="password" name="password" id="password" required><br><br>
        
        <button type="submit">Giriş Yap</button>
    </form>
    <p style="color: red;"><?= $loginMessage ?></p>
</body>
</html>
