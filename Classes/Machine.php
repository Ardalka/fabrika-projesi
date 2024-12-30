<?php
require_once '../Core/DB.php';
require_once '../Classes/Balance.php';

class Machine {
    private $db;

    public function __construct() {
        $this->db = new DB();
    }

    public function getAllMachines() {
        return $this->db->fetchAll("SELECT * FROM Machines");
    }

    public function calculateCarbonFootprint($machineId, $baseCarbonFootprint) {
        $machine = $this->getMachineById($machineId);
        if (!$machine) {
            throw new Exception("Makine bulunamadı.");
        }
    
        $health = $machine['Health'];
        // Sağlık %0 olamaz, minimum 1 olarak kabul edilir.
        $effectiveHealth = max($health, 1);
    
        // Dinamik karbon ayak izi hesaplama
        return $baseCarbonFootprint * (100 / $effectiveHealth);
    }
    

    public function getMachineById($machineId) {
        return $this->db->fetch(
            "SELECT * FROM Machines WHERE MachineID = :machine_id",
            [':machine_id' => $machineId]
        );
    }

    public function getMachineStats($machineId) {
        return $this->db->fetch(
            "SELECT * FROM MachineStats WHERE MachineID = :machineId",
            [':machineId' => $machineId]
        );
    }

    public function logMachineStats($machineId, $workTime, $energyUsed, $carbonProduced) {
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
                ':machineId' => $machineId
            ]
        );
    }

    public function updateMachineHealth($machineId, $usageHours) {
        $healthReduction = $usageHours * 0.5; // Örnek: Saat başına %0.5 azalma

        $this->db->execute(
            "UPDATE Machines SET Health = GREATEST(Health - :health_reduction, 0) WHERE MachineID = :machine_id",
            [
                ':health_reduction' => $healthReduction,
                ':machine_id' => $machineId
            ]
        );
    }

    public function performMaintenance($machineId) {
        // Makine bilgilerini al
        $machine = $this->db->fetch(
            "SELECT MachineID, Health, MaintenanceCostPerUnit FROM Machines WHERE MachineID = :machine_id",
            [':machine_id' => $machineId]
        );

        if (!$machine) {
            throw new Exception("Makine bulunamadı.");
        }

        // Sağlık durumuna göre bakım maliyetini hesapla
        $healthDeficit = 100 - $machine['Health']; // Eksik sağlık miktarı
        $maintenanceCost = $healthDeficit * $machine['MaintenanceCostPerUnit'];

        if ($healthDeficit <= 0) {
            throw new Exception("Makine zaten tam sağlık durumunda.");
        }

        // Bakiyeyi güncelle
        $balance = new Balance();
        $balance->updateBalance(-$maintenanceCost);
        $balance->recordTransaction(
            'maintenance',
            $maintenanceCost,
            "Makine Bakımı: {$machineId} - Sağlık Artışı: {$healthDeficit}%"
        );

        // Makineyi tam sağlığa geri döndür
        $this->db->execute(
            "UPDATE Machines SET Health = 100 WHERE MachineID = :machine_id",
            [':machine_id' => $machineId]
        );

        return "Bakım başarıyla tamamlandı. Maliyet: {$maintenanceCost} TL";
    }
}
