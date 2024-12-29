<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .carousel-inner img {
            max-height: 400px;
            object-fit: contain;
        }
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
        }
        .long-text-card {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            font-size: 1.2rem;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <?php
    require_once '../Classes/Navbar.php';
    session_start();
    $userRole = isset($_SESSION['role']) ? $_SESSION['role'] : null;
    $navbar = new Navbar($userRole);
    $navbar->render();
    ?>

    <!-- Slider -->
    <div id="factorySlider" class="carousel slide" data-bs-ride="carousel" style="margin-top: 56px;">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="../images/factory1.jpg" class="d-block w-100" alt="Factory Image 1">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Modern Üretim Hatları</h5>
                    <p>Yüksek teknoloji ile donatılmış üretim alanlarımız.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="../images/factory2.jpg" class="d-block w-100" alt="Factory Image 2">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Çevre Dostu Çözümler</h5>
                    <p>Karbon ayak izimizi en aza indiren üretim süreçleri.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="../images/factory3.jpg" class="d-block w-100" alt="Factory Image 3">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Güçlü Altyapı</h5>
                    <p>Her ihtiyaca uygun çözümler sunan altyapımız.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="../images/factory4.jpg" class="d-block w-100" alt="Factory Image 4">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Yenilikçi Teknikler</h5>
                    <p>Geleceğin teknolojileriyle üretimde fark yaratıyoruz.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#factorySlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#factorySlider" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Tanıtım Bölümü -->
    <div class="container mt-5">
        <h2 class="text-center">Fabrikamızı Keşfedin</h2>
        <div class="row text-center mt-4">
            <div class="col-md-4">
                <img src="../images/icon1.png" alt="Icon 1" class="mb-3" style="width: 50px;">
                <h5>Yüksek Kapasite</h5>
                <p>Yılda 1 milyon parça üretim kapasitesi.</p>
            </div>
            <div class="col-md-4">
                <img src="../images/icon2.png" alt="Icon 2" class="mb-3" style="width: 50px;">
                <h5>Çevre Dostu</h5>
                <p>Düşük karbon ayak izi ile üretim.</p>
            </div>
            <div class="col-md-4">
                <img src="../images/icon3.png" alt="Icon 3" class="mb-3" style="width: 50px;">
                <h5>Teknolojik Altyapı</h5>
                <p>Geleceğin teknolojileri ile donatılmış makineler.</p>
            </div>
        </div>
        <div class="row text-center mt-4">
            <div class="col-md-6">
                <img src="../images/icon4.png" alt="Icon 4" class="mb-3" style="width: 50px;">
                <h5>Fabrika İç Mekanı</h5>
                <p>Verimli ve güvenli bir çalışma ortamı.</p>
            </div>
            <div class="col-md-6">
                <img src="../images/icon5.png" alt="Icon 5" class="mb-3" style="width: 50px;">
                <h5>Ekip Çalışması</h5>
                <p>Birlikte daha güçlü bir gelecek inşa ediyoruz.</p>
            </div>
        </div>
        <!-- Uzun Metin Bölümü -->
        <div class="row justify-content-center mt-5">
            <div class="col-md-10">
                <div class="long-text-card">
                    <p>
                        Fabrikamız, ileri teknoloji kullanılarak çevre dostu bir üretim anlayışıyla kurulmuştur. Yıllık 1 milyon parça üretim kapasitesine sahip olan tesisimiz, enerji verimliliği ve düşük karbon salınımı hedefiyle çalışmaktadır.
                        Modern üretim hatlarımız, endüstri 4.0 standartlarına uygun olarak tasarlanmış olup, yüksek kaliteli ürünleri zamanında teslim etmek için optimize edilmiştir. Ekibimiz, sürdürülebilir bir gelecek için çalışmakta ve müşterilerimizin beklentilerini aşmayı hedeflemektedir.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Başarılarımız Bölümü -->
    <div class="container mt-5">
        <h2 class="text-center">Başarılarımız</h2>
        <div class="row text-center mt-4">
            <div class="col-md-4">
                <h5>20+ Yıl Deneyim</h5>
                <p>Fabrika sektörü liderlerinden biri olarak 20 yılı aşkın süredir hizmet veriyoruz.</p>
            </div>
            <div class="col-md-4">
                <h5>500+ Çalışan</h5>
                <p>Alanında uzman 500'den fazla çalışanımızla global projelere imza atıyoruz.</p>
            </div>
            <div class="col-md-4">
                <h5>100+ Ödül</h5>
                <p>Kalite ve yenilikçilik konusunda kazandığımız ödüllerle gurur duyuyoruz.</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-4">
                    <h5>İletişim</h5>
                    <p>Adres: Fabrika Mah. 123 Sok. No:45</p>
                    <p>Telefon: +90 555 555 5555</p>
                    <p>Email: info@fabrikaprojesi.com</p>
                </div>
                <div class="col-md-4 text-center">
                    <h5>Sosyal Medya</h5>
                    <a href="#" class="text-white me-2">Facebook</a>
                    <a href="#" class="text-white me-2">Twitter</a>
                    <a href="#" class="text-white">LinkedIn</a>
                </div>
                <div class="col-md-4 text-end">
                    <h5>Hızlı Linkler</h5>
                    <a href="index.php" class="text-white d-block">Ana Sayfa</a>
                    <a href="product_showcase.php" class="text-white d-block">Ürünler</a>
                    <a href="contact.php" class="text-white d-block">İletişim</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
