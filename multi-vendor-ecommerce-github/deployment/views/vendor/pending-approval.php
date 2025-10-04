<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow">
                <div class="card-body text-center p-5">
                    <div class="approval-icon mb-4">
                        <i class="fas fa-clock fa-5x text-warning"></i>
                    </div>
                    
                    <h2 class="mb-3">Vendor Application Under Review</h2>
                    
                    <?php if ($vendor && $vendor['approval_status'] === 'pending'): ?>
                        <div class="alert alert-info">
                            <h5 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>Application Submitted Successfully
                            </h5>
                            <p class="mb-0">
                                Thank you for applying to become a vendor on <?php echo SITE_NAME; ?>. 
                                Your application is currently under review by our team.
                            </p>
                        </div>
                        
                        <div class="vendor-details bg-light p-4 rounded mb-4">
                            <h5 class="mb-3">Your Application Details:</h5>
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <p><strong>Shop Name:</strong> <?php echo $vendor['shop_name']; ?></p>
                                    <p><strong>Email:</strong> <?php echo $vendor['email']; ?></p>
                                    <p><strong>Phone:</strong> <?php echo $vendor['phone']; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>City:</strong> <?php echo $vendor['city']; ?></p>
                                    <p><strong>State:</strong> <?php echo $vendor['state']; ?></p>
                                    <p><strong>Applied On:</strong> <?php echo formatDate($vendor['created_at']); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timeline mb-4">
                            <h5 class="mb-3">Application Process</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="timeline-item completed">
                                        <div class="timeline-icon">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <h6>Application Submitted</h6>
                                        <small class="text-muted">Completed</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="timeline-item active">
                                        <div class="timeline-icon">
                                            <i class="fas fa-search"></i>
                                        </div>
                                        <h6>Under Review</h6>
                                        <small class="text-warning">In Progress</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="timeline-item">
                                        <div class="timeline-icon">
                                            <i class="fas fa-store"></i>
                                        </div>
                                        <h6>Start Selling</h6>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    <?php elseif ($vendor && $vendor['approval_status'] === 'rejected'): ?>
                        <div class="alert alert-danger">
                            <h5 class="alert-heading">
                                <i class="fas fa-times-circle me-2"></i>Application Rejected
                            </h5>
                            <p class="mb-0">
                                Unfortunately, your vendor application has been rejected. 
                                Please contact our support team for more information.
                            </p>
                        </div>
                        
                        <div class="mt-4">
                            <a href="mailto:<?php echo ADMIN_EMAIL; ?>" class="btn btn-primary me-2">
                                <i class="fas fa-envelope me-2"></i>Contact Support
                            </a>
                            <a href="?page=vendor&action=profile" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Update Application
                            </a>
                        </div>
                        
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>Incomplete Application
                            </h5>
                            <p class="mb-0">
                                Your vendor application is incomplete. Please complete your profile to proceed.
                            </p>
                        </div>
                        
                        <div class="mt-4">
                            <a href="?page=vendor&action=profile" class="btn btn-primary">
                                <i class="fas fa-user-edit me-2"></i>Complete Profile
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="what-next mt-5">
                        <h5 class="mb-3">What happens next?</h5>
                        <div class="row text-start">
                            <div class="col-md-6">
                                <div class="feature-item mb-3">
                                    <i class="fas fa-search-plus text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Document Verification</h6>
                                        <small class="text-muted">We'll verify your business documents and information</small>
                                    </div>
                                </div>
                                <div class="feature-item mb-3">
                                    <i class="fas fa-phone text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Contact Verification</h6>
                                        <small class="text-muted">Our team may contact you for additional information</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-item mb-3">
                                    <i class="fas fa-check-circle text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Approval Decision</h6>
                                        <small class="text-muted">You'll receive an email with our decision</small>
                                    </div>
                                </div>
                                <div class="feature-item mb-3">
                                    <i class="fas fa-rocket text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Start Selling</h6>
                                        <small class="text-muted">Once approved, you can start adding products</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-info mt-5 pt-4 border-top">
                        <h6 class="mb-3">Need Help?</h6>
                        <p class="text-muted mb-3">
                            If you have any questions about your application or the vendor process, 
                            please don't hesitate to contact us.
                        </p>
                        <div class="contact-buttons">
                            <a href="mailto:<?php echo ADMIN_EMAIL; ?>" class="btn btn-outline-primary me-2">
                                <i class="fas fa-envelope me-2"></i>Email Support
                            </a>
                            <a href="?page=help" class="btn btn-outline-secondary">
                                <i class="fas fa-question-circle me-2"></i>Help Center
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.approval-icon {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.timeline {
    position: relative;
}

.timeline-item {
    text-align: center;
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 30px;
    right: -50%;
    width: 100%;
    height: 2px;
    background-color: #dee2e6;
    z-index: -1;
}

.timeline-item.completed::after {
    background-color: #28a745;
}

.timeline-item.active::after {
    background: linear-gradient(to right, #28a745 50%, #dee2e6 50%);
}

.timeline-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #dee2e6;
    color: #6c757d;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 15px;
}

.timeline-item.completed .timeline-icon {
    background-color: #28a745;
    color: white;
}

.timeline-item.active .timeline-icon {
    background-color: #ffc107;
    color: white;
    animation: pulse 2s infinite;
}

.feature-item {
    display: flex;
    align-items: flex-start;
}

.feature-item i {
    margin-top: 2px;
    font-size: 1.2rem;
}

.vendor-details {
    border-left: 4px solid var(--primary-color);
}

@media (max-width: 768px) {
    .timeline-item:not(:last-child)::after {
        display: none;
    }
    
    .timeline-item {
        margin-bottom: 2rem;
    }
    
    .feature-item {
        margin-bottom: 1.5rem !important;
    }
}
</style>
