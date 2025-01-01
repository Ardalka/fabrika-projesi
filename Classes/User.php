<?php
require_once '../Core/DB.php';

class User {
    private $db;

    public function __construct() {
        $this->db = new DB();
    }

    public function getAllUsers() {
        return $this->db->fetchAll("SELECT UserID AS id, username, email, role FROM users");
    }
    
    public function updateUserRole($userId, $role) {
        $this->db->execute(
            "UPDATE users SET role = :role WHERE UserID = :id",
            [':role' => $role, ':id' => $userId]
        );
    }
    
    public function deleteUser($userId) {
        $this->db->execute(
            "DELETE FROM users WHERE UserID = :id",
            [':id' => $userId]
        );
    }
    
}
?>
