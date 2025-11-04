<?php
require_once 'models/User.php';
require_once 'models/Category.php';
require_once 'includes/SessionManager.php';

class UserController {
    private $userModel;
    private $categoryModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->categoryModel = new Category();
    }
    
    public function login() {
        global $action;
        
        // Handle form submission
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = isset($_POST['username']) ? $_POST['username'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $rememberMe = isset($_POST['remember_me']) ? true : false;
            
            if (empty($username) || empty($password)) {
                $error = 'Please fill in all fields';
            } else {
                $user = $this->userModel->login($username, $password);
                
                if ($user) {
                    // User login successful using SessionManager
                    require_once 'includes/SessionManager.php';
                    SessionManager::setUser($user['id'], $user['username'], $rememberMe);
                    
                    // Update last login time
                    $this->userModel->updateLastLogin($user['id']);
                    
                    // Check if there's a redirect after login
                    if (isset($_SESSION['redirect_after_login'])) {
                        $redirect = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']);
                        header('Location: ' . $redirect);
                    } else {
                        // Redirect to home
                        header('Location: ' . SITE_URL);
                    }
                    exit;
                } else {
                    $error = 'Invalid username or password';
                }
            }
        }
        
        // Get all categories for the navbar
        $categories = $this->categoryModel->getAllCategories();
    
        $controller = 'user';
        
        // Page title
        $pageTitle = 'Login';
        
        // Load view
        include VIEWS_PATH . 'layouts/header.php';
        include VIEWS_PATH . 'user/signin.php';
        include VIEWS_PATH . 'layouts/footer.php';
    }
    
    public function logout() {
        // Use SessionManager for secure logout
        require_once 'includes/SessionManager.php';
        SessionManager::logout();
        
        // Redirect to home
        header('Location: ' . SITE_URL);
        exit;
    }
    
    public function signup() {
        global $action;
    
        // Handle form submission
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = isset($_POST['username']) ? $_POST['username'] : '';
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            
            if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
                $error = 'Please fill in all fields';
            } elseif ($password !== $confirm) {
                $error = 'Passwords do not match';
            } else {
                $result = $this->userModel->register($username, $email, $password);
                
                if ($result['success']) {
                    // Properly set session using SessionManager
                    require_once 'includes/SessionManager.php';
                    SessionManager::setUser($result['user_id'], $username);
                    
                    // Update last login time
                    $this->userModel->updateLastLogin($result['user_id']);
                    
                    header('Location: ' . SITE_URL);
                    exit;
                } else {
                    $error = $result['message'] ?? 'Registration failed. Please try again.';
                }
            }
        }
        
        // Get all categories for the navbar
        $categories = $this->categoryModel->getAllCategories();
        
        // Page title
        $pageTitle = 'Sign Up';
        
        // Load view
        include VIEWS_PATH . 'layouts/header.php';
        include VIEWS_PATH . 'user/signup.php';
        include VIEWS_PATH . 'layouts/footer.php';
    }

    public function orders() {
        // Check if user is logged in
        if (!SessionManager::isUserLoggedIn()) {
            $_SESSION['redirect_after_login'] = SITE_URL . 'user/orders';
            header('Location: ' . SITE_URL . 'user/login');
            exit;
        }
        
        // Get user orders
        require_once 'models/Order.php';
        $orderModel = new Order();
        $orders = $orderModel->getOrdersByUserId($_SESSION['user_id']);
        
        // Get all categories for the navbar
        $categories = $this->categoryModel->getAllCategories();
        
        // Page title
        $pageTitle = 'My Orders';
        
        // Load view
        include VIEWS_PATH . 'layouts/header.php';
        include VIEWS_PATH . 'user/orders.php';
        include VIEWS_PATH . 'layouts/footer.php';
    }
    
    public function orderDetail($id) {
        // Check if user is logged in
        if (!SessionManager::isUserLoggedIn()) {
            $_SESSION['redirect_after_login'] = SITE_URL . 'user/order-detail/' . $id;
            header('Location: ' . SITE_URL . 'user/login');
            exit;
        }
        
        // Get order details
        require_once 'models/Order.php';
        $orderModel = new Order();
        $order = $orderModel->getOrderById($id);
        
        // Check if order exists and belongs to the current user
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            header('Location: ' . SITE_URL . 'user/orders');
            exit;
        }
        
        // Get order items
        $orderItems = $orderModel->getOrderItems($id);
        
        // Get all categories for the navbar
        $categories = $this->categoryModel->getAllCategories();
        
        // Page title
        $pageTitle = 'Order #' . $id;
        
        // Load view
        include VIEWS_PATH . 'layouts/header.php';
        include VIEWS_PATH . 'user/order-detail.php';
        include VIEWS_PATH . 'layouts/footer.php';
    }

    public function profile() {
        // Check if user is logged in
        if (!SessionManager::isUserLoggedIn()) {
            $_SESSION['redirect_after_login'] = SITE_URL . 'user/profile';
            header('Location: ' . SITE_URL . 'user/login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $error = '';
        $success = '';

        // Check for avatar update messages
        if (isset($_SESSION['avatar_error'])) {
            $error = $_SESSION['avatar_error'];
            unset($_SESSION['avatar_error']);
        }
        
        if (isset($_SESSION['avatar_success'])) {
            $success = $_SESSION['avatar_success'];
            unset($_SESSION['avatar_success']);
        }
        
        // Get user data
        $userData = $this->userModel->getUserById($userId);
        
        if (!$userData) {
            // User not found, redirect to login
            session_unset();
            session_destroy();
            header('Location: ' . SITE_URL . 'user/login');
            exit;
        }
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $city = isset($_POST['city']) ? trim($_POST['city']) : '';
            $postalCode = isset($_POST['postal_code']) ? trim($_POST['postal_code']) : '';
            $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
            $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
            $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
            
            // Validate form data
            if (empty($username) || empty($email)) {
                $error = "Username and email are required";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Please enter a valid email address";
            } else {
                // Check if username or email already exists (for another user)
                if ($username !== $userData['username'] && $this->userModel->getUserByUsername($username)) {
                    $error = "Username already exists";
                } elseif ($email !== $userData['email'] && $this->userModel->getUserByEmail($email)) {
                    $error = "Email already exists";
                } else {
                    // Update user data
                    $updateData = [
                        'username' => $username,
                        'email' => $email,
                        'full_name' => $fullName,
                        'phone' => $phone,
                        'address' => $address,
                        'city' => $city,
                        'postal_code' => $postalCode
                    ];
                    
                    // Check if password change was requested
                    if (!empty($currentPassword) && !empty($newPassword)) {
                        // Verify current password
                        if (!password_verify($currentPassword, $userData['password'])) {
                            $error = "Current password is incorrect";
                        } elseif ($newPassword !== $confirmPassword) {
                            $error = "New passwords do not match";
                        } elseif (strlen($newPassword) < 6) {
                            $error = "Password must be at least 6 characters long";
                        } else {
                            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                        }
                    }
                    
                    // Update user if no error
                    if (empty($error)) {
                        if ($this->userModel->updateUser($userId, $updateData)) {
                            $success = "Profile updated successfully!";
                            
                            // Update session if username was changed
                            if ($username !== $userData['username']) {
                                $_SESSION['username'] = $username;
                            }
                            
                            // Refresh user data
                            $userData = $this->userModel->getUserById($userId);
                        } else {
                            $error = "Failed to update profile. Please try again.";
                        }
                    }
                }
            }
        }
        
        // Get all categories for the navbar
        $categories = $this->categoryModel->getAllCategories();
        
        // Page title
        $pageTitle = 'My Profile';
        
        // Load view
        include VIEWS_PATH . 'layouts/header.php';
        include VIEWS_PATH . 'user/profile.php';
        include VIEWS_PATH . 'layouts/footer.php';
    }

    public function updateAvatar() {
        // Check if user is logged in
        if (!SessionManager::isUserLoggedIn()) {
            $_SESSION['redirect_after_login'] = SITE_URL . 'user/profile';
            header('Location: ' . SITE_URL . 'user/login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
            $file = $_FILES['avatar'];
            
            // Check for errors
            if ($file['error'] === UPLOAD_ERR_OK) {
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                $fileType = mime_content_type($file['tmp_name']);
                
                if (!in_array($fileType, $allowedTypes)) {
                    $_SESSION['avatar_error'] = 'Only JPG and PNG images are allowed.';
                    header('Location: ' . SITE_URL . 'user/profile');
                    exit;
                }
                
                // Validate file size (max 2MB)
                if ($file['size'] > 2 * 1024 * 1024) {
                    $_SESSION['avatar_error'] = 'File size should be less than 2MB.';
                    header('Location: ' . SITE_URL . 'user/profile');
                    exit;
                }
                
                // Create directory if it doesn't exist
                $uploadDir = 'public/images/avatars/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Generate a unique filename
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'avatar_' . $userId . '_' . uniqid() . '.' . $ext;
                $filePath = $uploadDir . $filename;
                
                // Move the uploaded file
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    // Update the database - this will replace any external URL with a local path
                    if ($this->userModel->updateAvatar($userId, $filePath)) {
                        $_SESSION['avatar_success'] = 'Profile picture updated successfully.';
                    } else {
                        $_SESSION['avatar_error'] = 'Failed to update profile picture in database.';
                    }
                } else {
                    $_SESSION['avatar_error'] = 'Failed to upload file.';
                }
            } else {
                // Handle upload errors
                $uploadErrors = [
                    UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                    UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.',
                    UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
                ];
                
                $errorMessage = isset($uploadErrors[$file['error']]) ? $uploadErrors[$file['error']] : 'Unknown upload error.';
                $_SESSION['avatar_error'] = $errorMessage;
            }
            
            // Redirect back to profile
            header('Location: ' . SITE_URL . 'user/profile');
            exit;
        } else {
            // Invalid request
            header('Location: ' . SITE_URL . 'user/profile');
            exit;
        }
    }

    public function googleLogin() {
        // Include Google configuration
        require_once 'config/google_config.php';
        require_once __DIR__ . '/../vendor/autoload.php';
        // Initialize Google Client
        $client = new Google_Client();
        $client->setClientId(GOOGLE_CLIENT_ID);
        $client->setClientSecret(GOOGLE_CLIENT_SECRET);
        $client->setRedirectUri(GOOGLE_REDIRECT_URI);
        $client->addScope("email");
        $client->addScope("profile");
        
        // Generate the Google login URL and redirect
        $authUrl = $client->createAuthUrl();
        header('Location: ' . $authUrl);
        exit;
    }
    
    public function googleCallback() {
        // Include Google configuration
        require_once 'config/google_config.php';
        require_once 'vendor/autoload.php';
        
        // Initialize Google Client
        $client = new Google_Client();
        $client->setClientId(GOOGLE_CLIENT_ID);
        $client->setClientSecret(GOOGLE_CLIENT_SECRET);
        $client->setRedirectUri(GOOGLE_REDIRECT_URI);
        
        if (isset($_GET['code'])) {
            // Exchange authorization code for access token
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $client->setAccessToken($token['access_token']);
            
            // Get user profile
            $google_oauth = new Google\Service\Oauth2($client);
            $google_account_info = $google_oauth->userinfo->get();
            
            // Extract user data
            $userData = [
                'google_id' => $google_account_info->id,
                'email' => $google_account_info->email,
                'full_name' => $google_account_info->name,
                'avatar' => $google_account_info->picture
            ];
            
            // Create or update user in database
            $user = $this->userModel->createOrUpdateGoogleUser($userData);
            
            if ($user) {
                // Set session and redirect
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Check if there's a redirect after login
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header('Location: ' . $redirect);
                } else {
                    header('Location: ' . SITE_URL);
                }
                exit;
            }
        }
        
        // If we get here, something went wrong
        $_SESSION['login_error'] = 'Google login failed. Please try again.';
        header('Location: ' . SITE_URL . 'user/login');
        exit;
    }
}