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

$userId = $_SESSION['user_id'];

try {
    // Get cart items
    $cartItems = getCartItems($userId);
    
    if (empty($cartItems)) {
        echo json_encode(['success' => false, 'message' => 'Your cart is empty']);
        exit;
    }
    
    // Calculate totals
    $cartTotal = calculateCartTotal($cartItems);
    
    // Validate stock availability
    foreach ($cartItems as $item) {
        if ($item['manage_stock'] && $item['stock_quantity'] < $item['quantity']) {
            echo json_encode(['success' => false, 'message' => "Insufficient stock for {$item['name']}"]);
            exit;
        }
    }
    
    // Prepare order data
    $orderData = [
        'user_id' => $userId,
        'order_number' => generateOrderNumber(),
        'status' => 'pending',
        'total_amount' => $cartTotal['total'],
        'shipping_amount' => $cartTotal['shipping'],
        'tax_amount' => $cartTotal['tax'],
        'payment_method' => sanitize($_POST['payment_method']),
        'payment_status' => 'pending',
        'shipping_address' => json_encode([
            'first_name' => sanitize($_POST['shipping_first_name']),
            'last_name' => sanitize($_POST['shipping_last_name']),
            'email' => sanitize($_POST['shipping_email']),
            'phone' => sanitize($_POST['shipping_phone']),
            'address_1' => sanitize($_POST['shipping_address_1']),
            'address_2' => sanitize($_POST['shipping_address_2']),
            'city' => sanitize($_POST['shipping_city']),
            'state' => sanitize($_POST['shipping_state']),
            'pincode' => sanitize($_POST['shipping_pincode'])
        ]),
        'billing_address' => json_encode([
            'first_name' => sanitize($_POST['billing_first_name'] ?? $_POST['shipping_first_name']),
            'last_name' => sanitize($_POST['billing_last_name'] ?? $_POST['shipping_last_name']),
            'email' => sanitize($_POST['billing_email'] ?? $_POST['shipping_email']),
            'phone' => sanitize($_POST['billing_phone'] ?? $_POST['shipping_phone']),
            'address_1' => sanitize($_POST['billing_address_1'] ?? $_POST['shipping_address_1']),
            'address_2' => sanitize($_POST['billing_address_2'] ?? $_POST['shipping_address_2']),
            'city' => sanitize($_POST['billing_city'] ?? $_POST['shipping_city']),
            'state' => sanitize($_POST['billing_state'] ?? $_POST['shipping_state']),
            'pincode' => sanitize($_POST['billing_pincode'] ?? $_POST['shipping_pincode'])
        ]),
        'notes' => sanitize($_POST['order_notes'] ?? '')
    ];
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Create order
    $orderId = createOrder($orderData);
    
    if (!$orderId) {
        throw new Exception('Failed to create order');
    }
    
    // Create order items
    foreach ($cartItems as $item) {
        $price = $item['sale_price'] ?: $item['price'];
        $total = $price * $item['quantity'];
        
        $orderItemData = [
            'order_id' => $orderId,
            'product_id' => $item['product_id'],
            'vendor_id' => $item['vendor_id'],
            'quantity' => $item['quantity'],
            'price' => $price,
            'total' => $total,
            'attributes' => $item['attributes']
        ];
        
        createOrderItem($orderItemData);
        
        // Update product stock
        if ($item['manage_stock']) {
            updateProductStock($item['product_id'], $item['quantity']);
        }
        
        // Create vendor commission record
        createVendorCommission($item['vendor_id'], $orderId, $total);
    }
    
    // Handle payment based on method
    $paymentMethod = $orderData['payment_method'];
    
    if ($paymentMethod === 'cod') {
        // Cash on Delivery - Order is created successfully
        $pdo->commit();
        
        // Clear cart
        clearCart($userId);
        
        // Send order confirmation email
        sendOrderConfirmationEmail($orderId);
        
        echo json_encode([
            'success' => true,
            'message' => 'Order placed successfully',
            'order' => [
                'id' => $orderId,
                'order_number' => $orderData['order_number']
            ]
        ]);
        
    } elseif ($paymentMethod === 'razorpay') {
        // Create Razorpay order
        $razorpayOrder = createRazorpayOrder($orderId, $cartTotal['total']);
        
        if ($razorpayOrder) {
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Order created, proceed with payment',
                'order' => [
                    'id' => $orderId,
                    'order_number' => $orderData['order_number'],
                    'total' => $cartTotal['total'],
                    'razorpay_order_id' => $razorpayOrder['id'],
                    'shipping_address' => json_decode($orderData['shipping_address'], true)
                ]
            ]);
        } else {
            throw new Exception('Failed to create payment order');
        }
        
    } elseif ($paymentMethod === 'paypal') {
        // PayPal integration would go here
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Order created, proceed with PayPal payment',
            'order' => [
                'id' => $orderId,
                'order_number' => $orderData['order_number'],
                'total' => $cartTotal['total']
            ]
        ]);
        
    } else {
        throw new Exception('Invalid payment method');
    }
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function getCartItems($userId) {
    global $pdo;
    
    $sql = "SELECT c.*, p.name, p.price, p.sale_price, p.stock_quantity, p.manage_stock,
                   v.id as vendor_id, v.shop_name
            FROM cart c
            INNER JOIN products p ON c.product_id = p.id
            INNER JOIN vendors v ON p.vendor_id = v.id
            WHERE c.user_id = ? AND p.status = 'active'
            ORDER BY c.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function calculateCartTotal($cartItems) {
    $subtotal = 0;
    
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
    
    $shipping = $subtotal >= $freeShippingThreshold ? 0 : $shippingCharge;
    $total = $subtotal + $tax + $shipping;
    
    return [
        'subtotal' => $subtotal,
        'tax' => $tax,
        'shipping' => $shipping,
        'total' => $total,
        'tax_rate' => $taxRate
    ];
}

function createOrder($orderData) {
    global $pdo;
    
    $sql = "INSERT INTO orders (user_id, order_number, status, total_amount, shipping_amount, tax_amount, 
                               payment_method, payment_status, shipping_address, billing_address, notes, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $orderData['user_id'],
        $orderData['order_number'],
        $orderData['status'],
        $orderData['total_amount'],
        $orderData['shipping_amount'],
        $orderData['tax_amount'],
        $orderData['payment_method'],
        $orderData['payment_status'],
        $orderData['shipping_address'],
        $orderData['billing_address'],
        $orderData['notes']
    ]);
    
    return $result ? $pdo->lastInsertId() : false;
}

