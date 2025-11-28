<?php
class Notification {
    private $conn;
    private $table = 'notifications';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($userId, $type, $referenceId, $message) {
        $sql = "INSERT INTO {$this->table} (user_id, type, reference_id, message, is_read) VALUES (:user_id, :type, :reference_id, :message, 0)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':user_id' => $userId,
            ':type' => $type,
            ':reference_id' => $referenceId,
            ':message' => $message
        ]);
    }

    public function getByUser($userId, $limit = 10) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) AS cnt FROM {$this->table} WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['cnt'] ?? 0);
    }

    public function markAsRead($id, $userId) {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }

    public function markAllRead($userId) {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':user_id' => $userId]);
    }
}