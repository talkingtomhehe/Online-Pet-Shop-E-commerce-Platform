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

        <?php if (isset($recommendations) && $recommendations && $recommendations->num_rows > 0): ?>
        <div class="mt-5">
            <hr class="my-5">
            <div class="d-flex align-items-center justify-content-center mb-4">
                <h2 class="mb-0 me-3">Recommended For You</h2>
                <span class="badge bg-primary rounded-pill">For You</span>
            </div>
            
            <div class="product-container d-flex flex-wrap justify-content-center">
                <?php while($product = $recommendations->fetch_assoc()): ?>
                    <div class="product-col">
                        <a href="<?php echo SITE_URL; ?>products/detail/<?php echo $product['id']; ?>" class="text-decoration-none">
                            <div class="card product-card h-100 clickable-card border-primary" style="border-width: 1px;">
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-primary shadow-sm">AI Pick</span>
                                </div>
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
                // Fill row if needed
                $count = $recommendations->num_rows % 5;
                if ($count > 0) {
                    $needed = 5 - $count;
                    for ($i = 0; $i < $needed; $i++) {
                        echo '<div class="product-col"></div>';
                    }
                }
                ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="text-center mt-4 mb-5">
            <a href="<?php echo SITE_URL; ?>products" class="btn view-all-btn">View All Products</a>
        </div>
    </div>
</section>

<div class="top-bar">
    <!-- ...existing nav / header ... -->
</div>

