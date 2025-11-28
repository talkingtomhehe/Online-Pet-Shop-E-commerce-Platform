<?php
// filepath: c:\xampp\htdocs\chabongshop\controllers\HomeController.php
// Home page controller

require_once 'models/Product.php';
require_once 'models/Category.php';
require_once 'models/Notification.php'; // added (no path change)

class HomeController {
    private $productModel;
    private $categoryModel;
    private $notificationModel; // added
    
    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();

        // Try to reuse any existing DB connection or create one if config constants exist.
        $dbConn = null;
        if (isset($GLOBALS['db'])) {
            $dbConn = $GLOBALS['db'];
        } elseif (class_exists('Database') && method_exists('Database', 'getInstance')) {
            $dbConn = Database::getInstance();
        } elseif (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
            // create a simple mysqli connection as a last resort (avoid if app already has a DB class)
            $dbConn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($dbConn->connect_error) {
                $dbConn = null; // keep null to avoid exceptions
            }
        }

        // Instantiate Notification with a DB connection if the constructor expects one
        try {
            if ($dbConn !== null) {
                $this->notificationModel = new Notification($dbConn);
            } else {
                // If no DB object available, try to instantiate without argument in case constructor was updated
                $this->notificationModel = new Notification();
            }
        } catch (ArgumentCountError $e) {
            // Constructor requires an arg and we couldn't provide one — set null and handle gracefully
            $this->notificationModel = null;
        }
    }
    
    public function index() {
        // Get featured products
        $featuredProducts = $this->productModel->getFeaturedProducts(10);
        
        // Get all categories for the navbar
        $categories = $this->categoryModel->getAllCategories();

        // Set controller name for navigation highlighting
        $controller = 'home'; // Add this line
        
        // Page title
        $pageTitle = 'Home';
        
        // Notifications: always populate variables for the view (no session check)
        $unreadCount = 0;
        $notifications = [];

        // Try model methods if present
        if (method_exists($this->notificationModel, 'countUnread')) {
            $unreadCount = (int)$this->notificationModel->countUnread();
        }

        if (method_exists($this->notificationModel, 'getRecent')) {
            $notifications = $this->notificationModel->getRecent(10);
        } elseif (method_exists($this->notificationModel, 'getAll')) {
            // fallback method name
            $notifications = $this->notificationModel->getAll();
        }

        // Convert mysqli_result to array if needed
        if ($notifications instanceof mysqli_result) {
            $tmp = [];
            while ($row = $notifications->fetch_assoc()) {
                $tmp[] = $row;
            }
            $notifications = $tmp;
        } elseif (is_object($notifications) && method_exists($notifications, 'fetch_assoc')) {
            // generic cursor-like object handling
            $tmp = [];
            while ($row = $notifications->fetch_assoc()) {
                $tmp[] = $row;
            }
            $notifications = $tmp;
        } elseif (!is_array($notifications)) {
            // ensure it's always an array
            $notifications = [];
        }
        
        // Load view
        include VIEWS_PATH . 'layouts/header.php';
        include VIEWS_PATH . 'home/index.php';
        include VIEWS_PATH . 'layouts/footer.php';
    }

    public function contact() {
        // Set page title
        $pageTitle = 'Contact Us';
        
        // Get store locations
        require_once 'models/StoreLocation.php';
        $locationModel = new StoreLocation();
        $storeLocations = $locationModel->getActiveLocations();
        
        // Get main location for initial map center (first active location)
        $mainLocation = null;
        if ($storeLocations && $storeLocations->num_rows > 0) {
            $mainLocation = $storeLocations->fetch_assoc();
            // Reset pointer to start
            $storeLocations->data_seek(0);
        } else {
            // Default location if no stores in database (Bangkok)
            $mainLocation = [
                'latitude' => 10.773620109155816, 
                'longitude' => 106.65723158622284,
                'name' => 'Woof-Woof Pet Shop',
                'address' => '269 Đ. Lý Thường Kiệt, Phường 15, Quận 11, Hồ Chí Minh, Việt Nam', 
                'phone' => '+66 2 123 4567',
                'email' => 'contact@woofwoofpetshop.com',
                'hours' => 'Monday - Saturday: 9:00 AM - 7:00 PM<br>Sunday: 10:00 AM - 6:00 PM'
            ];
        }
        
        // Define contact information
        $contactInfo = [
            'phone' => '+66 2 123 4567',
            'email' => 'contact@woofwoofpetshop.com',
            'hours' => 'Monday - Saturday: 9:00 AM - 7:00 PM<br>Sunday: 10:00 AM - 6:00 PM',
            'social' => [
                'facebook' => 'https://facebook.com/woofwoofpetshop',
                'instagram' => 'https://instagram.com/woofwoofpetshop',
                'twitter' => 'https://twitter.com/woofwoofpetshop'
            ]
        ];
        
        // Handle form submission
        $message = '';
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
            $messageContent = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
            
            // Validate input
            if (empty($name) || empty($email) || empty($subject) || empty($messageContent)) {
                $error = 'Please fill in all required fields.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email address.';
            } else {
                // In a real application, send email here
                // For this example, we'll just show a success message
                $message = 'Thank you for your message! We will get back to you shortly.';
            }
        }
        
        // Load view
        include VIEWS_PATH . 'layouts/header.php';
        include VIEWS_PATH . 'contact.php';
        include VIEWS_PATH . 'layouts/footer.php';
    }
}
?>