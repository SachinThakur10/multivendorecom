<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
            </h2>
            
            <?php if (!empty($cart_items)): ?>
                <div class="row">
                    <!-- Cart Items -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <?php foreach ($cart_items as $item): ?>
                                    <div class="cart-item border-bottom pb-3 mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-2">
                                                <img src="assets/uploads/products/<?php echo $item['image'] ?? 'default.jpg'; ?>" 
                                                     alt="<?php echo $item['name']; ?>" class="img-fluid rounded">
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <h6 class="mb-1">
                                                    <a href="?page=products&action=details&id=<?php echo $item['product_id']; ?>" 
                                                       class="text-decoration-none text-dark">
                                                        <?php echo $item['name']; ?>
                                                    </a>
                                                </h6>
                                                <p class="text-muted small mb-1">by <?php echo $item['shop_name']; ?></p>
                                                
                                                <?php if ($item['attributes']): ?>
                                                    <div class="attributes">
                                                        <?php 
                                                        $attributes = json_decode($item['attributes'], true);
                                                        if ($attributes):
                                                        ?>
                                                            <?php foreach ($attributes as $attr => $value): ?>
                                                                <small class="text-muted">
                                                                    <?php echo ucfirst($attr); ?>: <?php echo $value['value']; ?>
                                                                </small><br>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="col-md-2">
                                                <div class="price">
                                                    <?php $price = $item['sale_price'] ?: $item['price']; ?>
                                                    <span class="fw-bold"><?php echo formatCurrency($price); ?></span>
                                                    <?php if ($item['sale_price']): ?>
                                                        <br><small class="text-muted text-decoration-line-through">
                                                            <?php echo formatCurrency($item['price']); ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-2">
                                                <div class="quantity-controls">
                                                    <div class="input-group input-group-sm">
                                                        <button class="btn btn-outline-secondary" type="button" 
                                                                onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)">
                                                            -
                                                        </button>
                                                        <input type="number" class="form-control text-center quantity-input" 
                                                               value="<?php echo $item['quantity']; ?>" 
                                                               min="1" 
                                                               max="<?php echo $item['manage_stock'] ? $item['stock_quantity'] : 999; ?>"
                                                               data-cart-id="<?php echo $item['id']; ?>">
                                                        <button class="btn btn-outline-secondary" type="button" 
                                                                onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)">
                                                            +
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-2 text-end">
                                                <div class="item-total mb-2">
                                                    <strong><?php echo formatCurrency($price * $item['quantity']); ?></strong>
                                                </div>
                                                <button class="btn btn-outline-danger btn-sm" 
                                                        onclick="removeFromCart(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Stock Warning -->
                                        <?php if ($item['manage_stock'] && $item['stock_quantity'] < $item['quantity']): ?>
                                            <div class="alert alert-warning mt-2 mb-0">
                                                <small>
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Only <?php echo $item['stock_quantity']; ?> items available in stock
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                                
                                <div class="cart-actions mt-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="?page=products" class="btn btn-outline-primary">
                                                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                                            </a>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <a href="?page=cart&action=clear" class="btn btn-outline-danger me-2"
                                               onclick="return confirm('Are you sure you want to clear your cart?')">
                                                <i class="fas fa-trash me-2"></i>Clear Cart
                                            </a>
                                            <button class="btn btn-secondary" onclick="updateAllQuantities()">
                                                <i class="fas fa-sync me-2"></i>Update Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cart Summary -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="summary-row d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span><?php echo formatCurrency($cart_total['subtotal']); ?></span>
                                </div>
                                
                                <div class="summary-row d-flex justify-content-between mb-2">
                                    <span>Tax (<?php echo $cart_total['tax_rate']; ?>%):</span>
                                    <span><?php echo formatCurrency($cart_total['tax']); ?></span>
                                </div>
                                
                                <div class="summary-row d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span>
                                        <?php if ($cart_total['shipping'] > 0): ?>
                                            <?php echo formatCurrency($cart_total['shipping']); ?>
                                        <?php else: ?>
                                            <span class="text-success">FREE</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                
                                <?php if ($cart_total['shipping'] > 0): ?>
                                    <div class="free-shipping-info mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Add <?php echo formatCurrency($cart_total['free_shipping_threshold'] - $cart_total['subtotal']); ?> 
                                            more for FREE shipping
                                        </small>
                                    </div>
                                <?php endif; ?>
                                
                                <hr>
                                
                                <div class="summary-row d-flex justify-content-between mb-3">
                                    <strong>Total:</strong>
                                    <strong class="text-primary"><?php echo formatCurrency($cart_total['total']); ?></strong>
                                </div>
                                
                                <!-- Coupon Code -->
                                <div class="coupon-section mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Coupon code" id="couponCode">
                                        <button class="btn btn-outline-secondary" type="button" onclick="applyCoupon()">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <a href="?page=cart&action=checkout" class="btn btn-primary btn-lg">
                                        <i class="fas fa-lock me-2"></i>Proceed to Checkout
                                    </a>
                                </div>
                                
                                <!-- Security Badges -->
                                <div class="security-badges text-center mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>Secure Checkout
                                    </small>
                                    <div class="payment-methods mt-2">
                                        <i class="fab fa-cc-visa text-muted me-1"></i>
                                        <i class="fab fa-cc-mastercard text-muted me-1"></i>
                                        <i class="fab fa-cc-paypal text-muted me-1"></i>
                                        <i class="fab fa-cc-stripe text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recently Viewed -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">Recently Viewed</h6>
                            </div>
                            <div class="card-body">
                                <div class="recently-viewed">
                                    <!-- This would be populated by JavaScript from localStorage -->
                                    <p class="text-muted small">No recently viewed items</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Empty Cart -->
                <div class="empty-cart text-center py-5">
                    <div class="empty-cart-icon mb-4">
                        <i class="fas fa-shopping-cart fa-5x text-muted"></i>
                    </div>
                    <h3 class="mb-3">Your cart is empty</h3>
                    <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
                    <a href="?page=products" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function updateQuantity(cartId, newQuantity) {
    if (newQuantity < 1) {
        removeFromCart(cartId);
        return;
    }
    
    updateCartQuantity(cartId, newQuantity);
}

