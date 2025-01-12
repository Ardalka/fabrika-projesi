<?php
require_once '../Core/DB.php';
require_once '../Classes/Material.php';

class Product {
    private $id;
    private $name;
    private $imagePath;
    private $description;
    private $materials;
    private $db;

    public function __construct($id, DB $db) {
        $this->db = $db;
        
        // Ürün bilgilerini veritabanından al
        $product = $this->db->fetch("SELECT * FROM Products WHERE ProductID = :id", [':id' => $id]);

        if (!$product) {
            throw new Exception("Ürün bulunamadı.");
        }

        $this->id = $product['ProductID'];
        $this->name = $product['ProductName'];
        $this->imagePath = $product['ImagePath'] ?? 'default.jpg';
        $this->description = $product['Description'] ?? 'Açıklama bulunmamaktadır.';

        // Ürün için gerekli malzemeleri yükle
        $this->materials = [];
        $productMaterials = $this->db->fetchAll(
            "SELECT pm.MaterialID, pm.Quantity 
             FROM ProductMaterials pm
             WHERE pm.ProductID = :id",
            [':id' => $id]
        );

        foreach ($productMaterials as $materialData) {
            $material = new Material($materialData['MaterialID'], $this->db, new Balance());
            $this->materials[] = [
                'material' => $material,
                'quantity' => $materialData['Quantity']
            ];
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getImagePath() {
        return $this->imagePath;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getMaterials() {
        return $this->materials;
    }

    public function updateProduct($name, $imagePath, $description) {
        $this->name = $name;
        $this->imagePath = $imagePath;
        $this->description = $description;
        
        $this->db->execute("UPDATE Products SET ProductName = :name, ImagePath = :imagePath, Description = :description WHERE ProductID = :id", 
            [':name' => $this->name, ':imagePath' => $this->imagePath, ':description' => $this->description, ':id' => $this->id]);
    }

    public function addMaterial(Material $material, $quantity) {
        $this->db->execute(
            "INSERT INTO ProductMaterials (ProductID, MaterialID, Quantity) VALUES (:product_id, :material_id, :quantity)",
            [
                ':product_id' => $this->id,
                ':material_id' => $material->getId(),
                ':quantity' => $quantity
            ]
        );
        
        $this->materials[] = ['material' => $material, 'quantity' => $quantity];
    }

    public function updateMaterialQuantity(Material $material, $quantity) {
        $this->db->execute(
            "UPDATE ProductMaterials SET Quantity = :quantity WHERE ProductID = :product_id AND MaterialID = :material_id",
            [
                ':quantity' => $quantity,
                ':product_id' => $this->id,
                ':material_id' => $material->getId()
            ]
        );
        
        foreach ($this->materials as &$mat) {
            if ($mat['material']->getId() == $material->getId()) {
                $mat['quantity'] = $quantity;
                break;
            }
        }
    }
}
