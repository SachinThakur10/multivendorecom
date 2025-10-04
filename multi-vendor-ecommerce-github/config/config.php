<?php
// Configuration file for Multi-Vendor E-commerce Platform

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'multivendor_ecommerce');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Configuration
define('SITE_NAME', 'StyleHub - Multi-Vendor Fashion Store');
define('SITE_URL', 'http://localhost/multi-vendor-ecommerce');
define('ADMIN_EMAIL', 'admin@stylehub.com');

// File Upload Configuration
define('UPLOAD_PATH', 'assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Pagination
define('PRODUCTS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 10);

// Commission Settings
define('DEFAULT_COMMISSION_RATE', 10); // 10% commission

// Payment Gateway Configuration (Add your keys here)
define('PAYPAL_CLIENT_ID', 'your_paypal_client_id');
define('PAYPAL_CLIENT_SECRET', 'your_paypal_client_secret');
define('RAZORPAY_KEY_ID', 'your_razorpay_key_id');
define('RAZORPAY_KEY_SECRET', 'your_razorpay_key_secret');

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_email@gmail.com');
define('SMTP_PASSWORD', 'your_app_password');

// Security
define('ENCRYPTION_KEY', 'your_32_character_encryption_key_here');

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Kolkata');
?>
