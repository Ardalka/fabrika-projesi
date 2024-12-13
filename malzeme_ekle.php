<?php
require 'db.php'; // Veritabanı bağlantısı
require 'malzeme.php'; // Malzeme sınıfını içeren dosya

// Mevcut malzemeleri sınıflar halinde tanımlayın
$malzemeler = [
    new Malzeme("Plastik", 500, 5),
    new Malzeme("Deri", 500, 15),
    new Malzeme("Alüminyum", 500, 25),
    new Malzeme("Çelik", 500, 20),
    new Malzeme("Cam", 500, 10)
];

$message = ""; // İşlem sonucunu göstermek için mesaj

// Form gönderildiğinde işlemleri yap
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $malzemeAdi = $_POST['malzeme'];
    $miktar = intval($_POST['miktar']);
    $toplamFiyat = 0;

    foreach ($malzemeler as $malzeme) {
        if ($malzeme->ad === $malzemeAdi) {
            // Toplam fiyatı hesapla
            $toplamFiyat = $miktar * $malzeme->adetFiyat;

            // Veritabanında stok artır
            $stmt = $pdo->prepare("UPDATE malzemeler SET StokMiktari = StokMiktari + :miktar WHERE MalzemeAdi = :malzemeAdi");
            $stmt->execute([':miktar' => $miktar, ':malzemeAdi' => $malzemeAdi]);

            // Stok bilgisini sınıf üzerinden de güncelle
            $malzeme->stokEkle($miktar);

            // İşlem sonucunu mesaj olarak göster
            $message = "{$miktar} adet {$malzemeAdi} satın alındı. Toplam harcama: {$toplamFiyat} TL.";
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Malzeme Satın Al</title>
</head>
<body>
    <h1>Malzeme Satın Al</h1>

    <!-- İşlem Mesajı -->
    <?php if (!empty($message)): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Satın Alma Formu -->
    <form action="malzeme_ekle.php" method="POST">
        <label for="malzeme">Malzeme Seçin:</label>
        <select name="malzeme" id="malzeme" required>
            <?php foreach ($malzemeler as $malzeme): ?>
                <option value="<?php echo $malzeme->ad; ?>">
                    <?php echo "{$malzeme->ad} (Fiyat: {$malzeme->adetFiyat} TL/adet)"; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="miktar">Miktar:</label>
        <input type="number" id="miktar" name="miktar" required>
        <br><br>

        <button type="submit">Satın Al</button>
    </form>
</body>
</html>
