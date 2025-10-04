<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Discover Fashion from Multiple Vendors</h1>
                <p class="lead mb-4">Shop the latest trends from trusted sellers. Find unique styles, competitive prices, and quality products all in one place.</p>
                <div class="d-flex gap-3">
                    <a href="?page=products" class="btn btn-light btn-lg">Shop Now</a>
                    <a href="?page=auth&action=register" class="btn btn-outline-light btn-lg">Become a Vendor</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/hero-image.jpg" alt="Fashion Collection" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Shop by Category</h2>
            <p class="text-muted">Explore our wide range of fashion categories</p>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="category-card text-center">
                            <a href="?page=products&category=<?php echo $category['slug']; ?>" class="text-decoration-none">
                                <div class="category-image mb-3">
                                    <img src="assets/images/categories/<?php echo $category['image'] ?? 'default.jpg'; ?>" 
                                         alt="<?php echo $category['name']; ?>" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <h5 class="fw-bold text-dark"><?php echo $category['name']; ?></h5>
                                <p class="text-muted"><?php echo $category['description']; ?></p>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<?php if (!empty($featured_products)): ?>
<section class="featured-products py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Featured Products</h2>
            <p class="text-muted">Handpicked products from our top vendors</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="product-card card h-100 shadow-sm">
                        <div class="product-image position-relative">
                            <img src="assets/uploads/products/<?php echo $product['primary_image'] ?? 'default.jpg'; ?>" 
                                 alt="<?php echo $product['name']; ?>" class="card-img-top" style="height: 250px; object-fit: cover;">
                            
                            <?php if ($product['sale_price']): ?>
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                    <?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>% OFF
                                </span>
                            <?php endif; ?>
                            
                            <div class="product-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center opacity-0">
                                <div class="btn-group">
                                    <button class="btn btn-primary btn-sm" onclick="addToCart(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="addToWishlist(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <a href="?page=products&action=details&id=<?php echo $product['id']; ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <h6 class="card-title">
                                <a href="?page=products&action=details&id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark">
                                    <?php echo $product['name']; ?>
                                </a>
                            </h6>
                            <p class="text-muted small mb-2">by <?php echo $product['shop_name']; ?></p>
                            
                            <div class="price mb-2">
                                <?php if ($product['sale_price']): ?>
                                    <span class="text-primary fw-bold"><?php echo formatCurrency($product['sale_price']); ?></span>
                                    <span class="text-muted text-decoration-line-through ms-2"><?php echo formatCurrency($product['price']); ?></span>
                                <?php else: ?>
                                    <span class="text-primary fw-bold"><?php echo formatCurrency($product['price']); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($product['avg_rating']): ?>
                                <div class="rating mb-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $product['avg_rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                    <span class="text-muted small ms-1">(<?php echo $product['review_count']; ?>)</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="?page=products" class="btn btn-primary btn-lg">View All Products</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Top Vendors Section -->
<?php if (!empty($top_vendors)): ?>
<section class="top-vendors py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Top Vendors</h2>
            <p class="text-muted">Trusted sellers with quality products</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($top_vendors as $vendor): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="vendor-card card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="vendor-logo mb-3">
                                <img src="assets/uploads/vendors/<?php echo $vendor['logo'] ?? 'default.jpg'; ?>" 
                                     alt="<?php echo $vendor['shop_name']; ?>" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                            </div>
                            <h5 class="card-title"><?php echo $vendor['shop_name']; ?></h5>
                            <p class="text-muted"><?php echo substr($vendor['description'], 0, 100) . '...'; ?></p>
                            <div class="vendor-stats">
                                <span class="badge bg-primary me-2">
                                    <i class="fas fa-star me-1"></i>4.5
                                </span>
                                <span class="text-muted small">
                                    <i class="fas fa-map-marker-alt me-1"></i><?php echo $vendor['city']; ?>
                                </span>
                            </div>
                            <a href="?page=vendor&action=profile&id=<?php echo $vendor['id']; ?>" class="btn btn-outline-primary btn-sm mt-3">
                                View Store
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Newsletter Section -->
<section class="newsletter-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h3 class="fw-bold mb-2">Stay Updated</h3>
                <p class="mb-0">Subscribe to our newsletter for the latest deals and fashion trends</p>
            </div>
            <div class="col-lg-6">
                <form class="d-flex gap-2">
                    <input type="email" class="form-control" placeholder="Enter your email" required>
                    <button type="submit" class="btn btn-light">Subscribe</button>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.category-card:hover .category-image img {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

.product-card:hover .product-overlay {
    opacity: 1 !important;
    background: rgba(0,0,0,0.7);
    transition: opacity 0.3s ease;
}

.vendor-card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
}

.rating .fa-star {
    font-size: 0.8rem;
}
</style>
