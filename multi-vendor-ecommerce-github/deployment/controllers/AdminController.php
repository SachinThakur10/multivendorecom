<?php
require_once 'models/Product.php';
require_once 'models/User.php';

class AdminController {
    private $productModel;
    private $userModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->userModel = new User();
        
        // Check if user is logged in and is an admin
        if (!isLoggedIn() || !hasRole('admin')) {
            redirect('?page=auth&action=login');
        }
    }
    
    public function index() {
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get recent activities
        $recentOrders = $this->getRecentOrders(5);
        $recentVendors = $this->getRecentVendors(5);
        $recentProducts = $this->getRecentProducts(5);
        
        $data = [
            'title' => 'Admin Dashboard - ' . SITE_NAME,
            'stats' => $stats,
            'recent_orders' => $recentOrders,
            'recent_vendors' => $recentVendors,
            'recent_products' => $recentProducts
        ];
        
        $this->render('admin/dashboard', $data);
    }
    
    public function vendors() {
        $page = $_GET['page_num'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $status = $_GET['status'] ?? 'all';
        
        // Get vendors based on status filter
        $vendors = $this->getVendors($status, $limit, $offset);
        
        // Get total count for pagination
        $totalVendors = $this->getVendorsCount($status);
        $pagination = paginate($totalVendors, $limit, $page);
        
        $data = [
            'title' => 'Manage Vendors - ' . SITE_NAME,
            'vendors' => $vendors,
            'pagination' => $pagination,
            'current_status' => $status
        ];
        
        $this->render('admin/vendors', $data);
    }
    
    public function approveVendor() {
        $vendorId = $_GET['id'] ?? 0;
        
        if ($this->updateVendorStatus($vendorId, 'approved')) {
            setFlashMessage('success', 'Vendor approved successfully');
            
            // Send approval email to vendor
            $this->sendVendorApprovalEmail($vendorId, 'approved');
        } else {
            setFlashMessage('error', 'Failed to approve vendor');
        }
        
        redirect('?page=admin&action=vendors');
    }
    
    public function rejectVendor() {
        $vendorId = $_GET['id'] ?? 0;
        
        if ($this->updateVendorStatus($vendorId, 'rejected')) {
            setFlashMessage('success', 'Vendor rejected');
            
            // Send rejection email to vendor
            $this->sendVendorApprovalEmail($vendorId, 'rejected');
        } else {
            setFlashMessage('error', 'Failed to reject vendor');
        }
        
        redirect('?page=admin&action=vendors');
    }
    
    public function products() {
        $page = $_GET['page_num'] ?? 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;
        $status = $_GET['status'] ?? 'all';
        
        // Get products based on status filter
        $products = $this->getProducts($status, $limit, $offset);
        
        // Get total count for pagination
        $totalProducts = $this->getProductsCount($status);
        $pagination = paginate($totalProducts, $limit, $page);
        
        $data = [
            'title' => 'Manage Products - ' . SITE_NAME,
            'products' => $products,
            'pagination' => $pagination,
            'current_status' => $status
        ];
        
        $this->render('admin/products', $data);
    }
    
    public function orders() {
        $page = $_GET['page_num'] ?? 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;
        $status = $_GET['status'] ?? 'all';
        
        // Get orders based on status filter
        $orders = $this->getOrders($status, $limit, $offset);
        
        // Get total count for pagination
        $totalOrders = $this->getOrdersCount($status);
        $pagination = paginate($totalOrders, $limit, $page);
        
        $data = [
            'title' => 'Manage Orders - ' . SITE_NAME,
            'orders' => $orders,
            'pagination' => $pagination,
            'current_status' => $status
        ];
        
        $this->render('admin/orders', $data);
    }
    
    public function customers() {
        $page = $_GET['page_num'] ?? 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;
        
        // Get customers
        $customers = $this->getCustomers($limit, $offset);
        
        // Get total count for pagination
        $totalCustomers = $this->getCustomersCount();
        $pagination = paginate($totalCustomers, $limit, $page);
        
        $data = [
            'title' => 'Manage Customers - ' . SITE_NAME,
            'customers' => $customers,
            'pagination' => $pagination
        ];
        
        $this->render('admin/customers', $data);
    }
    
    public function categories() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCategoryAction();
        }
        
        $categories = $this->getCategories();
        
        $data = [
            'title' => 'Manage Categories - ' . SITE_NAME,
            'categories' => $categories
        ];
        
        $this->render('admin/categories', $data);
    }
    
    public function settings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleSettingsUpdate();
        }
        
        $settings = $this->getSettings();
        
        $data = [
            'title' => 'Site Settings - ' . SITE_NAME,
            'settings' => $settings
        ];
        
        $this->render('admin/settings', $data);
    }
    
    public function reports() {
        $reportType = $_GET['type'] ?? 'sales';
        
        switch ($reportType) {
            case 'sales':
                $reportData = $this->getSalesReport();
                break;
            case 'vendors':
                $reportData = $this->getVendorsReport();
                break;
            case 'products':
                $reportData = $this->getProductsReport();
                break;
            default:
                $reportData = $this->getSalesReport();
                break;
        }
        
        $data = [
            'title' => 'Reports - ' . SITE_NAME,
            'report_type' => $reportType,
            'report_data' => $reportData
        ];
        
        $this->render('admin/reports', $data);
    }
    
    private function getDashboardStats() {
        global $pdo;
        
        $stats = [];
        
        // Total users
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users");
        $stmt->execute();
        $stats['total_users'] = $stmt->fetch()['count'];
        
        // Total vendors
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM vendors WHERE approval_status = 'approved'");
        $stmt->execute();
        $stats['total_vendors'] = $stmt->fetch()['count'];
        
        // Total products
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE status = 'active'");
        $stmt->execute();
        $stats['total_products'] = $stmt->fetch()['count'];
        
        // Total orders
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders");
        $stmt->execute();
        $stats['total_orders'] = $stmt->fetch()['count'];
        
        // Total sales
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE payment_status = 'paid'");
        $stmt->execute();
        $stats['total_sales'] = $stmt->fetch()['total'];
        
        // Pending vendor approvals
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM vendors WHERE approval_status = 'pending'");
        $stmt->execute();
        $stats['pending_vendors'] = $stmt->fetch()['count'];
        
        // This month sales
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders 
                              WHERE payment_status = 'paid' 
                              AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
                              AND YEAR(created_at) = YEAR(CURRENT_DATE())");
        $stmt->execute();
        $stats['monthly_sales'] = $stmt->fetch()['total'];
        
        // Pending orders
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
        $stmt->execute();
        $stats['pending_orders'] = $stmt->fetch()['count'];
        
        return $stats;
    }
    
    private function getRecentOrders($limit) {
        global $pdo;
        
        $sql = "SELECT o.*, u.name as customer_name 
                FROM orders o 
                INNER JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC 
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    private function getRecentVendors($limit) {
        global $pdo;
        
        $sql = "SELECT v.*, u.name, u.email 
                FROM vendors v 
                INNER JOIN users u ON v.user_id = u.id 
                ORDER BY v.created_at DESC 
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    private function getRecentProducts($limit) {
        global $pdo;
        
        $sql = "SELECT p.*, v.shop_name,
                       (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p 
                INNER JOIN vendors v ON p.vendor_id = v.id 
                ORDER BY p.created_at DESC 
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    private function getVendors($status, $limit, $offset) {
        global $pdo;
        
        $sql = "SELECT v.*, u.name, u.email, u.status as user_status 
                FROM vendors v 
                INNER JOIN users u ON v.user_id = u.id";
        
        $params = [];
        
        if ($status !== 'all') {
            $sql .= " WHERE v.approval_status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY v.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    private function getVendorsCount($status) {
        global $pdo;
        
        $sql = "SELECT COUNT(*) as count FROM vendors v";
        $params = [];
        
        if ($status !== 'all') {
            $sql .= " WHERE v.approval_status = ?";
            $params[] = $status;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }
    
    private function updateVendorStatus($vendorId, $status) {
        global $pdo;
        
        $stmt = $pdo->prepare("UPDATE vendors SET approval_status = ? WHERE id = ?");
        return $stmt->execute([$status, $vendorId]);
    }
    
    private function sendVendorApprovalEmail($vendorId, $status) {
        global $pdo;
        
        // Get vendor details
        $stmt = $pdo->prepare("SELECT v.*, u.name, u.email 
                              FROM vendors v 
                              INNER JOIN users u ON v.user_id = u.id 
                              WHERE v.id = ?");
        $stmt->execute([$vendorId]);
        $vendor = $stmt->fetch();
        
        if ($vendor) {
            $subject = $status === 'approved' ? 'Vendor Application Approved' : 'Vendor Application Status';
            
            if ($status === 'approved') {
                $message = "
                    <h2>Congratulations! Your vendor application has been approved.</h2>
                    <p>Dear {$vendor['name']},</p>
                    <p>We're excited to inform you that your vendor application for <strong>{$vendor['shop_name']}</strong> has been approved.</p>
                    <p>You can now start adding products and selling on our platform.</p>
                    <p><a href='" . SITE_URL . "?page=vendor'>Access your vendor dashboard</a></p>
                    <p>Welcome to " . SITE_NAME . "!</p>
                ";
            } else {
                $message = "
                    <h2>Vendor Application Update</h2>
                    <p>Dear {$vendor['name']},</p>
                    <p>Thank you for your interest in becoming a vendor on " . SITE_NAME . ".</p>
                    <p>After reviewing your application, we are unable to approve it at this time.</p>
                    <p>If you have any questions, please contact our support team.</p>
                ";
            }
            
            sendEmail($vendor['email'], $subject, $message);
        }
    }
    
    private function getProducts($status, $limit, $offset) {
        global $pdo;
        
        $sql = "SELECT p.*, v.shop_name,
                       (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p 
                INNER JOIN vendors v ON p.vendor_id = v.id";
        
        $params = [];
        
        if ($status !== 'all') {
            $sql .= " WHERE p.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    private function getProductsCount($status) {
        global $pdo;
        
        $sql = "SELECT COUNT(*) as count FROM products";
        $params = [];
        
        if ($status !== 'all') {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }
    
    private function getOrders($status, $limit, $offset) {
        global $pdo;
        
        $sql = "SELECT o.*, u.name as customer_name 
                FROM orders o 
                INNER JOIN users u ON o.user_id = u.id";
        
        $params = [];
        
        if ($status !== 'all') {
            $sql .= " WHERE o.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    private function getOrdersCount($status) {
        global $pdo;
        
        $sql = "SELECT COUNT(*) as count FROM orders";
        $params = [];
        
        if ($status !== 'all') {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }
    
    private function getCustomers($limit, $offset) {
        global $pdo;
        
        $sql = "SELECT u.*, 
                       (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as total_orders,
                       (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = u.id AND payment_status = 'paid') as total_spent
                FROM users u 
                WHERE u.role = 'customer' 
                ORDER BY u.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    private function getCustomersCount() {
        global $pdo;
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
        $stmt->execute();
        return $stmt->fetch()['count'];
    }
    
    private function getCategories() {
        global $pdo;
        
        $stmt = $pdo->prepare("SELECT c.*, 
                              (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count,
                              pc.name as parent_name
                              FROM categories c 
                              LEFT JOIN categories pc ON c.parent_id = pc.id 
                              ORDER BY c.sort_order, c.name");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    private function getSettings() {
        global $pdo;
        
        $stmt = $pdo->prepare("SELECT * FROM settings ORDER BY setting_key");
        $stmt->execute();
        $settings = $stmt->fetchAll();
        
        $settingsArray = [];
        foreach ($settings as $setting) {
            $settingsArray[$setting['setting_key']] = $setting['setting_value'];
        }
        
        return $settingsArray;
    }
    
    private function handleCategoryAction() {
        // Handle category CRUD operations
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'add':
                $this->addCategory();
                break;
            case 'edit':
                $this->editCategory();
                break;
            case 'delete':
                $this->deleteCategory();
                break;
        }
    }
    
    private function addCategory() {
        global $pdo;
        
        $name = sanitize($_POST['name']);
        $slug = generateSlug($name);
        $description = sanitize($_POST['description']);
        $parentId = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
        
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, parent_id, created_at) VALUES (?, ?, ?, ?, NOW())");
        
        if ($stmt->execute([$name, $slug, $description, $parentId])) {
            setFlashMessage('success', 'Category added successfully');
        } else {
            setFlashMessage('error', 'Failed to add category');
        }
    }
    
    private function editCategory() {
        global $pdo;
        
        $id = intval($_POST['category_id']);
        $name = sanitize($_POST['name']);
        $slug = generateSlug($name);
        $description = sanitize($_POST['description']);
        $parentId = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
        
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, parent_id = ? WHERE id = ?");
        
        if ($stmt->execute([$name, $slug, $description, $parentId, $id])) {
            setFlashMessage('success', 'Category updated successfully');
        } else {
            setFlashMessage('error', 'Failed to update category');
        }
    }
    
    private function deleteCategory() {
        global $pdo;
        
        $id = intval($_POST['category_id']);
        
        // Check if category has products
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
        $stmt->execute([$id]);
        $productCount = $stmt->fetch()['count'];
        
        if ($productCount > 0) {
            setFlashMessage('error', 'Cannot delete category with existing products');
            return;
        }
        
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            setFlashMessage('success', 'Category deleted successfully');
        } else {
            setFlashMessage('error', 'Failed to delete category');
        }
    }
    
    private function handleSettingsUpdate() {
        global $pdo;
        
        foreach ($_POST as $key => $value) {
            if ($key !== 'action') {
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, created_at) VALUES (?, ?, NOW()) 
                                      ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
                $stmt->execute([$key, $value, $value]);
            }
        }
        
        setFlashMessage('success', 'Settings updated successfully');
    }
    
    private function getSalesReport() {
        global $pdo;
        
        // Monthly sales for the last 12 months
        $stmt = $pdo->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                              SUM(total_amount) as total_sales,
                              COUNT(*) as total_orders
                              FROM orders 
                              WHERE payment_status = 'paid' 
                              AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH)
                              GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
                              ORDER BY month");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    private function getVendorsReport() {
        global $pdo;
        
        $stmt = $pdo->prepare("SELECT v.shop_name, v.city, v.state,
                              COUNT(p.id) as total_products,
                              COALESCE(SUM(oi.total), 0) as total_sales,
                              COUNT(DISTINCT o.id) as total_orders
                              FROM vendors v
                              LEFT JOIN products p ON v.id = p.vendor_id
                              LEFT JOIN order_items oi ON v.id = oi.vendor_id
                              LEFT JOIN orders o ON oi.order_id = o.id AND o.payment_status = 'paid'
                              WHERE v.approval_status = 'approved'
                              GROUP BY v.id
                              ORDER BY total_sales DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    private function getProductsReport() {
        global $pdo;
        
        $stmt = $pdo->prepare("SELECT p.name, p.price, p.stock_quantity, v.shop_name,
                              COUNT(oi.id) as times_sold,
                              COALESCE(SUM(oi.quantity), 0) as total_quantity_sold,
                              COALESCE(SUM(oi.total), 0) as total_revenue
                              FROM products p
                              INNER JOIN vendors v ON p.vendor_id = v.id
                              LEFT JOIN order_items oi ON p.id = oi.product_id
                              LEFT JOIN orders o ON oi.order_id = o.id AND o.payment_status = 'paid'
                              WHERE p.status = 'active'
                              GROUP BY p.id
                              ORDER BY total_revenue DESC
                              LIMIT 50");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    private function render($view, $data = []) {
        extract($data);
        include "views/layout/header.php";
        include "views/{$view}.php";
        include "views/layout/footer.php";
    }
}
?>
