<?php
require_once 'models/Admin.php';
require_once 'models/Category.php';
require_once 'models/Product.php';
require_once 'models/User.php';

class AdminController
{
    private $adminModel;
    private $categoryModel;
    private $productModel;
    private $userModel;

    public function __construct()
    {
        $this->adminModel = new Admin();
        $this->categoryModel = new Category();
        $this->productModel = new Product();
        $this->userModel = new User();
    }

    public function login()
    {
        // If admin is already logged in, redirect to dashboard
        require_once 'includes/SessionManager.php';
        if (SessionManager::isAdminLoggedIn()) {
            header('Location: ' . SITE_URL . 'admin/dashboard');
            exit;
        }

        // Handle form submission
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = isset($_POST['admin_username']) ? $_POST['admin_username'] : '';
            $password = isset($_POST['admin_password']) ? $_POST['admin_password'] : '';

            if (empty($username) || empty($password)) {
                $error = 'Please fill in all fields';
            } else {
                $admin = $this->adminModel->login($username, $password);

                if ($admin) {
                    // Set session and redirect
                    SessionManager::setAdmin($admin['id'], $admin['username']);

                    header('Location: ' . SITE_URL . 'admin/dashboard');
                    exit;
                } else {
                    $error = 'Invalid admin username or password';
                }
            }
        }

        // Page title
        $pageTitle = 'Admin Login';

