<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title mb-0">Order #<?php echo $order['id']; ?></h5>
            <a href="<?php echo SITE_URL; ?>admin/orders" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Orders
            </a>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Order Information</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                        <p><strong>Date:</strong> <?php echo date('M j, Y, g:i a', strtotime($order['created_at'])); ?></p>
                        <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                        <p>
                            <strong>Status:</strong>
                            <?php
                            $statusClass = 'bg-secondary';
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
                            <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($order['status']); ?></span>
                        </p>
                        
                        <form id="update-status-form" class="mt-3">
                            <div class="input-group">
                                <select class="form-select" id="order-status" name="status">
                                    <option value="">Change Status</option>
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button class="btn btn-primary" type="submit">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Customer Information</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                        <p><strong>City:</strong> <?php echo htmlspecialchars($order['city']); ?></p>
                        <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($order['postal_code']); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Order Items</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orderItems && $orderItems->num_rows > 0): ?>
                                <?php while($item = $orderItems->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($item['product_image'])): ?>
                                                    <img src="<?php echo SITE_URL . $item['product_image']; ?>" class="img-thumbnail me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                                <?php endif; ?>
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                    <small class="text-muted">ID: <?php echo $item['product_id']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td class="text-end">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No items found</td>
                                </tr>
                            <?php endif; ?>
                            
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total</strong></td>
                                <td class="text-end"><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('update-status-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const status = document.getElementById('order-status').value;
            if (!status) return;
            
            const formData = new FormData();
            formData.append('id', '<?php echo $order['id']; ?>');
            formData.append('status', status);
            
            fetch('<?php echo SITE_URL; ?>admin/update-order-status', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert(data.message);
                    // Reload page to reflect changes
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the order status');
            });
        });
    });
</script>