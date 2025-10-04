<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="admin-profile text-center mb-4">
                        <div class="admin-avatar mb-3">
                            <i class="fas fa-user-shield fa-3x text-primary"></i>
                        </div>
                        <h5 class="mb-1">Admin Panel</h5>
                        <p class="text-muted small"><?php echo SITE_NAME; ?></p>
                    </div>
                    
                    <nav class="admin-nav">
                        <ul class="nav nav-pills flex-column">
                            <li class="nav-item">
                                <a class="nav-link active" href="?page=admin">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?page=admin&action=vendors">
                                    <i class="fas fa-store me-2"></i>Vendors
                                    <?php if ($stats['pending_vendors'] > 0): ?>
                                        <span class="badge bg-warning ms-auto"><?php echo $stats['pending_vendors']; ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?page=admin&action=products">
                                    <i class="fas fa-box me-2"></i>Products
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?page=admin&action=orders">
                                    <i class="fas fa-shopping-bag me-2"></i>Orders
                                    <?php if ($stats['pending_orders'] > 0): ?>
                                        <span class="badge bg-danger ms-auto"><?php echo $stats['pending_orders']; ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?page=admin&action=customers">
                                    <i class="fas fa-users me-2"></i>Customers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?page=admin&action=categories">
                                    <i class="fas fa-tags me-2"></i>Categories
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?page=admin&action=reports">
                                    <i class="fas fa-chart-bar me-2"></i>Reports
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="?page=admin&action=settings">
                                    <i class="fas fa-cog me-2"></i>Settings
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
                <h2 class="mb-1">Admin Dashboard</h2>
                <p class="text-muted">Manage your e-commerce platform from here.</p>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card dashboard-card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-1"><?php echo $stats['total_users']; ?></h3>
                                    <p class="mb-0">Total Users</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-users fa-2x opacity-75"></i>
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
                                    <h3 class="mb-1"><?php echo $stats['total_vendors']; ?></h3>
                                    <p class="mb-0">Active Vendors</p>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-store fa-2x opacity-75"></i>
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
                    <div class="card dashboard-card text-white" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
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
            </div>
            
            <!-- Sales Overview -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Sales Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="sales-stat">
                                        <h4 class="text-success"><?php echo formatCurrency($stats['total_sales']); ?></h4>
                                        <p class="text-muted mb-0">Total Sales</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="sales-stat">
                                        <h4 class="text-primary"><?php echo formatCurrency($stats['monthly_sales']); ?></h4>
                                        <p class="text-muted mb-0">This Month</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="sales-stat">
                                        <h4 class="text-info"><?php echo formatCurrency($stats['monthly_sales'] / max(1, date('j'))); ?></h4>
                                        <p class="text-muted mb-0">Daily Average</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Stats</h5>
                        </div>
                        <div class="card-body">
                            <div class="quick-stat-item d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Pending Vendors</span>
                                <span class="badge bg-warning"><?php echo $stats['pending_vendors']; ?></span>
                            </div>
                            <div class="quick-stat-item d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Pending Orders</span>
                                <span class="badge bg-danger"><?php echo $stats['pending_orders']; ?></span>
                            </div>
                            <div class="quick-stat-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Active Products</span>
                                <span class="badge bg-success"><?php echo $stats['total_products']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Recent Orders -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Orders</h5>
                            <a href="?page=admin&action=orders" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recent_orders)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_orders as $order): ?>
                                                <tr>
                                                    <td>
                                                        <small class="text-muted"><?php echo $order['order_number']; ?></small>
                                                    </td>
                                                    <td><?php echo $order['customer_name']; ?></td>
                                                    <td><?php echo formatCurrency($order['total_amount']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $order['status'] === 'pending' ? 'warning' : ($order['status'] === 'delivered' ? 'success' : 'primary'); ?>">
                                                            <?php echo ucfirst($order['status']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No recent orders</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Vendors -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Vendors</h5>
                            <a href="?page=admin&action=vendors" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recent_vendors)): ?>
                                <?php foreach ($recent_vendors as $vendor): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="vendor-avatar me-3">
                                            <i class="fas fa-store fa-2x text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo $vendor['shop_name']; ?></h6>
                                            <small class="text-muted"><?php echo $vendor['name']; ?> • <?php echo $vendor['city']; ?></small>
                                        </div>
                                        <div>
                                            <span class="badge bg-<?php echo $vendor['approval_status'] === 'approved' ? 'success' : ($vendor['approval_status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                                <?php echo ucfirst($vendor['approval_status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-store fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No recent vendors</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Products -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Products</h5>
                            <a href="?page=admin&action=products" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recent_products)): ?>
                                <div class="row">
                                    <?php foreach ($recent_products as $product): ?>
                                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                            <div class="product-card">
                                                <img src="assets/uploads/products/<?php echo $product['primary_image'] ?? 'default.jpg'; ?>" 
                                                     alt="<?php echo $product['name']; ?>" class="product-image">
                                                <div class="product-info">
                                                    <h6 class="product-name"><?php echo substr($product['name'], 0, 30) . '...'; ?></h6>
                                                    <p class="text-muted small mb-1"><?php echo $product['shop_name']; ?></p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-primary fw-bold"><?php echo formatCurrency($product['price']); ?></span>
                                                        <small class="text-muted">Stock: <?php echo $product['stock_quantity']; ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No recent products</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-card {
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

.admin-nav .nav-link {
    color: #6c757d;
    border-radius: 8px;
    margin-bottom: 5px;
    transition: all 0.3s ease;
}

.admin-nav .nav-link:hover,
.admin-nav .nav-link.active {
    background-color: var(--primary-color);
    color: white;
}

.admin-nav .nav-link i {
    width: 20px;
}

.welcome-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 2rem;
    border-radius: 15px;
    border-left: 5px solid var(--primary-color);
}

.sales-stat {
    padding: 1rem;
    border-right: 1px solid #dee2e6;
}

.sales-stat:last-child {
    border-right: none;
}

.quick-stat-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.quick-stat-item:last-child {
    border-bottom: none;
}

.product-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.product-image {
    width: 100%;
    height: 120px;
    object-fit: cover;
}

.product-info {
    padding: 0.75rem;
}

.product-name {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.vendor-avatar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    border-radius: 50%;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    font-size: 0.875rem;
}

.table td {
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .dashboard-card h3 {
        font-size: 1.5rem;
    }
    
    .stat-icon i {
        font-size: 1.5rem !important;
    }
    
    .sales-stat {
        border-right: none;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 1rem;
    }
    
    .sales-stat:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
}
</style>
