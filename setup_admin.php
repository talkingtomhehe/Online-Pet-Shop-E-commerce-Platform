<?php
// Include necessary files
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h1>Admin Setup</h1>";

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Create admins table if it doesn't exist
$createTable = "CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if ($db->query($createTable)) {
    echo "<p>Admin table created or already exists.</p>";
} else {
    echo "<p>Error creating admin table: " . $db->error . "</p>";
    exit;
}

// Create a new admin account
$username = 'admin';
$password = 'admin123';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// First, delete any existing admin user with that username
$db->query("DELETE FROM admins WHERE username = 'admin'");

// Now insert the new admin
$insertAdmin = "INSERT INTO admins (username, password) VALUES ('{$username}', '{$hashedPassword}')";

if ($db->query($insertAdmin)) {
    echo "<p>Admin user created successfully!</p>";
    echo "<p>Username: <strong>admin</strong></p>";
    echo "<p>Password: <strong>admin123</strong></p>";
} else {
    echo "<p>Error creating admin user: " . $db->error . "</p>";
}

echo "<p>Now you can <a href='" . SITE_URL . "admin/login'>login to the admin panel</a>.</p>";
?>