        // Load view
        include VIEWS_PATH . 'admin/layouts/header.php';
        include VIEWS_PATH . 'admin/login.php';
        include VIEWS_PATH . 'admin/layouts/footer.php';
    }

    public function logout()
    {
        // Use SessionManager for secure logout
        require_once 'includes/SessionManager.php';
        SessionManager::logout();

        // Redirect to admin login
        header('Location: ' . SITE_URL . 'admin/login');
        exit;
    }

    // Update checkAdminAuth method
    private function checkAdminAuth()
    {
        require_once 'includes/SessionManager.php';
        if (!SessionManager::isAdminLoggedIn()) {
            header('Location: ' . SITE_URL . 'admin/login');
            exit;
        }
    }

    public function dashboard()
    {
        // Check if admin is logged in
        $this->checkAdminAuth();

        // Get database connection
        $database = new Database();
        $db = $database->getConnection();

        // Initialize variables
        $totalProducts = 0;
        $totalCategories = 0;
        $totalUsers = 0;
        $totalOrders = 0;
        $pendingOrders = 0;

        // Count products
        $result = $db->query("SELECT COUNT(*) as count FROM products");
        if ($result && $result->num_rows > 0) {
            $totalProducts = $result->fetch_assoc()['count'];
        }

        // Count categories
        $result = $db->query("SELECT COUNT(*) as count FROM categories");
        if ($result && $result->num_rows > 0) {
            $totalCategories = $result->fetch_assoc()['count'];
        }

        // Count users
        $result = $db->query("SELECT COUNT(*) as count FROM users");
        if ($result && $result->num_rows > 0) {
            $totalUsers = $result->fetch_assoc()['count'];
        }

        // Count orders
        $result = $db->query("SELECT COUNT(*) as count FROM orders");
        if ($result && $result->num_rows > 0) {
            $totalOrders = $result->fetch_assoc()['count'];
        }

        // Count pending orders
        $result = $db->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
        if ($result && $result->num_rows > 0) {
            $pendingOrders = $result->fetch_assoc()['count'];
        }

        // Get recent orders with join to users table
        $recentOrders = $db->query("SELECT o.id, o.total_amount, o.status, o.created_at, u.username 
                                    FROM orders o 
                                    LEFT JOIN users u ON o.user_id = u.id 
                                    ORDER BY o.created_at DESC 
                                    LIMIT 5");

        // Get recent users with last login time
        $recentUsers = $db->query("SELECT id, username, email, full_name, avatar, last_login, 
                                    (CASE WHEN last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE) THEN 1 ELSE 0 END) as is_online 
                                    FROM users 
                                    ORDER BY created_at DESC 
                                    LIMIT 10");

        // Page title
        $pageTitle = 'Admin Dashboard';

        // Load view
        include VIEWS_PATH . 'admin/layouts/header.php';
        include VIEWS_PATH . 'admin/dashboard.php';
        include VIEWS_PATH . 'admin/layouts/footer.php';
    }

    private function getPaginationUrl($baseUrl, $page, $additionalParams = [])
    {
        $params = array_merge(['page' => $page], $additionalParams);
        return $baseUrl . '?' . http_build_query($params);
    }

    /*** PRODUCT MANAGEMENT ***/

    public function products()
    {
        // Check if admin is logged in
        $this->checkAdminAuth();

        // Pagination
        $limit = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        // Get products with pagination
        $products = $this->productModel->getAllProducts('id', 'asc', $limit, $offset);
        $totalProducts = $this->productModel->countProducts();
        $totalPages = ceil($totalProducts / $limit);

        // Create URL generator for pagination
        $urlFunction = function ($page) {
            return $this->getPaginationUrl(SITE_URL . 'admin/products', $page);
        };

        // Make the category model available to the view
        $categoryModel = $this->categoryModel;

        // Page title
        $pageTitle = 'Manage Products';

        // Current page for pagination
        $currentPage = $page;

        // Load view
        include VIEWS_PATH . 'admin/layouts/header.php';
        include VIEWS_PATH . 'admin/products/index.php';
        include VIEWS_PATH . 'admin/layouts/footer.php';
    }

    public function addProduct()
    {
        // Check if admin is logged in
        $this->checkAdminAuth();

        $error = '';
        $success = '';
        $product = [
            'name' => '',
            'description' => '',
            'price' => '',
            'category_id' => '',
            'image' => '',
            'stock' => '',
            'featured' => 0
        ];

        // Get categories for dropdown
        $categories = $this->categoryModel->getAllCategories();

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $product = [
                'name' => isset($_POST['name']) ? trim($_POST['name']) : '',
                'description' => isset($_POST['description']) ? trim($_POST['description']) : '',
                'price' => isset($_POST['price']) ? (float)$_POST['price'] : 0,
                'category_id' => isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0,
                'stock' => isset($_POST['stock']) ? (int)$_POST['stock'] : 0,
                'featured' => isset($_POST['featured']) ? 1 : 0,
                'image' => ''
            ];

            // Validate form data
            if (empty($product['name'])) {
                $error = 'Product name is required';
            } elseif (empty($product['description'])) {
                $error = 'Product description is required';
            } elseif ($product['price'] <= 0) {
                $error = 'Valid price is required';
            } elseif ($product['category_id'] <= 0) {
                $error = 'Please select a category';
            } else {
                // Handle image upload
                $imagePath = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['image']['name'];
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);

                    if (in_array(strtolower($ext), $allowed)) {
                        $newFilename = uniqid() . '.' . $ext;
                        $uploadDir = 'public/images/products/';

                        // Create directory if it doesn't exist
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        $destination = $uploadDir . $newFilename;

                        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                            $imagePath = $destination;
                        } else {
                            $error = 'Failed to upload image';
                        }
                    } else {
                        $error = 'Invalid image format. Allowed formats: JPG, JPEG, PNG, GIF';
                    }
                }

                if (empty($error)) {
                    // Add product
                    $product['image'] = $imagePath;
                    $result = $this->productModel->addProduct($product);

                    if ($result) {
                        $_SESSION['admin_message'] = [
                            'type' => 'success',
                            'text' => 'Product added successfully'
                        ];
                        
                        header('Location: ' . SITE_URL . 'admin/products');
                        exit;
                    } else {
                        $error = 'Failed to add product';
                    }
                }
            }
        }

        // Page title
        $pageTitle = 'Add Product';

        // Load view
        include VIEWS_PATH . 'admin/layouts/header.php';
        include VIEWS_PATH . 'admin/products/form.php';
        include VIEWS_PATH . 'admin/layouts/footer.php';
    }

    public function editProduct($id)
    {
        // Check if admin is logged in
        $this->checkAdminAuth();

        $id = (int)$id;
        $error = '';
        $success = '';

        // Get product
        $product = $this->productModel->getProductById($id);

        if (!$product) {
            header('Location: ' . SITE_URL . 'admin/products');
            exit;
        }

        // Get categories for dropdown
        $categories = $this->categoryModel->getAllCategories();

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $product = [
                'id' => $id,
                'name' => isset($_POST['name']) ? trim($_POST['name']) : '',
                'description' => isset($_POST['description']) ? trim($_POST['description']) : '',
                'price' => isset($_POST['price']) ? (float)$_POST['price'] : 0,
                'category_id' => isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0,
                'stock' => isset($_POST['stock']) ? (int)$_POST['stock'] : 0,
                'featured' => isset($_POST['featured']) ? 1 : 0,
                'image' => isset($_POST['current_image']) ? $_POST['current_image'] : ''
            ];

            // Validate form data
            if (empty($product['name'])) {
                $error = 'Product name is required';
            } elseif (empty($product['description'])) {
                $error = 'Product description is required';
            } elseif ($product['price'] <= 0) {
                $error = 'Valid price is required';
            } elseif ($product['category_id'] <= 0) {
                $error = 'Please select a category';
            } else {
                // Handle image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['image']['name'];
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);

                    if (in_array(strtolower($ext), $allowed)) {
                        $newFilename = uniqid() . '.' . $ext;
                        $uploadDir = 'public/images/products/';

                        // Create directory if it doesn't exist
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        $destination = $uploadDir . $newFilename;

                        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                            // Delete old image if exists
                            if (!empty($product['image']) && file_exists($product['image'])) {
                                unlink($product['image']);
                            }
                            $product['image'] = $destination;
                        } else {
                            $error = 'Failed to upload image';
                        }
                    } else {
                        $error = 'Invalid image format. Allowed formats: JPG, JPEG, PNG, GIF';
                    }
                }

                if (empty($error)) {
                    // Update product
                    $result = $this->productModel->updateProduct($product);

                    if ($result) {
                        $_SESSION['admin_message'] = [
                            'type' => 'success',
                            'text' => 'Product updated successfully'
                        ];
                        
                        header('Location: ' . SITE_URL . 'admin/products');
                        exit;
                    } else {
                        $error = 'Failed to update product';
                    }
                }
            }
        }

        // Page title
        $pageTitle = 'Edit Product';

        // Load view
        include VIEWS_PATH . 'admin/layouts/header.php';
        include VIEWS_PATH . 'admin/products/form.php';
        include VIEWS_PATH . 'admin/layouts/footer.php';
    }

    public function deleteProduct()
    {
        // Check if admin is logged in
        $this->checkAdminAuth();

        // Check for AJAX request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];

            // Get product image
            $product = $this->productModel->getProductById($id);

            if ($product) {
                // Delete product
                $result = $this->productModel->deleteProduct($id);

                if ($result) {
                    // Delete image file if exists
                    if (!empty($product['image']) && file_exists($product['image'])) {
                        unlink($product['image']);
                    }
                    echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
            }
            exit;
        }

        // Redirect to products page
        header('Location: ' . SITE_URL . 'admin/products');
        exit;
    }

    /*** CATEGORY MANAGEMENT ***/

    public function categories()
    {
        // Check if admin is logged in
        $this->checkAdminAuth();

        // Pagination
        $limit = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        // Get all categories with pagination
        $categories = $this->categoryModel->getAllCategories($limit, $offset);
        $totalCategories = $this->categoryModel->countCategories();
        $totalPages = ceil($totalCategories / $limit);

        // Create URL generator for pagination
        $urlFunction = function ($page) {
            return $this->getPaginationUrl(SITE_URL . 'admin/categories', $page);
        };

        // Current page for pagination
        $currentPage = $page;

        // Page title
        $pageTitle = 'Manage Categories';

        // Load view
        include VIEWS_PATH . 'admin/layouts/header.php';
        include VIEWS_PATH . 'admin/categories/index.php';
        include VIEWS_PATH . 'admin/layouts/footer.php';
    }


    public function addCategory()
    {
        // Check if admin is logged in
        $this->checkAdminAuth();

        $error = '';
        $success = '';
        $category = [
            'name' => '',
            'description' => ''
        ];

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $category = [
                'name' => isset($_POST['name']) ? trim($_POST['name']) : '',
                'description' => isset($_POST['description']) ? trim($_POST['description']) : ''
            ];

            // Validate form data
            if (empty($category['name'])) {
                $error = 'Category name is required';
            } else {
                // Add category
                $result = $this->categoryModel->addCategory($category);

                if ($result) {
                    $_SESSION['admin_message'] = [
                        'type' => 'success',
                        'text' => 'Category added successfully'
                    ];
                    
                    header('Location: ' . SITE_URL . 'admin/categories');
                    exit;
                } else {
                    $error = 'Failed to add category';
                }
            }
        }

        // Page title
        $pageTitle = 'Add Category';

        // Load view
        include VIEWS_PATH . 'admin/layouts/header.php';
        include VIEWS_PATH . 'admin/categories/form.php';
        include VIEWS_PATH . 'admin/layouts/footer.php';
    }

    public function editCategory($id)
    {
        // Check if admin is logged in
        $this->checkAdminAuth();

        $id = (int)$id;
        $error = '';
        $success = '';

        // Get category
        $category = $this->categoryModel->getCategoryById($id);

        if (!$category) {
            header('Location: ' . SITE_URL . 'admin/categories');
            exit;
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $category = [
                'id' => $id,
                'name' => isset($_POST['name']) ? trim($_POST['name']) : '',
                'description' => isset($_POST['description']) ? trim($_POST['description']) : ''
            ];

            // Validate form data
            if (empty($category['name'])) {
                $error = 'Category name is required';
            } else {
                // Update category
                $result = $this->categoryModel->updateCategory($category);

                if ($result) {
                    $_SESSION['admin_message'] = [
                        'type' => 'success',
                        'text' => 'Category updated successfully'
                    ];
                    
                    header('Location: ' . SITE_URL . 'admin/categories');
                    exit;
                } else {
                    $error = 'Failed to update category';
                }
            }
        }

        // Page title
        $pageTitle = 'Edit Category';

        // Load view
        include VIEWS_PATH . 'admin/layouts/header.php';
        include VIEWS_PATH . 'admin/categories/form.php';
        include VIEWS_PATH . 'admin/layouts/footer.php';
    }

    public function deleteCategory()
    {
        // Check if admin is logged in
        $this->checkAdminAuth();

        // Check for AJAX request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];

            // Check if category has products
            $productsInCategory = $this->productModel->getProductsByCategory($id);

            if ($productsInCategory && $productsInCategory->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete category with products. Please reassign or delete products first.']);
                exit;
            }

            // Delete category
            $result = $this->categoryModel->deleteCategory($id);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
            }
            exit;
        }

        // Redirect to categories page
        header('Location: ' . SITE_URL . 'admin/categories');
        exit;
    }

    /*** USER MANAGEMENT ***/

    public function users()
    {
        // Check if admin is logged in
        $this->checkAdminAuth();

        // Pagination
        $limit = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        // Get users with pagination
        $users = $this->userModel->getAllUsers($limit, $offset);
        $totalUsers = $this->userModel->countUsers();
        $totalPages = ceil($totalUsers / $limit);

        // Create URL generator for pagination
        $urlFunction = function ($page) {
            return $this->getPaginationUrl(SITE_URL . 'admin/users', $page);
        };

        // Current page for pagination
        $currentPage = $page;

        // Page title
        $pageTitle = 'Manage Users';

        // Load view
        include VIEWS_PATH . 'admin/layouts/header.php';
        include VIEWS_PATH . 'admin/users/index.php';
        include VIEWS_PATH . 'admin/layouts/footer.php';
    }

    public function deleteUser()
    {
        // Check if admin is logged in
        $this->checkAdminAuth();

        // Check for AJAX request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];

            // Delete user
            $result = $this->userModel->deleteUser($id);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
            }
            exit;
        }

        // Redirect to users page
        header('Location: ' . SITE_URL . 'admin/users');
        exit;
    }

    /*** ORDER MANAGEMENT ***/

    public function orders()
    {
        // Check if admin is logged in
        $this->checkAdminAuth();

        // Get filter parameter
        $status = isset($_GET['status']) ? $_GET['status'] : '';

        // Pagination
        $limit = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        // Get orders with pagination
        require_once 'models/Order.php';
        $orderModel = new Order();
        $orders = $orderModel->getAllOrders($status, $limit, $offset);

        // Count total orders for pagination
        $totalOrders = $status ? $orderModel->countOrdersByStatus($status) : $orderModel->countOrders();
        $totalPages = ceil($totalOrders / $limit);

        // Create URL generator for pagination that preserves status filter
        $urlFunction = function ($page) use ($status) {
            $params = [];
            if (!empty($status)) {
                $params['status'] = $status;
            }
            return $this->getPaginationUrl(SITE_URL . 'admin/orders', $page, $params);
        };

        // Current page for pagination
        $currentPage = $page;

        // Page title
        $pageTitle = 'Manage Orders';

        // Load view
        include VIEWS_PATH . 'admin/layouts/header.php';
        include VIEWS_PATH . 'admin/orders/index.php';
        include VIEWS_PATH . 'admin/layouts/footer.php';
    }


    public function orderDetail($id)
    {
        // Check if admin is logged in
        $this->checkAdminAuth();

        // Get order details
        require_once 'models/Order.php';
        $orderModel = new Order();
        $order = $orderModel->getOrderById($id);

        if (!$order) {
            header('Location: ' . SITE_URL . 'admin/orders');
            exit;
        }

        // Get order items
        $orderItems = $orderModel->getOrderItems($id);

        // Page title
        $pageTitle = 'Order #' . $id;

        // Load view
        include VIEWS_PATH . 'admin/layouts/header.php';
        include VIEWS_PATH . 'admin/orders/detail.php';
        include VIEWS_PATH . 'admin/layouts/footer.php';
    }

    public function updateOrderStatus()
    {
        // Check if admin is logged in
        $this->checkAdminAuth();

        // Check for AJAX request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
            $id = (int)$_POST['id'];
            $status = $_POST['status'];

            require_once 'models/Order.php';
            $orderModel = new Order();

            // Update order status
            $result = $orderModel->updateStatus($id, $status);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
            }
            exit;
        }

        // Redirect to orders page
        header('Location: ' . SITE_URL . 'admin/orders');
        exit;
    }

    public function getOnlineUsers()
    {
        // Check if admin is logged in via AJAX
        if (!isset($_SESSION['admin_id'])) {
            echo json_encode(['error' => 'Authentication required']);
            exit;
        }

        // Get database connection
        $database = new Database();
        $db = $database->getConnection();

        // Get recent users with online status
        $result = $db->query("SELECT id, username, full_name, avatar, last_login, 
                            (CASE WHEN last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE) THEN 1 ELSE 0 END) as is_online 
                            FROM users 
                            ORDER BY last_activity DESC, created_at DESC 
                            LIMIT 10");

        $users = [];
        if ($result && $result->num_rows > 0) {
            while ($user = $result->fetch_assoc()) {
                // Format the data for JSON response
                $avatarUrl = !empty($user['avatar']) ?
                    SITE_URL . str_replace('\\', '/', $user['avatar']) :
                    SITE_URL . 'public/images/avatars/default.png';

                $users[] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'full_name' => $user['full_name'],
                    'avatar' => $avatarUrl,
                    'is_online' => (bool)$user['is_online']
                ];
            }
        }

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($users);
        exit;
    }

    public function locations()
    {
        // Check if admin is logged in
        $this->checkAdminAuth();

        $error = '';
        $success = '';

        // Create instance of StoreLocation model
        require_once 'models/StoreLocation.php';
        $locationModel = new StoreLocation();

        // Handle form submission for creating/updating locations
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if we're updating or adding
            if (isset($_POST['location_id']) && !empty($_POST['location_id'])) {
                // Updating existing location
                $locationId = (int)$_POST['location_id'];

                if ($locationModel->updateLocation($locationId, $_POST)) {
                    $success = 'Store location updated successfully.';
                } else {
                    $error = 'Failed to update store location.';
                }
            } else {
                // Adding new location
                if ($locationModel->createLocation($_POST)) {
                    $success = 'Store location added successfully.';
                } else {
                    $error = 'Failed to add store location.';
                }
            }
        }

        // Handle location deletion
        if (isset($_GET['delete']) && !empty($_GET['delete'])) {
            $locationId = (int)$_GET['delete'];

            if ($locationModel->deleteLocation($locationId)) {
                $success = 'Store location deleted successfully.';
            } else {
                $error = 'Failed to delete store location.';
            }
        }

        // Get all store locations
        $storeLocations = $locationModel->getActiveLocations();

        // Get location to edit if specified
        $locationToEdit = null;
        if (isset($_GET['edit']) && !empty($_GET['edit'])) {
            $locationId = (int)$_GET['edit'];
            $locationToEdit = $locationModel->getLocationById($locationId);
        }

        $pageTitle = 'Manage Store Locations';

        // Load view
        include VIEWS_PATH . 'admin/layouts/header.php';
        include VIEWS_PATH . 'admin/locations.php';
        include VIEWS_PATH . 'admin/layouts/footer.php';
    }
}
