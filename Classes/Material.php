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

    public function getAllMaterials() {
        return $this->db->fetchAll("SELECT * FROM Materials");
    }

    public function addMaterial($name, $cost, $stock) {
        $this->db->execute(
            "INSERT INTO Materials (MaterialName, CostPerUnit, Stock) VALUES (:name, :cost, :stock)",
            [
                ':name' => $name,
                ':cost' => $cost,
                ':stock' => $stock
            ]
        );
    }

    public function updateMaterial($id, $name, $cost, $stock) {
        $this->db->execute(
            "UPDATE Materials SET MaterialName = :name, CostPerUnit = :cost, Stock = :stock WHERE MaterialID = :id",
            [
                ':name' => $name,
                ':cost' => $cost,
                ':stock' => $stock,
                ':id' => $id
            ]
        );
    }

    public function deleteMaterial($id) {
        $this->db->execute(
            "DELETE FROM Materials WHERE MaterialID = :id",
            [':id' => $id]
        );
    }

    public function purchaseMaterial($id, $quantity) {
        // Malzeme bilgilerini al
        $material = $this->db->fetch("SELECT * FROM Materials WHERE MaterialID = :id", [':id' => $id]);

        if (!$material) {
            throw new Exception("Malzeme bulunamadı.");
        }

        $totalCost = $material['CostPerUnit'] * $quantity;

        // Bakiye kontrolü
        if (!$this->balance->isBalanceSufficient($totalCost)) {
            throw new Exception("Yetersiz bakiye.");
        }

        // Bakiyeden düş ve geçmiş kaydı ekle
        $this->balance->updateBalance(-$totalCost);
        $this->balance->recordTransaction('purchase', -$totalCost, "Malzeme Alımı: {$material['MaterialName']}, Miktar: $quantity");

        // Stok güncelle
        $this->db->execute(
            "UPDATE Materials SET Stock = Stock + :quantity WHERE MaterialID = :id",
            [
                ':quantity' => $quantity,
                ':id' => $id
            ]
        );
    }
}
?>