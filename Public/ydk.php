<?php
require_once '../Core/DB.php';
require_once '../Classes/Balance.php';
require_once '../Classes/Product.php';
require_once '../Classes/Machine.php';
require_once '../Classes/Navbar.php';
require_once '../Classes/Balance_Management.php';
require_once '../Classes/Machine.php';

class Sales {
    private $db;
    private $balance;

    public function __construct() {
        $this->db = new DB();
        $this->balance = new Balance();
    }

    public function recordSale($userId, $productId, $machineId, $quantity) {
        // Ürün bilgilerini al
        $product = $this->db->fetch("SELECT * FROM Products WHERE ProductID = :id", [':id' => $productId]);
        if (!$product) {
            throw new Exception("Ürün bulunamadı.");
        }

        // Makine bilgilerini al
        $machine = $this->db->fetch("SELECT * FROM Machines WHERE MachineID = :id", [':id' => $machineId]);
        if (!$machine) {
            throw new Exception("Makine bulunamadı.");
        }

        // Ürün-Malzeme ilişkilerini al
        $productMaterials = $this->db->fetchAll(
            "SELECT pm.MaterialID, pm.Quantity, m.Stock, m.CostPerUnit 
            FROM ProductMaterials pm
            JOIN Materials m ON pm.MaterialID = m.MaterialID
            WHERE pm.ProductID = :productId",
            [':productId' => $productId]
        );

        if (!$productMaterials) {
            throw new Exception("Ürün için gerekli malzemeler bulunamadı.");
        }

        // Gerekli malzemelerin stoğunu kontrol et ve düş
        foreach ($productMaterials as $material) {
            $requiredQuantity = $material['Quantity'] * $quantity;

            if ($material['Stock'] < $requiredQuantity) {
                throw new Exception("Yetersiz stok: " . $material['MaterialID']);
            }

            $this->db->execute(
                "UPDATE Materials SET Stock = Stock - :quantity WHERE MaterialID = :id",
                [
                    ':quantity' => $requiredQuantity,
                    ':id' => $material['MaterialID']
                ]
            );
        }

        // Üretim hesaplamaları
        $productionTime = $machine['ProductionRate'] * $quantity;
        $energyUsed = $machine['EnergyConsumption'] * $quantity;
        $carbonProduced = $machine['CarbonFootprint'] * $quantity;

        // Satış fiyatını hesapla ve bakiyeye ekle
        $salePrice = array_reduce($productMaterials, function ($total, $material) use ($quantity) {
            return $total + ($material['CostPerUnit'] * $material['Quantity'] * $quantity);
        }, 0);

        $this->balance->updateBalance($salePrice);

        // Satışı kaydet
        $this->db->execute(
            "INSERT INTO Sales (UserID, ProductID, MachineID, Quantity, ProductionTime, EnergyUsed, CarbonProduced) 
            VALUES (:user_id, :product_id, :machine_id, :quantity, :production_time, :energy_used, :carbon_produced)",
            [
                ':user_id' => $userId,
                ':product_id' => $productId,
                ':machine_id' => $machineId,
                ':quantity' => $quantity,
                ':production_time' => $productionTime,
                ':energy_used' => $energyUsed,
                ':carbon_produced' => $carbonProduced
            ]
        );

        return "Satış başarıyla kaydedildi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Satışı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php $navbar->render(); ?>

<div class="container mt-5">
    <h1>Ürün Satışı</h1>
    <form action="" method="POST">
        <!-- Ürün Seçimi -->
        <div class="mb-3">
            <label for="product" class="form-label">Ürün Seçin:</label>
            <select id="product" name="product" class="form-select" required>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['name'] ?>"><?= $product['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Makine Seçimi -->
        <div class="mb-3">
            <label for="machine" class="form-label">Makine Seçin:</label>
            <select id="machine" name="machine" class="form-select" required>
                <?php foreach ($machines as $machine): ?>
                    <option value="<?= $machine['name'] ?>"><?= $machine['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Miktar Girişi -->
        <div class="mb-3">
            <label for="quantity" class="form-label">Miktar:</label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="1" required>
        </div>

        <!-- Gönder Butonu -->
        <button type="submit" class="btn btn-primary">Hesapla</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
