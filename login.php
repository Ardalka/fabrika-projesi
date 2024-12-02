<?php
require_once 'db.php'; // Veritabanı bağlantısı

$loginMessage = '';
$registerMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // Giriş işlemi
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $query = "SELECT * FROM kullanicilar WHERE Eposta = :email";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':email' => $email]);
        $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($kullanici) {
            if (password_verify($password, $kullanici['Sifre'])) {
                session_start();
                $_SESSION['kullaniciID'] = $kullanici['KullaniciID'];
                $_SESSION['yetkiSeviyesi'] = $kullanici['YetkiSeviyesi'];

                header("Location: index.php");
                exit();
            } else {
                $loginMessage = "Hatalı şifre! Lütfen tekrar deneyin.";
            }
        } else {
            $loginMessage = "Kullanıcı bulunamadı! Lütfen kayıt olun.";
        }
    } elseif (isset($_POST['register'])) {
        // Kayıt işlemi
        $kullaniciAdi = trim($_POST['kullaniciAdi']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $passwordConfirm = trim($_POST['passwordConfirm']);

        if (empty($kullaniciAdi) || empty($email) || empty($password) || empty($passwordConfirm)) {
            $registerMessage = "Tüm alanlar doldurulmalıdır!";
        } elseif ($password !== $passwordConfirm) {
            $registerMessage = "Şifreler eşleşmiyor!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO kullanicilar (KullaniciAdi, Eposta, Sifre) VALUES (:kullaniciAdi, :email, :sifre)";
            $stmt = $pdo->prepare($query);

            try {
                $stmt->execute([
                    ':kullaniciAdi' => $kullaniciAdi,
                    ':email' => $email,
                    ':sifre' => $hashedPassword,
                ]);
                $registerMessage = "Kayıt başarılı! Artık giriş yapabilirsiniz.";
            } catch (PDOException $e) {
                $registerMessage = "Kayıt başarısız: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş ve Kayıt</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="container" id="container">
    <!-- Giriş Yap -->
    <div class="form-container sign-in-container">
        <form method="POST" action="">
            <h1>Giriş Yap</h1>
            <input type="email" name="email" placeholder="E-posta" required>
            <input type="password" name="password" placeholder="Şifre" required>
            <a href="#">Şifrenizi mi unuttunuz?</a>
            <button type="submit" name="login">Giriş Yap</button>
            <?php if (!empty($loginMessage)): ?>
                <p class="error-message"><?= $loginMessage ?></p>
            <?php endif; ?>
        </form>
    </div>

    <!-- Kayıt Ol -->
    <div class="form-container sign-up-container">
        <form method="POST" action="">
            <h1>Kayıt Ol</h1>
            <input type="text" name="kullaniciAdi" placeholder="Kullanıcı Adı" required>
            <input type="email" name="email" placeholder="E-posta" required>
            <input type="password" id="password" name="password" placeholder="Şifre" required>
            <input type="password" id="passwordConfirm" name="passwordConfirm" placeholder="Şifre Tekrar" required>
            <label><input type="checkbox" id="showPassword"> Şifreyi Göster</label>
            <button type="submit" name="register">Kayıt Ol</button>
            <?php if (!empty($registerMessage)): ?>
                <p class="error-message"><?= $registerMessage ?></p>
            <?php endif; ?>
        </form>
    </div>

    <!-- Overlay -->
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Tekrar Hoş Geldiniz!</h1>
                <p>Giriş yapmak için bilgilerinizi girin</p>
                <button class="ghost" id="signIn">Giriş Yap</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Merhaba, Arkadaş!</h1>
                <p>Kayıt olmak için bilgilerinizi girin</p>
                <button class="ghost" id="signUp">Kayıt Ol</button>
            </div>
        </div>
    </div>
</div>
<button class="theme-toggle" id="themeToggle">Tema Değiştir</button>
<script src="login.js"></script>
</body>
</html>
