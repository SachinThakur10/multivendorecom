<?php
require_once 'models/Product.php';

class CartController {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new Product();
    }
    
    public function index() {
        if (!isLoggedIn()) {
            redirect('?page=auth&action=login');
        }
        
        $cartItems = $this->getCartItems();
        $cartTotal = $this->calculateCartTotal($cartItems);
        
        $data = [
            'title' => 'Shopping Cart - ' . SITE_NAME,
            'cart_items' => $cartItems,
            'cart_total' => $cartTotal
        ];
        
        $this->render('cart/index', $data);
    }
    
    public function add() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login first']);
            exit;
        }
        
        $productId = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']) ?: 1;
        $attributes = json_decode($_POST['attributes'] ?? '{}', true);
        
        // Check if product exists and is active
        $product = $this->productModel->find($productId);
        if (!$product || $product['status'] !== 'active') {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }
        
        // Check stock
        if ($product['manage_stock'] && $product['stock_quantity'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
            exit;
        }
        
        global $pdo;
        $userId = $_SESSION['user_id'];
        
        // Check if item already exists in cart
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $existingItem = $stmt->fetch();
        
        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem['quantity'] + $quantity;
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([$newQuantity, $existingItem['id']]);
        } else {
            // Add new item
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity, attributes, created_at) VALUES (?, ?, ?, ?, NOW())");
            $result = $stmt->execute([$userId, $productId, $quantity, json_encode($attributes)]);
        }
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Product added to cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
        }
        exit;
    }
    
    public function update() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login first']);
            exit;
        }
        
        $cartId = intval($_POST['cart_id']);
        $quantity = intval($_POST['quantity']);
        $userId = $_SESSION['user_id'];
        
        if ($quantity < 1) {
            $this->remove();
            return;
        }
        
        global $pdo;
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
        $result = $stmt->execute([$quantity, $cartId, $userId]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Cart updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
        }
        exit;
    }
    
    public function remove() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login first']);
            exit;
        }
        
        $cartId = intval($_POST['cart_id']);
        $userId = $_SESSION['user_id'];
        
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $result = $stmt->execute([$cartId, $userId]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
        }
        exit;
    }
    
    public function clear() {
        if (!isLoggedIn()) {
            redirect('?page=auth&action=login');
        }
        
        global $pdo;
        $userId = $_SESSION['user_id'];
        
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $result = $stmt->execute([$userId]);
        
        if ($result) {
            setFlashMessage('success', 'Cart cleared successfully');
        } else {
            setFlashMessage('error', 'Failed to clear cart');
        }
        
        redirect('?page=cart');
    }
    
    public function checkout() {
        if (!isLoggedIn()) {
            redirect('?page=auth&action=login');
        }
        
        $cartItems = $this->getCartItems();
        
        if (empty($cartItems)) {
            setFlashMessage('error', 'Your cart is empty');
            redirect('?page=cart');
        }
        
        $cartTotal = $this->calculateCartTotal($cartItems);
        
        $data = [
            'title' => 'Checkout - ' . SITE_NAME,
            'cart_items' => $cartItems,
            'cart_total' => $cartTotal
        ];
        
        $this->render('cart/checkout', $data);
    }
    
    private function getCartItems() {
        if (!isLoggedIn()) {
            return [];
        }
        
        global $pdo;
        $userId = $_SESSION['user_id'];
        
        $sql = "SELECT c.*, p.name, p.price, p.sale_price, p.stock_quantity, p.manage_stock,
                       v.shop_name,
                       (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
                FROM cart c
                INNER JOIN products p ON c.product_id = p.id
                INNER JOIN vendors v ON p.vendor_id = v.id
                WHERE c.user_id = ? AND p.status = 'active'
                ORDER BY c.created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    private function calculateCartTotal($cartItems) {
        $subtotal = 0;
        $tax = 0;
        $shipping = 0;
        
        foreach ($cartItems as $item) {
            $price = $item['sale_price'] ?: $item['price'];
            $subtotal += $price * $item['quantity'];
        }
        
        // Calculate tax (18% GST)
        $taxRate = floatval(getSetting('tax_rate', 18));
        $tax = ($subtotal * $taxRate) / 100;
        
        // Calculate shipping
        $shippingCharge = floatval(getSetting('shipping_charge', 50));
        $freeShippingThreshold = floatval(getSetting('free_shipping_threshold', 500));
        
        if ($subtotal >= $freeShippingThreshold) {
            $shipping = 0;
        } else {
            $shipping = $shippingCharge;
        }
        
        $total = $subtotal + $tax + $shipping;
        
        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'tax_rate' => $taxRate,
            'free_shipping_threshold' => $freeShippingThreshold
        ];
    }
    
    private function render($view, $data = []) {
        extract($data);
        include "views/layout/header.php";
        include "views/{$view}.php";
        include "views/layout/footer.php";
    }
}
?>
