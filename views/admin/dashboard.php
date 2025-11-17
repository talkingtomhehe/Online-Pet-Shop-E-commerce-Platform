<div class="row mb-4">
    <!-- Products Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 text-muted">Total Products</h6>
                    <div class="icon-circle bg-light">
                        <i class="bi bi-box-seam text-primary"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold"><?php echo $totalProducts; ?></div>
            </div>
        </div>
    </div>
    
    <!-- Categories Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 text-muted">Total Categories</h6>
                    <div class="icon-circle bg-light">
                        <i class="bi bi-grid text-success"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold"><?php echo $totalCategories; ?></div>
            </div>
        </div>
    </div>
    
    <!-- Users Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 text-muted">Total Users</h6>
                    <div class="icon-circle bg-light">
                        <i class="bi bi-people text-info"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold"><?php echo $totalUsers; ?></div>
            </div>
        </div>
    </div>
    
    <!-- Orders Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 text-muted">Total Orders</h6>
                    <div class="icon-circle bg-light">
                        <i class="bi bi-cart-check text-warning"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold"><?php echo $totalOrders; ?></div>
                <div class="small text-muted"><?php echo $pendingOrders; ?> pending orders</div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Recent Orders</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php if ($recentOrders && $recentOrders->num_rows > 0): ?>
                        <?php while ($order = $recentOrders->fetch_assoc()): ?>
                            <a href="<?php echo SITE_URL; ?>admin/order-detail/<?php echo $order['id']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Order #<?php echo $order['id']; ?></strong>
                                    <span class="text-muted d-block">by <?php echo htmlspecialchars($order['username'] ?? 'Unknown'); ?></span>
                                    <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></small>
                                </div>
                                <div class="text-end">
                                    <span class="badge 
                                        <?php 
                                        switch($order['status']) {
                                            case 'pending': echo 'bg-warning text-dark'; break;
                                            case 'processing': echo 'bg-info text-dark'; break;
                                            case 'shipped': echo 'bg-primary'; break;
                                            case 'delivered': echo 'bg-success'; break;
                                            case 'cancelled': echo 'bg-danger'; break;
                                            default: echo 'bg-secondary'; break;
                                        } 
                                        ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                    <div class="mt-1 text-end"><?php echo '$' . number_format($order['total_amount'], 2); ?></div>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted">No recent orders to display.</p>
                    <?php endif; ?>
                    
                    <div class="text-center mt-3">
                        <a href="<?php echo SITE_URL; ?>admin/orders" class="btn btn-sm btn-outline-primary">View All Orders</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update the Recent Users card -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Recent Users</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush" id="recent-users-list">
                    <?php if ($recentUsers && $recentUsers->num_rows > 0): ?>
                        <?php while ($user = $recentUsers->fetch_assoc()): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center" 
                                data-user-id="<?php echo $user['id']; ?>">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <!-- Avatar -->
                                        <div class="me-2">
                                            <?php 
                                            $userAvatar = !empty($user['avatar']) ? 
                                                (strpos($user['avatar'], 'http') === 0 ? 
                                                    $user['avatar'] : // External URL
                                                    SITE_URL . str_replace('\\', '/', $user['avatar']) // Local path
                                                ) : 
                                                SITE_URL . 'public/images/avatars/default.png'; // Default 
                                            ?>
                                            <img src="<?php echo $userAvatar; ?>" alt="<?php echo htmlspecialchars($user['username']); ?>" 
                                                class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                            <small class="d-block text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <?php if ($user['is_online']): ?>
                                        <span class="badge bg-success" data-user-status>Online</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary" data-user-status>Offline</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted">No recent users to display.</p>
                    <?php endif; ?>
                    
                    <div class="text-center mt-3">
                        <a href="<?php echo SITE_URL; ?>admin/users" class="btn btn-sm btn-outline-primary">View All Users</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to update online status indicators
    function updateOnlineStatus() {
        fetch('<?php echo SITE_URL; ?>admin/get-online-users')
            .then(response => response.json())
            .then(users => {
                // Get all user elements with status badges
                const userItems = document.querySelectorAll('#recent-users-list .list-group-item');
                
                userItems.forEach((element, index) => {
                    if (users[index]) {
                        const user = users[index];
                        const statusBadge = element.querySelector('[data-user-status]') || 
                                           element.querySelector('.badge');
                        
                        if (statusBadge) {
                            if (user.is_online) {
                                statusBadge.textContent = 'Online';
                                statusBadge.className = 'badge bg-success';
                            } else {
                                statusBadge.textContent = 'Offline';
                                statusBadge.className = 'badge bg-secondary';
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching online users:', error));
    }
    
    // Only run on the admin dashboard
    if (document.querySelector('#recent-users-list')) {
        // Update immediately
        updateOnlineStatus();
        
        // Then update every 10 seconds
        setInterval(updateOnlineStatus, 10000);
    }
});
</script>