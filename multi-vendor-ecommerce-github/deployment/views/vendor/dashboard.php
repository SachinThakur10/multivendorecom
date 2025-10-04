<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="vendor-profile text-center mb-4">
                        <img src="assets/uploads/vendors/<?php echo $vendor['logo'] ?? 'default.jpg'; ?>" 
                             alt="<?php echo $vendor['shop_name']; ?>" class="rounded-circle mb-3" 
                             style="width: 80px; height: 80px; object-fit: cover;">
                        <h5 class="mb-1"><?php echo $vendor['shop_name']; ?></h5>
                        <p class="text-muted small"><?php echo $vendor['city'] . ', ' . $vendor['state']; ?></p>
                        <span class="badge bg-success">Approved Vendor</span>
                    </div>
                    
                    <nav class="vendor-nav">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a class="nav-link active" href="?page=vendor">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?page=vendor&action=products">
                                    <i class="fas fa-box me-2"></i>My Products
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?page=vendor&action=addProduct">
                                    <i class="fas fa-plus me-2"></i>Add Product
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?page=vendor&action=orders">
                                    <i class="fas fa-shopping-bag me-2"></i>Orders
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?page=vendor&action=sales">
                                    <i class="fas fa-chart-line me-2"></i>Sales Report
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?page=vendor&action=profile">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <!-- Welcome Section -->
            <div class="welcome-section mb-4">
                <h2 class="mb-1">Welcome back, <?php echo $vendor['shop_name']; ?>!</h2>
                <p class="text-muted">Here's what's happening with your store today.</p>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card dashboard-card text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-1"><?php echo $stats['total_products']; ?></h3>
                                    <p class="mb-0">Total Products</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-box fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card dashboard-card text-white" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-1"><?php echo $stats['total_orders']; ?></h3>
                                    <p class="mb-0">Total Orders</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-shopping-bag fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card dashboard-card text-white" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-1"><?php echo formatCurrency($stats['total_sales']); ?></h3>
                                    <p class="mb-0">Total Sales</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-rupee-sign fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card dashboard-card text-white" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-1"><?php echo $stats['pending_orders']; ?></h3>
                                    <p class="mb-0">Pending Orders</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-clock fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Recent Orders -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Orders</h5>
                            <a href="?page=vendor&action=orders" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recent_orders)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Customer</th>
                                                <th>Status</th>
                                                <th>Total</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_orders as $order): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo $order['order_number']; ?></strong>
                                                    </td>
                                                    <td><?php echo $order['customer_name']; ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $order['status'] === 'pending' ? 'warning' : ($order['status'] === 'delivered' ? 'success' : 'primary'); ?>">
                                                            <?php echo ucfirst($order['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo formatCurrency($order['total_amount']); ?></td>
                                                    <td><?php echo formatDate($order['created_at']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                    <h5>No orders yet</h5>
                                    <p class="text-muted">Your orders will appear here once customers start purchasing.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Products -->
                <div class="col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Products</h5>
                            <a href="?page=vendor&action=products" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recent_products)): ?>
                                <?php foreach ($recent_products as $product): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="assets/uploads/products/<?php echo $product['primary_image'] ?? 'default.jpg'; ?>" 
                                             alt="<?php echo $product['name']; ?>" class="me-3 rounded" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo substr($product['name'], 0, 30) . '...'; ?></h6>
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">Stock: <?php echo $product['stock_quantity']; ?></small>
                                                <small class="text-primary fw-bold"><?php echo formatCurrency($product['price']); ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                    <h6>No products yet</h6>
                                    <p class="text-muted small">Start by adding your first product.</p>
                                    <a href="?page=vendor&action=addProduct" class="btn btn-primary btn-sm">Add Product</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <a href="?page=vendor&action=addProduct" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-plus mb-2"></i><br>
                                        Add New Product
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="?page=vendor&action=orders" class="btn btn-outline-success w-100">
                                        <i class="fas fa-eye mb-2"></i><br>
                                        View Orders
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="?page=vendor&action=sales" class="btn btn-outline-info w-100">
                                        <i class="fas fa-chart-bar mb-2"></i><br>
                                        Sales Report
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="?page=vendor&action=profile" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-cog mb-2"></i><br>
                                        Settings
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 15px;
    transition: transform 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    opacity: 0.7;
}

.vendor-nav .nav-link {
    color: #6c757d;
    border-radius: 8px;
    margin-bottom: 5px;
    transition: all 0.3s ease;
}

.vendor-nav .nav-link:hover,
.vendor-nav .nav-link.active {
    background-color: var(--primary-color);
    color: white;
}

.vendor-nav .nav-link i {
    width: 20px;
}

.welcome-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 2rem;
    border-radius: 15px;
    border-left: 5px solid var(--primary-color);
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.quick-actions .btn {
    height: 100px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}

@media (max-width: 768px) {
    .dashboard-card h3 {
        font-size: 1.5rem;
    }
    
    .stat-icon i {
        font-size: 1.5rem !important;
    }
}
</style>
