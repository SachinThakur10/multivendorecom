<?php
// Production Configuration for Multi-Vendor E-commerce Platform
// Copy this to config.php and update with your hosting details

// Database Configuration - UPDATE THESE WITH YOUR HOSTING DETAILS
define('DB_HOST', 'sql200.epizy.com'); // Your hosting DB server
define('DB_NAME', 'epiz_xxxxx_ecommerce'); // Your database name
define('DB_USER', 'epiz_xxxxx'); // Your database username  
define('DB_PASS', 'your_database_password'); // Your database password

// Site Configuration - UPDATE WITH YOUR DOMAIN
define('SITE_URL', 'https://yourstore.epizy.com'); // Your website URL
define('SITE_NAME', 'Your Store Name'); // Your store name
define('ADMIN_EMAIL', 'admin@yourstore.com'); // Admin email

// Security Settings
define('SECRET_KEY', 'your-secret-key-here-change-this'); // Change this!
define('DEBUG_MODE', false); // Set to false for production
define('ERROR_REPORTING', false); // Disable error reporting in production

// File Upload Settings
define('UPLOAD_PATH', 'assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination
define('PRODUCTS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 10);
define('USERS_PER_PAGE', 15);

// E-commerce Settings
define('DEFAULT_CURRENCY', 'INR');
define('CURRENCY_SYMBOL', '₹');
define('TAX_RATE', 18); // GST percentage
define('SHIPPING_CHARGE', 50);
define('FREE_SHIPPING_THRESHOLD', 500);
define('DEFAULT_COMMISSION_RATE', 10); // 10% commission

// Payment Gateway Settings - ADD YOUR KEYS
// Razorpay
define('RAZORPAY_KEY_ID', 'rzp_test_your_key_here'); // Your Razorpay Key ID
define('RAZORPAY_KEY_SECRET', 'your_razorpay_secret_here'); // Your Razorpay Secret

// PayPal
define('PAYPAL_CLIENT_ID', 'your_paypal_client_id_here'); // Your PayPal Client ID
define('PAYPAL_CLIENT_SECRET', 'your_paypal_secret_here'); // Your PayPal Secret
define('PAYPAL_MODE', 'sandbox'); // 'sandbox' for testing, 'live' for production

// Email Configuration - UPDATE WITH YOUR EMAIL SETTINGS
define('SMTP_HOST', 'smtp.gmail.com'); // Your SMTP host
define('SMTP_PORT', 587); // SMTP port
define('SMTP_USERNAME', 'your-email@gmail.com'); // Your email
define('SMTP_PASSWORD', 'your-app-password'); // Your email app password
define('SMTP_ENCRYPTION', 'tls'); // TLS or SSL
define('FROM_EMAIL', 'noreply@yourstore.com'); // From email address
define('FROM_NAME', 'Your Store Name'); // From name

// Session Settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('REMEMBER_ME_LIFETIME', 30 * 24 * 3600); // 30 days

// Cache Settings
define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600); // 1 hour

// Image Settings
define('PRODUCT_IMAGE_WIDTH', 800);
define('PRODUCT_IMAGE_HEIGHT', 600);
define('THUMBNAIL_WIDTH', 300);
define('THUMBNAIL_HEIGHT', 225);

// Social Media Links (Optional)
define('FACEBOOK_URL', 'https://facebook.com/yourstore');
define('TWITTER_URL', 'https://twitter.com/yourstore');
define('INSTAGRAM_URL', 'https://instagram.com/yourstore');

// Google Analytics (Optional)
define('GOOGLE_ANALYTICS_ID', 'UA-XXXXXXXX-X'); // Your GA tracking ID

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Error Handling for Production
if (!DEBUG_MODE) {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', 'error.log');
}

// Additional Security Headers
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
?>
