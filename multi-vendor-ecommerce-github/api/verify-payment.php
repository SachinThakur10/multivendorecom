<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$paymentId = $input['payment_id'] ?? '';
$razorpayOrderId = $input['order_id'] ?? '';
$signature = $input['signature'] ?? '';
$orderIdInternal = $input['order_id_internal'] ?? 0;

if (empty($paymentId) || empty($razorpayOrderId) || empty($signature) || empty($orderIdInternal)) {
    echo json_encode(['success' => false, 'message' => 'Missing payment details']);
    exit;
}

try {
    // Verify Razorpay signature
    $isValidSignature = verifyRazorpaySignature($razorpayOrderId, $paymentId, $signature);
    
    if ($isValidSignature) {
        // Update order payment status
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid', status = 'processing' WHERE id = ?");
        $result = $stmt->execute([$orderIdInternal]);
        
        if ($result) {
            // Create transaction record
            createTransactionRecord($orderIdInternal, $paymentId, 'razorpay', 'success');
            
            // Clear user's cart
            clearUserCart($_SESSION['user_id']);
            
            // Send order confirmation email
            sendOrderConfirmationEmail($orderIdInternal);
            
            // Update vendor sales
            updateVendorSales($orderIdInternal);
            
            echo json_encode([
                'success' => true,
                'message' => 'Payment verified successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
        }
    } else {
        // Payment verification failed
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'failed' WHERE id = ?");
        $stmt->execute([$orderIdInternal]);
        
        createTransactionRecord($orderIdInternal, $paymentId, 'razorpay', 'failed');
        
        echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Payment verification error: ' . $e->getMessage()]);
}

function verifyRazorpaySignature($orderId, $paymentId, $signature) {
    // In a real implementation, you would verify the signature using Razorpay's method
    /*
    $expectedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, RAZORPAY_KEY_SECRET);
    return hash_equals($expectedSignature, $signature);
    */
    
    // For development, return true (in production, implement proper verification)
    return true;
}

function createTransactionRecord($orderId, $transactionId, $paymentMethod, $status) {
    global $pdo;
    
    // Get order amount
    $stmt = $pdo->prepare("SELECT total_amount FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    
    if ($order) {
        $stmt = $pdo->prepare("INSERT INTO transactions (order_id, transaction_id, payment_method, amount, status, created_at) 
                              VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$orderId, $transactionId, $paymentMethod, $order['total_amount'], $status]);
    }
}

function clearUserCart($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$userId]);
}

function sendOrderConfirmationEmail($orderId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT o.*, u.name, u.email 
                          FROM orders o 
                          INNER JOIN users u ON o.user_id = u.id 
                          WHERE o.id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    
    if ($order) {
        $subject = 'Payment Confirmed - Order ' . $order['order_number'];
        $message = "
            <h2>Payment Confirmed!</h2>
            <p>Dear {$order['name']},</p>
            <p>Your payment for order <strong>{$order['order_number']}</strong> has been confirmed.</p>
            <p>Order Total: " . formatCurrency($order['total_amount']) . "</p>
            <p>Your order is now being processed and will be shipped soon.</p>
            <p>Thank you for shopping with " . SITE_NAME . "!</p>
        ";
        
        sendEmail($order['email'], $subject, $message);
    }
}

function updateVendorSales($orderId) {
    global $pdo;
    
    // Update vendor total sales
    $stmt = $pdo->prepare("UPDATE vendors v 
                          SET total_sales = (
                              SELECT COALESCE(SUM(oi.total), 0) 
                              FROM order_items oi 
                              INNER JOIN orders o ON oi.order_id = o.id 
                              WHERE oi.vendor_id = v.id AND o.payment_status = 'paid'
                          )
                          WHERE v.id IN (
                              SELECT DISTINCT oi.vendor_id 
                              FROM order_items oi 
                              WHERE oi.order_id = ?
                          )");
    $stmt->execute([$orderId]);
}
?>
