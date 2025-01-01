<?php
class Report {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getDailySales($date) {
        return $this->db->fetchAll(
            "SELECT 
                s.SaleID, 
                s.SaleDate, 
                p.ProductName, 
                s.Quantity, 
                s.TotalPrice, 
                (
                    s.TotalPrice - (
                        (
                            SELECT SUM(pm.Quantity * m.CostPerUnit)
                            FROM ProductMaterials pm
                            JOIN Materials m ON pm.MaterialID = m.MaterialID
                            WHERE pm.ProductID = s.ProductID
                        ) * s.Quantity +   -- Malzeme Maliyeti
                        (ma.EnergyConsumption * 1.4 * s.Quantity) + -- Elektrik Maliyeti
                        (ma.CarbonFootprint * 1.4 * s.Quantity)     -- Karbon Vergisi
                    )
                ) AS ProfitLoss
             FROM 
                Sales s
             JOIN 
                Products p ON s.ProductID = p.ProductID
             JOIN 
                Machines ma ON s.MachineID = ma.MachineID
             WHERE 
                DATE(s.SaleDate) = :date",
            [':date' => $date]
        );
    }
    

    public function getProductSalesData($date) {
        return $this->db->fetchAll(
            "SELECT 
                p.ProductName, 
                SUM(s.Quantity) AS TotalSold
             FROM 
                Sales s
             JOIN 
                Products p ON s.ProductID = p.ProductID
             WHERE 
                DATE(s.SaleDate) = :date
             GROUP BY 
                p.ProductName
             ORDER BY 
                TotalSold DESC",
            [':date' => $date]
        );
    }

    public function getMostSoldProduct($date) {
        return $this->db->fetch(
            "SELECT 
                p.ProductName, 
                SUM(s.Quantity) AS TotalSold
             FROM 
                Sales s
             JOIN 
                Products p ON s.ProductID = p.ProductID
             WHERE 
                DATE(s.SaleDate) = :date
             GROUP BY 
                p.ProductName
             ORDER BY 
                TotalSold DESC
             LIMIT 1",
            [':date' => $date]
        );
    }

    public function getMaterialPurchaseData($date) {
        return $this->db->fetchAll(
            "SELECT 
                m.MaterialName, 
                SUM(p.Quantity) AS TotalPurchased
             FROM 
                Purchases p
             JOIN 
                Materials m ON p.MaterialID = m.MaterialID
             WHERE 
                DATE(p.PurchaseDate) = :date
             GROUP BY 
                m.MaterialName
             ORDER BY 
                TotalPurchased DESC",
            [':date' => $date]
        );
    }

    public function getMostPurchasedMaterial($date) {
        return $this->db->fetch(
            "SELECT 
                m.MaterialName, 
                SUM(p.Quantity) AS TotalPurchased
             FROM 
                Purchases p
             JOIN 
                Materials m ON p.MaterialID = m.MaterialID
             WHERE 
                DATE(p.PurchaseDate) = :date
             GROUP BY 
                m.MaterialName
             ORDER BY 
                TotalPurchased DESC
             LIMIT 1",
            [':date' => $date]
        );
    }

    public function getDailyUsedMaterials($date) {
        return $this->db->fetchAll(
            "SELECT 
                m.MaterialName, 
                SUM(pm.Quantity * s.Quantity) AS TotalUsed
             FROM 
                Sales s
             JOIN 
                ProductMaterials pm ON s.ProductID = pm.ProductID
             JOIN 
                Materials m ON pm.MaterialID = m.MaterialID
             WHERE 
                DATE(s.SaleDate) = :date
             GROUP BY 
                m.MaterialName
             ORDER BY 
                TotalUsed DESC",
            [':date' => $date]
        );
    }
    public function getMostUsedMaterial($date) {
        return $this->db->fetch(
            "SELECT 
                m.MaterialName, 
                SUM(pm.Quantity * s.Quantity) AS TotalUsed
             FROM 
                Sales s
             JOIN 
                ProductMaterials pm ON s.ProductID = pm.ProductID
             JOIN 
                Materials m ON pm.MaterialID = m.MaterialID
             WHERE 
                DATE(s.SaleDate) = :date
             GROUP BY 
                m.MaterialName
             ORDER BY 
                TotalUsed DESC
             LIMIT 1",
            [':date' => $date]
        );
    }
    
}
