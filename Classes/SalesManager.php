<?php
require_once '../Core/DB.php';
require_once '../Classes/Balance.php';
require_once '../Classes/Machine.php';
require_once '../Classes/Product.php';

class SalesManager {
    private $db;
    private $balance;

    public function __construct(DB $db, Balance $balance) {
        $this->db = $db;
        $this->balance = $balance;
    }

    public function recordSale($userId, Product $product, Machine $machine, $quantity) {
        // Ürün-Malzeme ilişkilerini al
        $productMaterials = $product->getMaterials();
    
        if (empty($productMaterials)) {
            throw new Exception("Ürün için gerekli malzemeler bulunamadı.");
        }
    
        // Gerekli malzemelerin stoğunu kontrol et ve düş
        foreach ($productMaterials as $materialData) {
            $material = $materialData['material']; // Material nesnesi
            $requiredQuantity = $materialData['quantity'] * $quantity; // Gerekli miktar
    
            if ($material->getStock() < $requiredQuantity) {
                throw new Exception("Yetersiz stok: " . $material->getName() . " (ID: " . $material->getId() . ")");
            }
    
            // Stok düş
            $material->reduceStock($requiredQuantity);
        }
    
        // Üretim hesaplamaları
        $productionTime = $machine->getProductionRate() * $quantity;
        $energyUsed = $machine->getEnergyConsumption() * $quantity;
    
        // Dinamik karbon ayak izi hesaplama (makinenin sağlığına bağlı)
        $health = max($machine->getHealth(), 1); // Sağlık %0 olmamalı, minimum 1 kabul edilir
        $carbonProduced = $machine->getCarbonFootprint() * $quantity * (100 / $health);
    
        // Makine istatistiklerini güncelle
        $machine->logMachineStats($productionTime, $energyUsed, $carbonProduced);
        $machine->updateHealth($productionTime);
    
        // Satış fiyatını hesapla (malzeme maliyetleri + makine fiyat çarpanı)
        $basePrice = array_reduce($productMaterials, function ($total, $materialData) use ($quantity) {
            $material = $materialData['material'];
            return $total + ($material->getCostPerUnit() * $materialData['quantity'] * $quantity);
        }, 0);
    
        $salePrice = $basePrice * $machine->getPriceMultiplier(); // Fiyat çarpanı uygulanıyor
    
        // Bakiyeyi güncelle ve işlem kaydet
        if (!$this->balance->isBalanceSufficient($salePrice)) {
            throw new Exception("Bakiye yetersiz, satış gerçekleştirilemez.");
        }
    
        $this->balance->updateBalance($salePrice);
        $this->balance->recordTransaction(
            'Satış', 
            $salePrice, 
            "Satış: Ürün ID {$product->getId()}, Adı: {$product->getName()}, Miktar: $quantity, Makine: {$machine->getName()}"
        );
    
        // Satışı kaydet
        $this->db->execute(
            "INSERT INTO Sales (UserID, ProductID, MachineID, Quantity, ProductionTime, EnergyUsed, CarbonProduced, TotalPrice) 
            VALUES (:user_id, :product_id, :machine_id, :quantity, :production_time, :energy_used, :carbon_produced, :total_price)",
            [
                ':user_id' => $userId,
                ':product_id' => $product->getId(),
                ':machine_id' => $machine->getId(),
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
