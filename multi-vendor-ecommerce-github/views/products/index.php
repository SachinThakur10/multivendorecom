<div class="container py-4">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filters
                    </h5>
                </div>
                <div class="card-body">
                    <form id="filterForm" method="GET">
                        <input type="hidden" name="page" value="products">
                        
                        <!-- Search -->
                        <div class="mb-3">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" name="search" 
                                   value="<?php echo $_GET['search'] ?? ''; ?>" placeholder="Search products...">
                        </div>
                        
                        <!-- Categories -->
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['slug']; ?>" 
                                            <?php echo (($_GET['category'] ?? '') === $category['slug']) ? 'selected' : ''; ?>>
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label">Price Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" class="form-control" name="price_min" 
                                           placeholder="Min" value="<?php echo $_GET['price_min'] ?? ''; ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" name="price_max" 
                                           placeholder="Max" value="<?php echo $_GET['price_max'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Vendors -->
                        <div class="mb-3">
                            <label class="form-label">Vendor</label>
                            <select class="form-select" name="vendor">
                                <option value="">All Vendors</option>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?php echo $vendor['id']; ?>" 
                                            <?php echo (($_GET['vendor'] ?? '') == $vendor['id']) ? 'selected' : ''; ?>>
                                        <?php echo $vendor['shop_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="?page=products" class="btn btn-outline-secondary">Clear Filters</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-lg-9 col-md-8">
            <!-- Results Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Products</h4>
                    <p class="text-muted mb-0">
                        Showing <?php echo count($products); ?> of <?php echo $pagination['total_pages'] * PRODUCTS_PER_PAGE; ?> products
                    </p>
                </div>
                
                <div class="d-flex gap-2">
                    <select class="form-select" style="width: auto;" onchange="applySorting(this.value)">
                        <option value="">Sort by</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="name">Name: A to Z</option>
                        <option value="newest">Newest First</option>
                        <option value="rating">Highest Rated</option>
                    </select>
                    
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary active" onclick="setView('grid')">
                            <i class="fas fa-th"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="setView('list')">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Products -->
            <?php if (!empty($products)): ?>
                <div class="row g-4" id="productsGrid">
                    <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="product-card card h-100 shadow-sm">
                                <div class="product-image position-relative">
                                    <img src="assets/uploads/products/<?php echo $product['primary_image'] ?? 'default.jpg'; ?>" 
                                         alt="<?php echo $product['name']; ?>" class="card-img-top" style="height: 250px; object-fit: cover;">
                                    
                                    <?php if ($product['sale_price']): ?>
                                        <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                            <?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>% OFF
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($product['stock_quantity'] <= 0 && $product['manage_stock']): ?>
                                        <span class="badge bg-secondary position-absolute top-0 end-0 m-2">Out of Stock</span>
                                    <?php endif; ?>
                                    
                                    <div class="product-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center opacity-0">
                                        <div class="btn-group">
                                            <?php if ($product['stock_quantity'] > 0 || !$product['manage_stock']): ?>
                                                <button class="btn btn-primary btn-sm" onclick="addToCart(<?php echo $product['id']; ?>)">
                                                    <i class="fas fa-cart-plus"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-outline-primary btn-sm wishlist-btn" 
                                                    data-product-id="<?php echo $product['id']; ?>" 
                                                    onclick="addToWishlist(<?php echo $product['id']; ?>)">
                                                <i class="far fa-heart"></i>
                                            </button>
                                            <a href="?page=products&action=details&id=<?php echo $product['id']; ?>" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="?page=products&action=details&id=<?php echo $product['id']; ?>" 
                                           class="text-decoration-none text-dark">
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
                                    
                                    <?php if ($product['manage_stock']): ?>
                                        <div class="stock-info">
                                            <?php if ($product['stock_quantity'] > 0): ?>
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>In Stock (<?php echo $product['stock_quantity']; ?>)
                                                </small>
                                            <?php else: ?>
                                                <small class="text-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Out of Stock
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <nav class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagination['has_prev']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page_num' => $pagination['current_page'] - 1])); ?>">
                                        Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page_num' => $i])); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['has_next']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page_num' => $pagination['current_page'] + 1])); ?>">
                                        Next
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4>No products found</h4>
                    <p class="text-muted">Try adjusting your search or filter criteria</p>
                    <a href="?page=products" class="btn btn-primary">View All Products</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function applySorting(sortBy) {
    if (sortBy) {
        const url = new URL(window.location);
        url.searchParams.set('sort', sortBy);
        window.location.href = url.toString();
    }
}

function setView(viewType) {
    const grid = document.getElementById('productsGrid');
    const buttons = document.querySelectorAll('.btn-group .btn');
    
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    if (viewType === 'list') {
        grid.className = 'row g-3';
        grid.querySelectorAll('.col-lg-4').forEach(col => {
            col.className = 'col-12';
        });
    } else {
        grid.className = 'row g-4';
        grid.querySelectorAll('.col-12').forEach(col => {
            col.className = 'col-lg-4 col-md-6 col-sm-6';
        });
    }
}
</script>
