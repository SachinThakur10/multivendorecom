<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$count = 0;

if (isLoggedIn()) {
    $userId = $_SESSION['user_id'];
    
    try {
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total_items 
                              FROM cart c 
                              INNER JOIN products p ON c.product_id = p.id 
                              WHERE c.user_id = ? AND p.status = 'active'");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        $count = $result['total_items'] ?? 0;
    } catch (Exception $e) {
        $count = 0;
    }
}

echo json_encode(['count' => $count]);
?>
