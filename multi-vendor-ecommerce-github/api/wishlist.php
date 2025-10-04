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
        addToWishlist();
        break;
    
    case 'remove':
        removeFromWishlist();
        break;
    
    case 'get':
        getWishlistItems();
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function addToWishlist() {
    global $pdo, $userId;
    
    $productId = intval($_POST['product_id']);
    
    // Check if product exists and is active
    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? AND status = 'active'");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        return;
    }
    
    // Check if already in wishlist
    $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo json_encode(['success' => false, 'message' => 'Product already in wishlist']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id, created_at) VALUES (?, ?, NOW())");
        $result = $stmt->execute([$userId, $productId]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Product added to wishlist']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add to wishlist']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function removeFromWishlist() {
    global $pdo, $userId;
    
    $productId = intval($_POST['product_id']);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $result = $stmt->execute([$userId, $productId]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Product removed from wishlist']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove from wishlist']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function getWishlistItems() {
    global $pdo, $userId;
    
    try {
        $sql = "SELECT w.*, p.name, p.price, p.sale_price, p.stock_quantity, p.manage_stock,
                       v.shop_name,
                       (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image,
                       (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND status = 'approved') as avg_rating,
                       (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND status = 'approved') as review_count
                FROM wishlist w
                INNER JOIN products p ON w.product_id = p.id
                INNER JOIN vendors v ON p.vendor_id = v.id
                WHERE w.user_id = ? AND p.status = 'active'
                ORDER BY w.created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $items = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'items' => $items]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>
