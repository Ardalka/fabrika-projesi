<?php
require_once '../Core/DB.php';

class Machine {
    private $db;

    public function __construct() {
        $this->db = new DB();
    }

    public function getAllMachines() {
        return $this->db->fetchAll("SELECT * FROM Machines");
    }

    public function getMachineById($machineId) {
        return $this->db->fetch(
            "SELECT * FROM Machines WHERE MachineID = :machine_id",
            [':machine_id' => $machineId]
        );
    }

    public function logMachineStats($machineId, $workTime, $energyUsed, $carbonProduced) {
        $this->db->execute(
            "UPDATE Machines SET 
             TotalWorkTime = TotalWorkTime + :workTime,
             TotalEnergyUsed = TotalEnergyUsed + :energyUsed,
             TotalCarbonProduced = TotalCarbonProduced + :carbonProduced,
             Health = GREATEST(Health - (:workTime * 0.5), 0)
             WHERE MachineID = :machineId",
            [
                ':workTime' => $workTime,
                ':energyUsed' => $energyUsed,
                ':carbonProduced' => $carbonProduced,
                ':machineId' => $machineId
            ]
        );
    }

    public function getMachineStats($machineId) {
        return $this->db->fetch(
            "SELECT TotalWorkTime, TotalEnergyUsed, TotalCarbonProduced, Health 
             FROM Machines WHERE MachineID = :machineId",
            [':machineId' => $machineId]
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
        $this->db->execute(
            "UPDATE Machines SET Health = 100 WHERE MachineID = :machine_id",
            [':machine_id' => $machineId]
        );
    }

    public function addMachine($name, $productionRate, $energyConsumption, $carbonFootprint, $health = 100) {
        $this->db->execute(
            "INSERT INTO Machines (MachineName, ProductionRate, EnergyConsumption, CarbonFootprint, Health, TotalWorkTime, TotalEnergyUsed, TotalCarbonProduced) 
            VALUES (:name, :production_rate, :energy_consumption, :carbon_footprint, :health, 0, 0, 0)",
            [
                ':name' => $name,
                ':production_rate' => $productionRate,
                ':energy_consumption' => $energyConsumption,
                ':carbon_footprint' => $carbonFootprint,
                ':health' => $health
            ]
        );
    }

    public function deleteMachine($machineId) {
        $this->db->execute(
            "DELETE FROM Machines WHERE MachineID = :machine_id",
            [':machine_id' => $machineId]
        );
    }
}
?>
