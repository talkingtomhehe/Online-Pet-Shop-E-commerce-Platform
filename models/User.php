<?php
class User
{
    private $db;
    private $table = 'users';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Register new user
    public function register($username, $email, $password)
    {
        // Check if username exists
        $sql = "SELECT id FROM {$this->table} WHERE username = '{$username}'";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return ['success' => false, 'message' => 'Username already exists'];
        }

        // Check if email exists
        $sql = "SELECT id FROM {$this->table} WHERE email = '{$email}'";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return ['success' => false, 'message' => 'Email already exists'];
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Escape input
        $username = $this->db->real_escape_string($username);
        $email = $this->db->real_escape_string($email);

        $sql = "INSERT INTO {$this->table} (username, email, password) 
                VALUES ('{$username}', '{$email}', '{$hashed_password}')";

        if ($this->db->query($sql)) {
            return ['success' => true, 'user_id' => $this->db->insert_id];
        }

        return ['success' => false, 'message' => 'Registration failed'];
    }

    // User login
    public function login($username, $password)
    {
        $username = $this->db->real_escape_string($username);

        $sql = "SELECT * FROM {$this->table} WHERE username = '{$username}'";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }

        return false;
    }

    // Update password
    public function updatePassword($id, $password)
    {
        $id = (int)$id;
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE {$this->table} SET password = '{$hashed_password}' WHERE id = {$id}";

        return $this->db->query($sql);
    }

    // Get all users
    public function getAllUsers($limit = null, $offset = null)
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

    // Delete user
    public function deleteUser($id)
    {
        $id = (int)$id;
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";

        return $this->db->query($sql);
    }

    // Count all users
    public function countUsers()
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc()['count'];
        }

        return 0;
    }

    public function getUserById($userId)
    {
        $userId = (int)$userId;

        $sql = "SELECT * FROM {$this->table} WHERE id = {$userId} LIMIT 1";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return false;
    }

    public function updateUser($userId, $data)
    {
        $userId = (int)$userId;

        $setClauses = [];
        foreach ($data as $column => $value) {
            if ($value !== null) {
                $escapedValue = $this->db->real_escape_string($value);
                $setClauses[] = "{$column} = '{$escapedValue}'";
            }
        }

        // Add updated_at timestamp if your table has it
        $setClauses[] = "updated_at = NOW()";

        $setClause = implode(', ', $setClauses);

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = {$userId}";

        return $this->db->query($sql);
    }

    public function getUserByUsername($username)
    {
        $username = $this->db->real_escape_string($username);

        $sql = "SELECT * FROM {$this->table} WHERE username = '{$username}' LIMIT 1";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return false;
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email)
    {
        $email = $this->db->real_escape_string($email);

        $sql = "SELECT * FROM {$this->table} WHERE email = '{$email}' LIMIT 1";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return false;
    }

    public function updateLastActivity($userId)
    {
        $userId = (int)$userId;
        $sql = "UPDATE {$this->table} SET last_activity = NOW() WHERE id = {$userId}";
        return $this->db->query($sql);
    }

    /**
     * Update user avatar
     */
    public function updateAvatar($userId, $avatarPath)
    {
        $userId = (int)$userId;
        $avatarPath = $this->db->real_escape_string($avatarPath);

        $sql = "UPDATE {$this->table} SET avatar = '{$avatarPath}' WHERE id = {$userId}";
        return $this->db->query($sql);
    }

    /**
     * Check if a Google ID exists in the database
     */
    public function getUserByGoogleId($googleId)
    {
        $googleId = $this->db->real_escape_string($googleId);

        $sql = "SELECT * FROM {$this->table} WHERE google_id = '{$googleId}' LIMIT 1";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return false;
    }

    /**
     * Create or update a user with Google data
     */
    public function createOrUpdateGoogleUser($userData)
    {
        // Check if user with this Google ID exists
        $existingUser = $this->getUserByGoogleId($userData['google_id']);

        if ($existingUser) {
            // Update existing user
            $userId = $existingUser['id'];
            $username = $existingUser['username']; // Keep existing username

            $sql = "UPDATE {$this->table} SET 
                    email = '{$userData['email']}',
                    full_name = '{$userData['full_name']}',
                    avatar = '{$userData['avatar']}',
                    last_login = NOW() 
                    WHERE id = {$userId}";

            $this->db->query($sql);
            return $existingUser;
        } else {
            // Check if email already exists
            $existingEmail = $this->getUserByEmail($userData['email']);

            if ($existingEmail) {
                // Link Google ID to existing account
                $userId = $existingEmail['id'];
                $sql = "UPDATE {$this->table} SET 
                        google_id = '{$userData['google_id']}',
                        full_name = '{$userData['full_name']}', 
                        avatar = '{$userData['avatar']}',
                        last_login = NOW() 
                        WHERE id = {$userId}";

                $this->db->query($sql);
                return $existingEmail;
            } else {
                // Create new user
                $username = $this->generateUniqueUsername($userData['email']);
                $password = password_hash(bin2hex(random_bytes(10)), PASSWORD_DEFAULT);

                $sql = "INSERT INTO {$this->table} 
                        (username, email, password, full_name, google_id, avatar, created_at, last_login) 
                        VALUES 
                        ('{$username}', '{$userData['email']}', '{$password}', '{$userData['full_name']}', 
                        '{$userData['google_id']}', '{$userData['avatar']}', NOW(), NOW())";

                if ($this->db->query($sql)) {
                    return $this->getUserById($this->db->insert_id);
                }
            }
        }

        return false;
    }

    /**
     * Generate a unique username based on email
     */
    private function generateUniqueUsername($email)
    {
        // Extract username part from email
        $parts = explode('@', $email);
        $baseUsername = $parts[0];

        // Check if username exists
        $username = $baseUsername;
        $counter = 1;

        while ($this->getUserByUsername($username)) {
            // Add number to username
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Get user avatar URL (handles both local and external URLs)
     */
    public function getUserAvatar($userId)
    {
        $userId = (int)$userId;

        $sql = "SELECT avatar FROM {$this->table} WHERE id = {$userId}";
        $result = $this->db->query($sql);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (!empty($user['avatar'])) {
                // Check if it's already an external URL
                if (strpos($user['avatar'], 'http') === 0) {
                    return $user['avatar']; // Return as-is
                }
                // Otherwise, it's a local path
                return $user['avatar']; // Will be prefixed with SITE_URL where used
            }
        }

        // Return default avatar path (without SITE_URL)
        return 'public/images/avatars/default.png';
    }

    public function updateLastLogin($userId)
    {
        $userId = (int)$userId;
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE id = {$userId}";
        return $this->db->query($sql);
    }
}
