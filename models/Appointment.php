<?php
class Appointment {
    private $conn;
    private $table = 'appointments';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a new appointment with status 'pending'
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, service_id, staff_id, appointment_date, appointment_time, customer_notes, status) 
                  VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = mysqli_prepare($this->conn, $query);
        
        if (!$stmt) {
            return false;
        }
        
        mysqli_stmt_bind_param(
            $stmt, 
            "iiisss",
            $data['user_id'],
            $data['service_id'],
            $data['staff_id'],
            $data['appointment_date'],
            $data['appointment_time'],
            $data['customer_notes']
        );
        
        $result = mysqli_stmt_execute($stmt);
        
        if ($result) {
            return mysqli_insert_id($this->conn);
        }
        
        return false;
    }

    /**
     * Check if a time slot is available
     * Returns true if available, false if already booked
     */
    public function isSlotAvailable($date, $time, $staffId) {
        $query = "SELECT COUNT(*) as count 
                  FROM " . $this->table . " 
                  WHERE appointment_date = ? 
                  AND appointment_time = ? 
                  AND staff_id = ? 
                  AND status != 'cancelled'";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $date, $time, $staffId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        return $row['count'] == 0;
    }

    /**
     * Get all appointments with filters (for admin dashboard)
     */
    public function getAllAppointments($filters = []) {
        $query = "SELECT 
                    a.id,
                    a.appointment_date,
                    a.appointment_time,
                    a.status,
                    a.customer_notes,
                    a.created_at,
                    u.id as user_id,
                    u.full_name as customer_name,
                    u.email as customer_email,
                    u.phone as customer_phone,
                    s.id as service_id,
                    s.name as service_name,
                    s.duration_minutes,
                    s.price,
                    st.id as staff_id,
                    st.name as staff_name,
                    st.role as staff_role
                  FROM " . $this->table . " a
                  INNER JOIN users u ON a.user_id = u.id
                  INNER JOIN spa_services s ON a.service_id = s.id
                  INNER JOIN staff st ON a.staff_id = st.id
                  WHERE 1=1";
        
        $params = [];
        $types = "";
        
        // Apply filters
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query .= " AND a.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        if (isset($filters['date']) && !empty($filters['date'])) {
            $query .= " AND a.appointment_date = ?";
            $params[] = $filters['date'];
            $types .= "s";
        }
        
        if (isset($filters['user_id']) && !empty($filters['user_id'])) {
            $query .= " AND a.user_id = ?";
            $params[] = $filters['user_id'];
            $types .= "i";
        }
        
        $query .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC, a.created_at DESC";
        
        if (!empty($params)) {
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        } else {
            $result = mysqli_query($this->conn, $query);
        }
        
        if (!$result) {
            return [];
        }
        
        $appointments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $appointments[] = $row;
        }
        
        return $appointments;
    }

    /**
     * Update appointment status (for admin approve/reject)
     */
    public function updateStatus($id, $status) {
        $validStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $query = "UPDATE " . $this->table . " 
                  SET status = ? 
                  WHERE id = ?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Get appointment by ID
     */
    public function getById($id) {
        $query = "SELECT 
                    a.*,
                    u.name as customer_name,
                    u.email as customer_email,
                    u.phone as customer_phone,
                    s.name as service_name,
                    s.duration_minutes,
                    s.price,
                    st.name as staff_name,
                    st.role as staff_role
                  FROM " . $this->table . " a
                  INNER JOIN users u ON a.user_id = u.id
                  INNER JOIN spa_services s ON a.service_id = s.id
                  INNER JOIN staff st ON a.staff_id = st.id
                  WHERE a.id = ?";
        
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }

    /**
     * Get user's appointments
     */
    public function getUserAppointments($userId) {
        $filters = ['user_id' => $userId];
        return $this->getAllAppointments($filters);
    }

    /**
     * Get available time slots for a specific date and staff
     */
    public function getAvailableSlots($date, $staffId) {
        // Shop hours: 9 AM - 7 PM
        $shopOpen = 9;
        $shopClose = 19;
        
        $availableSlots = [];
        
        for ($hour = $shopOpen; $hour < $shopClose; $hour++) {
            // Check both :00 and :30 slots
            foreach (['00', '30'] as $minute) {
                $time = sprintf("%02d:%s:00", $hour, $minute);
                
                if ($this->isSlotAvailable($date, $time, $staffId)) {
                    $availableSlots[] = [
                        'time' => $time,
                        'display' => date('g:i A', strtotime($time))
                    ];
                }
            }
        }
        
        return $availableSlots;
    }
}
