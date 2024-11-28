<?php
class Malzeme {
    public $ad;
    public $stokMiktari;
    public $adetFiyat;

    public function __construct($ad, $stokMiktari, $adetFiyat) {
        $this->ad = $ad;
        $this->stokMiktari = $stokMiktari;
        $this->adetFiyat = $adetFiyat;
    }

    // Stok miktarını azalt
    function stokAzalt($pdo, $malzemeAdi, $miktar) {
        $stmt = $pdo->prepare("UPDATE malzemeler SET StokMiktari = StokMiktari - :miktar WHERE MalzemeAdi = :malzemeAdi");
        $stmt->execute([':miktar' => $miktar, ':malzemeAdi' => $malzemeAdi]);
    }
    

    // Stok miktarını artır
    public function stokEkle($miktar) {
        $this->stokMiktari += $miktar;
    }
}

?>