function updateAllQuantities() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    
    quantityInputs.forEach(input => {
        const cartId = input.dataset.cartId;
        const quantity = parseInt(input.value);
        
        if (quantity > 0) {
            updateCartQuantity(cartId, quantity);
        }
    });
    
    // Reload page after a short delay
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function applyCoupon() {
    const couponCode = document.getElementById('couponCode').value.trim();
    
    if (!couponCode) {
        showNotification('Please enter a coupon code', 'error');
        return;
    }
    
    $.ajax({
        url: 'api/coupon.php',
        method: 'POST',
        data: {
            action: 'apply',
            coupon_code: couponCode
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification('Coupon applied successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(response.message || 'Invalid coupon code', 'error');
            }
        },
        error: function() {
            showNotification('Something went wrong. Please try again.', 'error');
        }
    });
}

// Update cart total dynamically
function updateCartTotal() {
    // This would make an AJAX call to recalculate totals
    $.get('api/cart-total.php', function(data) {
        if (data.success) {
            // Update the summary section
            location.reload();
        }
    });
}

// Auto-save quantity changes
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('change', function() {
        const cartId = this.dataset.cartId;
        const quantity = parseInt(this.value);
        
        if (quantity > 0) {
            updateCartQuantity(cartId, quantity);
        }
    });
});

// Load recently viewed items
$(document).ready(function() {
    loadRecentlyViewed();
});

function loadRecentlyViewed() {
    const recentlyViewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');
    
    if (recentlyViewed.length > 0) {
        let html = '';
        recentlyViewed.slice(0, 3).forEach(item => {
            html += `
                <div class="recently-viewed-item d-flex mb-2">
                    <img src="assets/uploads/products/${item.image}" class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <a href="?page=products&action=details&id=${item.id}" class="text-decoration-none">
                            <small class="text-dark">${item.name}</small>
                        </a>
                        <br><small class="text-primary">${item.price}</small>
                    </div>
                </div>
            `;
        });
        
        document.querySelector('.recently-viewed').innerHTML = html;
    }
}
</script>

<style>
.cart-item {
    transition: all 0.3s ease;
}

.cart-item:hover {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin: -15px;
}

.quantity-controls .input-group {
    width: 120px;
}

.summary-row {
    font-size: 0.95rem;
}

.empty-cart-icon {
    opacity: 0.3;
}

.security-badges {
    border-top: 1px solid #dee2e6;
    padding-top: 15px;
}

.payment-methods i {
    font-size: 1.2rem;
}

.recently-viewed-item img {
    border-radius: 4px;
}
</style>
