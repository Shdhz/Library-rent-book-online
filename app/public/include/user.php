<?php
class User {
    private $conn;
    private $table_name = "users";

    public $user_id;
    public $nama_lengkap;
    public $email;
    public $username;
    public $password;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login() {
        // Query to check if username exists
        $query = "SELECT user_id, username, password, role, nama_lengkap FROM " . $this->table_name . " WHERE username = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->username);
        $stmt->execute();

        return $stmt;
    }
    public function findByIdentifier($identifier) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = ? OR email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $identifier);
        $stmt->bindParam(2, $identifier);
        $stmt->execute();
        return $stmt;
    }

    public function updatePassword($id, $password) {
        $query = "UPDATE " . $this->table_name . " SET password = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $password);
        $stmt->bindParam(2, $id);
        $stmt->execute();
    }
}
?>
