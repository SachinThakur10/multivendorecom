<?php
require_once 'models/Product.php';

class ProductController {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new Product();
    }
    
    public function index() {
        $page = $_GET['page_num'] ?? 1;
        $limit = PRODUCTS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        // Build conditions based on filters
        $conditions = ['status' => 'active'];
        
        // Category filter
        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $categoryId = $this->getCategoryIdBySlug($_GET['category']);
            if ($categoryId) {
                $conditions['category_id'] = $categoryId;
            }
        }
        
        // Search filter
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $conditions['search'] = sanitize($_GET['search']);
        }
        
        // Price filters
        if (isset($_GET['price_min']) && !empty($_GET['price_min'])) {
            $conditions['price_min'] = floatval($_GET['price_min']);
        }
        
        if (isset($_GET['price_max']) && !empty($_GET['price_max'])) {
            $conditions['price_max'] = floatval($_GET['price_max']);
        }
        
        // Vendor filter
        if (isset($_GET['vendor']) && !empty($_GET['vendor'])) {
            $conditions['vendor_id'] = intval($_GET['vendor']);
        }
        
        // Get products
        $products = $this->productModel->getProductsWithDetails($conditions, $limit, $offset);
        
        // Get total count for pagination
        $totalProducts = $this->productModel->count($conditions);
        $pagination = paginate($totalProducts, $limit, $page);
        
        // Get categories for filter
        $categories = $this->getCategories();
        
        // Get vendors for filter
        $vendors = $this->getVendors();
        
        $data = [
            'title' => 'Products - ' . SITE_NAME,
            'products' => $products,
            'pagination' => $pagination,
            'categories' => $categories,
            'vendors' => $vendors,
            'current_filters' => $_GET
        ];
        
        $this->render('products/index', $data);
    }
    
    public function details($id) {
        $product = $this->productModel->getProductDetails($id);
        
        if (!$product || $product['status'] !== 'active') {
            setFlashMessage('error', 'Product not found');
            redirect('?page=products');
        }
        
        // Get related products
        $relatedProducts = $this->productModel->getProductsByCategory($product['category_id'], 4);
        $relatedProducts = array_filter($relatedProducts, function($p) use ($id) {
            return $p['id'] != $id;
        });
        
        // Get product reviews
        $reviews = $this->getProductReviews($id);
        
        $data = [
            'title' => $product['name'] . ' - ' . SITE_NAME,
            'product' => $product,
            'related_products' => $relatedProducts,
            'reviews' => $reviews
        ];
        
        $this->render('products/details', $data);
    }
    
    private function getCategoryIdBySlug($slug) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ? AND status = 'active'");
        $stmt->execute([$slug]);
        $result = $stmt->fetch();
        return $result ? $result['id'] : null;
    }
    
    private function getCategories() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE status = 'active' ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    private function getVendors() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT v.id, v.shop_name FROM vendors v 
                              INNER JOIN users u ON v.user_id = u.id 
                              WHERE v.approval_status = 'approved' AND u.status = 'active' 
                              ORDER BY v.shop_name");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    private function getProductReviews($productId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT r.*, u.name as user_name 
                              FROM reviews r 
                              INNER JOIN users u ON r.user_id = u.id 
                              WHERE r.product_id = ? AND r.status = 'approved' 
                              ORDER BY r.created_at DESC");
        $stmt->execute([$productId]);
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
