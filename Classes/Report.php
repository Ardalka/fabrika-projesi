<?php
require_once '../Core/DB.php';

class Report {
    private $db;

    public function __construct() {
        $this->db = new DB();
    }

    public function getCarbonFootprintByMachine() {
        return $this->db->fetchAll(
            "SELECT m.MachineName, SUM(s.CarbonProduced) AS TotalCarbon 
             FROM Sales s 
             JOIN Machines m ON s.MachineID = m.MachineID 
             GROUP BY m.MachineName"
        );
    }

    public function getEnergyConsumptionByMachine() {
        return $this->db->fetchAll(
            "SELECT m.MachineName, SUM(s.EnergyUsed) AS TotalEnergy 
             FROM Sales s 
             JOIN Machines m ON s.MachineID = m.MachineID 
             GROUP BY m.MachineName"
        );
    }

    public function getMostSoldProduct() {
        return $this->db->fetch(
            "SELECT p.ProductName, SUM(s.Quantity) AS TotalSold 
             FROM Sales s 
             JOIN Products p ON s.ProductID = p.ProductID 
             GROUP BY p.ProductName 
             ORDER BY TotalSold DESC LIMIT 1"
        );
    }

    public function getSalesByProduct() {
        return $this->db->fetchAll(
            "SELECT p.ProductName, SUM(s.Quantity) AS TotalSold 
             FROM Sales s 
             JOIN Products p ON s.ProductID = p.ProductID 
             GROUP BY p.ProductName"
        );
    }
}
?>
