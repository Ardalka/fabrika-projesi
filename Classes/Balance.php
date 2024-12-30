<?php
require_once '../Core/DB.php';

class Balance {
    private $db;

    public function __construct() {
        $this->db = new DB();
    }

    public function getBalanceHistory() {
        return $this->db->fetchAll("SELECT * FROM BalanceHistory ORDER BY TransactionDate DESC");
    }

    // Mevcut bakiye bilgisini getirir
    public function getBalance() {
        $result = $this->db->fetch("SELECT CurrentBalance FROM FactoryBalance WHERE BalanceID = 1");
        return $result['CurrentBalance'];
    }

    // Bakiyeyi günceller (artırma veya azaltma)
    public function updateBalance($amount) {
        $this->db->execute(
            "UPDATE FactoryBalance SET CurrentBalance = CurrentBalance + :amount WHERE BalanceID = 1",
            [':amount' => $amount]
        );
    }

    public function calculateCarbonTax() {
        // MachineStats tablosundaki toplam karbon ayak izini hesapla
        $totalCarbon = $this->db->fetch(
            "SELECT SUM(TotalCarbonProduced) AS TotalCarbon FROM MachineStats"
        )['TotalCarbon'];
    
        $taxRate = 1.0; // 1 TL/kg
        $carbonTax = $totalCarbon * $taxRate;
    
        return [
            'totalCarbon' => $totalCarbon,
            'carbonTax' => $carbonTax
        ];
    }

    public function payCarbonTax($carbonTax) {
        $balance = $this->getBalance();
    
        if ($balance < $carbonTax) {
            throw new Exception("Yetersiz bakiye. Vergiyi ödemek için daha fazla bakiyeye ihtiyacınız var.");
        }
    
        // Bakiyeden düş
        $this->updateBalance(-$carbonTax);
    
        // Bakiye geçmişine işlem kaydet
        $this->recordTransaction(
            'tax', 
            $carbonTax, 
            "Karbon Ayak İzi Vergisi Ödendi"
        );
    
        // MachineStats'taki karbon birikimini sıfırla
        $this->db->execute("UPDATE MachineStats SET TotalCarbonProduced = 0");
    
        return "Vergi başarıyla ödendi.";
    }
    
    public function calculateElectricityBill() {
        // MachineStats tablosundan toplam elektrik tüketimini al
        $totalEnergyUsed = $this->db->fetch(
            "SELECT SUM(TotalEnergyUsed) AS TotalEnergy FROM MachineStats"
        )['TotalEnergy'];
    
        $electricityRate = 1.5; // 1.5 TL/kWh (örnek birim fiyat)
        $electricityBill = $totalEnergyUsed * $electricityRate;
    
        return [
            'totalEnergy' => $totalEnergyUsed,
            'electricityBill' => $electricityBill
        ];
    }
    
    public function payElectricityBill($electricityBill) {
        $balance = $this->getBalance();
    
        if ($balance < $electricityBill) {
            throw new Exception("Yetersiz bakiye. Elektrik faturasını ödemek için daha fazla bakiyeye ihtiyacınız var.");
        }
    
        // Bakiyeden düş
        $this->updateBalance(-$electricityBill);
    
        // Bakiye geçmişine işlem kaydet
        $this->recordTransaction(
            'electricity', 
            $electricityBill, 
            "Elektrik Faturası Ödendi"
        );
    
        // MachineStats'taki elektrik tüketimini sıfırla
        $this->db->execute("UPDATE MachineStats SET TotalEnergyUsed = 0");
    
        return "Elektrik faturası başarıyla ödendi.";
    }
    

    // Bakiyenin yetersiz olup olmadığını kontrol eder
    public function isBalanceSufficient($amount) {
        $currentBalance = $this->getBalance();
        return $currentBalance >= $amount;
    }

    // Bakiye hareketi
    public function recordTransaction($type, $amount, $description) {
        $this->db->execute(
            "INSERT INTO BalanceHistory (TransactionType, Amount, Description) 
             VALUES (:type, :amount, :description)",
            [
                ':type' => $type,
                ':amount' => $amount,
                ':description' => $description
            ]
        );
    }
}
?>
