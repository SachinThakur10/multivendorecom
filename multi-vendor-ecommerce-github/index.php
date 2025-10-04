<?php
// Multi-Vendor E-commerce Platform
// Entry point for the application

// Start session
session_start();

// Include configuration and autoloader
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Simple routing
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Route to appropriate controller
switch ($page) {
    case 'home':
        require_once 'controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
    
    case 'products':
        require_once 'controllers/ProductController.php';
        $controller = new ProductController();
        if ($action === 'details') {
            $controller->details($_GET['id'] ?? 0);
        } else {
            $controller->index();
        }
        break;
    
    case 'cart':
        require_once 'controllers/CartController.php';
        $controller = new CartController();
        $controller->$action();
        break;
    
    case 'auth':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->$action();
        break;
    
    case 'vendor':
        require_once 'controllers/VendorController.php';
        $controller = new VendorController();
        $controller->$action();
        break;
    
    case 'admin':
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->$action();
        break;
    
    default:
        require_once 'controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
}
?>
