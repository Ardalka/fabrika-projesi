<?php
class Makine {
    public $ad;
    public $karbonAyakIziSaatlik;
    public $elektrikTuketimiSaatlik;
    public $uretimSuresiFaktor;
    public $saglikDurumu;
    public $fiyatFaktor; // Fiyat çarpanı
    public $calismaSaati = 0;
    public $elektrikTuketimi = 0;
    public $karbonAyakIzi = 0;

    public function __construct($ad, $karbonAyakIziSaatlik, $elektrikTuketimiSaatlik, $uretimSuresiFaktor, $fiyatFaktor, $saglikDurumu = 100) {
        $this->ad = $ad;
        $this->karbonAyakIziSaatlik = $karbonAyakIziSaatlik;
        $this->elektrikTuketimiSaatlik = $elektrikTuketimiSaatlik;
        $this->uretimSuresiFaktor = $uretimSuresiFaktor; // Üretim süresi çarpanı
        $this->fiyatFaktor = $fiyatFaktor; // Fiyat çarpanı
        $this->saglikDurumu = $saglikDurumu; // Sağlık durumu
    }

    public function calis($parca, $adet) {
        $toplamSure = $this->uretimSuresiFaktor * $adet; // Üretim süresi
        $toplamElektrik = $this->elektrikTuketimiSaatlik * $toplamSure; // Elektrik tüketimi
        $toplamKarbonAyakIzi = $this->karbonAyakIziSaatlik * $toplamSure; // Karbon ayak izi

        $this->calismaSaati += $toplamSure;
        $this->elektrikTuketimi += $toplamElektrik;
        $this->karbonAyakIzi += $toplamKarbonAyakIzi;

        return [
            'toplamSure' => $toplamSure,
            'toplamElektrik' => $toplamElektrik,
            'toplamKarbonAyakIzi' => $toplamKarbonAyakIzi
        ];
    }

    public function saglikAzalt($miktar) {
        $this->saglikDurumu -= $miktar;
        if ($this->saglikDurumu < 0) {
            $this->saglikDurumu = 0; // Sağlık durumu negatif olamaz
        }
    }

    public function bakimYap() {
        $this->saglikDurumu = 100; // Sağlık durumunu %100'e getir
    }

    public function fiyatHesapla($parca, $adet) {
        return ($parca->uretimMaliyeti * $adet) * $this->fiyatFaktor;
    }
}

?>
