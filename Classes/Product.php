<?php
require_once '../Core/DB.php';

class Product {
    private $db;

    public function __construct() {
        $this->db = new DB();
    }
    
    // Parça Verilerini Alma Fonksiyonu
    public function getAllProducts() {
        return $this->db->fetchAll("SELECT * FROM Products");
    }

    // Parça Güncelleme Fonksiyonu
    public function updateProduct($id, $name) {
        $this->db->execute(
            "UPDATE Products SET ProductName = :name WHERE ProductID = :id",
            [
                ':name' => $name,
                ':id' => $id
            ]
        );
    }
    
    // Malzeme Verilerini Alma Fonksiyonu
    public function getAllMaterials() {
        return $this->db->fetchAll("SELECT * FROM Materials");
    }
    
    // Parça Malzeme İlişkisi ile Verileri Alma Fonksiyonu
    public function getProductMaterials($productId) {
        return $this->db->fetchAll(
            "SELECT pm.MaterialID, m.MaterialName, pm.Quantity 
             FROM ProductMaterials pm
             JOIN Materials m ON pm.MaterialID = m.MaterialID
             WHERE pm.ProductID = :product_id",
            [':product_id' => $productId]
        );
    }

    public function deleteProductMaterials($productId) {
        $this->db->execute(
            "DELETE FROM ProductMaterials WHERE ProductID = :product_id",
            [':product_id' => $productId]
        );
    }

    public function addProductMaterial($productId, $materialId, $quantity) {
        $this->db->execute(
            "INSERT INTO ProductMaterials (ProductID, MaterialID, Quantity) VALUES (:product_id, :material_id, :quantity)",
            [
                ':product_id' => $productId,
                ':material_id' => $materialId,
                ':quantity' => $quantity
            ]
        );
    }

    public function updateProductMaterial($productId, $materialId, $quantity) {
        $this->db->execute(
            "UPDATE ProductMaterials SET Quantity = :quantity WHERE ProductID = :product_id AND MaterialID = :material_id",
            [
                ':quantity' => $quantity,
                ':product_id' => $productId,
                ':material_id' => $materialId
            ]
        );
    }
}
?>
