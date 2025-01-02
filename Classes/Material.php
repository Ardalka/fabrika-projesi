<?php
require_once '../Core/DB.php';
require_once '../Classes/Balance.php';

class Material {
    private $db;
    private $balance;

    public function __construct() {
        $this->db = new DB();
        $this->balance = new Balance();
    }

    // Tüm Materyal Verilerini Alma
    public function getAllMaterials() {
        return $this->db->fetchAll("SELECT * FROM Materials");
    }

    // Materyal Stok Satın Alma Fonksiyonu
    public function purchaseMaterial($materialId, $quantity) {
        // Malzeme bilgilerini al
        $material = $this->db->fetch("SELECT * FROM Materials WHERE MaterialID = :id", [':id' => $materialId]);
        if (!$material) {
            throw new Exception("Malzeme bulunamadı.");
        }
    
        // Toplam maliyeti hesapla
        $totalCost = $material['CostPerUnit'] * $quantity;
    
        // Bakiyeyi güncelle
        $this->balance->updateBalance(-$totalCost);
        $this->balance->recordTransaction(
            'Satın Alım', 
            -$totalCost, 
            "Satın Alım: Malzeme ID {$material['MaterialID']}, Adı: {$material['MaterialName']}, Miktar: $quantity"
        );
    
        // Malzeme stoğunu güncelle
        $this->db->execute(
            "UPDATE Materials SET Stock = Stock + :quantity WHERE MaterialID = :id",
            [
                ':quantity' => $quantity,
                ':id' => $materialId
            ]
        );
    
        // Satın alım kaydı ekle
        $this->db->execute(
            "INSERT INTO Purchases (MaterialID, Quantity, TotalCost, PurchaseDate) 
            VALUES (:materialId, :quantity, :totalCost, NOW())",
            [
                ':materialId' => $materialId,
                ':quantity' => $quantity,
                ':totalCost' => $totalCost
            ]
        );
    
        // Toplam maliyeti döndür
        return $totalCost;
    }
    
}
?>