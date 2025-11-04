<?php
// filepath: c:\xampp\htdocs\chabongshop\views\products\index.php
// Products listing view
?>
<section class="py-5">
    <div class="container">
        <h1 class="mb-4"><?php echo $pageTitle; ?></h1>
        
        <div class="row">
            <!-- Categories sidebar -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Categories</h5>
                    </div>
                    <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item <?php echo ($action !== 'category') ? 'active' : ''; ?>">
                            <a href="<?php echo SITE_URL; ?>products" class="text-decoration-none <?php echo ($action !== 'category') ? 'text-white' : ''; ?>">All Products</a>
                        </li>
                        <?php if ($categories && $categories->num_rows > 0): ?>
                            <?php $categories->data_seek(0); ?>
                            <?php while($cat = $categories->fetch_assoc()): ?>
                                <?php 
                                // Compare with the URL parameter after decoding
                                $decodedId = ($id !== null) ? urldecode($id) : '';
                                $isActive = ($action === 'category' && $decodedId === $cat['name']);
                                ?>
                                <li class="list-group-item <?php echo $isActive ? 'active' : ''; ?>">
                                    <a href="<?php echo SITE_URL; ?>products/category/<?php echo urlencode($cat['name']); ?>" 
                                    class="text-decoration-none <?php echo $isActive ? 'text-white' : ''; ?>">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </ul>
                    </div>
                </div>
            </div>
            
            <!-- Products grid -->
            <div class="col-md-9">
                <?php if ($products && $products->num_rows > 0): ?>
                    <!-- Sorting options -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="me-2">Sort by:</span>
                            <div class="btn-group">
                                <a href="<?php echo getSortURL('name', $sort, $order); ?>" class="btn btn-sm <?php echo ($sort == 'name') ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                                    Name <?php echo getSortIcon('name', $sort, $order); ?>
                                </a>
                                <a href="<?php echo getSortURL('price', $sort, $order); ?>" class="btn btn-sm <?php echo ($sort == 'price') ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                                    Price <?php echo getSortIcon('price', $sort, $order); ?>
                                </a>
                                <a href="<?php echo getSortURL('id', $sort, $order); ?>" class="btn btn-sm <?php echo ($sort == 'id') ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                                    Newest <?php echo getSortIcon('id', $sort, $order); ?>
                                </a>
                            </div>
                        </div>
                        <div class="text-muted small">
                            <?php echo $totalProducts; ?> products found
                        </div>
                    </div>
                    
                    <!-- Product container with flex wrap -->
                    <div class="product-container d-flex flex-wrap">
                        <?php while($product = $products->fetch_assoc()): ?>
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
                        $count = $products->num_rows % 5;
                        if ($count > 0) {
                            $needed = 5 - $count;
                            for ($i = 0; $i < $needed; $i++) {
                                echo '<div class="product-col"></div>';
                            }
                        }
                        ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <!-- Previous button -->
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo getPaginationURL($page - 1); ?>">Previous</a>
                            </li>
                            
                            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo getPaginationURL($i); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Next button -->
                            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo getPaginationURL($page + 1); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="alert alert-info">No products found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>