<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">All Orders</h5>

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
                                    
                                    <?php if ($order['status'] === 'cancelled' && !empty($order['cancellation_reason'])): ?>
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#cancellationModal"
                                                data-id="<?php echo $order['id']; ?>"
                                                data-customer="<?php echo htmlspecialchars($order['name']); ?>"
                                                data-date="<?php echo date('M j, Y, g:i a', strtotime($order['created_at'])); ?>"
                                                data-reason="<?php echo htmlspecialchars($order['cancellation_reason']); ?>"
                                                title="View Cancellation Reason">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                    <?php endif; ?>
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

<div class="modal fade" id="cancellationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Cancellation Reason - Order #<span id="modalOrderId"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1"><strong>Customer:</strong> <span id="modalCustomer"></span></p>
                <p class="mb-3"><strong>Order Date:</strong> <span id="modalDate"></span></p>
                <hr>
                <p class="mb-1"><strong>Reason:</strong></p>
                <p id="modalReason" style="white-space: pre-wrap;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var cancellationModal = document.getElementById('cancellationModal');
    
    if (cancellationModal) {
        cancellationModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            var button = event.relatedTarget;
            
            // Extract info from data-* attributes
            var orderId = button.getAttribute('data-id');
            var customer = button.getAttribute('data-customer');
            var date = button.getAttribute('data-date');
            var reason = button.getAttribute('data-reason');
            
            // Update the modal's content
            cancellationModal.querySelector('#modalOrderId').textContent = orderId;
            cancellationModal.querySelector('#modalCustomer').textContent = customer;
            cancellationModal.querySelector('#modalDate').textContent = date;
            cancellationModal.querySelector('#modalReason').textContent = reason;
        });
    }
});
</script>