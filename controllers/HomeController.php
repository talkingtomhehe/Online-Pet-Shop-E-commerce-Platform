<?php
// filepath: c:\xampp\htdocs\chabongshop\controllers\HomeController.php
// Home page controller

require_once 'models/Product.php';
require_once 'models/Category.php';

class HomeController {
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
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