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
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order #<?php echo $order['id']; ?></h5>
                    <a href="<?php echo SITE_URL; ?>user/orders" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Back to Orders
                    </a>
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