<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$action = $_POST['action'] ?? '';
$userId = $_SESSION['user_id'];

switch ($action) {
    case 'add':
        addToCart();
        break;
    
    case 'update':
        updateCart();
        break;
    
    case 'remove':
        removeFromCart();
        break;
    
    case 'get':
        getCartItems();
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function addToCart() {
    global $pdo, $userId;
    
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']) ?: 1;
    $attributes = json_decode($_POST['attributes'] ?? '{}', true);
    
    // Check if product exists and is active
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        return;
    }
    
    // Check stock
    if ($product['manage_stock'] && $product['stock_quantity'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
        return;
    }
    
    // Check if item already exists in cart
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    $existingItem = $stmt->fetch();
    
    try {
        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem['quantity'] + $quantity;
            
            // Check stock again for new quantity
            if ($product['manage_stock'] && $product['stock_quantity'] < $newQuantity) {
                echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
                return;
            }
            
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, attributes = ?, updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([$newQuantity, json_encode($attributes), $existingItem['id']]);
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
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function updateCart() {
    global $pdo, $userId;
    
    $cartId = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);
    
    if ($quantity < 1) {
        removeFromCart();
        return;
    }
    
    // Get cart item and product details
    $stmt = $pdo->prepare("SELECT c.*, p.stock_quantity, p.manage_stock 
                          FROM cart c 
                          INNER JOIN products p ON c.product_id = p.id 
                          WHERE c.id = ? AND c.user_id = ?");
    $stmt->execute([$cartId, $userId]);
    $cartItem = $stmt->fetch();
    
    if (!$cartItem) {
        echo json_encode(['success' => false, 'message' => 'Cart item not found']);
        return;
    }
    
    // Check stock
    if ($cartItem['manage_stock'] && $cartItem['stock_quantity'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
        $result = $stmt->execute([$quantity, $cartId, $userId]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Cart updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function removeFromCart() {
    global $pdo, $userId;
    
    $cartId = intval($_POST['cart_id']);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $result = $stmt->execute([$cartId, $userId]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function getCartItems() {
    global $pdo, $userId;
    
    try {
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
        $items = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'items' => $items]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>
