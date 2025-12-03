<?php
class Notification {
    private $conn;
    private $table = 'notifications';

    public function __construct() {
        // Assuming your Database class returns a MySQLi connection
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($userId, $message, $link) {
        // SQL matches your requested format (skipping ID as it is auto-increment)
        $sql = "INSERT INTO {$this->table} (user_id, message, link, is_read, created_at) VALUES (?, ?, ?, 0, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        
        // "iss" = integer (user_id), string (message), string (link)
        $stmt->bind_param("iss", $userId, $message, $link);
        
        return $stmt->execute();
    }

    public function getByUser($userId, $limit = 10) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        
        // "ii" = integer, integer
        $stmt->bind_param("ii", $userId, $limit);
        
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) AS cnt FROM {$this->table} WHERE user_id = ? AND is_read = 0";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return (int)($row['cnt'] ?? 0);
    }

    public function markAsRead($id, $userId) {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE id = ? AND user_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $id, $userId);
        
        return $stmt->execute();
    }

    public function markAllRead($userId) {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE user_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        
        return $stmt->execute();
    }
}
?>