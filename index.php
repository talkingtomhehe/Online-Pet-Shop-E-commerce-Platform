<?php
// Main routing file

// Include SessionManager
require_once 'includes/SessionManager.php';
// Initialize session
SessionManager::init();

require_once 'config/env.php';

// Include configuration files
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Update user's last activity timestamp if logged in
if (SessionManager::isUserLoggedIn()) {
    // Update session activity
    SessionManager::updateActivity();

    // Update user's last activity in database
    require_once 'models/User.php';
    $db = new Database();
    $userActivityModel = new User($db->getConnection());
    $userActivityModel->updateLastActivity($_SESSION['user_id']);
}

// Check for session timeout (2 hours)
if (SessionManager::checkSessionTimeout(7200)) {
    // Session timed out, redirect to login with message
    $_SESSION['login_error'] = 'Your session has expired. Please log in again.';
    header('Location: ' . SITE_URL . 'user/login');
    exit;
}

// Parse the URL to determine controller and action
$request = $_SERVER['REQUEST_URI'];
$basePath = '/petshop/';

// Remove base path from the request URI
$request = str_replace($basePath, '', $request);

// Separate query string from path
$requestParts = explode('?', $request);
$path = $requestParts[0];

// Parse the URL path
$urlParts = explode('/', $path);
$controller = isset($urlParts[0]) && !empty($urlParts[0]) ? $urlParts[0] : 'home';
$action = isset($urlParts[1]) && !empty($urlParts[1]) ? $urlParts[1] : 'index';
$id = isset($urlParts[2]) ? $urlParts[2] : null;

// Clean the parameters
$controller = strtolower(strip_tags($controller));
$action = strtolower(strip_tags($action));

// Map URL to controller
switch ($controller) {
    case 'products':
        require_once 'controllers/ProductController.php';
        $productController = new ProductController();

        if ($action === 'detail' && $id) {
            $productController->detail($id);
        } elseif ($action === 'category' && $id) {
            $productController->category($id);
        } elseif ($action === 'search') {
            $productController->search();
        } elseif ($action === 'ajax-search') {
            $productController->ajaxSearch();
        } else {
            $productController->index();
        }
        break;

    case 'home':
        require_once 'controllers/HomeController.php';
        $homeController = new HomeController();

        if ($action === 'index') {
            $homeController->index();
        } else {
            $homeController->index();
        }
        break;

    case 'contact':
        require_once 'controllers/HomeController.php';
        $homeController = new HomeController();
        $homeController->contact();
        break;

    case 'user':
        require_once 'controllers/UserController.php';
        $userController = new UserController();

        if ($action === 'login') {
            $userController->login();
        } elseif ($action === 'google-login') {
            $userController->googleLogin();
        } elseif ($action === 'google-callback') {
            $userController->googleCallback();
        } elseif ($action === 'signup') {
            $userController->signup();
        } elseif ($action === 'logout') {
            $userController->logout();
        } elseif ($action === 'profile') {
            $userController->profile();
        } elseif ($action === 'orders') {
            $userController->orders();
        } elseif ($action === 'order-detail' && $id) {
            $userController->orderDetail($id);
        } elseif ($action === 'update-avatar') {
            $userController->updateAvatar();
        } else {
            $userController->login();
        }
        break;

    case 'admin':
        require_once 'controllers/AdminController.php';
        $adminController = new AdminController();

        if ($action === 'login') {
            $adminController->login();
        } elseif ($action === 'logout') {
            $adminController->logout();
        } elseif ($action === 'dashboard') {
            $adminController->dashboard();
        }
        // Products management
        elseif ($action === 'products') {
            $adminController->products();
        } elseif ($action === 'add-product') {
            $adminController->addProduct();
        } elseif ($action === 'edit-product' && $id) {
            $adminController->editProduct($id);
        } elseif ($action === 'delete-product') {
            $adminController->deleteProduct();
        }
        // Categories management
        elseif ($action === 'categories') {
            $adminController->categories();
        } elseif ($action === 'add-category') {
            $adminController->addCategory();
        } elseif ($action === 'edit-category' && $id) {
            $adminController->editCategory($id);
        } elseif ($action === 'delete-category') {
            $adminController->deleteCategory();
        }
        // Users management
        elseif ($action === 'users') {
            $adminController->users();
        } elseif ($action === 'delete-user') {
            $adminController->deleteUser();
        } elseif ($action === 'get-online-users') {
            $adminController->getOnlineUsers();
        }
        // Order management
        elseif ($action === 'orders') {
            $adminController->orders();
        } elseif ($action === 'order-detail' && $id) {
            $adminController->orderDetail($id);
        } elseif ($action === 'update-order-status') {
            $adminController->updateOrderStatus();
        } elseif ($action === 'locations') {
            $adminController->locations();
        } else {
            $adminController->login();
        }
        break;

    case 'cart':
        require_once 'controllers/CartController.php';
        $cartController = new CartController();

        if ($action === 'add') {
            $cartController->add();
        } elseif ($action === 'update') {
            $cartController->update();
        } elseif ($action === 'remove') {
            $cartController->remove();
        } elseif ($action === 'clear') {
            $cartController->clear();
        } elseif ($action === 'checkout') {
            $cartController->checkout();
        } else {
            $cartController->index();
        }
        break;

    case 'ajax':
        require_once 'controllers/AjaxController.php';
        $ajaxController = new AjaxController();

        if ($action === 'check-username') {
            $ajaxController->checkUsername();
        } elseif ($action === 'check-email') {
            $ajaxController->checkEmail();
        }
        break;

    default:
        require_once 'controllers/HomeController.php';
        $homeController = new HomeController();
        $homeController->index();
        break;
}
