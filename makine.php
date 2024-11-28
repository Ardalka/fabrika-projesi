<?php
class Makine {
    public $ad;
    public $karbonAyakIzi;
    public $elektrikTuketimi;
    public $uretimSuresi;
    public $calismaSaati = 0;
    public $uretebildigiParcalar = []; // Makinenin üretebileceği parçalar

    public function __construct($ad, $karbonAyakIzi, $elektrikTuketimi, $uretimSuresi) {
        $this->ad = $ad;
        $this->karbonAyakIzi = $karbonAyakIzi;
        $this->elektrikTuketimi = $elektrikTuketimi;
        $this->uretimSuresi = $uretimSuresi;
    }

    // Makineye üretebileceği parçaları ekle
    public function parcaEkle($parca) {
        $this->uretebildigiParcalar[] = $parca;
    }

    // Üretim işlemi
    public function calis($parca, $adet) {
        if (!in_array($parca, $this->uretebildigiParcalar)) {
            throw new Exception("{$this->ad} makinesi {$parca->ad} üretemez.");
        }

        $toplamSure = $this->uretimSuresi * $adet;
        $toplamElektrik = $this->elektrikTuketimi * $adet;
        $toplamKarbonAyakIzi = $this->karbonAyakIzi * $adet;

        $this->calismaSaati += $toplamSure;

        return [
            'toplamSure' => $toplamSure,
            'toplamElektrik' => $toplamElektrik,
            'toplamKarbonAyakIzi' => $toplamKarbonAyakIzi
        ];
    }
}

?>
