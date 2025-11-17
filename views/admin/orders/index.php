<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">All Orders</h5>

            <!-- Status filter -->
            <div class="btn-group">
                <a href="<?php echo SITE_URL; ?>admin/orders" class="btn <?php echo empty($_GET['status']) ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    All
                </a>
                <a href="<?php echo SITE_URL; ?>admin/orders?status=pending" class="btn <?php echo ($_GET['status'] ?? '') == 'pending' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    Pending
                </a>
                <a href="<?php echo SITE_URL; ?>admin/orders?status=processing" class="btn <?php echo ($_GET['status'] ?? '') == 'processing' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    Processing
                </a>
                <a href="<?php echo SITE_URL; ?>admin/orders?status=shipped" class="btn <?php echo ($_GET['status'] ?? '') == 'shipped' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    Shipped
                </a>
                <a href="<?php echo SITE_URL; ?>admin/orders?status=delivered" class="btn <?php echo ($_GET['status'] ?? '') == 'delivered' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    Delivered
                </a>
                <a href="<?php echo SITE_URL; ?>admin/orders?status=cancelled" class="btn <?php echo ($_GET['status'] ?? '') == 'cancelled' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    Cancelled
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders && $orders->num_rows > 0): ?>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($order['name']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($order['username']); ?></small>
                                </td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <?php
                                    $statusClass = 'bg-secondary';
                                    switch ($order['status']) {
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
                                </td>
                                <td><?php echo date('M j, Y, g:i a', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <a href="<?php echo SITE_URL; ?>admin/order-detail/<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No orders found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
        // Include pagination
        include VIEWS_PATH . 'shared/pagination.php';
        ?>
    </div>
</div>