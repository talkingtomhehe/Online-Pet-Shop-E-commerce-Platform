<?php
// filepath: c:\xampp\htdocs\chabongshop\config\config.php
// General configuration settings

// Site settings
define('SITE_NAME', 'ChaBong Shop');
define('SITE_URL', 'http://localhost/petshop/');

// Path settings
define('BASE_PATH', dirname(__DIR__) . '/');
define('CONTROLLERS_PATH', BASE_PATH . 'controllers/');
define('MODELS_PATH', BASE_PATH . 'models/');
define('VIEWS_PATH', BASE_PATH . 'views/');
define('INCLUDES_PATH', BASE_PATH . 'includes/');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);