<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Woof-woof Admin' : 'Woof-woof Admin Panel'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Admin CSS -->
    <style>
        :root {
            --primary-color: #b77c52;
            --secondary-color: #6d5741;
            --light-color: #f5f0e9;
            --border-color: #e9e2d9;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        
        .admin-sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: #fff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .admin-content {
            padding: 20px;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.5rem 1rem;
            border-radius: 4px;
            margin-bottom: 5px;
            transition: all 0.2s;
        }
        
        .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.1);
        }
        
        .nav-link.active {
            color: #fff;
            background-color: var(--primary-color);
        }
        
        .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 8px;
        }
        
        .admin-header {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            padding: 0.75rem 1.5rem;
        }
        
        .brand-logo {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .user-dropdown img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }
        
        .dropdown-item:active {
            background-color: var(--primary-color);
        }
        
        .card {
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: #9a683f;
            border-color: #9a683f;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .rounded-circle.bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .rounded-circle.bg-success {
            background-color: #5d9c59 !important;
        }
        
        .rounded-circle.bg-info {
            background-color: #5b8fb9 !important;
        }
        
        @media (max-width: 992px) {
            .admin-sidebar {
                position: fixed;
                top: 0;
                left: -260px;
                width: 260px;
                height: 100%;
                z-index: 1050;
                transition: left 0.3s;
            }
            
            .admin-sidebar.show {
                left: 0;
            }
            
            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1040;
                display: none;
            }
            
            .sidebar-backdrop.show {
                display: block;
            }
        }
    </style>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>public/css/alert.css">
    <script src="<?php echo SITE_URL; ?>public/js/alert.js"></script>
</head>
<body>
<?php if (isset($_SESSION['admin_id'])): ?>
    <!-- Mobile Sidebar Toggle -->
    <div class="d-lg-none">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <button class="navbar-toggler border-0" type="button" id="sidebarToggle">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand" href="<?php echo SITE_URL; ?>admin/dashboard">
                    Woof-woof Admin
                </a>
                <div class="dropdown">
                    <a href="#" class="text-white dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>">View Site</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>admin/logout">Sign out</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    
    <!-- Sidebar Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block admin-sidebar collapse" id="sidebarMenu">
                <div class="d-flex flex-column p-3 h-100">
                    <a href="<?php echo SITE_URL; ?>admin/dashboard" class="d-none d-lg-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <i class="bi bi-shop me-2"></i>
                        <span class="fs-4">Woof-woof Admin</span>
                    </a>
                    <hr>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="<?php echo SITE_URL; ?>admin/dashboard" class="nav-link <?php echo ($pageTitle == 'Admin Dashboard') ? 'active' : ''; ?>">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo SITE_URL; ?>admin/products" class="nav-link <?php echo ($pageTitle == 'Manage Products') ? 'active' : ''; ?>">
                                <i class="bi bi-box"></i> Products
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo SITE_URL; ?>admin/categories" class="nav-link <?php echo ($pageTitle == 'Manage Categories') ? 'active' : ''; ?>">
                                <i class="bi bi-tags"></i> Categories
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo SITE_URL; ?>admin/users" class="nav-link <?php echo ($pageTitle == 'Manage Users') ? 'active' : ''; ?>">
                                <i class="bi bi-people"></i> Users
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo SITE_URL; ?>admin/orders" class="nav-link <?php echo ($pageTitle == 'Orders') ? 'active' : ''; ?>">
                                <i class="bi bi-bag"></i> Orders
                                <?php if (isset($pendingOrders) && $pendingOrders > 0): ?>
                                    <span class="badge bg-danger"><?php echo $pendingOrders; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo SITE_URL; ?>admin/locations" class="nav-link <?php echo ($pageTitle == 'Store Locations') ? 'active' : ''; ?>">
                                <i class="bi bi-geo-alt me-2"></i> Store Locations
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo SITE_URL; ?>admin/settings" class="nav-link <?php echo ($pageTitle == 'Settings') ? 'active' : ''; ?>">
                                <i class="bi bi-gear"></i> Settings
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i>
                            <strong><?php echo isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'Admin'; ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>admin/profile">Profile</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>">View Site</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>admin/logout">Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4 mb-3 border-bottom">
                    <h1 class="h2"><?php echo $pageTitle; ?></h1>
                </div>
<?php else: ?>
    <!-- Simple header for login page -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center mb-5">
                <h1>Woof-woof Admin Panel</h1>
            </div>
        </div>
<?php endif; ?>