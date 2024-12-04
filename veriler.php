<?php
require 'db.php';
require 'classes/makine.php';
require 'classes/malzeme.php';
require 'classes/parca.php';

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
?>
