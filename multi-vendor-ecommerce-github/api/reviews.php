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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$productId = intval($_POST['product_id']);
$rating = intval($_POST['rating']);
$title = sanitize($_POST['title'] ?? '');
$comment = sanitize($_POST['comment']);
$userId = $_SESSION['user_id'];

// Validation
if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
    exit;
}

if (empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Comment is required']);
    exit;
}

try {
    // Check if product exists
    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? AND status = 'active'");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
    
    // Check if user has purchased this product
    $stmt = $pdo->prepare("SELECT DISTINCT o.id 
                          FROM orders o 
                          INNER JOIN order_items oi ON o.id = oi.order_id 
                          WHERE o.user_id = ? AND oi.product_id = ? AND o.payment_status = 'paid'");
    $stmt->execute([$userId, $productId]);
    $order = $stmt->fetch();
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'You can only review products you have purchased']);
        exit;
    }
    
    // Check if user has already reviewed this product for this order
    $stmt = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND product_id = ? AND order_id = ?");
    $stmt->execute([$userId, $productId, $order['id']]);
    $existingReview = $stmt->fetch();
    
    if ($existingReview) {
        echo json_encode(['success' => false, 'message' => 'You have already reviewed this product']);
        exit;
    }
    
    // Create review
    $stmt = $pdo->prepare("INSERT INTO reviews (product_id, user_id, order_id, rating, title, comment, status, created_at) 
                          VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
    
    $result = $stmt->execute([$productId, $userId, $order['id'], $rating, $title, $comment]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Review submitted successfully. It will be published after approval.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit review']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
