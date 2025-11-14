<?php
class Order
{
    public $db;
    private $table = 'orders';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Get all orders with optional status filter
    public function getAllOrders($status = '', $limit = null, $offset = null)
    {
        $sql = "SELECT o.*, u.username FROM {$this->table} o 
            LEFT JOIN users u ON o.user_id = u.id";

        if (!empty($status)) {
            $status = $this->db->real_escape_string($status);
            $sql .= " WHERE o.status = '{$status}'";
        }

        $sql .= " ORDER BY o.created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->query($sql);
    }

    // Get order by ID
    public function getOrderById($id)
    {
        $id = (int)$id;
        $sql = "SELECT o.*, u.username, u.email as user_email FROM {$this->table} o 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE o.id = {$id}";

        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return false;
    }

    public function getOrdersByUserId($userId)
    {
        $userId = (int)$userId;
        $sql = "SELECT * FROM {$this->table} WHERE user_id = {$userId} ORDER BY created_at DESC";

        return $this->db->query($sql);
    }

    // Get order items
    public function getOrderItems($orderId)
    {
        $orderId = (int)$orderId;
        $sql = "SELECT oi.*, p.name, p.image_url FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = {$orderId}";

        return $this->db->query($sql);
    }

    // Update order status
    public function updateStatus($id, $status)
    {
        $id = (int)$id;
        $status = $this->db->real_escape_string($status);

        $sql = "UPDATE {$this->table} SET status = '{$status}' WHERE id = {$id}";

        return $this->db->query($sql);
    }

    // Count orders by status
    public function countOrdersByStatus($status)
    {
        $status = $this->db->real_escape_string($status);
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = '{$status}'";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc()['count'];
        }

        return 0;
    }

    // Count all orders
    public function countOrders()
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc()['count'];
        }

        return 0;
    }

    // Get recent orders for dashboard
    public function getRecentOrders($limit = 5)
    {
        $sql = "SELECT o.*, u.username FROM {$this->table} o 
                LEFT JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC LIMIT {$limit}";

        return $this->db->query($sql);
    }

    // Create a new order
    public function createOrder($data)
    {
        $userId = (int)$data['user_id'];
        $name = $this->db->real_escape_string($data['name']);
        $email = $this->db->real_escape_string($data['email']);
        $address = $this->db->real_escape_string($data['address']);
        $city = $this->db->real_escape_string($data['city']);
        $postalCode = $this->db->real_escape_string($data['postal_code']);
        $phone = isset($data['phone']) ? $this->db->real_escape_string($data['phone']) : '';
        $totalAmount = (float)$data['total_amount'];

        $sql = "INSERT INTO {$this->table} (user_id, name, email, address, city, postal_code, phone, total_amount, status, created_at) 
                VALUES ({$userId}, '{$name}', '{$email}', '{$address}', '{$city}', '{$postalCode}', '{$phone}', {$totalAmount}, 'pending', NOW())";

        if ($this->db->query($sql)) {
            return $this->db->insert_id;
        }

        return false;
    }

    // Add an order item
    public function addOrderItem($data)
    {
        $orderId = (int)$data['order_id'];
        $productId = (int)$data['product_id'];
        $quantity = (int)$data['quantity'];
        $price = (float)$data['price'];

        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                VALUES ({$orderId}, {$productId}, {$quantity}, {$price})";

        return $this->db->query($sql);
    }
}
