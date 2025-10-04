    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-store me-2"></i>StyleHub
                    </h5>
                    <p class="text-muted">Your one-stop destination for fashion from multiple vendors. Discover the latest trends and styles from trusted sellers.</p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="?page=home" class="text-muted text-decoration-none">Home</a></li>
                        <li><a href="?page=products" class="text-muted text-decoration-none">Products</a></li>
                        <li><a href="?page=about" class="text-muted text-decoration-none">About Us</a></li>
                        <li><a href="?page=contact" class="text-muted text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="?page=products&category=men" class="text-muted text-decoration-none">Men</a></li>
                        <li><a href="?page=products&category=women" class="text-muted text-decoration-none">Women</a></li>
                        <li><a href="?page=products&category=kids" class="text-muted text-decoration-none">Kids</a></li>
                        <li><a href="?page=products&category=accessories" class="text-muted text-decoration-none">Accessories</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="?page=help" class="text-muted text-decoration-none">Help Center</a></li>
                        <li><a href="?page=shipping" class="text-muted text-decoration-none">Shipping Info</a></li>
                        <li><a href="?page=returns" class="text-muted text-decoration-none">Returns</a></li>
                        <li><a href="?page=faq" class="text-muted text-decoration-none">FAQ</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Sell With Us</h6>
                    <ul class="list-unstyled">
                        <li><a href="?page=auth&action=register" class="text-muted text-decoration-none">Become a Vendor</a></li>
                        <li><a href="?page=vendor-guide" class="text-muted text-decoration-none">Seller Guide</a></li>
                        <li><a href="?page=vendor-support" class="text-muted text-decoration-none">Seller Support</a></li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> StyleHub. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="payment-methods">
                        <i class="fab fa-cc-visa text-muted me-2"></i>
                        <i class="fab fa-cc-mastercard text-muted me-2"></i>
                        <i class="fab fa-cc-paypal text-muted me-2"></i>
                        <i class="fab fa-cc-stripe text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    
    <!-- Load cart count -->
    <script>
        $(document).ready(function() {
            loadCartCount();
        });
        
        function loadCartCount() {
            $.get('api/cart-count.php', function(data) {
                $('#cart-count').text(data.count || 0);
            });
        }
    </script>
</body>
</html>
