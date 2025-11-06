<?php
// Product details view
?>
<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item">
                    <a href="<?php echo SITE_URL; ?>products/category/<?php echo $product['category_id']; ?>">
                        <?php echo htmlspecialchars($product['category_name'] ?? 'Category'); ?>
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>
        
        <div class="row mt-4">
            <!-- Product Image -->
            <div class="col-md-6">
                <div class="card border-0">
                    <img src="<?php echo SITE_URL . htmlspecialchars($product['image_url']); ?>" 
                        class="img-fluid rounded" 
                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                        style="max-width: 75%; margin: 60px auto 0;">
                </div>
            </div>
            
            <!-- Product Details -->
            <div class="col-md-6 product-details">
                <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="badge bg-secondary mb-3"><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></p>
                <h3 class="text-primary mb-4">$<?php echo number_format($product['price'], 2); ?></h3>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="quantity" class="form-label">Quantity</label>
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary quantity-btn" id="decrease-quantity">-</button>
                            <input type="number" class="form-control text-center" id="quantity" min="1" value="1" max="<?php echo isset($product['stock']) ? $product['stock'] : 10; ?>">
                            <button type="button" class="btn btn-outline-secondary quantity-btn" id="increase-quantity">+</button>
                        </div>
                        <?php if (isset($product['stock']) && $product['stock'] > 0): ?>
                            <small class="text-muted"><?php echo $product['stock']; ?> items available</small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="d-flex gap-2 mb-4">
                    <button class="btn btn-outline-dark cart-btn" id="add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                        <i class="bi bi-cart-plus me-2"></i>Add to Cart
                    </button>
                    <button class="btn btn-dark" id="buy-now-btn" data-product-id="<?php echo $product['id']; ?>">
                        <i class="bi bi-bag me-2"></i>Buy Now
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Product Description - Moved below product info & buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Product Description</h5>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <div class="mt-5">
            <h3 class="mb-4">You May Also Like</h3>
            
            <?php if ($relatedProducts && $relatedProducts->num_rows > 0): ?>
                <div class="product-container d-flex flex-wrap">
                    <?php while($relatedProduct = $relatedProducts->fetch_assoc()): ?>
                        <div class="product-col">
                            <a href="<?php echo SITE_URL; ?>products/detail/<?php echo $relatedProduct['id']; ?>" class="text-decoration-none">
                                <div class="card product-card h-100 clickable-card">
                                    <img src="<?php echo SITE_URL . htmlspecialchars($relatedProduct['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($relatedProduct['name']); ?></h5>
                                        <p class="card-text">$<?php echo number_format($relatedProduct['price'], 2); ?></p>
                                        <?php 
                                        if (isset($relatedProduct['category_id'])) {
                                            $relatedCategory = $this->categoryModel->getCategoryById($relatedProduct['category_id']);
                                            if ($relatedCategory) {
                                                echo '<small class="text-muted">' . htmlspecialchars($relatedCategory['name']) . '</small>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                    
                    <?php
                    // Add empty placeholder columns if needed to complete the row
                    $count = $relatedProducts->num_rows % 5;
                    if ($count > 0) {
                        $needed = 5 - $count;
                        for ($i = 0; $i < $needed; $i++) {
                            echo '<div class="product-col"></div>';
                        }
                    }
                    ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No related products found</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const decreaseBtn = document.getElementById('decrease-quantity');
    const increaseBtn = document.getElementById('increase-quantity');
    const maxStock = <?php echo isset($product['stock']) ? $product['stock'] : 10; ?>;
    
    // Handle quantity decrease button
    decreaseBtn.addEventListener('click', function() {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    });
    
    // Handle quantity increase button
    increaseBtn.addEventListener('click', function() {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue < maxStock) {
            quantityInput.value = currentValue + 1;
        } else {
            // Show stock limit message
            showToast(`Only ${maxStock} items available`, 'warning');
        }
    });
    
    // Validate manual quantity changes
    quantityInput.addEventListener('change', function() {
        let value = parseInt(this.value);
        if (isNaN(value) || value < 1) {
            this.value = 1;
        } else if (value > maxStock) {
            this.value = maxStock;
            showToast(`Only ${maxStock} items available`, 'warning');
        }
    });

    // Add to cart button
    document.getElementById('add-to-cart-btn').addEventListener('click', function() {
        const productId = this.dataset.productId;
        const quantity = document.getElementById('quantity').value;
        
        addToCart(productId, quantity);
    });
    
    // Buy now button
    document.getElementById('buy-now-btn').addEventListener('click', function() {
        const productId = this.dataset.productId;
        const quantity = document.getElementById('quantity').value;
        
        addToCart(productId, quantity, true);
    });
    
    // Add to cart function
    function addToCart(productId, quantity, goToCheckout = false) {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        
        // Add buy_now flag if this is a direct checkout
        if (goToCheckout) {
            formData.append('buy_now', '1');
        }
        
        fetch('<?php echo SITE_URL; ?>cart/add', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.buyNow || goToCheckout) {
                    // Show message before redirect
                    showToast('Proceeding to checkout...', 'info');
                    // Redirect to checkout
                    setTimeout(() => {
                        window.location.href = '<?php echo SITE_URL; ?>cart/checkout';
                    }, 1000);
                } else {
                    // Show success message for normal add to cart
                    showToast('Product added to cart');
                    
                    // Update cart count in navbar if available
                    try {
                        const cartCountElement = document.querySelector('.cart-count');
                        if (cartCountElement && data.cartCount) {
                            cartCountElement.textContent = data.cartCount;
                        }
                    } catch(e) {
                        console.log('Could not update cart count:', e);
                    }
                }
            } else {
                if (data.redirect) {
                    if (confirm('You need to log in to add items to your cart. Go to login page?')) {
                        window.location.href = data.redirect;
                    }
                } else {
                    // Show error message
                    showToast(data.message, 'danger');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred. Please try again.', 'danger');
        });
    }
    
    // Toast notification function
    function showToast(message, type = 'success') {
        // Create toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }
        
        // Create toast element
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.setAttribute('id', toastId);
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Initialize and show toast
        const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 3000 });
        bsToast.show();
        
        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }
});
</script>