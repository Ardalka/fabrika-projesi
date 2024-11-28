<?php
require 'db.php'; // Veritabanı bağlantısı

// Stokları çekiyoruz
try {
    $stmt = $pdo->query("SELECT * FROM malzemeler");
    $malzemeler = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Hata: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Durumu</title>
    <style>
        table {
            width: 50%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Kalan Stoklar</h1>
    <table>
        <thead>
            <tr>
                <th>Malzeme Adı</th>
                <th>Kalan Stok</th>
                <th>Adet Fiyatı (TL)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($malzemeler as $malzeme): ?>
            <tr>
                <td><?php echo $malzeme['MalzemeAdi']; ?></td>
                <td><?php echo $malzeme['StokMiktari']; ?></td>
                <td><?php echo $malzeme['AdetFiyati']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
