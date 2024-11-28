<?php
// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = ""; // Şifreniz varsa buraya ekleyin
$dbname = "arabafabrikası"; // Veritabanı adını buraya güncelledik

// Bağlantıyı kur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Formdan gelen verileri al
$item_name = $_POST['item_name'];
$quantity = intval($_POST['quantity']);
$price = floatval($_POST['price']);

// Verileri veritabanına ekle
$sql = "INSERT INTO orders (item_name, quantity, price) VALUES ('$item_name', $quantity, $price)";

if ($conn->query($sql) === TRUE) {
    echo "Sipariş başarıyla kaydedildi.";
} else {
    echo "Hata: " . $sql . "<br>" . $conn->error;
}

// Bağlantıyı kapat
$conn->close();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Menü</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Sipariş Menü</h2>
        <form action="get_order.php" method="post">
            <div class="form-group">
                <label for="item">Ürün Seçiniz:</label>
                <select class="form-control" id="item" name="item_name">
                    <option value="Burger">Burger - 50 TL</option>
                    <option value="Pizza">Pizza - 70 TL</option>
                    <option value="Pasta">Pasta - 40 TL</option>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Miktar:</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1">
            </div>
            <input type="hidden" name="price" id="price" value="50">
            <button type="submit" class="btn btn-primary">Sipariş Ver</button>
        </form>
    </div>

    <script>
        // Ürün seçildiğinde fiyat bilgisini otomatik güncelleyen script
        document.getElementById("item").addEventListener("change", function () {
            const prices = { "Burger": 50, "Pizza": 70, "Pasta": 40 };
            document.getElementById("price").value = prices[this.value];
        });
    </script>
</body>
</html>
