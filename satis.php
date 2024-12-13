<?php
require 'db.php'; // Veritabanı bağlantısı
require 'makine.php';
require 'malzeme.php';
require 'parca.php';

$message = '';

// Malzeme tanımları
$plastik = new Malzeme("Plastik", 500, 5);
$aluminyum = new Malzeme("Alüminyum", 500, 25);
$celik = new Malzeme("Çelik", 500, 20);
$cam = new Malzeme("Cam", 500, 10);
$deri = new Malzeme("Deri", 500, 15);

// Parça tanımları
$kaput = new Parca("Kaput", 325, 325 * 1.5);
$kaput->malzemeEkle($aluminyum, 5);
$kaput->malzemeEkle($celik, 10);

$ayna = new Parca("Ayna", 35, 35 * 1.5);
$ayna->malzemeEkle($plastik, 3);
$ayna->malzemeEkle($cam, 2);

$direksiyon = new Parca("Direksiyon", 120, 120 * 1.5);
$direksiyon->malzemeEkle($plastik, 2);
$direksiyon->malzemeEkle($deri, 1);
$direksiyon->malzemeEkle($aluminyum, 4);

$lastik = new Parca("Lastik", 125, 125 * 1.5);
$lastik->malzemeEkle($plastik, 5);
$lastik->malzemeEkle($celik, 5);

$far = new Parca("Far", 70, 70 * 1.5);
$far->malzemeEkle($aluminyum, 2);
$far->malzemeEkle($cam, 2);

$vites = new Parca("Vites", 175, 175 * 1.5);
$vites->malzemeEkle($deri, 5);
$vites->malzemeEkle($celik, 5);

$parcalar = [
    'Kaput' => $kaput,
    'Ayna' => $ayna,
    'Direksiyon' => $direksiyon,
    'Lastik' => $lastik,
    'Far' => $far,
    'Vites' => $vites
];

// Makine tanımları
$makineA = new Makine("Makine A", 30, 10, 4, 1.5); // Yavaş makine
$makineB = new Makine("Makine B", 50, 60, 1, 1.7); // Hızlı makine
$makineC = new Makine("Makine C", 30, 15, 3, 1.6); // Orta makine

$makineler = [$makineA, $makineB, $makineC];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parcaAdi = $_POST['parca'];
    $adet = intval($_POST['adet']);
    $secilenMakineID = intval($_POST['makine']); // Kullanıcıdan seçilen makine

    if (isset($parcalar[$parcaAdi])) {
        $parca = $parcalar[$parcaAdi];
        $makine = $makineler[$secilenMakineID - 1]; // Kullanıcının seçtiği makine

        try {
            $pdo->beginTransaction();

            // Parçayı üret ve fiyatı hesapla
            $uretimSonucu = $makine->calis($parca, $adet);
            $toplamFiyat = $makine->fiyatHesapla($parca, $adet); // Makineye göre fiyat
            $makine->saglikAzalt(1 * $adet); // Her parça üretiminde sağlık %1 azalır
            $stmt = $pdo->prepare("UPDATE makineler 
                                   SET CalismaSaati = CalismaSaati + :calismaSaati,
                                       ElektrikTuketimi = ElektrikTuketimi + :elektrikTuketimi,
                                       KarbonAyakIzi = KarbonAyakIzi + :karbonAyakIzi,
                                       SaglikDurumu = :saglikDurumu
                                   WHERE MakineID = :makineID");
            $stmt->execute([
                ':calismaSaati' => $uretimSonucu['toplamSure'],
                ':elektrikTuketimi' => $uretimSonucu['toplamElektrik'],
                ':karbonAyakIzi' => $uretimSonucu['toplamKarbonAyakIzi'],
                ':saglikDurumu' => $makine->saglikDurumu,
                ':makineID' => $secilenMakineID
            ]);
            

            // Stok kontrolü ve azaltma işlemi
            foreach ($parca->malzemeler as $malzemeBilgi) {
                $malzeme = $malzemeBilgi['malzeme'];
                $gerekliMiktar = $malzemeBilgi['miktar'] * $adet;

                // Stok kontrolü
                if ($malzeme->stokMiktari < $gerekliMiktar) {
                    throw new Exception("Yetersiz stok: " . $malzeme->ad);
                }

                // Stok azalt
                $malzeme->stokMiktari -= $gerekliMiktar;

                // Veritabanında stok güncelle
                $stmt = $pdo->prepare("UPDATE malzemeler SET StokMiktari = StokMiktari - :gerekliMiktar WHERE MalzemeAdi = :malzemeAdi");
                $stmt->execute([
                    ':gerekliMiktar' => $gerekliMiktar,
                    ':malzemeAdi' => $malzeme->ad
                ]);
            }

            // Üretim verisini kaydet
            foreach ($parca->malzemeler as $malzemeBilgi) {
                $stmt = $pdo->prepare("INSERT INTO uretimler (ParcaAdi, MakineID, MalzemeAdi, KullanilanAdet) 
                                       VALUES (:parcaAdi, :makineID, :malzemeAdi, :kullanilanAdet)");
                $stmt->execute([
                    ':parcaAdi' => $parca->ad,
                    ':makineID' => $secilenMakineID,
                    ':malzemeAdi' => $malzemeBilgi['malzeme']->ad,
                    ':kullanilanAdet' => $malzemeBilgi['miktar'] * $adet
                ]);
            }

            // Satış verisini kaydet
            $stmt = $pdo->prepare("INSERT INTO satislar (ParcaAdi, MakineID, Miktar, ToplamFiyat) 
                                   VALUES (:parcaAdi, :makineID, :miktar, :toplamFiyat)");
            $stmt->execute([
                ':parcaAdi' => $parca->ad,
                ':makineID' => $secilenMakineID,
                ':miktar' => $adet,
                ':toplamFiyat' => $toplamFiyat
            ]);

            $pdo->commit(); // İşlemi tamamla
            $message = "Satış başarılı! {$adet} adet {$parca->ad} üretildi ve Makine {$secilenMakineID} kullanıldı.";
        } catch (Exception $e) {
            $pdo->rollBack(); // Hata durumunda işlemi geri al
            $message = "Hata: " . $e->getMessage();
        }
    } else {
        $message = "Geçersiz parça seçimi.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satış</title>
</head>
<body>
    <h1>Satış Sayfası</h1>
    <?php if (!empty($message)) echo "<p>$message</p>"; ?>

    <form action="satis.php" method="POST">
        <label for="parca">Parça Seçin:</label>
        <select name="parca" id="parca">
            <?php foreach ($parcalar as $parcaAdi => $parca): ?>
                <option value="<?php echo $parcaAdi; ?>"><?php echo $parcaAdi; ?></option>
            <?php endforeach; ?>
        </select>
        <br>

        <label for="adet">Adet:</label>
        <input type="number" name="adet" id="adet" min="1" required>
        <br>

        <label for="makine">Makine Seçin:</label>
        <select name="makine" id="makine">
            <option value="1">Makine A (Yavaş - Düşük Fiyat)</option>
            <option value="2">Makine B (Hızlı - Yüksek Fiyat)</option>
            <option value="3">Makine C (Orta Hız - Orta Fiyat)</option>
        </select>
        <br>

        <button type="submit">Satın Al</button>
    </form>
</body>
</html>
