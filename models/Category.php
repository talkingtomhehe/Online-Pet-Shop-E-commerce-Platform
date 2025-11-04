<?php
// filepath: c:\xampp\htdocs\chabongshop\models\Category.php
// Category model

class Category
{
    private $db;
    private $table = 'categories';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Get all categories
    public function getAllCategories($limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY id ASC";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->query($sql);
    }

    // Get category by ID
    public function getCategoryById($id)
    {
        $id = (int)$id;
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id}";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return false;
    }

    // Get category by name
    public function getCategoryByName($name)
    {
        $name = $this->db->real_escape_string($name);
        $sql = "SELECT * FROM {$this->table} WHERE name = '{$name}'";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return false;
    }

    // Add new category
    public function addCategory($category)
    {
        $name = $this->db->real_escape_string($category['name']);
        $description = $this->db->real_escape_string($category['description']);

        $sql = "INSERT INTO {$this->table} (name, description) VALUES ('{$name}', '{$description}')";

        if ($this->db->query($sql)) {
            return $this->db->insert_id;
        }

        return false;
    }

    // Update existing category
    public function updateCategory($category)
    {
        $id = (int)$category['id'];
        $name = $this->db->real_escape_string($category['name']);
        $description = $this->db->real_escape_string($category['description']);

        $sql = "UPDATE {$this->table} SET name = '{$name}', description = '{$description}' WHERE id = {$id}";

        return $this->db->query($sql);
    }

    // Delete category
    public function deleteCategory($id)
    {
        $id = (int)$id;
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";

        return $this->db->query($sql);
    }

    // Count all categories
    public function countCategories()
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc()['count'];
        }

        return 0;
    }
}
