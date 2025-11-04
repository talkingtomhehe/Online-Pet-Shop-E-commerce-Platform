<?php
class Admin {
    public $db;
    private $table = 'admins';
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Admin login
    public function login($username, $password) {
        $username = $this->db->real_escape_string($username);
        
        $sql = "SELECT * FROM {$this->table} WHERE username = '{$username}' LIMIT 1";
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $admin['password'])) {
                return $admin;
            }
        }
        
        return false;
    }
    
    // Get admin by ID
    public function getAdminById($id) {
        $id = (int)$id;
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id}";
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
    
    // Update admin password
    public function updatePassword($id, $password) {
        $id = (int)$id;
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE {$this->table} SET password = '{$hashed_password}' WHERE id = {$id}";
        
        return $this->db->query($sql);
    }
}