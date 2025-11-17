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
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Order History</h5>
                </div>
                <div class="card-body">
                    <?php if ($orders && $orders->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = $orders->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
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
                                            </td>
                                            <td>
                                                <a href="<?php echo SITE_URL; ?>user/order-detail/<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <img src="<?php echo SITE_URL; ?>public/images/empty-order.svg" alt="No Orders" style="width: 120px; opacity: 0.5;">
                            <h5 class="mt-3">You haven't placed any orders yet</h5>
                            <p class="text-muted">Browse our products and place your first order!</p>
                            <a href="<?php echo SITE_URL; ?>products" class="btn btn-primary mt-2">Shop Now</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>