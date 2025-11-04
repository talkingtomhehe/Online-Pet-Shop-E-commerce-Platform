<?php
// filepath: c:\xampp\htdocs\chabongshop\models\Product.php
// Product model

class Product {
    private $db;
    private $table = 'products';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAllProducts($sort = 'id', $order = 'asc', $limit = null, $offset = null) {
        // Update SQL to use image_url field
        $sql = "SELECT * FROM {$this->table}";
        
        // Add sorting
        $sql .= " ORDER BY {$sort} {$order}";
        
        // Add pagination
        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        return $this->db->query($sql);
    }
    
    // Get product by ID
    public function getProductById($id) {
        $id = (int)$id;
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = {$id}";
                
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
    
    // Add new product
    public function addProduct($product) {
        $name = $this->db->real_escape_string($product['name']);
        $description = $this->db->real_escape_string($product['description']);
        $price = (float)$product['price'];
        $category_id = (int)$product['category_id'];
        $image = $this->db->real_escape_string($product['image']);
        $stock = (int)$product['stock'];
        $featured = (int)$product['featured'];
        
        $sql = "INSERT INTO {$this->table} (name, description, price, category_id, image_url, stock, featured) 
                VALUES ('{$name}', '{$description}', {$price}, {$category_id}, '{$image}', {$stock}, {$featured})";
        
        if ($this->db->query($sql)) {
            return $this->db->insert_id;
        }
        
        return false;
    }
    
    // Update existing product
    public function updateProduct($product) {
        $id = (int)$product['id'];
        $name = $this->db->real_escape_string($product['name']);
        $description = $this->db->real_escape_string($product['description']);
        $price = (float)$product['price'];
        $category_id = (int)$product['category_id'];
        $image = $this->db->real_escape_string($product['image']);
        $stock = (int)$product['stock'];
        $featured = (int)$product['featured'];
        
        $sql = "UPDATE {$this->table} SET 
                name = '{$name}',
                description = '{$description}', 
                price = {$price}, 
                category_id = {$category_id}, 
                image_url = '{$image}', 
                stock = {$stock}, 
                featured = {$featured}
                WHERE id = {$id}";
        
        return $this->db->query($sql);
    }
    
    // Delete product
    public function deleteProduct($id) {
        $id = (int)$id;
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
        
        return $this->db->query($sql);
    }
    
    // Get featured products
    public function getFeaturedProducts($limit = 10) {
        // Specifically query for featured=1 products
        $sql = "SELECT * FROM {$this->table} WHERE featured = 1 ORDER BY id DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
        }
        
        return $this->db->query($sql);
    }
    
    // Get products by category
    public function getProductsByCategory($categoryId, $sort = 'id', $order = 'desc', $limit = null, $offset = null) {
        $categoryId = (int)$categoryId;
        $sql = "SELECT * FROM {$this->table} WHERE category_id = {$categoryId}";
        
        // Add sorting
        $sql .= " ORDER BY {$sort} {$order}";
        
        // Add pagination
        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        return $this->db->query($sql);
    }

    // Add this method if it doesn't exist
    public function countProductsByCategory($categoryId) {
        $categoryId = (int)$categoryId;
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE category_id = {$categoryId}";
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc()['total'];
        }
        
        return 0;
    }
    
    // Get related products
    public function getRelatedProducts($categoryId, $currentProductId, $limit = 5) {
        $categoryId = (int)$categoryId;
        $currentProductId = (int)$currentProductId;
        
        // Get products from the same category, excluding the current product
        $sql = "SELECT * FROM {$this->table} 
                WHERE category_id = {$categoryId} 
                AND id != {$currentProductId}
                ORDER BY RAND()
                LIMIT {$limit}";
                
        return $this->db->query($sql);
    }
    
    // Count all products
    public function countProducts() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc()['count'];
        }
        
        return 0;
    }

    public function searchProducts($search, $sort = 'id', $order = 'desc', $limit = null, $offset = null) {
        $search = $this->db->real_escape_string($search);
        
        $sql = "SELECT p.* FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.name LIKE '{$search}%' 
                OR c.name LIKE '%{$search}%'
                ORDER BY p.{$sort} {$order}";
        
        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
            
            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        return $this->db->query($sql);
    }

    // Update product stock
    public function updateStock($productId, $newStock) {
        $productId = (int)$productId;
        $newStock = (int)$newStock;
        
        // Ensure stock doesn't go below 0
        if ($newStock < 0) {
            $newStock = 0;
        }
        
        $sql = "UPDATE {$this->table} SET stock = {$newStock} WHERE id = {$productId}";
        
        return $this->db->query($sql);
    }
}