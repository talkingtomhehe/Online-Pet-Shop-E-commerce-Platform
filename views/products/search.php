<section class="py-5">
    <div class="container">
        <h1 class="mb-4">Search Results for "<?php echo htmlspecialchars($search); ?>"</h1>
        
        <!-- Enhanced search box at the top -->
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <form role="search" action="<?php echo SITE_URL; ?>products/search" method="get">
                    <div class="input-group">
                        <input class="form-control search-input dynamic-search-input" type="search" name="search" 
                               placeholder="Search products..." aria-label="Search" 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="row">
            <!-- Products grid -->
            <div class="col-md-9">
                <!-- Sorting options and result count -->
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
                </div>
                
                <!-- Dynamic search results container -->
                <div id="dynamic-search-results">
                    <?php if ($products && $products->num_rows > 0): ?>
                        <!-- Product container with flex wrap -->
                        <div class="product-container d-flex flex-wrap">
                            <?php while($product = $products->fetch_assoc()): ?>
                                <div class="product-col">
                                    <a href="<?php echo SITE_URL; ?>products/detail/<?php echo $product['id']; ?>" class="text-decoration-none">
                                        <div class="card product-card h-100 clickable-card">
                                            <img src="<?php echo SITE_URL . htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <?php 
                                                    // Highlight search term in product name
                                                    $name = htmlspecialchars($product['name']);
                                                    if (!empty($search)) {
                                                        $name = preg_replace('/(' . preg_quote($search, '/') . ')/i', '<span class="highlight">$1</span>', $name);
                                                    }
                                                    echo $name;
                                                    ?>
                                                </h5>
                                                <p class="card-text">$<?php echo number_format($product['price'], 2); ?></p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        
                        <!-- Pagination -->
                        <div id="pagination-container">
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
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <h4 class="alert-heading">No products found</h4>
                            <p>We couldn't find any products matching "<?php echo htmlspecialchars($search); ?>".</p>
                            <hr>
                            <p class="mb-0">Try using different keywords or check out our <a href="<?php echo SITE_URL; ?>products">product catalog</a>.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>