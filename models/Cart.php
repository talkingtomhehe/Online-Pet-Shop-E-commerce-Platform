<?php
class Cart {
    private $db;
    private $table = 'cart_items';
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Update cart item quantity
    public function updateQuantity($itemId, $quantity) {
        $itemId = (int)$itemId;
        $quantity = (int)$quantity;
        
        if ($quantity <= 0) {
            return $this->removeItem($itemId);
        }
        
        $sql = "UPDATE {$this->table} SET quantity = {$quantity}
                WHERE id = {$itemId}";
        return $this->db->query($sql);
    }
    
    // Remove item from cart
    public function removeItem($itemId) {
        $itemId = (int)$itemId;
        
        $sql = "DELETE FROM {$this->table} WHERE id = {$itemId}";
        return $this->db->query($sql);
    }
    
    // Get all items in user's cart with product details
    public function getCartItems($userId) {
        $userId = (int)$userId;
        
        $sql = "SELECT ci.*, p.name, p.price, p.image_url, p.stock, p.category_id, c.name as category_name, (p.price * ci.quantity) as subtotal 
                FROM {$this->table} ci 
                JOIN products p ON ci.product_id = p.id 
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE ci.user_id = {$userId}";
                
        return $this->db->query($sql);
    }
    
    // Get cart total
    public function getCartTotal($userId) {
        $userId = (int)$userId;
        
        $sql = "SELECT SUM(p.price * ci.quantity) as total 
                FROM {$this->table} ci
                JOIN products p ON ci.product_id = p.id 
                WHERE ci.user_id = {$userId}";
                
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc()['total'] ?? 0;
        }
        
        return 0;
    }
    
    // Get cart item by ID
    public function getCartItemById($itemId) {
        $itemId = (int)$itemId;
        
        $sql = "SELECT * FROM {$this->table} WHERE id = {$itemId} LIMIT 1";
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
    
    // Clear cart
    public function clearCart($userId) {
        $userId = (int)$userId;
        
        $sql = "DELETE FROM {$this->table} WHERE user_id = {$userId}";
        return $this->db->query($sql);
    }
    
    // Count cart items
    public function countItems($userId) {
        $userId = (int)$userId;
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = {$userId}";
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return (int)$result->fetch_assoc()['count'];
        }
        
        return 0;
    }
    
    // Add to cart
    public function addToCart($userId, $productId, $quantity) {
        $userId = (int)$userId;
        $productId = (int)$productId;
        $quantity = (int)$quantity;
        
        // Check if item already exists in cart
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = {$userId} AND product_id = {$productId} LIMIT 1";
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $item = $result->fetch_assoc();
            $newQuantity = $item['quantity'] + $quantity;
            
            return $this->updateQuantity($item['id'], $newQuantity);
        } else {
            $sql = "INSERT INTO {$this->table} (user_id, product_id, quantity, created_at) 
                    VALUES ({$userId}, {$productId}, {$quantity}, NOW())";
            return $this->db->query($sql);
        }
    }
}