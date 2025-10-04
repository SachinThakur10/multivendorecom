<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-credit-card me-2"></i>Checkout
            </h2>
            
            <!-- Progress Steps -->
            <div class="checkout-progress mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="step active">
                            <div class="step-number">1</div>
                            <div class="step-title">Shipping Details</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-title">Payment Method</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-title">Order Review</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form id="checkoutForm" method="POST" action="api/checkout.php">
                <div class="row">
                    <!-- Checkout Form -->
                    <div class="col-lg-8">
                        <!-- Shipping Address -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-shipping-fast me-2"></i>Shipping Address
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">First Name *</label>
                                            <input type="text" class="form-control" name="shipping_first_name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Last Name *</label>
                                            <input type="text" class="form-control" name="shipping_last_name" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" name="shipping_email" 
                                           value="<?php echo $_SESSION['user_email'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Phone Number *</label>
                                    <input type="tel" class="form-control" name="shipping_phone" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Address Line 1 *</label>
                                    <input type="text" class="form-control" name="shipping_address_1" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Address Line 2</label>
                                    <input type="text" class="form-control" name="shipping_address_2">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">City *</label>
                                            <input type="text" class="form-control" name="shipping_city" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">State *</label>
                                            <input type="text" class="form-control" name="shipping_state" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Pincode *</label>
                                            <input type="text" class="form-control" name="shipping_pincode" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Billing Address -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-invoice me-2"></i>Billing Address
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="sameAsShipping" checked>
                                    <label class="form-check-label" for="sameAsShipping">
                                        Same as shipping address
                                    </label>
                                </div>
                                
                                <div id="billingAddressForm" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">First Name</label>
                                                <input type="text" class="form-control" name="billing_first_name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Last Name</label>
                                                <input type="text" class="form-control" name="billing_last_name">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" class="form-control" name="billing_email">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" name="billing_phone">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Address Line 1</label>
                                        <input type="text" class="form-control" name="billing_address_1">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Address Line 2</label>
                                        <input type="text" class="form-control" name="billing_address_2">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">City</label>
                                                <input type="text" class="form-control" name="billing_city">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">State</label>
                                                <input type="text" class="form-control" name="billing_state">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Pincode</label>
                                                <input type="text" class="form-control" name="billing_pincode">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Method -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>Payment Method
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="payment-methods">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="razorpay" value="razorpay" checked>
                                        <label class="form-check-label" for="razorpay">
                                            <i class="fas fa-credit-card me-2"></i>
                                            Credit/Debit Card, UPI, Net Banking (Razorpay)
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="paypal" value="paypal">
                                        <label class="form-check-label" for="paypal">
                                            <i class="fab fa-paypal me-2"></i>
                                            PayPal
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="cod" value="cod">
                                        <label class="form-check-label" for="cod">
                                            <i class="fas fa-money-bill-wave me-2"></i>
                                            Cash on Delivery
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Order Notes -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-sticky-note me-2"></i>Order Notes (Optional)
                                </h5>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" name="order_notes" rows="3" 
                                          placeholder="Special instructions for your order..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card sticky-top" style="top: 20px;">
                            <div class="card-header">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <!-- Cart Items -->
                                <div class="order-items mb-3">
                                    <?php foreach ($cart_items as $item): ?>
                                        <div class="order-item d-flex mb-2">
                                            <img src="assets/uploads/products/<?php echo $item['image'] ?? 'default.jpg'; ?>" 
                                                 alt="<?php echo $item['name']; ?>" class="me-2" 
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 small"><?php echo $item['name']; ?></h6>
                                                <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                            </div>
                                            <div class="text-end">
                                                <?php $price = $item['sale_price'] ?: $item['price']; ?>
                                                <span class="fw-bold"><?php echo formatCurrency($price * $item['quantity']); ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <hr>
                                
                                <!-- Pricing Breakdown -->
                                <div class="pricing-breakdown">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <span><?php echo formatCurrency($cart_total['subtotal']); ?></span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Tax (<?php echo $cart_total['tax_rate']; ?>%):</span>
                                        <span><?php echo formatCurrency($cart_total['tax']); ?></span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Shipping:</span>
                                        <span>
                                            <?php if ($cart_total['shipping'] > 0): ?>
                                                <?php echo formatCurrency($cart_total['shipping']); ?>
                                            <?php else: ?>
                                                <span class="text-success">FREE</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between mb-3">
                                        <strong>Total:</strong>
                                        <strong class="text-primary h5"><?php echo formatCurrency($cart_total['total']); ?></strong>
                                    </div>
                                </div>
                                
                                <!-- Terms and Conditions -->
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                    <label class="form-check-label small" for="agreeTerms">
                                        I agree to the <a href="#" class="text-decoration-none">Terms & Conditions</a> 
                                        and <a href="#" class="text-decoration-none">Privacy Policy</a>
                                    </label>
                                </div>
                                
                                <!-- Place Order Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg" id="placeOrderBtn">
                                        <i class="fas fa-lock me-2"></i>Place Order
                                    </button>
                                </div>
                                
                                <!-- Security Info -->
                                <div class="security-info text-center mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Your payment information is secure and encrypted
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.checkout-progress {
    margin-bottom: 2rem;
}

