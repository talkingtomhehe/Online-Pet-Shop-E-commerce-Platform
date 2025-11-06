<?php
// filepath: c:\xampp\htdocs\chabongshop\controllers\ProductController.php
// Products controller

require_once 'models/Product.php';
require_once 'models/Category.php';

class ProductController {
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }
    
    public function index() {
        global $action, $id;
        // Get sorting parameters
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
        $order = isset($_GET['order']) ? $_GET['order'] : 'desc';
        
        // Validate sort field
        $allowedSortFields = ['id', 'name', 'price'];
        if (!in_array($sort, $allowedSortFields)) {
            $sort = 'id';
        }
        
        // Validate sort order
        $allowedOrders = ['asc', 'desc'];
        if (!in_array($order, $allowedOrders)) {
            $order = 'desc';
        }
        
        // Pagination
        $limit = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        
        // Get products
        $products = $this->productModel->getAllProducts($sort, $order, $limit, $offset);
        $totalProducts = $this->productModel->countProducts();
        $totalPages = ceil($totalProducts / $limit);
        
        // Get all categories for the navbar and sidebar
        $categories = $this->categoryModel->getAllCategories();
        
        // Page title
        $pageTitle = 'All Products';
        
        // Load view
        include VIEWS_PATH . 'layouts/header.php';
        include VIEWS_PATH . 'products/index.php';
        include VIEWS_PATH . 'layouts/footer.php';
    }
    
    public function detail($id) {
        global $action;
        // Get product details
        $product = $this->productModel->getProductById($id);
        
        if ($product) {
            // Get related products
            $relatedProducts = $this->productModel->getRelatedProducts($product['category_id'], $product['id']);
            
            // Get all categories for the navbar
            $categories = $this->categoryModel->getAllCategories();
            
            // Page title
            $pageTitle = $product['name'];
            
            // Load view
            include VIEWS_PATH . 'layouts/header.php';
            include VIEWS_PATH . 'products/detail.php';
            include VIEWS_PATH . 'layouts/footer.php';
        } else {
            $this->showError404();
        }
    }
    
    public function category($categoryName) {
        global $action, $id;
        
        $categoryName = urldecode($categoryName);
    
        // Get category info
        $category = $this->categoryModel->getCategoryByName($categoryName);
        
        if ($category) {
            // Get sorting parameters
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
            $order = isset($_GET['order']) ? $_GET['order'] : 'desc';
            
            // Validate sort field
            $allowedSortFields = ['id', 'name', 'price'];
            if (!in_array($sort, $allowedSortFields)) {
                $sort = 'id';
            }
            
            // Validate sort order
            $allowedOrders = ['asc', 'desc'];
            if (!in_array($order, $allowedOrders)) {
                $order = 'desc';
            }
            
            // Pagination
            $limit = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;
            
            // CHANGE THESE TWO LINES:
            // Instead of using category name, use category ID
            $products = $this->productModel->getProductsByCategory($category['id'], $sort, $order, $limit, $offset);
            $totalProducts = $this->productModel->countProductsByCategory($category['id']);
            $totalPages = ceil($totalProducts / $limit);
            
            // Get all categories for the navbar and sidebar
            $categories = $this->categoryModel->getAllCategories();
            
            // Make sure these variables are available to the view
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            
            // Page title
            $pageTitle = $category['name'];
            
            // Load view
            include VIEWS_PATH . 'layouts/header.php';
            include VIEWS_PATH . 'products/index.php';
            include VIEWS_PATH . 'layouts/footer.php';
        } else {
            $this->showError404();
        }
    }
    
    public function search() {
        global $action, $id;
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        
        if (!empty($search)) {
            // Get sorting parameters
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
            $order = isset($_GET['order']) ? $_GET['order'] : 'desc';
            
            // Validate sort field
            $allowedSortFields = ['id', 'name', 'price'];
            if (!in_array($sort, $allowedSortFields)) {
                $sort = 'id';
            }
            
            // Validate sort order
            $allowedOrders = ['asc', 'desc'];
            if (!in_array($order, $allowedOrders)) {
                $order = 'desc';
            }
            
            // Pagination
            $limit = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;
            
            // Get products
            $products = $this->productModel->searchProducts($search, $sort, $order, $limit, $offset);
            $totalProducts = $this->productModel->countProducts(null, $search);
            $totalPages = ceil($totalProducts / $limit);
            
            // Get all categories for the navbar and sidebar
            $categories = $this->categoryModel->getAllCategories();
    
            $controller = 'products';
            
            // Page title
            $pageTitle = 'Search: ' . htmlspecialchars($search);
            
            // Load view - use the dedicated search view instead of the index view
            include VIEWS_PATH . 'layouts/header.php';
            include VIEWS_PATH . 'products/search.php';
            include VIEWS_PATH . 'layouts/footer.php';
        } else {
            // Redirect to products page if search is empty
            header('Location: ' . SITE_URL . 'products');
            exit;
        }
    }
    
    private function showError404() {
        http_response_code(404);
        include VIEWS_PATH . 'layouts/header.php';
        include VIEWS_PATH . 'errors/404.php';
        include VIEWS_PATH . 'layouts/footer.php';
    }

    public function ajaxSearch() {
        // Get search query
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        $allResults = isset($_GET['all']) && $_GET['all'] === 'true';
        
        // Perform search
        $results = [];
        $totalCount = 0;
        if (!empty($query)) {
            // Get total count first (without limits)
            $allSearchResults = $this->productModel->searchProducts($query);
            $totalCount = $allSearchResults->num_rows;
            
            $maxResults = $allResults ? null : 5;
            $searchResults = $this->productModel->searchProducts($query, 'name', 'asc', $maxResults);
            
            if ($searchResults && $searchResults->num_rows > 0) {
                while ($product = $searchResults->fetch_assoc()) {
                    $image = $product['image_url'];
                    
                    // Add to results array
                    $results[] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'image' => $image,
                        'category_name' => isset($product['category_name']) ? $product['category_name'] : null
                    ];
                }
            }
        }
        
        // Return JSON response with total count
        header('Content-Type: application/json');
        echo json_encode([
            'results' => $results,
            'totalCount' => $totalCount
        ]);
        exit;
    }
}