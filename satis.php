<?php
require 'db.php';
require 'makine.php';
require 'malzeme.php';
require 'parca.php';

// Malzemeleri ve parçaları burada tanımlıyoruz
$plastik = new Malzeme("Plastik", 500, 5);
$deri = new Malzeme("Deri", 500, 15);
$aluminyum = new Malzeme("Alüminyum", 500, 25);
$celik = new Malzeme("Çelik", 500, 20);
$cam = new Malzeme("Cam", 500, 10);

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

$makineA = new Makine("Makine A", 36, 30, 3);
$makineA->parcaEkle($kaput);
$makineA->parcaEkle($lastik);

$makineB = new Makine("Makine B", 20, 18, 1);
$makineB->parcaEkle($ayna);
$makineB->parcaEkle($far);

$makineC = new Makine("Makine C", 16, 20, 2);
$makineC->parcaEkle($direksiyon);
$makineC->parcaEkle($vites);

$parcalar = [
    'Kaput' => $kaput,
    'Ayna' => $ayna,
    'Direksiyon' => $direksiyon,
    'Lastik' => $lastik,
    'Far' => $far,
    'Vites' => $vites
];

// POST ile form verilerini işleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parcaAdi = $_POST['parca'];
    $adet = intval($_POST['adet']);

    if (isset($parcalar[$parcaAdi])) {
        $parca = $parcalar[$parcaAdi];

        try {
            // Makineyi seçiyoruz
            $makine = null;
            foreach ([$makineA, $makineB, $makineC] as $m) {
                if (in_array($parca, $m->uretebildigiParcalar)) {
                    $makine = $m;
                    break;
                }
            }
        
            if ($makine === null) {
                throw new Exception("Bu parça için uygun makine bulunamadı.");
            }
        
            // Parçayı üretmeden önce stok kontrolü ve azaltma
            foreach ($parca->malzemeler as $malzemeBilgi) {
                $malzeme = $malzemeBilgi['malzeme'];
                $gerekliMiktar = $malzemeBilgi['miktar'] * $adet;
        
                // Stok kontrolü
                if ($malzeme->stokMiktari < $gerekliMiktar) {
                    throw new Exception("Yetersiz stok: " . $malzeme->ad);
                }
        
                // Stok miktarını azalt
                $malzeme->stokMiktari -= $gerekliMiktar;
        
                // Veritabanını güncelle
                $stmt = $pdo->prepare("UPDATE malzemeler SET StokMiktari = StokMiktari - :gerekliMiktar WHERE MalzemeAdi = :malzemeAdi");
                $stmt->execute([
                    ':gerekliMiktar' => $gerekliMiktar,
                    ':malzemeAdi' => $malzeme->ad
                ]);
            }
        
            // Parçayı üret
            $uretimSonucu = $makine->calis($parca, $adet);
            $toplamFiyat = $parca->satisFiyati * $adet;
        
            // Veritabanına üretim ve satış bilgilerini kaydet
            $pdo->beginTransaction();
        
            // Üretim verisi
            foreach ($parca->malzemeler as $malzemeBilgi) {
                $stmt = $pdo->prepare("INSERT INTO uretimler (ParcaAdi, MalzemeAdi, KullanilanAdet) 
                                       VALUES (:parcaAdi, :malzemeAdi, :kullanilanAdet)");
                $stmt->execute([
                    ':parcaAdi' => $parca->ad,
                    ':malzemeAdi' => $malzemeBilgi['malzeme']->ad,
                    ':kullanilanAdet' => $malzemeBilgi['miktar'] * $adet
                ]);
            }
        
            // Satış verisi
            $stmt = $pdo->prepare("INSERT INTO satislar (ParcaAdi, Miktar, ToplamFiyat) 
                                   VALUES (:parcaAdi, :miktar, :toplamFiyat)");
            $stmt->execute([
                ':parcaAdi' => $parca->ad,
                ':miktar' => $adet,
                ':toplamFiyat' => $toplamFiyat
            ]);
        
            $pdo->commit();
        
            $message = "Satış başarılı! {$adet} adet {$parca->ad} üretildi ve satıldı.";
        } catch (Exception $e) {
            $pdo->rollBack();
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
        <button type="submit">Satın Al</button>
    </form>
</body>
</html>