.step {
    text-align: center;
    position: relative;
}

.step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 20px;
    right: -50%;
    width: 100%;
    height: 2px;
    background-color: #dee2e6;
    z-index: -1;
}

.step.active:not(:last-child)::after {
    background-color: var(--primary-color);
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #dee2e6;
    color: #6c757d;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 10px;
}

.step.active .step-number {
    background-color: var(--primary-color);
    color: white;
}

.step-title {
    font-size: 0.9rem;
    color: #6c757d;
}

.step.active .step-title {
    color: var(--primary-color);
    font-weight: 600;
}

.order-item img {
    border: 1px solid #dee2e6;
}

.pricing-breakdown {
    font-size: 0.95rem;
}

.security-info {
    border-top: 1px solid #dee2e6;
    padding-top: 15px;
}

@media (max-width: 768px) {
    .step:not(:last-child)::after {
        display: none;
    }
    
    .checkout-progress .row {
        text-align: center;
    }
    
    .checkout-progress .col-md-4 {
        margin-bottom: 1rem;
    }
}
</style>

<script>
// Toggle billing address form
document.getElementById('sameAsShipping').addEventListener('change', function() {
    const billingForm = document.getElementById('billingAddressForm');
    const billingInputs = billingForm.querySelectorAll('input');
    
    if (this.checked) {
        billingForm.style.display = 'none';
        billingInputs.forEach(input => input.required = false);
    } else {
        billingForm.style.display = 'block';
        billingInputs.forEach(input => {
            if (input.name.includes('first_name') || input.name.includes('last_name') || 
                input.name.includes('email') || input.name.includes('phone') || 
                input.name.includes('address_1') || input.name.includes('city') || 
                input.name.includes('state') || input.name.includes('pincode')) {
                input.required = true;
            }
        });
    }
});

// Handle form submission
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const paymentMethod = formData.get('payment_method');
    
    // Show loading state
    const submitBtn = document.getElementById('placeOrderBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    submitBtn.disabled = true;
    
    // Submit order
    fetch('api/checkout.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (paymentMethod === 'razorpay') {
                initiateRazorpayPayment(data.order);
            } else if (paymentMethod === 'paypal') {
                initiatePayPalPayment(data.order);
            } else {
                // COD - redirect to success page
                window.location.href = `?page=order&action=success&id=${data.order.id}`;
            }
        } else {
            showNotification(data.message || 'Failed to place order', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        showNotification('Something went wrong. Please try again.', 'error');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Razorpay payment integration
function initiateRazorpayPayment(order) {
    const options = {
        key: '<?php echo RAZORPAY_KEY_ID; ?>',
        amount: order.total * 100, // Amount in paise
        currency: 'INR',
        name: '<?php echo SITE_NAME; ?>',
        description: `Order #${order.order_number}`,
        order_id: order.razorpay_order_id,
        handler: function(response) {
            // Payment successful
            verifyPayment(response, order.id);
        },
        prefill: {
            name: order.shipping_address.first_name + ' ' + order.shipping_address.last_name,
            email: order.shipping_address.email,
            contact: order.shipping_address.phone
        },
        theme: {
            color: '#667eea'
        },
        modal: {
            ondismiss: function() {
                // Payment cancelled
                const submitBtn = document.getElementById('placeOrderBtn');
                submitBtn.innerHTML = '<i class="fas fa-lock me-2"></i>Place Order';
                submitBtn.disabled = false;
            }
        }
    };
    
    const rzp = new Razorpay(options);
    rzp.open();
}

// Verify payment
function verifyPayment(paymentResponse, orderId) {
    fetch('api/verify-payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            payment_id: paymentResponse.razorpay_payment_id,
            order_id: paymentResponse.razorpay_order_id,
            signature: paymentResponse.razorpay_signature,
            order_id_internal: orderId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = `?page=order&action=success&id=${orderId}`;
        } else {
            showNotification('Payment verification failed', 'error');
        }
    });
}

// PayPal payment integration (placeholder)
function initiatePayPalPayment(order) {
    // PayPal integration would go here
    showNotification('PayPal integration coming soon', 'info');
    
    const submitBtn = document.getElementById('placeOrderBtn');
    submitBtn.innerHTML = '<i class="fas fa-lock me-2"></i>Place Order';
    submitBtn.disabled = false;
}

// Auto-fill shipping address from user profile (if available)
$(document).ready(function() {
    // This would be populated from user's saved addresses
});
</script>

<!-- Razorpay Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
