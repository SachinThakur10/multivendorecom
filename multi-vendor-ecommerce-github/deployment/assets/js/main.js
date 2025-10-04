// Main JavaScript file for Multi-Vendor E-commerce Platform

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Load cart count on page load
    loadCartCount();
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
});

// Cart Functions
function addToCart(productId, quantity = 1, attributes = {}) {
    $.ajax({
        url: 'api/cart.php',
        method: 'POST',
        data: {
            action: 'add',
            product_id: productId,
            quantity: quantity,
            attributes: JSON.stringify(attributes)
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification('Product added to cart!', 'success');
                loadCartCount();
            } else {
                showNotification(response.message || 'Failed to add product to cart', 'error');
            }
        },
        error: function() {
            showNotification('Something went wrong. Please try again.', 'error');
        }
    });
}

function removeFromCart(cartId) {
    if (confirm('Are you sure you want to remove this item from cart?')) {
        $.ajax({
            url: 'api/cart.php',
            method: 'POST',
            data: {
                action: 'remove',
                cart_id: cartId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification('Item removed from cart', 'success');
                    loadCartCount();
                    location.reload(); // Reload cart page
                } else {
                    showNotification(response.message || 'Failed to remove item', 'error');
                }
            },
            error: function() {
                showNotification('Something went wrong. Please try again.', 'error');
            }
        });
    }
}

function updateCartQuantity(cartId, quantity) {
    if (quantity < 1) {
        removeFromCart(cartId);
        return;
    }
    
    $.ajax({
        url: 'api/cart.php',
        method: 'POST',
        data: {
            action: 'update',
            cart_id: cartId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                loadCartCount();
                updateCartTotal();
            } else {
                showNotification(response.message || 'Failed to update quantity', 'error');
            }
        },
        error: function() {
            showNotification('Something went wrong. Please try again.', 'error');
        }
    });
}

function loadCartCount() {
    $.get('api/cart-count.php', function(data) {
        $('#cart-count').text(data.count || 0);
    }).fail(function() {
        $('#cart-count').text('0');
    });
}

// Wishlist Functions
function addToWishlist(productId) {
    $.ajax({
        url: 'api/wishlist.php',
        method: 'POST',
        data: {
            action: 'add',
            product_id: productId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification('Product added to wishlist!', 'success');
                updateWishlistIcon(productId, true);
            } else {
                if (response.message.includes('login')) {
                    window.location.href = '?page=auth&action=login';
                } else {
                    showNotification(response.message || 'Failed to add to wishlist', 'error');
                }
            }
        },
        error: function() {
            showNotification('Something went wrong. Please try again.', 'error');
        }
    });
}

function removeFromWishlist(productId) {
    $.ajax({
        url: 'api/wishlist.php',
        method: 'POST',
        data: {
            action: 'remove',
            product_id: productId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showNotification('Product removed from wishlist', 'success');
                updateWishlistIcon(productId, false);
            } else {
                showNotification(response.message || 'Failed to remove from wishlist', 'error');
            }
        },
        error: function() {
            showNotification('Something went wrong. Please try again.', 'error');
        }
    });
}

function updateWishlistIcon(productId, inWishlist) {
    const icon = $(`.wishlist-btn[data-product-id="${productId}"] i`);
    if (inWishlist) {
        icon.removeClass('far').addClass('fas text-danger');
    } else {
        icon.removeClass('fas text-danger').addClass('far');
    }
}

// Product Functions
function quickView(productId) {
    $.get(`api/product-details.php?id=${productId}`, function(data) {
        if (data.success) {
            showProductModal(data.product);
        } else {
            showNotification('Failed to load product details', 'error');
        }
    });
}

