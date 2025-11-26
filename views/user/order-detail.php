<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">My Account</h5>
                    <div class="list-group list-group-flush">
                        <a href="<?php echo SITE_URL; ?>user/profile" class="list-group-item list-group-item-action">Profile</a>
                        <a href="<?php echo SITE_URL; ?>user/orders" class="list-group-item list-group-item-action active">Orders</a>
                        <a href="<?php echo SITE_URL; ?>user/logout" class="list-group-item list-group-item-action text-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order #<?php echo $order['id']; ?></h5>
                    <div>
                        <?php if ($order['status'] === 'pending'): ?>
                            <button type="button" class="btn btn-sm btn-danger me-2" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                                <i class="bi bi-x-circle"></i> Cancel Order
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo SITE_URL; ?>user/orders" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Back to Orders
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <p class="mb-1"><strong>Date:</strong> <?php echo date('F d, Y h:i A', strtotime($order['created_at'])); ?></p>
                            <p class="mb-1">
                                <strong>Status:</strong> 
                                <?php
                                $statusClass = 'bg-secondary';
                                $statusText = ucfirst($order['status']);
                                
                                switch($order['status']) {
                                    case 'pending':
                                        $statusClass = 'bg-warning text-dark';
                                        break;
                                    case 'processing':
                                        $statusClass = 'bg-info text-dark';
                                        break;
                                    case 'shipped':
                                        $statusClass = 'bg-primary';
                                        break;
                                    case 'delivered':
                                        $statusClass = 'bg-success';
                                        break;
                                    case 'cancelled':
                                        $statusClass = 'bg-danger';
                                        break;
                                }
                                ?>
                                <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                            </p>
                            <p class="mb-1"><strong>Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Shipping Information</h6>
                            <p class="mb-1"><?php echo htmlspecialchars($order['name']); ?></p>
                            <p class="mb-1"><?php echo htmlspecialchars($order['address']); ?></p>
                            <p class="mb-1"><?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['postal_code']); ?></p>
                            <p class="mb-1"><?php echo htmlspecialchars($order['email']); ?></p>
                            <?php if (!empty($order['phone'])): ?>
                                <p class="mb-1"><?php echo htmlspecialchars($order['phone']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($orderItems && $orderItems->num_rows > 0): ?>
                                    <?php while ($item = $orderItems->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($item['image_url'])): ?>
                                                        <img src="<?php echo SITE_URL . str_replace('\\', '/', $item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-thumbnail me-3" style="width: 60px;">
                                                    <?php endif; ?>
                                                    <span><?php echo htmlspecialchars($item['name']); ?></span>
                                                </div>
                                            </td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No items found for this order.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order #<?php echo $order['id']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?php echo SITE_URL; ?>user/cancel-order">
                <div class="modal-body">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    
                    <div class="alert-warning1 p-3 mb-3 rounded">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Warning:</strong> This action cannot be undone. Your order will be cancelled and the items will be returned to stock.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Please select a reason for cancellation:</strong></label>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancellation_reason" id="reason1" value="Changed my mind" required>
                            <label class="form-check-label" for="reason1">
                                Changed my mind
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancellation_reason" id="reason2" value="Found a better price">
                            <label class="form-check-label" for="reason2">
                                Found a better price
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancellation_reason" id="reason3" value="Ordered by mistake">
                            <label class="form-check-label" for="reason3">
                                Ordered by mistake
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancellation_reason" id="reason4" value="Shipping cost too high">
                            <label class="form-check-label" for="reason4">
                                Shipping cost too high
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancellation_reason" id="reason5" value="Other">
                            <label class="form-check-label" for="reason5">
                                Other
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .alert-warning1 {
    background-color: #fff3cd; 
    color: #664d03;            
    border: 1px solid #ffecb5; 
    padding: 1rem;            
    border-radius: 0.375rem;   
    }
</style>