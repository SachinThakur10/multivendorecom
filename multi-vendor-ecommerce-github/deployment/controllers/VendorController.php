<?php
require_once 'models/Product.php';
require_once 'models/User.php';

class VendorController {
    private $productModel;
    private $userModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->userModel = new User();
        
        // Check if user is logged in and is a vendor
        if (!isLoggedIn() || !hasRole('vendor')) {
            redirect('?page=auth&action=login');
        }
    }
    
    public function index() {
        $vendor = $this->getVendorDetails();
        
        if (!$vendor || $vendor['approval_status'] !== 'approved') {
            $this->render('vendor/pending-approval', [
                'title' => 'Vendor Dashboard - ' . SITE_NAME,
                'vendor' => $vendor
            ]);
            return;
        }
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats($vendor['id']);
        
        // Get recent orders
        $recentOrders = $this->getRecentOrders($vendor['id']);
        
        // Get recent products
        $recentProducts = $this->productModel->getProductsByVendor($vendor['id'], 5);
        
        $data = [
            'title' => 'Vendor Dashboard - ' . SITE_NAME,
            'vendor' => $vendor,
            'stats' => $stats,
            'recent_orders' => $recentOrders,
            'recent_products' => $recentProducts
        ];
        
        $this->render('vendor/dashboard', $data);
    }
    
    public function products() {
        $vendor = $this->getVendorDetails();
        $page = $_GET['page_num'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Get vendor products
        $products = $this->productModel->getProductsByVendor($vendor['id'], $limit, $offset);
        
        // Get total count for pagination
        $totalProducts = $this->productModel->count(['vendor_id' => $vendor['id']]);
        $pagination = paginate($totalProducts, $limit, $page);
        
        $data = [
            'title' => 'My Products - ' . SITE_NAME,
            'vendor' => $vendor,
            'products' => $products,
            'pagination' => $pagination
        ];
        
        $this->render('vendor/products', $data);
    }
    
    public function addProduct() {
        $vendor = $this->getVendorDetails();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleProductSubmission($vendor['id']);
        }
        
        // Get categories for dropdown
        $categories = $this->getCategories();
        
        $data = [
            'title' => 'Add Product - ' . SITE_NAME,
            'vendor' => $vendor,
            'categories' => $categories
        ];
        
        $this->render('vendor/add-product', $data);
    }
    
    public function editProduct() {
        $vendor = $this->getVendorDetails();
        $productId = $_GET['id'] ?? 0;
        
        // Get product and verify ownership
        $product = $this->productModel->find($productId);
        if (!$product || $product['vendor_id'] != $vendor['id']) {
            setFlashMessage('error', 'Product not found');
            redirect('?page=vendor&action=products');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleProductUpdate($productId, $vendor['id']);
        }
        
        // Get product details with images and attributes
        $product = $this->productModel->getProductDetails($productId);
        $categories = $this->getCategories();
        
        $data = [
            'title' => 'Edit Product - ' . SITE_NAME,
            'vendor' => $vendor,
            'product' => $product,
            'categories' => $categories
        ];
        
        $this->render('vendor/edit-product', $data);
    }
    
    public function deleteProduct() {
        $vendor = $this->getVendorDetails();
        $productId = $_GET['id'] ?? 0;
        
        // Verify ownership
        $product = $this->productModel->find($productId);
        if (!$product || $product['vendor_id'] != $vendor['id']) {
            setFlashMessage('error', 'Product not found');
            redirect('?page=vendor&action=products');
        }
        
        if ($this->productModel->delete($productId)) {
            setFlashMessage('success', 'Product deleted successfully');
        } else {
            setFlashMessage('error', 'Failed to delete product');
        }
        
        redirect('?page=vendor&action=products');
    }
    
    public function orders() {
        $vendor = $this->getVendorDetails();
        $page = $_GET['page_num'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Get vendor orders
        $orders = $this->getVendorOrders($vendor['id'], $limit, $offset);
        
        // Get total count for pagination
        $totalOrders = $this->getVendorOrdersCount($vendor['id']);
        $pagination = paginate($totalOrders, $limit, $page);
        
        $data = [
            'title' => 'Orders - ' . SITE_NAME,
            'vendor' => $vendor,
            'orders' => $orders,
            'pagination' => $pagination
        ];
        
        $this->render('vendor/orders', $data);
    }
    
    public function sales() {
        $vendor = $this->getVendorDetails();
        
        // Get sales statistics
        $salesStats = $this->getSalesStats($vendor['id']);
        
        // Get monthly sales data for chart
        $monthlySales = $this->getMonthlySales($vendor['id']);
        
        $data = [
            'title' => 'Sales Report - ' . SITE_NAME,
            'vendor' => $vendor,
            'sales_stats' => $salesStats,
            'monthly_sales' => $monthlySales
        ];
        
        $this->render('vendor/sales', $data);
    }
    
    public function profile() {
        $vendor = $this->getVendorDetails();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleProfileUpdate($vendor['id']);
        }
        
        $data = [
            'title' => 'Vendor Profile - ' . SITE_NAME,
            'vendor' => $vendor
        ];
        
        $this->render('vendor/profile', $data);
    }
    
    private function getVendorDetails() {
        $userId = $_SESSION['user_id'];
        return $this->userModel->getUserWithVendor($userId);
    }
    
    private function getDashboardStats($vendorId) {
        global $pdo;
        
        $stats = [];
        
        // Total products
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE vendor_id = ?");
        $stmt->execute([$vendorId]);
        $stats['total_products'] = $stmt->fetch()['count'];
        
        // Total orders
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT o.id) as count 
                              FROM orders o 
                              INNER JOIN order_items oi ON o.id = oi.order_id 
                              WHERE oi.vendor_id = ?");
        $stmt->execute([$vendorId]);
        $stats['total_orders'] = $stmt->fetch()['count'];
        
        // Total sales
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(oi.total), 0) as total 
                              FROM order_items oi 
                              INNER JOIN orders o ON oi.order_id = o.id 
                              WHERE oi.vendor_id = ? AND o.payment_status = 'paid'");
        $stmt->execute([$vendorId]);
        $stats['total_sales'] = $stmt->fetch()['total'];
        
        // Pending orders
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT o.id) as count 
                              FROM orders o 
                              INNER JOIN order_items oi ON o.id = oi.order_id 
                              WHERE oi.vendor_id = ? AND o.status = 'pending'");
        $stmt->execute([$vendorId]);
        $stats['pending_orders'] = $stmt->fetch()['count'];
        
        return $stats;
    }
    
    private function getRecentOrders($vendorId, $limit = 5) {
        global $pdo;
        
        $sql = "SELECT DISTINCT o.*, u.name as customer_name 
                FROM orders o 
                INNER JOIN order_items oi ON o.id = oi.order_id 
                INNER JOIN users u ON o.user_id = u.id 
                WHERE oi.vendor_id = ? 
                ORDER BY o.created_at DESC 
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$vendorId, $limit]);
        return $stmt->fetchAll();
    }
    
    private function getVendorOrders($vendorId, $limit, $offset) {
        global $pdo;
        
        $sql = "SELECT DISTINCT o.*, u.name as customer_name,
                       (SELECT SUM(oi2.total) FROM order_items oi2 WHERE oi2.order_id = o.id AND oi2.vendor_id = ?) as vendor_total
                FROM orders o 
                INNER JOIN order_items oi ON o.id = oi.order_id 
                INNER JOIN users u ON o.user_id = u.id 
                WHERE oi.vendor_id = ? 
                ORDER BY o.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$vendorId, $vendorId, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    private function getVendorOrdersCount($vendorId) {
        global $pdo;
        
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT o.id) as count 
                              FROM orders o 
                              INNER JOIN order_items oi ON o.id = oi.order_id 
                              WHERE oi.vendor_id = ?");
        $stmt->execute([$vendorId]);
        return $stmt->fetch()['count'];
    }
    
    private function getSalesStats($vendorId) {
        global $pdo;
        
        $stats = [];
        
        // This month sales
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(oi.total), 0) as total 
                              FROM order_items oi 
                              INNER JOIN orders o ON oi.order_id = o.id 
                              WHERE oi.vendor_id = ? AND o.payment_status = 'paid' 
                              AND MONTH(o.created_at) = MONTH(CURRENT_DATE()) 
                              AND YEAR(o.created_at) = YEAR(CURRENT_DATE())");
        $stmt->execute([$vendorId]);
        $stats['this_month'] = $stmt->fetch()['total'];
        
        // Last month sales
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(oi.total), 0) as total 
                              FROM order_items oi 
                              INNER JOIN orders o ON oi.order_id = o.id 
                              WHERE oi.vendor_id = ? AND o.payment_status = 'paid' 
                              AND MONTH(o.created_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) 
                              AND YEAR(o.created_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
        $stmt->execute([$vendorId]);
        $stats['last_month'] = $stmt->fetch()['total'];
        
        return $stats;
    }
    
    private function getMonthlySales($vendorId) {
        global $pdo;
        
        $sql = "SELECT DATE_FORMAT(o.created_at, '%Y-%m') as month, 
                       SUM(oi.total) as total 
                FROM order_items oi 
                INNER JOIN orders o ON oi.order_id = o.id 
                WHERE oi.vendor_id = ? AND o.payment_status = 'paid' 
                AND o.created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH) 
                GROUP BY DATE_FORMAT(o.created_at, '%Y-%m') 
                ORDER BY month";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll();
    }
    
    private function getCategories() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE status = 'active' ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    private function handleProductSubmission($vendorId) {
        // Handle product creation logic
        $productData = [
            'vendor_id' => $vendorId,
            'category_id' => intval($_POST['category_id']),
            'name' => sanitize($_POST['name']),
            'description' => sanitize($_POST['description']),
            'short_description' => sanitize($_POST['short_description']),
            'price' => floatval($_POST['price']),
            'sale_price' => !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : null,
            'sku' => sanitize($_POST['sku']),
            'stock_quantity' => intval($_POST['stock_quantity']),
            'manage_stock' => isset($_POST['manage_stock']) ? 1 : 0,
            'weight' => !empty($_POST['weight']) ? floatval($_POST['weight']) : null,
            'dimensions' => sanitize($_POST['dimensions']),
            'status' => 'active'
        ];
        
        // Handle image uploads
        $images = [];
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $key => $name) {
                if (!empty($name)) {
                    $file = [
                        'name' => $_FILES['images']['name'][$key],
                        'type' => $_FILES['images']['type'][$key],
                        'tmp_name' => $_FILES['images']['tmp_name'][$key],
                        'error' => $_FILES['images']['error'][$key],
                        'size' => $_FILES['images']['size'][$key]
                    ];
                    
                    $upload = uploadFile($file, 'products');
                    if ($upload['success']) {
                        $images[] = [
                            'url' => $upload['filename'],
                            'alt' => $productData['name']
                        ];
                    }
                }
            }
        }
        
        // Handle attributes
        $attributes = [];
        if (!empty($_POST['attribute_names'])) {
            foreach ($_POST['attribute_names'] as $key => $name) {
                if (!empty($name) && !empty($_POST['attribute_values'][$key])) {
                    $attributes[] = [
                        'name' => sanitize($name),
                        'value' => sanitize($_POST['attribute_values'][$key]),
                        'price_adjustment' => floatval($_POST['attribute_prices'][$key] ?? 0),
                        'stock_quantity' => intval($_POST['attribute_stock'][$key] ?? 0)
                    ];
                }
            }
        }
        
        $productId = $this->productModel->createProduct($productData, $images, $attributes);
        
        if ($productId) {
            setFlashMessage('success', 'Product added successfully');
            redirect('?page=vendor&action=products');
        } else {
            setFlashMessage('error', 'Failed to add product');
        }
    }
    
    private function handleProductUpdate($productId, $vendorId) {
        // Similar to handleProductSubmission but for updates
        $productData = [
            'category_id' => intval($_POST['category_id']),
            'name' => sanitize($_POST['name']),
            'description' => sanitize($_POST['description']),
            'short_description' => sanitize($_POST['short_description']),
            'price' => floatval($_POST['price']),
            'sale_price' => !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : null,
            'sku' => sanitize($_POST['sku']),
            'stock_quantity' => intval($_POST['stock_quantity']),
            'manage_stock' => isset($_POST['manage_stock']) ? 1 : 0,
            'weight' => !empty($_POST['weight']) ? floatval($_POST['weight']) : null,
            'dimensions' => sanitize($_POST['dimensions'])
        ];
        
        if ($this->productModel->update($productId, $productData)) {
            setFlashMessage('success', 'Product updated successfully');
            redirect('?page=vendor&action=products');
        } else {
            setFlashMessage('error', 'Failed to update product');
        }
    }
    
    private function handleProfileUpdate($vendorId) {
        global $pdo;
        
        $vendorData = [
            'shop_name' => sanitize($_POST['shop_name']),
            'description' => sanitize($_POST['description']),
            'address' => sanitize($_POST['address']),
            'city' => sanitize($_POST['city']),
            'state' => sanitize($_POST['state']),
            'pincode' => sanitize($_POST['pincode']),
            'gst_number' => sanitize($_POST['gst_number']),
            'bank_account' => sanitize($_POST['bank_account']),
            'ifsc_code' => sanitize($_POST['ifsc_code'])
        ];
        
        // Handle logo upload
        if (!empty($_FILES['logo']['name'])) {
            $upload = uploadFile($_FILES['logo'], 'vendors');
            if ($upload['success']) {
                $vendorData['logo'] = $upload['filename'];
            }
        }
        
        $fields = [];
        $params = [];
        foreach ($vendorData as $field => $value) {
            if ($value !== null) {
                $fields[] = "$field = ?";
                $params[] = $value;
            }
        }
        $params[] = $vendorId;
        
        $sql = "UPDATE vendors SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute($params)) {
            setFlashMessage('success', 'Profile updated successfully');
        } else {
            setFlashMessage('error', 'Failed to update profile');
        }
        
        redirect('?page=vendor&action=profile');
    }
    
    private function render($view, $data = []) {
        extract($data);
        include "views/layout/header.php";
        include "views/{$view}.php";
        include "views/layout/footer.php";
    }
}
?>
