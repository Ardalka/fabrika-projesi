<?php
require_once '../Core/DB.php';

class Product {
    private $db;

    public function __construct() {
        $this->db = new DB();
    }

    public function getAllProducts() {
        return $this->db->fetchAll("SELECT * FROM Products");
    }

    public function addProduct($name) {
        $this->db->execute(
            "INSERT INTO Products (ProductName) VALUES (:name)",
            [':name' => $name]
        );
    }

    public function updateProduct($id, $name) {
        $this->db->execute(
            "UPDATE Products SET ProductName = :name WHERE ProductID = :id",
            [
                ':name' => $name,
                ':id' => $id
            ]
        );
    }

    public function deleteProduct($id) {
        $this->db->execute(
            "DELETE FROM Products WHERE ProductID = :id",
            [':id' => $id]
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
}
?>
