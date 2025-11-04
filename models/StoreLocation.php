<?php
class StoreLocation {
    private $db;
    private $table = 'store_locations';
    
    public function __construct() {
        // Create database connection
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    /**
     * Get all active store locations
     */
    public function getActiveLocations() {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1";
        $result = $this->db->query($sql);
        
        return $result;
    }
    
    /**
     * Get a single location by ID
     */
    public function getLocationById($id) {
        $id = (int)$id;
        
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id}";
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
    
    /**
     * Create a new store location
     */
    public function createLocation($data) {
        // Sanitize inputs
        $name = $this->db->real_escape_string($data['name']);
        $address = $this->db->real_escape_string($data['address']);
        $latitude = (float)$data['latitude'];
        $longitude = (float)$data['longitude'];
        $phone = $this->db->real_escape_string($data['phone']);
        $email = $this->db->real_escape_string($data['email']);
        $hours = $this->db->real_escape_string($data['hours']);
        $is_active = isset($data['is_active']) ? 1 : 0;
        
        $sql = "INSERT INTO {$this->table} 
                (name, address, latitude, longitude, phone, email, hours, is_active) 
                VALUES 
                ('{$name}', '{$address}', {$latitude}, {$longitude}, '{$phone}', '{$email}', '{$hours}', {$is_active})";
                
        return $this->db->query($sql);
    }
    
    /**
     * Update an existing store location
     */
    public function updateLocation($id, $data) {
        // Sanitize inputs
        $id = (int)$id;
        $name = $this->db->real_escape_string($data['name']);
        $address = $this->db->real_escape_string($data['address']);
        $latitude = (float)$data['latitude'];
        $longitude = (float)$data['longitude'];
        $phone = $this->db->real_escape_string($data['phone']);
        $email = $this->db->real_escape_string($data['email']);
        $hours = $this->db->real_escape_string($data['hours']);
        $is_active = isset($data['is_active']) ? 1 : 0;
        
        $sql = "UPDATE {$this->table} SET
                name = '{$name}',
                address = '{$address}',
                latitude = {$latitude},
                longitude = {$longitude},
                phone = '{$phone}',
                email = '{$email}',
                hours = '{$hours}',
                is_active = {$is_active}
                WHERE id = {$id}";
                
        return $this->db->query($sql);
    }
    
    /**
     * Delete a store location
     */
    public function deleteLocation($id) {
        $id = (int)$id;
        
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
        
        return $this->db->query($sql);
    }
}