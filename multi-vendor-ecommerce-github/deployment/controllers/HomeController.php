<?php
require_once 'models/Product.php';
require_once 'models/User.php';

class HomeController {
    private $productModel;
    private $userModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->userModel = new User();
    }
    
    public function index() {
        // Get featured products
        $featuredProducts = $this->productModel->getFeaturedProducts(8);
        
        // Get categories
        $categories = $this->getCategories();
        
        // Get top vendors
        $topVendors = $this->userModel->getVendors('approved');
        $topVendors = array_slice($topVendors, 0, 6);
        
        // Get latest products
        $latestProducts = $this->productModel->getProductsWithDetails(['status' => 'active'], 8);
        
        $data = [
            'title' => 'Home - ' . SITE_NAME,
            'featured_products' => $featuredProducts,
            'categories' => $categories,
            'top_vendors' => $topVendors,
            'latest_products' => $latestProducts
        ];
        
        $this->render('home', $data);
    }
    
    private function getCategories() {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order, name");
        $stmt->execute();
        $categories = $stmt->fetchAll();
        
        // Get subcategories for each category
        foreach ($categories as &$category) {
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? AND status = 'active' ORDER BY sort_order, name");
            $stmt->execute([$category['id']]);
            $category['subcategories'] = $stmt->fetchAll();
        }
        
        return $categories;
    }
    
    private function render($view, $data = []) {
        extract($data);
        include "views/layout/header.php";
        include "views/{$view}.php";
        include "views/layout/footer.php";
    }
}
?>
