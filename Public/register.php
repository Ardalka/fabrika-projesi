<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-card {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        .register-card h2 {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <h2>Kayıt Ol</h2>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>
        <form action="process_register.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Kullanıcı Adı</label>
                <input type="text" id="Username" name="Username" class="form-control" placeholder="Kullanıcı adınızı giriniz" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Email giriniz" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Şifre</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Şifrenizi giriniz" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Şifre Tekrar</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Şifrenizi Doğrulayınız" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Kayıt ol</button>
        </form>
        <p class="mt-3 text-center">
            Zaten hesabın var mı? <a href="login.php">Giriş Yap</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
