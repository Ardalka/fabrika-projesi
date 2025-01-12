<?php
require_once '../Core/DB.php';

class Machine {
    private $id;
    private $name;
    private $productionRate;
    private $energyConsumption;
    private $carbonFootprint;
    private $priceMultiplier;
    private $carbonMultiplier;
    private $health;
    private $maintenanceCostPerUnit;
    private $db;

    public function __construct($id, DB $db) {
        $this->db = $db;
        
        // Makine bilgilerini veritabanından al
        $machine = $this->db->fetch("SELECT * FROM Machines WHERE MachineID = :id", [':id' => $id]);

        if (!$machine) {
            throw new Exception("Makine bulunamadı.");
        }

        $this->id = $machine['MachineID'];
        $this->name = $machine['MachineName'];
        $this->productionRate = $machine['ProductionRate'];
        $this->energyConsumption = $machine['EnergyConsumption'];
        $this->carbonFootprint = $machine['CarbonFootprint'];
        $this->priceMultiplier = $machine['PriceMultiplier'];
        $this->carbonMultiplier = $machine['CarbonMultiplier'];
        $this->health = $machine['Health'];
        $this->maintenanceCostPerUnit = $machine['MaintenanceCostPerUnit'];
    }

    // GETTER METOTLARI
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getProductionRate() {
        return $this->productionRate;
    }

    public function getEnergyConsumption() {
        return $this->energyConsumption;
    }

    public function getCarbonFootprint() {
        return $this->carbonFootprint;
    }

    public function getPriceMultiplier() {
        return $this->priceMultiplier;
    }

    public function getHealth() {
        return $this->health;
    }

    public function getCarbonMultiplier() {
        return $this->carbonMultiplier;
    }

    public function getMaintenanceCostPerUnit() {
        return $this->maintenanceCostPerUnit;
    }

    // 🔴 MAKİNE KULLANIM VERİLERİNİ GÜNCELLEYEN METOT
    public function logMachineStats($workTime, $energyUsed, $carbonProduced) {
        $this->db->execute(
            "UPDATE MachineStats SET 
             TotalWorkTime = TotalWorkTime + :workTime,
             TotalEnergyUsed = TotalEnergyUsed + :energyUsed,
             TotalCarbonProduced = TotalCarbonProduced + :carbonProduced
             WHERE MachineID = :machineId",
            [
                ':workTime' => $workTime,
                ':energyUsed' => $energyUsed,
                ':carbonProduced' => $carbonProduced,
                ':machineId' => $this->id
            ]
        );
    }

    public function getMachineStats() {
        $stats = $this->db->fetch(
            "SELECT TotalWorkTime, TotalEnergyUsed, TotalCarbonProduced 
             FROM MachineStats 
             WHERE MachineID = :machineId",
            [':machineId' => $this->id]
        );
    
        return $stats ?: ['TotalWorkTime' => 0, 'TotalEnergyUsed' => 0, 'TotalCarbonProduced' => 0];
    }
    

    // 🔴 MAKİNE SAĞLIĞINI GÜNCELLEYEN METOT
    public function updateHealth($usageHours) {
        $healthReduction = $usageHours * 0.5; // Örnek olarak saat başına %0.5 azalma
        
        $this->health = max($this->health - $healthReduction, 0);
        
        $this->db->execute(
            "UPDATE Machines SET Health = :health WHERE MachineID = :machine_id",
            [
                ':health' => $this->health,
                ':machine_id' => $this->id
            ]
        );
    }

    // 🔴 MAKİNE BAKIM METODU
    public function performMaintenance() {
        if ($this->health >= 100) {
            throw new Exception("Makine zaten tam sağlık durumunda.");
        }

        // Bakım maliyetini hesapla
        $healthDeficit = 100 - $this->health;
        $maintenanceCost = $healthDeficit * $this->maintenanceCostPerUnit;

        // Bakiye kontrolü için Balance sınıfı çağırılabilir (Balance sınıfını güncelleyerek yapılabilir)

        // Sağlığı %100'e çıkar
        $this->health = 100;
        $this->db->execute(
            "UPDATE Machines SET Health = 100 WHERE MachineID = :machine_id",
            [':machine_id' => $this->id]
        );

        return "Bakım başarıyla tamamlandı. Maliyet: {$maintenanceCost} TL";
    }


}
?>
