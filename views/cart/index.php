<div class="container py-5">
    <h2 class="mb-4">Your Shopping Cart</h2>
    
    <?php if ($cartItems && $cartItems->num_rows > 0): ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th colspan="2">Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($item = $cartItems->fetch_assoc()): ?>
                                    <tr class="cart-item" data-item-id="<?php echo $item['id']; ?>" data-price="<?php echo $item['price']; ?>">
                                        <td width="100">
                                            <img src="<?php echo SITE_URL . htmlspecialchars($item['image_url']); ?>" class="img-thumbnail" width="80">
                                        </td>
                                        <td>
                                            <a href="<?php echo SITE_URL; ?>products/detail/<?php echo $item['product_id']; ?>" class="text-decoration-none text-dark">
                                                <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            </a>
                                            <?php if (!empty($item['category'])): ?>
                                                <small class="text-muted"><?php echo htmlspecialchars($item['category']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td>
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <button type="button" class="btn btn-outline-secondary quantity-btn decrement">-</button>
                                                <input type="number" class="form-control text-center quantity-input" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo isset($item['stock']) ? $item['stock'] : 10; ?>">
                                                <button type="button" class="btn btn-outline-secondary quantity-btn increment">+</button>
                                            </div>
                                        </td>
                                        <td class="item-subtotal">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mb-4">
                    <a href="<?php echo SITE_URL; ?>products" class="btn btn-outline-dark">
                        <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                    </a>
                    <button id="clear-cart" class="btn btn-outline-danger">
                        <i class="bi bi-trash me-2"></i>Remove All Items
                    </button>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal</span>
                            <span class="cart-subtotal">$<?php echo number_format($cartTotal, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total</strong>
                            <strong class="cart-total">$<?php echo number_format($cartTotal, 2); ?></strong>
                        </div>
                        <div class="d-grid">
                        <a href="<?php echo SITE_URL; ?>cart/checkout?from_cart=1" class="btn btn-primary btn-lg">Proceed to Checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
            </div>
            <h3 class="mb-3">Your cart is empty</h3>
            <p class="text-muted mb-4">Looks like you haven't added any products to your cart yet.</p>
            <a href="<?php echo SITE_URL; ?>products" class="btn btn-primary">
                Start Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update quantity when + or - buttons are clicked
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            const cartItem = this.closest('.cart-item');
            let value = parseInt(input.value);
            
            if (this.classList.contains('increment')) {
                input.value = value + 1;
                updateCartItemQuantity(cartItem, value + 1);
            } else if (this.classList.contains('decrement') && value > 1) {
                input.value = value - 1;
                updateCartItemQuantity(cartItem, value - 1);
            }
        });
    });
    
    // Update when quantity input changes directly
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const cartItem = this.closest('.cart-item');
            let value = parseInt(this.value);
            
            if (isNaN(value) || value < 1) {
                this.value = 1;
                value = 1;
            }
            
            updateCartItemQuantity(cartItem, value);
        });
    });
    
    // Remove item button
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            const cartItem = this.closest('.cart-item');
            const itemId = cartItem.dataset.itemId;
            
            if (confirm('Are you sure you want to remove this item?')) {
                removeCartItem(itemId);
            }
        });
    });
    
    // Clear cart button
    document.getElementById('clear-cart')?.addEventListener('click', function() {
        if (confirm('Are you sure you want to remove all items from your cart?')) {
            clearCart();
        }
    });
    
    // Function to update cart item quantity
    function updateCartItemQuantity(cartItem, quantity) {
        const itemId = cartItem.dataset.itemId;
        const price = parseFloat(cartItem.dataset.price);
        
        // Update subtotal display immediately
        const subtotal = price * quantity;
        cartItem.querySelector('.item-subtotal').textContent = '$' + subtotal.toFixed(2);
        
        // Update total
        updateCartTotal();
        
        // Send update to server
        const formData = new FormData();
        formData.append('item_id', itemId);
        formData.append('quantity', quantity);
        
        fetch('<?php echo SITE_URL; ?>cart/update', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // First check if response is ok before attempting to parse JSON
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                alert(data.message);
                // Only reload if there's an actual error
                location.reload();
            }
            // If success, do nothing - we already updated the UI
        })
        .catch(error => {
            console.error('Error:', error);
            // Do not show an alert here as it's working correctly
        });
    }
    
    // Function to remove cart item
    function removeCartItem(itemId) {
        const formData = new FormData();
        formData.append('item_id', itemId);
        
        fetch('<?php echo SITE_URL; ?>cart/remove', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Remove item from DOM
                const item = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);
                if (item) {
                    item.remove();
                }
                
                // Update totals
                updateCartTotal();
                
                // If no items left, reload page
                if (document.querySelectorAll('.cart-item').length === 0) {
                    location.reload();
                }
            } else {
                alert(data.message || 'Failed to remove item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Don't show alert here
        });
    }
    
    // Function to clear cart
    function clearCart() {
        // Add a spinner or disable the button to prevent multiple clicks
        const clearBtn = document.getElementById('clear-cart');
        if (clearBtn) {
            clearBtn.disabled = true;
            clearBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Removing...';
        }

        // Make the AJAX request
        fetch('<?php echo SITE_URL; ?>cart/clear', {
            method: 'POST'
        })
        .then(response => {
            console.log('Clear cart response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Clear cart response:', data);
            if (data.success) {
                // Success - reload page to show empty cart
                window.location.reload();
            } else {
                // Error - show message and re-enable button
                alert(data.message || 'Failed to clear cart');
                if (clearBtn) {
                    clearBtn.disabled = false;
                    clearBtn.innerHTML = '<i class="bi bi-trash me-2"></i>Remove All Items';
                }
            }
        })
        .catch(error => {
            console.error('Error clearing cart:', error);
            alert('An error occurred while trying to clear your cart. Please try again.');
            if (clearBtn) {
                clearBtn.disabled = false;
                clearBtn.innerHTML = '<i class="bi bi-trash me-2"></i>Remove All Items';
            }
        });
    }
    
    // Calculate and update cart total
    function updateCartTotal() {
        let total = 0;
        
        document.querySelectorAll('.cart-item').forEach(item => {
            const price = parseFloat(item.dataset.price);
            const quantity = parseInt(item.querySelector('.quantity-input').value);
            total += price * quantity;
        });
        
        document.querySelector('.cart-subtotal').textContent = '$' + total.toFixed(2);
        document.querySelector('.cart-total').textContent = '$' + total.toFixed(2);
    }
});
</script>