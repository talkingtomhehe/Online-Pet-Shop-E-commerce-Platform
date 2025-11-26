<?php
class Staff {
    private $conn;
    private $table = 'staff';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all active staff members
     */
    public function getAllActive() {
        $query = "SELECT id, name, role 
                  FROM " . $this->table . " 
                  WHERE is_active = 1 
                  ORDER BY name ASC";
        
        $result = mysqli_query($this->conn, $query);
        
        if (!$result) {
            return [];
        }
        
        $staff = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $staff[] = $row;
        }
        
        return $staff;
    }

    /**
     * Get staff member by ID
     */
    public function getById($id) {
        $query = "SELECT id, name, role 
                  FROM " . $this->table . " 
                  WHERE id = ? AND is_active = 1";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }

    /**
     * Get all staff (including inactive, for admin)
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name ASC";
        $result = mysqli_query($this->conn, $query);
        
        if (!$result) {
            return [];
        }
        
        $staff = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $staff[] = $row;
        }
        
        return $staff;
    }
}
