<?php
// filepath: c:\xampp\htdocs\chabongshop\views\home\index.php
// Home page view
?>
<!-- Add banner image below navbar -->
<div class="banner-container">
    <img src="<?php echo SITE_URL; ?>public/images/front-view-beautiful-dog-with-copy-space_23-2148786562.avif" 
         class="img-fluid w-100 banner-image" 
         alt="Pet Shop Banner">
    <div class="banner-overlay">
        <div class="container">
            <div class="banner-content">
                <h1>Welcome to <br>Woof-woof</h1>
                <p>Quality products for your furry friends</p>
                <!-- The Shop Now button has been removed -->
            </div>
        </div>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Featured Pet Products</h2>
        
        <div class="product-container d-flex flex-wrap">
            <?php if ($featuredProducts && $featuredProducts->num_rows > 0): ?>
                <?php while($product = $featuredProducts->fetch_assoc()): ?>
                    <div class="product-col">
                        <a href="<?php echo SITE_URL; ?>products/detail/<?php echo $product['id']; ?>" class="text-decoration-none">
                            <div class="card product-card h-100 clickable-card">
                                <img src="<?php echo SITE_URL . htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text">$<?php echo number_format($product['price'], 2); ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
                
                <?php
                // Add empty placeholder columns if needed to complete the row
                $count = $featuredProducts->num_rows % 5;
                if ($count > 0) {
                    $needed = 5 - $count;
                    for ($i = 0; $i < $needed; $i++) {
                        echo '<div class="product-col"></div>';
                    }
                }
                ?>
            <?php else: ?>
                <div class="col-12 text-center"><p>No products found</p></div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4 mb-5">
            <a href="<?php echo SITE_URL; ?>products" class="btn view-all-btn">View All Products</a>
        </div>
    </div>
</section>

<div class="top-bar">
    <!-- ...existing nav / header ... -->
</div>

<!-- <script>
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.getElementById('notification-toggle');
    var dropdown = document.getElementById('notification-dropdown');
    var badge = document.getElementById('notification-badge');

    if (toggle) {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });
    }

    document.addEventListener('click', function() {
        if (dropdown) dropdown.style.display = 'none';
    });

    // Handle clicking a notification: mark as read and navigate
    var items = document.querySelectorAll('.notification-item');
        items.forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                var notificationId = this.getAttribute('data-id');
            var href = this.getAttribute('href');

            // call AJAX mark-read
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo SITE_URL; ?>ajax/mark-notification-read', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        // navigate after marking
                        window.location.href = href;
                    }
                };
                xhr.send('id=' + encodeURIComponent(notificationId));
            });
        });

    var markAllBtn = document.getElementById('mark-all-read');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo SITE_URL; ?>ajax/mark-notification-read', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            // Send id=0 to mark all (AjaxController can interpret 0)
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (badge) badge.style.display = 'none';
                    var items = document.querySelectorAll('.notification-item');
                    items.forEach(function(it){
                        it.style.opacity = 0.7;
                    });
                }
            };
            xhr.send('id=0'); // mark all read
        });
    }
});
</script> -->