function showProductModal(product) {
    // Create and show product modal
    const modalHtml = `
        <div class="modal fade" id="productModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${product.name}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <img src="assets/uploads/products/${product.primary_image}" class="img-fluid" alt="${product.name}">
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted">by ${product.shop_name}</p>
                                <div class="price mb-3">
                                    <span class="h4 text-primary">₹${product.sale_price || product.price}</span>
                                    ${product.sale_price ? `<span class="text-muted text-decoration-line-through ms-2">₹${product.price}</span>` : ''}
                                </div>
                                <p>${product.short_description}</p>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary" onclick="addToCart(${product.id})">Add to Cart</button>
                                    <button class="btn btn-outline-primary" onclick="addToWishlist(${product.id})">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal and add new one
    $('#productModal').remove();
    $('body').append(modalHtml);
    $('#productModal').modal('show');
}

// Search and Filter Functions
function applyFilters() {
    const form = $('#filterForm');
    const formData = form.serialize();
    window.location.href = '?' + formData;
}

function clearFilters() {
    window.location.href = '?page=products';
}

// Notification Functions
function showNotification(message, type = 'info') {
    const alertClass = type === 'error' ? 'danger' : type;
    const notification = `
        <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('body').append(notification);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}

// Form Validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return false;
    }
    return true;
}

// Image Upload Preview
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $(`#${previewId}`).attr('src', e.target.result).show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Price Range Slider
function initPriceSlider(minPrice, maxPrice) {
    if ($('#priceRange').length) {
        $('#priceRange').slider({
            range: true,
            min: minPrice,
            max: maxPrice,
            values: [minPrice, maxPrice],
            slide: function(event, ui) {
                $('#priceMin').val(ui.values[0]);
                $('#priceMax').val(ui.values[1]);
                $('#priceDisplay').text(`₹${ui.values[0]} - ₹${ui.values[1]}`);
            }
        });
    }
}

// Lazy Loading for Images
function initLazyLoading() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
}

// Smooth Scrolling
function smoothScroll(target) {
    document.querySelector(target).scrollIntoView({
        behavior: 'smooth'
    });
}

// Copy to Clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showNotification('Copied to clipboard!', 'success');
    }, function() {
        showNotification('Failed to copy to clipboard', 'error');
    });
}

// Format Currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR'
    }).format(amount);
}

// Debounce Function
function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction() {
        const context = this;
        const args = arguments;
        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

// Loading Spinner
function showLoading(element) {
    $(element).html('<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>');
}

function hideLoading(element, originalContent) {
    $(element).html(originalContent);
}

// Initialize on page load
$(document).ready(function() {
    initLazyLoading();
    
    // Initialize any price sliders
    if ($('#priceRange').length) {
        initPriceSlider(0, 10000);
    }
    
    // Handle quantity input changes
    $('.quantity-input').on('input', debounce(function() {
        const cartId = $(this).data('cart-id');
        const quantity = parseInt($(this).val());
        if (quantity > 0) {
            updateCartQuantity(cartId, quantity);
        }
    }, 500));
    
    // Handle search input
    $('#searchInput').on('input', debounce(function() {
        const query = $(this).val();
        if (query.length > 2) {
            searchProducts(query);
        }
    }, 300));
});

// Search Products (for autocomplete)
function searchProducts(query) {
    $.get(`api/search.php?q=${encodeURIComponent(query)}`, function(data) {
        if (data.success && data.products.length > 0) {
            showSearchSuggestions(data.products);
        } else {
            hideSearchSuggestions();
        }
    });
}

function showSearchSuggestions(products) {
    let suggestions = '<div class="search-suggestions position-absolute bg-white border rounded shadow-sm w-100" style="top: 100%; z-index: 1000;">';
    products.forEach(product => {
        suggestions += `
            <a href="?page=products&action=details&id=${product.id}" class="d-block p-2 text-decoration-none text-dark border-bottom">
                <div class="d-flex align-items-center">
                    <img src="assets/uploads/products/${product.primary_image}" class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                    <div>
                        <div class="fw-bold">${product.name}</div>
                        <small class="text-muted">₹${product.price}</small>
                    </div>
                </div>
            </a>
        `;
    });
    suggestions += '</div>';
    
    $('.search-container').append(suggestions);
}

function hideSearchSuggestions() {
    $('.search-suggestions').remove();
}

// Close search suggestions when clicking outside
$(document).on('click', function(e) {
    if (!$(e.target).closest('.search-container').length) {
        hideSearchSuggestions();
    }
});