function createOrderItem($orderItemData) {
    global $pdo;
    
    $sql = "INSERT INTO order_items (order_id, product_id, vendor_id, quantity, price, total, attributes, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $orderItemData['order_id'],
        $orderItemData['product_id'],
        $orderItemData['vendor_id'],
        $orderItemData['quantity'],
        $orderItemData['price'],
        $orderItemData['total'],
        $orderItemData['attributes']
    ]);
}

function updateProductStock($productId, $quantity) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
    return $stmt->execute([$quantity, $productId]);
}

function createVendorCommission($vendorId, $orderId, $saleAmount) {
    global $pdo;
    
    // Get vendor commission rate
    $stmt = $pdo->prepare("SELECT commission_rate FROM vendors WHERE id = ?");
    $stmt->execute([$vendorId]);
    $vendor = $stmt->fetch();
    
    $commissionRate = $vendor['commission_rate'] ?? DEFAULT_COMMISSION_RATE;
    $commissionAmount = calculateCommission($saleAmount, $commissionRate);
    
    $stmt = $pdo->prepare("INSERT INTO vendor_commissions (vendor_id, order_id, sale_amount, commission_rate, commission_amount, created_at) 
                          VALUES (?, ?, ?, ?, ?, NOW())");
    
    return $stmt->execute([$vendorId, $orderId, $saleAmount, $commissionRate, $commissionAmount]);
}

function clearCart($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    return $stmt->execute([$userId]);
}

function createRazorpayOrder($orderId, $amount) {
    // This is a placeholder for Razorpay integration
    // In a real implementation, you would use Razorpay PHP SDK
    
    /*
    $api = new Razorpay\Api\Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);
    
    $orderData = [
        'receipt' => 'order_' . $orderId,
        'amount' => $amount * 100, // Amount in paise
        'currency' => 'INR'
    ];
    
    $razorpayOrder = $api->order->create($orderData);
    return $razorpayOrder;
    */
    
    // Mock response for development
    return [
        'id' => 'order_' . time() . rand(1000, 9999),
        'amount' => $amount * 100,
        'currency' => 'INR'
    ];
}

function sendOrderConfirmationEmail($orderId) {
    global $pdo;
    
    // Get order details
    $stmt = $pdo->prepare("SELECT o.*, u.name, u.email 
                          FROM orders o 
                          INNER JOIN users u ON o.user_id = u.id 
                          WHERE o.id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    
    if ($order) {
        $subject = 'Order Confirmation - ' . $order['order_number'];
        $shippingAddress = json_decode($order['shipping_address'], true);
        
        $message = "
            <h2>Order Confirmation</h2>
            <p>Dear {$order['name']},</p>
            <p>Thank you for your order! Your order <strong>{$order['order_number']}</strong> has been received and is being processed.</p>
            <p><strong>Order Details:</strong></p>
            <ul>
                <li>Order Number: {$order['order_number']}</li>
                <li>Order Date: " . formatDate($order['created_at']) . "</li>
                <li>Total Amount: " . formatCurrency($order['total_amount']) . "</li>
                <li>Payment Method: " . ucfirst($order['payment_method']) . "</li>
            </ul>
            <p><strong>Shipping Address:</strong></p>
            <p>
                {$shippingAddress['first_name']} {$shippingAddress['last_name']}<br>
                {$shippingAddress['address_1']}<br>
                " . (!empty($shippingAddress['address_2']) ? $shippingAddress['address_2'] . '<br>' : '') . "
                {$shippingAddress['city']}, {$shippingAddress['state']} - {$shippingAddress['pincode']}<br>
                Phone: {$shippingAddress['phone']}
            </p>
            <p>You will receive another email when your order ships.</p>
            <p>Thank you for shopping with " . SITE_NAME . "!</p>
        ";
        
        sendEmail($order['email'], $subject, $message);
    }
}
?>
