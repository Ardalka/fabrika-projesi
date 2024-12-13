<?php
require 'db.php'; 

// Makine bazlı üretim verilerini al
$stmt = $pdo->query("SELECT uretimler.MakineID, makineler.MakineAdi, COUNT(*) as UretimSayisi 
                     FROM uretimler 
                     JOIN makineler ON uretimler.MakineID = makineler.MakineID 
                     GROUP BY uretimler.MakineID");
$makineUretimVerileri = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Malzeme kullanım verilerini al
$stmt = $pdo->query("SELECT MalzemeAdi, SUM(KullanilanAdet) as ToplamKullanilan 
                     FROM uretimler 
                     GROUP BY MalzemeAdi");
$malzemeKullanimiVerileri = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Raporları</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Admin Raporları</h1>

    <!-- Makine Üretim Grafiği -->
    <canvas id="makineUretimGrafik" width="400" height="200"></canvas>

    <!-- Malzeme Kullanım Grafiği -->
    <canvas id="malzemeKullanimGrafik" width="400" height="200"></canvas>

    <script>
        // Makine Üretim Verileri
        const makineUretimData = {
            labels: [
                <?php foreach ($makineUretimVerileri as $veri) echo "'{$veri['MakineAdi']}',"; ?>
            ],
            datasets: [{
                label: 'Üretim Sayısı',
                data: [
                    <?php foreach ($makineUretimVerileri as $veri) echo "{$veri['UretimSayisi']},"; ?>
                ],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };

        // Malzeme Kullanım Verileri
        const malzemeKullanimData = {
            labels: [
                <?php foreach ($malzemeKullanimiVerileri as $veri) echo "'{$veri['MalzemeAdi']}',"; ?>
            ],
            datasets: [{
                label: 'Kullanılan Miktar',
                data: [
                    <?php foreach ($malzemeKullanimiVerileri as $veri) echo "{$veri['ToplamKullanilan']},"; ?>
                ],
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        };

        // Makine Üretim Grafiği
        const makineUretimCtx = document.getElementById('makineUretimGrafik').getContext('2d');
        new Chart(makineUretimCtx, {
            type: 'bar',
            data: makineUretimData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Makine Bazlı Üretim'
                    }
                }
            }
        });

        // Malzeme Kullanım Grafiği
        const malzemeKullanimCtx = document.getElementById('malzemeKullanimGrafik').getContext('2d');
        new Chart(malzemeKullanimCtx, {
            type: 'pie',
            data: malzemeKullanimData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Malzeme Kullanımı'
                    }
                }
            }
        });
    </script>
</body>
</html>
