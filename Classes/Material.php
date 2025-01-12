<?php
require_once '../Core/DB.php';
require_once '../Classes/Balance.php';

class Material {
    private $id;
    private $name;
    private $costPerUnit;
    private $stock;
    private $db;
    private $balance;

    public function __construct($id, DB $db, Balance $balance) {
        $this->db = $db;
        $this->balance = $balance;
        
        // Veritabanından malzeme bilgilerini al
        $material = $this->db->fetch("SELECT * FROM Materials WHERE MaterialID = :id", [':id' => $id]);

        if (!$material) {
            throw new Exception("Malzeme bulunamadı.");
        }

        $this->id = $material['MaterialID'];
        $this->name = $material['MaterialName'];
        $this->costPerUnit = $material['CostPerUnit'];
        $this->stock = $material['Stock'];
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getCostPerUnit() {
        return $this->costPerUnit;
    }

    public function getStock() {
        return $this->stock;
    }

    
    public function increaseStock($quantity) {
        if ($quantity <= 0) {
            throw new Exception("Geçersiz stok artırma miktarı.");
        }
        
        $this->stock += $quantity;
        $this->updateStockInDB();
    }

    public function reduceStock($quantity) {
        if ($quantity <= 0) {
            throw new Exception("Geçersiz stok azaltma miktarı.");
        }
        
        if ($this->stock < $quantity) {
            throw new Exception("Yetersiz stok: {$this->name} (ID: {$this->id}).");
        }
        
        $this->stock -= $quantity;
        $this->updateStockInDB();
    }

    private function updateStockInDB() {
        $this->db->execute("UPDATE Materials SET Stock = :stock WHERE MaterialID = :id", 
            [':stock' => $this->stock, ':id' => $this->id]);
    }

    public function purchaseMaterial($quantity) {
        if ($quantity <= 0) {
            throw new Exception("Geçersiz satın alma miktarı.");
        }
        
        $totalCost = $this->costPerUnit * $quantity;
        
        if (!$this->balance->isBalanceSufficient($totalCost)) {
            throw new Exception("Yetersiz bakiye.");
        }
        
        $this->balance->updateBalance(-$totalCost);
        $this->balance->recordTransaction(
            'Satın Alım', 
            -$totalCost, 
            "Satın Alım: Malzeme ID {$this->id}, Adı: {$this->name}, Miktar: $quantity"
        );

        $this->increaseStock($quantity);
        
        $this->db->execute(
            "INSERT INTO Purchases (MaterialID, Quantity, TotalCost, PurchaseDate) 
            VALUES (:materialId, :quantity, :totalCost, NOW())",
            [
                ':materialId' => $this->id,
                ':quantity' => $quantity,
                ':totalCost' => $totalCost
            ]
        );

        return $totalCost;
    }
}
?>
