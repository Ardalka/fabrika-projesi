<?php
require "makine.php";
require 'db.php';

// Eğer POST ile bir bakım işlemi yapılırsa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['makineID'])) {
    $makineID = intval($_POST['makineID']);

    try {
        // Sağlık durumunu %100'e çıkar ve diğer değerleri sıfırla
        $stmt = $pdo->prepare("UPDATE makineler 
                               SET SaglikDurumu = 100, CalismaSaati = 0, ElektrikTuketimi = 0, KarbonAyakIzi = 0 
                               WHERE MakineID = :makineID");
        $stmt->execute([':makineID' => $makineID]);

        $message = "Bakım başarılı! Makine {$makineID} yenilendi.";
    } catch (Exception $e) {
        $message = "Hata: " . $e->getMessage();
    }
}

// Veritabanından tüm makineleri al
$stmt = $pdo->query("SELECT * FROM makineler");
$makineler = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makineler</title>
</head>
<body>
    <h1>Makineler</h1>

    <!-- Mesaj Gösterimi -->
    <?php if (isset($message)): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <table border="1">
        <thead>
            <tr>
                <th>MakineID</th>
                <th>Makine Adı</th>
                <th>Çalışma Saati</th>
                <th>Elektrik Tüketimi</th>
                <th>Karbon Ayak İzi</th>
                <th>Son Çalışma Tarihi</th>
                <th>Sağlık Durumu</th>
                <th>Bakım</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($makineler as $makine): ?>
            <tr>
                <td><?php echo $makine['MakineID']; ?></td>
                <td><?php echo $makine['MakineAdi']; ?></td>
                <td><?php echo $makine['CalismaSaati']; ?> saat</td>
                <td><?php echo $makine['ElektrikTuketimi']; ?> kWh</td>
                <td><?php echo $makine['KarbonAyakIzi']; ?> kg</td>
                <td><?php echo $makine['SonCalismaTarihi'] ?? 'Yok'; ?></td>
                <td><?php echo $makine['SaglikDurumu']; ?>%</td>
                <td>
                    <?php if ($makine['SaglikDurumu'] < 100): ?>
                        <form action="makineler.php" method="POST">
                            <input type="hidden" name="makineID" value="<?php echo $makine['MakineID']; ?>">
                            <button type="submit">Bakım Yap</button>
                        </form>
                    <?php else: ?>
                        Sağlıklı
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
