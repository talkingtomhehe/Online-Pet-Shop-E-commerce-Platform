<section class="py-5">
    <div class="container">
        <h1 class="mb-4">Checkout</h1>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-permanent">
                <?php echo $success; ?>
                <div class="mt-3">
                    <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">Return to Home</a>
                </div>
            </div>
        <?php else: ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="row">
                <!-- Order Form -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                        <h5 class="mb-4">Shipping Information</h5>
                        <!-- Add profile completion reminder -->
                        <?php if (empty($userData['address']) || empty($userData['city']) || empty($userData['postal_code'])): ?>
                            <div class="alert alert-info mb-4">
                                <i class="bi bi-info-circle me-2"></i>
                                Your profile information is incomplete. Consider <a href="<?php echo SITE_URL; ?>user/profile" class="alert-link">updating your profile</a> to make checkout easier next time.
                            </div>
                        <?php endif; ?>

                        <!-- Add checkbox for different address -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="use-different-address">
                            <label class="form-check-label" for="use-different-address">
                                Ship to a different address?
                            </label>
                        </div>
                            <form id="checkout-form" method="post" action="<?php echo SITE_URL; ?>cart/checkout">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                            value="<?php echo htmlspecialchars($userData['full_name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                            value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address *</label>
                                    <input type="text" class="form-control" id="address" name="address" 
                                        value="<?php echo htmlspecialchars($userData['address'] ?? ''); ?>" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="city" class="form-label">City *</label>
                                        <input type="text" class="form-control" id="city" name="city" 
                                            value="<?php echo htmlspecialchars($userData['city'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="state" class="form-label">State/Province</label>
                                        <input type="text" class="form-control" id="state" name="state"
                                            value="<?php echo htmlspecialchars($userData['state'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="postal_code" class="form-label">Postal Code *</label>
                                        <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                            value="<?php echo htmlspecialchars($userData['postal_code'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                                </div>
                                <div class="mb-4">
                                    <label for="notes" class="form-label">Order Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Special instructions for delivery"></textarea>
                                </div>
                                
                                <h5 class="mb-4 mt-5">Payment Method</h5>
                                <div class="mb-4">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" checked>
                                        <label class="form-check-label" for="credit_card">
                                            Credit / Debit Card
                                        </label>
                                    </div>
                                    <div id="credit-card-form" class="bg-light p-3 rounded mb-4">
                                        <div class="mb-3">
                                            <label for="card_number" class="form-label">Card Number *</label>
                                            <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="card_expiry" class="form-label">Expiry Date *</label>
                                                <input type="text" class="form-control" id="card_expiry" name="card_expiry" placeholder="MM/YY" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="card_cvv" class="form-label">CVV *</label>
                                                <input type="text" class="form-control" id="card_cvv" name="card_cvv" placeholder="123" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                        <label class="form-check-label" for="paypal">
                                            PayPal
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod">
                                        <label class="form-check-label" for="cod">
                                            Cash on Delivery
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-grid mt-5">
                                    <button type="submit" class="btn btn-primary btn-lg">Place Order</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 2rem;">
                        <div class="card-header bg-transparent py-3">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <!-- Cart items list -->
                            <div class="mb-4">
                                <?php if (isset($buyNowMode) && $buyNowMode && isset($buyNowProduct)): ?>
                                    <!-- Buy Now Mode - Show only the product being purchased directly -->
                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                            <span class="d-block"><?php echo htmlspecialchars($buyNowProduct['name']); ?></span>
                                            <small class="text-muted">Qty: <?php echo $buyNowProduct['quantity']; ?></small>
                                        </div>
                                        <span>$<?php echo number_format($buyNowProduct['price'] * $buyNowProduct['quantity'], 2); ?></span>
                                    </div>
                                <?php elseif (isset($cartItems) && $cartItems->num_rows > 0): ?>
                                    <!-- Regular Cart Checkout - Loop through cart items -->
                                    <?php $cartItems->data_seek(0); // Reset result pointer ?>
                                    <?php while($item = $cartItems->fetch_assoc()): ?>
                                        <div class="d-flex justify-content-between mb-3">
                                            <div>
                                                <span class="d-block"><?php echo htmlspecialchars($item['name']); ?></span>
                                                <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                            </div>
                                            <span>$<?php echo number_format($item['subtotal'], 2); ?></span>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="text-center py-2">
                                        <p>No items in cart</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <hr>
                            
                            <!-- Totals -->
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>$<?php 
                                    if (isset($buyNowMode) && $buyNowMode && isset($buyNowProduct)) {
                                        echo number_format($buyNowSubtotal, 2); 
                                    } else {
                                        echo number_format($cartTotal, 2);
                                    }
                                ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping</span>
                                <span>Free</span>
                            </div>
                            <div class="d-flex justify-content-between mb-0">
                                <strong>Total</strong>
                                <strong class="text-primary">$<?php 
                                    if (isset($buyNowMode) && $buyNowMode && isset($buyNowProduct)) {
                                        echo number_format($buyNowSubtotal, 2); 
                                    } else {
                                        echo number_format($cartTotal, 2);
                                    }
                                ?></strong>
                            </div>
                            
                            <hr>
                            
                            <!-- Return links -->
                            <div class="text-center mt-3">
                                <?php if (isset($buyNowMode) && $buyNowMode): ?>
                                    <a href="<?php echo SITE_URL; ?>products/detail/<?php echo $buyNowProduct['id']; ?>" class="btn btn-outline-dark btn-sm">
                                        <i class="bi bi-arrow-left me-1"></i> Back to Product
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo SITE_URL; ?>cart" class="btn btn-outline-dark btn-sm">
                                        <i class="bi bi-arrow-left me-1"></i> Return to Cart
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle payment method forms
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'credit_card') {
                document.getElementById('credit-card-form').style.display = 'block';
                document.getElementById('card_number').setAttribute('required', '');
                document.getElementById('card_expiry').setAttribute('required', '');
                document.getElementById('card_cvv').setAttribute('required', '');
            } else {
                document.getElementById('credit-card-form').style.display = 'none';
                document.getElementById('card_number').removeAttribute('required');
                document.getElementById('card_expiry').removeAttribute('required');
                document.getElementById('card_cvv').removeAttribute('required');
            }
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const useDifferentAddressCheckbox = document.getElementById('use-different-address');
    const nameField = document.getElementById('name');
    const emailField = document.getElementById('email');
    const addressField = document.getElementById('address');
    const cityField = document.getElementById('city');
    const stateField = document.getElementById('state');
    const postalCodeField = document.getElementById('postal_code');
    const phoneField = document.getElementById('phone');
    
    // Store original values
    const originalValues = {
        name: nameField.value,
        email: emailField.value,
        address: addressField.value,
        city: cityField.value,
        state: stateField.value,
        postal_code: postalCodeField.value,
        phone: phoneField.value
    };
    
    // Function to toggle form fields
    useDifferentAddressCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // Use different address - clear form fields
            nameField.value = '';
            // Keep email as it's likely the same
            addressField.value = '';
            cityField.value = '';
            stateField.value = '';
            postalCodeField.value = '';
            phoneField.value = '';
        } else {
            // Use profile data - restore original values
            nameField.value = originalValues.name;
            emailField.value = originalValues.email;
            addressField.value = originalValues.address;
            cityField.value = originalValues.city;
            stateField.value = originalValues.state;
            postalCodeField.value = originalValues.postal_code;
            phoneField.value = originalValues.phone;
        }
    });
});
</script>