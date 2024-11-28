<?php
class Parca {
    public $ad;
    public $uretimMaliyeti;
    public $satisFiyati;
    public $makine;
    public $malzemeler = []; // ['malzeme' => Malzeme, 'miktar' => miktar]

    public function __construct($ad, $uretimMaliyeti, $satisFiyati) {
        $this->ad = $ad;
        $this->uretimMaliyeti = $uretimMaliyeti;
        $this->satisFiyati = $satisFiyati;
    }

    public function makineEkle($makine) {
        $this->makine = $makine;
    }

    public function malzemeEkle($malzeme, $miktar) {
        $this->malzemeler[] = ['malzeme' => $malzeme, 'miktar' => $miktar];
    }

    public function uret($adet) {
        foreach ($this->malzemeler as $malzemeBilgi) {
            $gerekliMalzeme = $malzemeBilgi['malzeme'];
            $gerekliMiktar = $malzemeBilgi['miktar'] * $adet;

            if (!$gerekliMalzeme->stokAzalt($gerekliMiktar)) {
                throw new Exception("Stok yetersiz: " . $gerekliMalzeme->ad);
            }
        }

        return $this->makine->uret($adet);
    }
}

?>
