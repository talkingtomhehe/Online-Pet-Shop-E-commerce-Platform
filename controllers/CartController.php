<?php
require_once 'models/Cart.php';
require_once 'models/Product.php';
require_once 'models/Category.php';
require_once 'includes/SessionManager.php';

class CartController {
    private $cartModel;
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        $this->cartModel = new Cart();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }
    
    public function index() {
        global $action;
        
        // Check if user is logged in
        if (!SessionManager::isUserLoggedIn()) {
            $_SESSION['redirect_after_login'] = SITE_URL . 'cart';
            header('Location: ' . SITE_URL . 'user/login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Get cart items
        $cartItems = $this->cartModel->getCartItems($userId);
        $cartTotal = $this->cartModel->getCartTotal($userId);
        
        // Get all categories for the navbar
        $categories = $this->categoryModel->getAllCategories();
        
        // Page title
        $pageTitle = 'Shopping Cart';
        
        // Load view
        include VIEWS_PATH . 'layouts/header.php';
        include VIEWS_PATH . 'cart/index.php';
        include VIEWS_PATH . 'layouts/footer.php';
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            $buyNow = isset($_POST['buy_now']) && $_POST['buy_now'] == '1';
            // Check if user is logged in
            if (!SessionManager::isUserLoggedIn()) {
                // Return JSON with redirect
                echo json_encode([
                    'success' => false, 
                    'message' => 'Please log in to add items to your cart',
                    'redirect' => SITE_URL . 'user/login'
                ]);
                exit;
            }
            
            if ($productId <= 0 || $quantity <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
                exit;
            }
            
            // Get product info (to check stock)
            $product = $this->productModel->getProductById($productId);
            
            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                exit;
            }
            
            // Check if there's enough stock
            if (isset($product['stock']) && $product['stock'] < $quantity) {
                echo json_encode(['success' => false, 'message' => 'Not enough stock available. Only ' . $product['stock'] . ' items left.']);
                exit;
            }
            
            try {
                // Add to cart
                $userId = $_SESSION['user_id'];
                if ($buyNow) {
                    // For Buy Now: Store the product in session to use in checkout
                    $_SESSION['buy_now_product'] = [
                        'product_id' => $productId,
                        'quantity' => $quantity
                    ];
                    
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Proceeding to checkout',
                        'buyNow' => true
                    ]);
                } else {
                    // Regular Add to Cart
                    $result = $this->cartModel->addToCart($userId, $productId, $quantity);
                    
                    if ($result) {
                        // Get updated cart count
                        $cartCount = $this->cartModel->countItems($userId);
                        
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Product added to cart',
                            'cartCount' => $cartCount,
                            'buyNow' => false
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
                    }
                }
            } catch (Exception $e) {
                error_log("Error adding to cart: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'An error occurred while processing your request']);
            }
            exit;
        }
        
        // If not POST, redirect to cart page
        header('Location: ' . SITE_URL . 'cart');
        exit;
    }
    
    public function update() {
        // Check if user is logged in
        if (!SessionManager::isUserLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please log in to update your cart']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Debug output
            error_log('POST data: ' . print_r($_POST, true));
            
            $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
            
            if ($itemId <= 0 || $quantity <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid item or quantity']);
                exit;
            }
            
            // Get cart item
            $cartItem = $this->cartModel->getCartItemById($itemId);
            
            // Debug output
            error_log('Cart item: ' . print_r($cartItem, true));
            
            if (!$cartItem) {
                echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
                exit;
            }
            
            // Check if item belongs to current user
            if ($cartItem['user_id'] != $_SESSION['user_id']) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized action']);
                exit;
            }
            
            // Update cart
            $result = $this->cartModel->updateQuantity($itemId, $quantity);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Cart updated']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
            }
            exit;
        }
        
        header('Location: ' . SITE_URL . 'cart');
        exit;
    }
    
    public function remove() {
        // Check if user is logged in
        if (!SessionManager::isUserLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please log in to remove items from your cart']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Debug output
            error_log('Remove POST data: ' . print_r($_POST, true));
            
            $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
            
            if ($itemId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid item']);
                exit;
            }
            
            // Get cart item
            $cartItem = $this->cartModel->getCartItemById($itemId);
            
            // Debug output
            error_log('Cart item to remove: ' . print_r($cartItem, true));
            
            if (!$cartItem) {
                echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
                exit;
            }
            
            // Check if item belongs to current user
            if ($cartItem['user_id'] != $_SESSION['user_id']) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized action']);
                exit;
            }
            
            // Remove from cart
            $result = $this->cartModel->removeItem($itemId);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to remove item from cart']);
            }
            exit;
        }
        
        header('Location: ' . SITE_URL . 'cart');
        exit;
    }

    public function clear() {
        // Check if user is logged in
        if (!SessionManager::isUserLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please log in to clear your cart']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $result = $this->cartModel->clearCart($userId);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Cart cleared successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to clear cart']);
        }
        exit;
    }
    
    public function checkout() {
        // Check if user is logged in
        if (!SessionManager::isUserLoggedIn()) {
            $_SESSION['redirect_after_login'] = SITE_URL . 'cart/checkout';
            header('Location: ' . SITE_URL . 'user/login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Default values
        $cartItems = null;
        $cartTotal = 0;
        $buyNowMode = false;
        $buyNowProduct = null;
        $buyNowSubtotal = 0;
        
        // Check if coming from cart page specifically
        $fromCart = isset($_GET['from_cart']) && $_GET['from_cart'] == '1';
        
        // If coming from regular cart, clear any buy_now product in session
        if ($fromCart && isset($_SESSION['buy_now_product'])) {
            unset($_SESSION['buy_now_product']);
        }
        
        // Check if this is a direct "Buy Now" checkout
        if (isset($_SESSION['buy_now_product'])) {
            $buyNowMode = true;
            
            // Get the product
            $productId = $_SESSION['buy_now_product']['product_id'];
            $quantity = $_SESSION['buy_now_product']['quantity'];
            
            $buyNowProduct = $this->productModel->getProductById($productId);
            
            if ($buyNowProduct) {
                // Calculate subtotal
                $buyNowSubtotal = $buyNowProduct['price'] * $quantity;
                // Add quantity to product array for view
                $buyNowProduct['quantity'] = $quantity;
            } else {
                // Product not found, redirect to home
                unset($_SESSION['buy_now_product']);
                header('Location: ' . SITE_URL);
                exit;
            }
        } else {
            // Regular checkout from cart
            // Get cart items
            $cartItems = $this->cartModel->getCartItems($userId);
            $cartTotal = $this->cartModel->getCartTotal($userId);
            
            // If cart is empty, redirect to cart page
            if ($this->cartModel->countItems($userId) === 0) {
                header('Location: ' . SITE_URL . 'cart');
                exit;
            }
        }
        
        // Get user profile data for pre-filling the form
        require_once 'models/User.php';
        $userModel = new User();
        $userData = $userModel->getUserById($userId);
        
        // Process checkout form
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate form
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $city = isset($_POST['city']) ? trim($_POST['city']) : '';
            $postalCode = isset($_POST['postal_code']) ? trim($_POST['postal_code']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
            
            if (empty($name) || empty($email) || empty($address) || empty($city) || empty($postalCode)) {
                $error = 'Please fill in all required fields';
            } else {
                // Create an order
                require_once 'models/Order.php';
                $orderModel = new Order();
                
                // Prepare order data
                $orderData = [
                    'user_id' => $userId,
                    'name' => $name,
                    'email' => $email,
                    'address' => $address,
                    'city' => $city,
                    'postal_code' => $postalCode,
                    'phone' => $phone,
                    'notes' => $notes,
                    'total_amount' => $buyNowMode ? $buyNowSubtotal : $cartTotal
                ];
                
                // Create the order
                $orderId = $orderModel->createOrder($orderData);
                
                if ($orderId) {
                    if ($buyNowMode) {
                        // Add the single product as an order item
                        $orderModel->addOrderItem([
                            'order_id' => $orderId,
                            'product_id' => $buyNowProduct['id'],
                            'quantity' => $buyNowProduct['quantity'],
                            'price' => $buyNowProduct['price']
                        ]);
                        
                        // Update product stock (reduce by quantity purchased)
                        $this->productModel->updateStock($buyNowProduct['id'], $buyNowProduct['stock'] - $buyNowProduct['quantity']);
                        
                        // Clear the buy now session variable
                        unset($_SESSION['buy_now_product']);
                    } else {
                        // Add all cart items as order items
                        $cartItems->data_seek(0); // Reset result pointer
                        while ($item = $cartItems->fetch_assoc()) {
                            $orderModel->addOrderItem([
                                'order_id' => $orderId,
                                'product_id' => $item['product_id'],
                                'quantity' => $item['quantity'],
                                'price' => $item['price']
                            ]);
                            
                            // Update product stock (reduce by quantity purchased)
                            $this->productModel->updateStock($item['product_id'], $item['stock'] - $item['quantity']);
                        }
                        
                        // Clear the cart
                        $this->cartModel->clearCart($userId);
                    }
                    
                    $success = 'Order placed successfully! Thank you for your purchase. <a href="' . SITE_URL . 'user/order-detail/' . $orderId . '">View your order</a>';
                } else {
                    $error = 'Failed to create your order. Please try again.';
                }
            }
        }
        
        // Get all categories for the navbar
        $categories = $this->categoryModel->getAllCategories();
        
        // Page title
        $pageTitle = 'Checkout';
        
        // Load view
        include VIEWS_PATH . 'layouts/header.php';
        include VIEWS_PATH . 'cart/checkout.php';
        include VIEWS_PATH . 'layouts/footer.php';
    }
}