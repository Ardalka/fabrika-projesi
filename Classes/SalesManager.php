<?php
require_once '../Core/DB.php';
require_once '../Classes/Balance.php';
require_once '../Classes/Machine.php';

class SalesManager {
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
    
        // Dinamik karbon ayak izi hesaplama (makinenin sağlığına bağlı)
        $health = max($machine['Health'], 1); // Sağlık %0 olmamalı, minimum 1 kabul edilir
        $carbonProduced = $machine['CarbonFootprint'] * $quantity * (100 / $health);
    
        // Makine verilerini güncelle
        $machineInstance = new Machine();
        $machineInstance->logMachineStats($machineId, $productionTime, $energyUsed, $carbonProduced);
    
        // Sağlık durumunu güncelle
        $machineInstance->updateMachineHealth($machineId, $productionTime);
    
        // Satış fiyatını hesapla ve makine fiyat çarpanını uygula
        $basePrice = array_reduce($productMaterials, function ($total, $material) use ($quantity) {
            return $total + ($material['CostPerUnit'] * $material['Quantity'] * $quantity);
        }, 0);
    
        $salePrice = $basePrice * $machine['PriceMultiplier']; // Fiyat çarpanı uygulanıyor
    
        // Bakiye güncelle ve işlem kaydet
        $this->balance->updateBalance($salePrice);
        $this->balance->recordTransaction(
            'Satış', 
            $salePrice, 
            "Satış: Ürün ID {$product['ProductID']}, Adı: {$product['ProductName']}, Miktar: $quantity, Makine: {$machine['MachineName']}"
        );
    
        // Satışı kaydet
        $this->db->execute(
            "INSERT INTO Sales (UserID, ProductID, MachineID, Quantity, ProductionTime, EnergyUsed, CarbonProduced, TotalPrice) 
            VALUES (:user_id, :product_id, :machine_id, :quantity, :production_time, :energy_used, :carbon_produced, :total_price)",
            [
                ':user_id' => $userId,
                ':product_id' => $productId,
                ':machine_id' => $machineId,
                ':quantity' => $quantity,
                ':production_time' => $productionTime,
                ':energy_used' => $energyUsed,
                ':carbon_produced' => $carbonProduced,
                ':total_price' => $salePrice
            ]
        );
    
        return "Satış başarıyla kaydedildi.";
    }

    
}
?>
