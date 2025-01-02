<?php
require_once '../Core/DB.php';
require_once '../Classes/Balance.php';

class Machine {
    private $db;

    public function __construct() {
        $this->db = new DB();
    }

    // Tüm Makinelerin Verilerini Alma
    public function getAllMachines() {
        return $this->db->fetchAll("SELECT * FROM Machines");
    }

    // Karbon Ayak İzi Hesaplama Fonksiyonu
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
    
    // Id'sine Göre Makine Verisini Alma
    public function getMachineById($machineId) {
        return $this->db->fetch(
            "SELECT * FROM Machines WHERE MachineID = :machine_id",
            [':machine_id' => $machineId]
        );
    }

    // Makine Hareketlerinin Verilerini Alma
    public function getMachineStats($machineId) {
        return $this->db->fetch(
            "SELECT * FROM MachineStats WHERE MachineID = :machineId",
            [':machineId' => $machineId]
        );
    }

    // Makine Hareketlerine Veri Girişi Fonksiyonu
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

    // Makine Sağlığını Güncelleme Fonksiyonu
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

    // Makine Bakımını Yapma Fonksiyonu
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
            'Bakım',
            $maintenanceCost,
            "Makine Bakımı: {$machineId} - Sağlık Artışı: {$healthDeficit}%"
        );
    
        // Makineyi tam sağlığa geri döndür
        $this->db->execute(
            "UPDATE Machines SET Health = 100 WHERE MachineID = :machine_id",
            [':machine_id' => $machineId]
        );
    
        // Toplam çalışma süresini sıfırla
        $this->db->execute(
            "UPDATE MachineStats SET TotalWorkTime = 0 WHERE MachineID = :machine_id",
            [':machine_id' => $machineId]
        );
    
        return "Bakım başarıyla tamamlandı. Maliyet: {$maintenanceCost} TL";
    }
    
}
