<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Fabrikaya Giriş</h1>
        <form action="login_process.php" method="POST">
            <label for="username">Kullanıcı Adı:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Şifre:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <button type="submit">Giriş Yap</button>
        </form>
    </div>
</body>
</html>
