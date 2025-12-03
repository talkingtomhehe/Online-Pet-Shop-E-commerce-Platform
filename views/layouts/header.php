<?php
// filepath: c:\xampp\htdocs\petshop\views\layouts\header.php
// Common header for all pages
require_once __DIR__ . '/../../includes/SessionManager.php';

// --- ADD THIS LINE HERE ---
// Ensure the Notification model is loaded on EVERY page (Products, Cart, etc.)
require_once __DIR__ . '/../../models/Notification.php'; 

$unreadCount = isset($unreadCount) ? intval($unreadCount) : 0;
if (!isset($notifications) || !is_array($notifications)) {
    $notifications = [];
    $notifModel = null;

    // if (class_exists('Notification')) {
    //     try {
    //         // Try no-arg constructor first
    //         $notifModel = new Notification();
    //     } catch (\ArgumentCountError $e) {
    //         // If constructor requires a DB connection, attempt to provide one
    //         try {
    //             if (class_exists('Database')) {
    //                 $db = new Database();
    //                 $conn = $db->getConnection(); // can be mysqli or PDO
    //                 if ($conn instanceof PDO) {
    //                     $notifModel = new Notification($conn);
    //                 } elseif ($conn instanceof mysqli) {
    //                     // create a PDO fallback using DB_* constants
    //                     if (defined('DB_HOST') && defined('DB_USER') && defined('DB_PASS') && defined('DB_NAME')) {
    //                         $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    //                         $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    //                         $notifModel = new Notification($pdo);
    //                     }
    //                 }
    //             }
    //         } catch (\Throwable $e) {
    //             $notifModel = null;
    //         }
    //     } catch (\Throwable $e) {
    //         $notifModel = null;
    //     }
    //}

    if (is_object($notifModel)) {
        try {
            $userId = null;
            if (method_exists('SessionManager', 'getUserId')) {
                $userId = SessionManager::getUserId();
            }

            if (!empty($userId) && method_exists($notifModel, 'getByUser')) {
                if (method_exists($notifModel, 'getUnreadCount')) {
                    $unreadCount = (int)$notifModel->getUnreadCount($userId);
                } else {
                    $unreadCount = 0;
                }
                $notifications = $notifModel->getByUser($userId, 10);
            // } elseif (method_exists($notifModel, 'getLatest')) {
            //     // Show latest global notifications when not logged in
            //     $notifications = $notifModel->getLatest(10);
            //     $unreadCount = 0;
            }
        } catch (\Throwable $e) {
            // ignore and keep defaults
        }
    }

    // Ensure $notifications is an array
    if (!is_array($notifications)) {
        $notifications = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>public/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const SITE_URL = '<?php echo SITE_URL; ?>';
    </script>
    <script src="<?php echo SITE_URL; ?>public/js/search.js"></script>
    <script src="<?php echo SITE_URL; ?>public/js/login.js"></script>
    <script src="<?php echo SITE_URL; ?>public/js/register.js"></script>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>public/css/alert.css">
    <script src="<?php echo SITE_URL; ?>public/js/alert.js"></script>
</head>
<body>
    <header>
        <!-- Utility bar (account, login, etc) -->
        <div class="utility-bar">
            <div class="container-fluid d-flex justify-content-end">
                <?php if(SessionManager::isUserLoggedIn()): ?>
                    <div class="dropdown">
                        <?php 
                        // Get user avatar
                        require_once __DIR__ . '/../../models/User.php';
                        $db = new Database();
                        $userAvatarModel = new User($db->getConnection());
                        $avatarPath = $userAvatarModel->getUserAvatar($_SESSION['user_id']);

                        // Check if it's an external URL or local path
                        if (strpos($avatarPath, 'http') === 0) {
                            $avatarUrl = $avatarPath; // External URL, use as-is
                        } else {
                            $avatarUrl = SITE_URL . str_replace('\\', '/', $avatarPath); // Local path
                        }
                        ?>
                        <button class="utility-btn dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo $avatarUrl; ?>" alt="Profile" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>user/profile">
                                <i class="bi bi-person me-2"></i> My Profile
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>user/orders">
                                <i class="bi bi-box-seam me-2"></i> My Orders
                            </a></li>
                            <li><a class="dropdown-item" href="index.php?page=user-appointments">
                                <i class="bi bi-calendar-check me-2"></i> My Appointments
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>user/logout">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>user/login" class="utility-btn me-2">Sign In</a>
                    <a href="<?php echo SITE_URL; ?>user/signup" class="utility-btn">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Main navigation -->
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container-fluid">
                <!-- Logo -->
                <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                    <img src="<?php echo SITE_URL; ?>public/images/logo.png" alt="<?php echo SITE_NAME; ?> Logo">
                </a>
                
                <!-- Mobile cart button - visible on small screens only -->
                <?php if(SessionManager::isUserLoggedIn()): ?>
                <div class="mobile-cart d-lg-none d-flex align-items-center"> <!-- added d-flex align-items-center -->
                    <a class="nav-link position-relative" href="<?php echo SITE_URL; ?>cart">
                        <i class="bi bi-cart3 fs-5"></i>
                        <?php 
                        require_once 'models/Cart.php';
                        $cartModel = new Cart();
                        $cartCount = $cartModel->countItems($_SESSION['user_id']);
                        if ($cartCount > 0) {
                            echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">' . $cartCount . '</span>';
                        }
                        ?>
                    </a>

                    <!-- Notification (mobile) -->
                    <a class="nav-link position-relative ms-2" href="#" id="notification-toggle-mobile">
                        <i class="bi bi-bell fs-5"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?php echo $unreadCount; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <?php endif; ?>
                
                <!-- Mobile search form - for very small screens -->
                <form class="mobile-search d-lg-none" role="search" action="<?php echo SITE_URL; ?>products/search" method="get">
                    <div class="input-group">
                        <input class="form-control" type="search" name="search" placeholder="Search" aria-label="Search" value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Toggle button for mobile menu -->
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Offcanvas menu for mobile -->
                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title brand-text" id="offcanvasNavbarLabel"><?php echo SITE_NAME; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <!-- Navigation links -->
                        <ul class="navbar-nav justify-content-center flex-grow-1 align-items-center nav-underline">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $controller === 'home' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>">Home</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle <?php echo $controller === 'products' ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Products
                                </a>
                                <ul class="dropdown-menu">
                                    <?php if (isset($categories) && $categories->num_rows > 0): ?>
                                        <?php while($category = $categories->fetch_assoc()): ?>
                                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>products/category/<?php echo urlencode($category['name']); ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
                                        <?php endwhile; ?>
                                        <li><hr class="dropdown-divider"></li>
                                    <?php endif; ?>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>products">All Products</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($pageTitle == 'Book a Spa Appointment' || $controller == 'booking') ? 'active' : ''; ?>" href="index.php?page=booking">
                                    Spa Booking
                                    <!--<i class="bi bi-scissors me-1"></i> Spa Booking-->
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($pageTitle == 'Contact Us') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>contact">
                                    Contact Us
                                </a>
                            </li>
                        </ul>
                        
                        <!-- Mobile-only search form inside offcanvas -->
                        <form class="d-flex d-lg-none mt-3 mb-3" role="search" action="<?php echo SITE_URL; ?>products/search" method="get">
                            <div class="input-group">
                                <input class="form-control" type="search" name="search" placeholder="Search" aria-label="Search" value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Desktop cart button - hidden on small screens -->
                <?php if(SessionManager::isUserLoggedIn()): ?>
                <div class="nav-item d-none d-lg-flex d-flex align-items-center position-relative"> 
                    
                    <a class="nav-link" href="<?php echo SITE_URL; ?>cart">
                        <i class="bi bi-cart3 fs-5"></i>
                        <?php 
                        if (!isset($cartModel)) {
                            require_once 'models/Cart.php';
                            $cartModel = new Cart();
                        }
                        $cartCount = $cartModel->countItems($_SESSION['user_id']);
                        if ($cartCount > 0) {
                            echo '<span class="badge rounded-pill bg-primary">' . $cartCount . '</span>';
                        }
                        ?>
                    </a>

                    <a class="nav-link" href="#" id="notification-toggle">
                        <i class="bi bi-bell fs-5"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span id="notification-badge" class="badge rounded-pill bg-danger"><?php echo $unreadCount; ?></span>
                        <?php endif; ?>
                    </a>

                    <div id="notification-dropdown" class="notification-dropdown">
                        <div class="notification-header">
                            <span>Notifications</span>
                            <button id="mark-all-read" class="link-btn">Mark all read</button>
                        </div>
                        <div class="notification-content">
                            <?php if (empty($notifications)): ?>
                                <div class="notification-empty">No new notifications</div>
                            <?php else: ?>
                                <?php foreach($notifications as $notif): ?>
                                    <a href="<?php echo isset($notif['link']) ? $notif['link'] : '#'; ?>" class="notification-item" data-id="<?php echo $notif['id']; ?>">
                                        <?php echo htmlspecialchars($notif['message']); ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
                <?php endif; ?>
                
                <!-- Desktop search form -->
                <form id="live-search" class="d-none d-lg-flex position-relative" role="search" action="<?php echo SITE_URL; ?>products/search" method="get">
                    <input id="search-input" class="form-control search-input" type="search" name="search" placeholder="Search" aria-label="Search" value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
                    <button type="submit" class="btn" style="position: absolute; right: 0; border: none; top: 0; height: 100%; background: none;">
                        <i class="bi bi-search search-icon"></i>
                    </button>
                    <div id="search-hints" class="d-none"></div>
                </form>

                <!-- Mobile search form -->
                <form id="mobile-live-search" class="d-flex d-lg-none mt-3 mb-3 position-relative" role="search" action="<?php echo SITE_URL; ?>products/search" method="get">
                    <div class="input-group">
                        <input id="mobile-search-input" class="form-control" type="search" name="search" placeholder="Search products..." aria-label="Search" value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <div id="mobile-search-hints" class="d-none"></div>
                </form>
            </div>
        </nav>
    </header>

    <main>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const markReadBtn = document.getElementById('mark-all-read');
    
    if (markReadBtn) {
        markReadBtn.addEventListener('click', function(e) {
            e.preventDefault(); 

            const url = SITE_URL + 'index.php?page=home&action=mark_read';

            fetch(url, {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log("✅ Notifications marked as read.");
                    
                    // 1. Hide the Badge
                    const badge = document.getElementById('notification-badge');
                    if (badge) badge.style.display = 'none';

                    // 2. Hide Mobile Badge
                    const mobileBadge = document.querySelector('.mobile-cart .badge.bg-danger');
                    if (mobileBadge) mobileBadge.style.display = 'none';
                    
                    // 3. CLEAR THE LIST (Make messages disappear)
                    const contentDiv = document.querySelector('.notification-content');
                    if (contentDiv) {
                        contentDiv.innerHTML = '<div class="notification-empty">No new notifications</div>';
                    }

                } else {
                    console.error("❌ Server Error:", data.message);
                }
            })
            .catch(error => console.error('❌ Fetch Error:', error));
        });
    }
});
</script>