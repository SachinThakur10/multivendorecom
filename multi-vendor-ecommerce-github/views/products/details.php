<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?page=home">Home</a></li>
            <li class="breadcrumb-item"><a href="?page=products">Products</a></li>
            <li class="breadcrumb-item active"><?php echo $product['name']; ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="product-images">
                <!-- Main Image -->
                <div class="main-image mb-3">
                    <img id="mainImage" src="assets/uploads/products/<?php echo $product['images'][0]['image_url'] ?? 'default.jpg'; ?>" 
                         alt="<?php echo $product['name']; ?>" class="img-fluid rounded shadow">
                </div>
                
                <!-- Thumbnail Images -->
                <?php if (count($product['images']) > 1): ?>
                    <div class="thumbnail-images">
                        <div class="row g-2">
                            <?php foreach ($product['images'] as $index => $image): ?>
                                <div class="col-3">
                                    <img src="assets/uploads/products/<?php echo $image['image_url']; ?>" 
                                         alt="<?php echo $image['alt_text']; ?>" 
                                         class="img-fluid rounded thumbnail-img <?php echo $index === 0 ? 'active' : ''; ?>"
                                         onclick="changeMainImage(this.src)">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="col-lg-6 col-md-6">
            <div class="product-details">
                <h1 class="product-title"><?php echo $product['name']; ?></h1>
                
                <div class="vendor-info mb-3">
                    <span class="text-muted">Sold by: </span>
                    <a href="?page=vendor&action=profile&id=<?php echo $product['vendor_id']; ?>" class="text-decoration-none">
                        <strong><?php echo $product['shop_name']; ?></strong>
                    </a>
                </div>
                
                <!-- Rating -->
                <?php if ($product['avg_rating']): ?>
                    <div class="rating mb-3">
                        <div class="d-flex align-items-center">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= $product['avg_rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                            <?php endfor; ?>
                            <span class="ms-2"><?php echo number_format($product['avg_rating'], 1); ?></span>
                            <span class="text-muted ms-1">(<?php echo $product['review_count']; ?> reviews)</span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Price -->
                <div class="price mb-4">
                    <?php if ($product['sale_price']): ?>
                        <span class="h3 text-primary fw-bold"><?php echo formatCurrency($product['sale_price']); ?></span>
                        <span class="h5 text-muted text-decoration-line-through ms-3"><?php echo formatCurrency($product['price']); ?></span>
                        <span class="badge bg-danger ms-2">
                            <?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>% OFF
                        </span>
                    <?php else: ?>
                        <span class="h3 text-primary fw-bold"><?php echo formatCurrency($product['price']); ?></span>
                    <?php endif; ?>
                </div>
                
                <!-- Short Description -->
                <?php if ($product['short_description']): ?>
                    <div class="short-description mb-4">
                        <p class="lead"><?php echo $product['short_description']; ?></p>
                    </div>
                <?php endif; ?>
                
                <!-- Product Attributes -->
                <?php if (!empty($product['attributes'])): ?>
                    <div class="product-attributes mb-4">
                        <?php 
                        $groupedAttributes = [];
                        foreach ($product['attributes'] as $attr) {
                            $groupedAttributes[$attr['attribute_name']][] = $attr;
                        }
                        ?>
                        
                        <?php foreach ($groupedAttributes as $attrName => $attributes): ?>
                            <div class="attribute-group mb-3">
                                <label class="form-label fw-bold"><?php echo ucfirst($attrName); ?>:</label>
                                <div class="attribute-options">
                                    <?php foreach ($attributes as $attr): ?>
                                        <button type="button" class="btn btn-outline-secondary btn-sm me-2 mb-2 attribute-btn"
                                                data-attribute="<?php echo $attrName; ?>" 
                                                data-value="<?php echo $attr['attribute_value']; ?>"
                                                data-price="<?php echo $attr['price_adjustment']; ?>">
                                            <?php echo $attr['attribute_value']; ?>
                                            <?php if ($attr['price_adjustment'] > 0): ?>
                                                <small>(+<?php echo formatCurrency($attr['price_adjustment']); ?>)</small>
                                            <?php endif; ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Stock Status -->
                <div class="stock-status mb-4">
                    <?php if ($product['manage_stock']): ?>
                        <?php if ($product['stock_quantity'] > 0): ?>
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-check-circle me-1"></i>In Stock (<?php echo $product['stock_quantity']; ?> available)
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger fs-6">
                                <i class="fas fa-times-circle me-1"></i>Out of Stock
                            </span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="badge bg-success fs-6">
                            <i class="fas fa-check-circle me-1"></i>In Stock
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- Quantity and Actions -->
                <div class="product-actions">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Quantity:</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(-1)">-</button>
                                <input type="number" class="form-control text-center" id="quantity" value="1" min="1" 
                                       max="<?php echo $product['manage_stock'] ? $product['stock_quantity'] : 999; ?>">
                                <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(1)">+</button>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2 d-md-flex">
                                <?php if (!$product['manage_stock'] || $product['stock_quantity'] > 0): ?>
                                    <button class="btn btn-primary btn-lg flex-fill" onclick="addToCartWithAttributes()">
                                        <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                    </button>
                                    <button class="btn btn-success btn-lg flex-fill" onclick="buyNow()">
                                        <i class="fas fa-bolt me-2"></i>Buy Now
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-lg flex-fill" disabled>
                                        <i class="fas fa-times me-2"></i>Out of Stock
                                    </button>
                                <?php endif; ?>
                                
                                <button class="btn btn-outline-primary btn-lg wishlist-btn" 
                                        data-product-id="<?php echo $product['id']; ?>" 
                                        onclick="addToWishlist(<?php echo $product['id']; ?>)">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Product Features -->
                <div class="product-features mt-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="feature-item text-center p-3 border rounded">
                                <i class="fas fa-shipping-fast text-primary mb-2"></i>
                                <div class="small">Free Shipping</div>
                                <div class="text-muted small">On orders above ₹500</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="feature-item text-center p-3 border rounded">
                                <i class="fas fa-undo text-primary mb-2"></i>
                                <div class="small">Easy Returns</div>
                                <div class="text-muted small">7 days return policy</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Details Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button">
                        Description
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button">
                        Specifications
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">
                        Reviews (<?php echo count($reviews); ?>)
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="productTabsContent">
                <!-- Description -->
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <div class="p-4">
                        <?php if ($product['description']): ?>
                            <?php echo nl2br($product['description']); ?>
                        <?php else: ?>
                            <p class="text-muted">No description available for this product.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Specifications -->
                <div class="tab-pane fade" id="specifications" role="tabpanel">
                    <div class="p-4">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td><strong>SKU</strong></td>
                                        <td><?php echo $product['sku'] ?: 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Weight</strong></td>
                                        <td><?php echo $product['weight'] ? $product['weight'] . ' kg' : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Dimensions</strong></td>
                                        <td><?php echo $product['dimensions'] ?: 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Category</strong></td>
                                        <td><?php echo $product['category_name']; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Reviews -->
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <div class="p-4">
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-item border-bottom pb-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?php echo $review['user_name']; ?></h6>
                                            <div class="rating mb-2">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <small class="text-muted"><?php echo formatDate($review['created_at']); ?></small>
                                    </div>
                                    
                                    <?php if ($review['title']): ?>
                                        <h6 class="fw-bold"><?php echo $review['title']; ?></h6>
                                    <?php endif; ?>
                                    
                                    <p class="mb-0"><?php echo $review['comment']; ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                        <?php endif; ?>
                        
                        <?php if (isLoggedIn()): ?>
                            <div class="mt-4">
                                <h5>Write a Review</h5>
                                <form id="reviewForm" method="POST" action="api/reviews.php">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Rating</label>
                                        <div class="rating-input">
                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                                <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" required>
                                                <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" placeholder="Review title">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Comment</label>
                                        <textarea class="form-control" name="comment" rows="4" placeholder="Write your review..." required></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Submit Review</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h4 class="mb-4">Related Products</h4>
                <div class="row g-4">
                    <?php foreach ($related_products as $relatedProduct): ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="product-card card h-100 shadow-sm">
                                <div class="product-image position-relative">
                                    <img src="assets/uploads/products/<?php echo $relatedProduct['primary_image'] ?? 'default.jpg'; ?>" 
                                         alt="<?php echo $relatedProduct['name']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                                    
                                    <div class="product-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center opacity-0">
                                        <div class="btn-group">
                                            <button class="btn btn-primary btn-sm" onclick="addToCart(<?php echo $relatedProduct['id']; ?>)">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                            <a href="?page=products&action=details&id=<?php echo $relatedProduct['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="?page=products&action=details&id=<?php echo $relatedProduct['id']; ?>" class="text-decoration-none text-dark">
                                            <?php echo $relatedProduct['name']; ?>
                                        </a>
                                    </h6>
                                    <div class="price">
                                        <?php if ($relatedProduct['sale_price']): ?>
                                            <span class="text-primary fw-bold"><?php echo formatCurrency($relatedProduct['sale_price']); ?></span>
                                            <span class="text-muted text-decoration-line-through ms-2"><?php echo formatCurrency($relatedProduct['price']); ?></span>
                                        <?php else: ?>
                                            <span class="text-primary fw-bold"><?php echo formatCurrency($relatedProduct['price']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.thumbnail-img {
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.thumbnail-img.active,
.thumbnail-img:hover {
    opacity: 1;
    border: 2px solid var(--primary-color);
}

.attribute-btn.active {
    background-color: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating-input input {
    display: none;
}

.rating-input label {
    cursor: pointer;
    color: #ddd;
    font-size: 1.5rem;
    margin-right: 5px;
}

.rating-input label:hover,
.rating-input label:hover ~ label,
.rating-input input:checked ~ label {
    color: #ffc107;
}
</style>

<script>
let selectedAttributes = {};

function changeMainImage(src) {
    document.getElementById('mainImage').src = src;
    
    // Update thumbnail active state
    document.querySelectorAll('.thumbnail-img').forEach(img => {
        img.classList.remove('active');
    });
    event.target.classList.add('active');
}

function changeQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    const newValue = currentValue + change;
    const maxValue = parseInt(quantityInput.max);
    
    if (newValue >= 1 && newValue <= maxValue) {
        quantityInput.value = newValue;
    }
}

function addToCartWithAttributes() {
    const quantity = parseInt(document.getElementById('quantity').value);
    const productId = <?php echo $product['id']; ?>;
    
    addToCart(productId, quantity, selectedAttributes);
}

function buyNow() {
    const quantity = parseInt(document.getElementById('quantity').value);
    const productId = <?php echo $product['id']; ?>;
    
    // Add to cart first, then redirect to checkout
    $.ajax({
        url: 'api/cart.php',
        method: 'POST',
        data: {
            action: 'add',
            product_id: productId,
            quantity: quantity,
            attributes: JSON.stringify(selectedAttributes)
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                window.location.href = '?page=cart&action=checkout';
            } else {
                showNotification(response.message || 'Failed to add product to cart', 'error');
            }
        },
        error: function() {
            showNotification('Something went wrong. Please try again.', 'error');
        }
    });
}

// Handle attribute selection
document.querySelectorAll('.attribute-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const attribute = this.dataset.attribute;
        const value = this.dataset.value;
        const price = parseFloat(this.dataset.price) || 0;
        
        // Remove active class from other buttons in same group
        document.querySelectorAll(`[data-attribute="${attribute}"]`).forEach(b => {
            b.classList.remove('active');
        });
        
        // Add active class to clicked button
        this.classList.add('active');
        
        // Store selected attribute
        selectedAttributes[attribute] = {
            value: value,
            price_adjustment: price
        };
    });
});

// Handle review form submission
document.getElementById('reviewForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('api/reviews.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Review submitted successfully!', 'success');
            setTimeout(() => location.reload(), 2000);
        } else {
            showNotification(data.message || 'Failed to submit review', 'error');
        }
    })
    .catch(error => {
        showNotification('Something went wrong. Please try again.', 'error');
    });
});
</script>
