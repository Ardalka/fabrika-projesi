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
