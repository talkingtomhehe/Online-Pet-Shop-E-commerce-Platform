<?php
class SessionManager {
    // Session lifetime in seconds (30 days)
    const SESSION_LIFETIME = 2592000;
    const REMEMBER_ME_COOKIE = 'petshop_remember';
    
    public static function init() {
        // Set session cookie parameters for better security
        session_set_cookie_params([
            'lifetime' => self::SESSION_LIFETIME,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Migrate existing sessions to include user_type
        self::migrateExistingSession();
        
        // Regenerate session ID periodically to prevent session fixation
        if (!isset($_SESSION['last_regeneration'])) {
            self::regenerateSession();
        } else {
            // Regenerate session every hour
            if ($_SESSION['last_regeneration'] < (time() - 3600)) {
                self::regenerateSession();
            }
        }
        
        // Check for remember me cookie and auto-login if needed
        self::checkRememberMeCookie();
    }
    
    private static function regenerateSession() {
        // Regenerate ID and keep data
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
    
    public static function setUser($userId, $username, $rememberMe = false) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['user_type'] = 'user';
        $_SESSION['last_activity'] = time();
        
        if ($rememberMe) {
            self::createRememberMeCookie($userId, 'user');
        }
    }
    
    public static function setAdmin($adminId, $username) {
        $_SESSION['admin_id'] = $adminId;
        $_SESSION['admin_username'] = $username;
        $_SESSION['is_admin'] = true;
        $_SESSION['user_type'] = 'admin';
        $_SESSION['last_activity'] = time();
    }
    
    public static function isUserLoggedIn() {
        return isset($_SESSION['user_id']) && 
               (!isset($_SESSION['user_type']) || $_SESSION['user_type'] === 'user');
    }
    
    public static function isAdminLoggedIn() {
        return isset($_SESSION['admin_id']) && 
               (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin');
    }

    private static function migrateExistingSession() {
        // If user is logged in but user_type is not set
        if (isset($_SESSION['user_id']) && !isset($_SESSION['user_type'])) {
            $_SESSION['user_type'] = 'user';
        }
        
        // If admin is logged in but user_type is not set
        if (isset($_SESSION['admin_id']) && !isset($_SESSION['user_type'])) {
            $_SESSION['user_type'] = 'admin';
            $_SESSION['is_admin'] = true;
        }
    }
    
    private static function createRememberMeCookie($id, $type) {
        // Create a token: user/admin ID + random string + timestamp
        $token = bin2hex(random_bytes(32));
        $expires = time() + self::SESSION_LIFETIME;
        
        // Get database connection and store token
        $database = new Database();
        $db = $database->getConnection();
        
        // Store token hash in database
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        $id = (int)$id;
        
        if ($type === 'user') {
            $sql = "UPDATE users SET remember_token = '{$hashedToken}', token_expires = FROM_UNIXTIME({$expires}) 
                    WHERE id = {$id}";
        } else {
            $sql = "UPDATE admins SET remember_token = '{$hashedToken}', token_expires = FROM_UNIXTIME({$expires}) 
                    WHERE id = {$id}";
        }
        
        if ($db->query($sql)) {
            // Set the cookie with user ID, token and type
            $cookieValue = base64_encode(json_encode([
                'id' => $id,
                'token' => $token,
                'type' => $type
            ]));
            
            setcookie(
                self::REMEMBER_ME_COOKIE,
                $cookieValue,
                [
                    'expires' => $expires,
                    'path' => '/',
                    'domain' => '',
                    'secure' => isset($_SERVER['HTTPS']),
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]
            );
        }
    }
    
    private static function checkRememberMeCookie() {
        // If user is already logged in, no need to check
        if (self::isUserLoggedIn() || self::isAdminLoggedIn()) {
            return;
        }
        
        // Check for remember me cookie
        if (isset($_COOKIE[self::REMEMBER_ME_COOKIE])) {
            try {
                $cookieData = json_decode(base64_decode($_COOKIE[self::REMEMBER_ME_COOKIE]), true);
                
                if (isset($cookieData['id']) && isset($cookieData['token']) && isset($cookieData['type'])) {
                    $id = (int)$cookieData['id'];
                    $token = $cookieData['token'];
                    $type = $cookieData['type'];
                    
                    // Get database connection
                    $database = new Database();
                    $db = $database->getConnection();
                    
                    // Get user/admin data from database
                    if ($type === 'user') {
                        $sql = "SELECT id, username, remember_token, token_expires > NOW() as token_valid 
                                FROM users WHERE id = {$id}";
                    } else {
                        $sql = "SELECT id, username, remember_token, token_expires > NOW() as token_valid 
                                FROM admins WHERE id = {$id}";
                    }
                    
                    $result = $db->query($sql);
                    
                    if ($result && $result->num_rows > 0) {
                        $data = $result->fetch_assoc();
                        
                        // Verify token is valid and not expired
                        if ($data['token_valid'] && password_verify($token, $data['remember_token'])) {
                            // Auto login
                            if ($type === 'user') {
                                self::setUser($data['id'], $data['username']);
                            } else {
                                self::setAdmin($data['id'], $data['username']);
                            }
                            
                            // Create a new remember me cookie (rotation)
                            self::createRememberMeCookie($id, $type);
                        }
                    }
                }
            } catch (Exception $e) {
                // Invalid cookie data, clear it
                self::clearRememberMeCookie();
            }
        }
    }
    
    public static function clearRememberMeCookie() {
        if (isset($_COOKIE[self::REMEMBER_ME_COOKIE])) {
            setcookie(
                self::REMEMBER_ME_COOKIE,
                '',
                [
                    'expires' => time() - 3600,
                    'path' => '/',
                    'domain' => '',
                    'secure' => isset($_SERVER['HTTPS']),
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]
            );
        }
    }
    
    public static function logout() {
        self::clearRememberMeCookie();
        
        // If user is logged in, clear remember token in database
        if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'user') {
            $id = (int)$_SESSION['user_id'];
            $database = new Database();
            $db = $database->getConnection();
            $db->query("UPDATE users SET remember_token = NULL, token_expires = NULL WHERE id = {$id}");
        }
        
        // If admin is logged in, clear remember token in database
        if (isset($_SESSION['admin_id']) && $_SESSION['user_type'] === 'admin') {
            $id = (int)$_SESSION['admin_id'];
            $database = new Database();
            $db = $database->getConnection();
            $db->query("UPDATE admins SET remember_token = NULL, token_expires = NULL WHERE id = {$id}");
        }
        
        // Clear all session data
        session_unset();
        session_destroy();
    }
    
    public static function updateActivity() {
        $_SESSION['last_activity'] = time();
    }
    
    public static function checkSessionTimeout($timeoutSeconds = 7200) {
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > $timeoutSeconds)) {
            // Session has timed out
            self::logout();
            return true;
        }
        return false;
    }
}