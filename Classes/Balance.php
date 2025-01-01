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

    public function calculateCarbonTax() {
        // MachineStats tablosundaki toplam karbon ayak izini hesapla
        $totalCarbon = $this->db->fetch(
            "SELECT SUM(TotalCarbonProduced) AS TotalCarbon FROM MachineStats"
        )['TotalCarbon'];
    
        $taxRate = 1.4; // 1 TL/kg
        $carbonTax = $totalCarbon * $taxRate;
    
        return [
            'totalCarbon' => $totalCarbon,
            'carbonTax' => $carbonTax
        ];
    }

    public function payCarbonTax($carbonTax) {
        // Mevcut bakiyeyi al
        $balance = $this->getBalance();
    
        // Bakiyenin yeterli olup olmadığını kontrol et
        if ($balance < $carbonTax) {
            throw new Exception("Yetersiz bakiye. Karbon ayak izi vergisini ödemek için daha fazla bakiyeye ihtiyacınız var.");
        }
    
        // Bakiyeden karbon vergisini düş
        $this->updateBalance(-$carbonTax);
    
        // Bakiye geçmişine işlem kaydet
        $this->recordTransaction(
            'Karbon Vergisi', // İşlem türü
            -$carbonTax, // Negatif değer (bakiyeden düşmek için)
            "Karbon Ayak İzi Vergisi Ödendi" // Açıklama
        );
    
        // MachineStats tablosundaki toplam karbon ayak izini sıfırla
        $this->db->execute("UPDATE MachineStats SET TotalCarbonProduced = 0");
    
        return "Karbon ayak izi vergisi başarıyla ödendi.";
    }
    
    
    public function calculateElectricityBill() {
        // MachineStats tablosundan toplam elektrik tüketimini al
        $totalEnergyUsed = $this->db->fetch(
            "SELECT SUM(TotalEnergyUsed) AS TotalEnergy FROM MachineStats"
        )['TotalEnergy'];
    
        $electricityRate = 1.4;
        $electricityBill = $totalEnergyUsed * $electricityRate;
    
        return [
            'totalEnergy' => $totalEnergyUsed,
            'electricityBill' => $electricityBill
        ];
    }
    
    public function payElectricityBill($electricityBill) {
        // Mevcut bakiyeyi al
        $balance = $this->getBalance();
    
        // Bakiyenin yeterli olup olmadığını kontrol et
        if ($balance < $electricityBill) {
            throw new Exception("Yetersiz bakiye. Elektrik faturasını ödemek için daha fazla bakiyeye ihtiyacınız var.");
        }
    
        // Bakiyeden elektrik faturasını düş
        $this->updateBalance(-$electricityBill);
    
        // Bakiye geçmişine işlem kaydet
        $this->recordTransaction(
            'Elektrik Faturası',  // İşlem türü
            -$electricityBill, // Negatif değer (bakiyeden düşmek için)
            "Elektrik Faturası Ödendi" // Açıklama
        );
    
        // MachineStats tablosundaki elektrik tüketimini sıfırla
        $this->db->execute("UPDATE MachineStats SET TotalEnergyUsed = 0");
    
        return "Elektrik faturası başarıyla ödendi.";
    }
    
    

    // Bakiyenin yetersiz olup olmadığını kontrol eder
    public function isBalanceSufficient($amount) {
        $currentBalance = $this->getBalance();
        return $currentBalance >= $amount;
    }


}
?>
