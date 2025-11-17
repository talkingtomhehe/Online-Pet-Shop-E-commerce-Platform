<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (isset($_SESSION['admin_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['admin_message']['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['admin_message']['text']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['admin_message']); ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">All Categories</h5>
            <a href="<?php echo SITE_URL; ?>admin/add-category" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add New Category
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($categories && $categories->num_rows > 0): ?>
                        <?php while ($category = $categories->fetch_assoc()):
                            // Count products in this category
                            $productsInCategory = $this->productModel->getProductsByCategory($category['id']);
                            $productCount = $productsInCategory ? $productsInCategory->num_rows : 0;
                        ?>
                            <tr>
                                <td><?php echo $category['id']; ?></td>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td><?php echo htmlspecialchars($category['description'] ?? ''); ?></td>
                                <td><?php echo $productCount; ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo SITE_URL; ?>admin/edit-category/<?php echo $category['id']; ?>" class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger delete-category"
                                            data-id="<?php echo $category['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                            data-product-count="<?php echo $productCount; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No categories found</td>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the category: <strong id="categoryName"></strong>?</p>
                <div id="categoryHasProductsWarning" class="alert alert-warning">
                    This category contains <strong id="productCount"></strong> products. You must reassign or delete these products before deleting the category.
                </div>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Delete category confirmation
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
        const categoryHasProductsWarning = document.getElementById('categoryHasProductsWarning');
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        let categoryIdToDelete = null;

        document.querySelectorAll('.delete-category').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const productCount = parseInt(this.dataset.productCount);

                categoryIdToDelete = id;
                document.getElementById('categoryName').textContent = name;
                document.getElementById('productCount').textContent = productCount;

                // Show warning and disable delete button if category has products
                if (productCount > 0) {
                    categoryHasProductsWarning.style.display = 'block';
                    confirmDeleteBtn.disabled = true;
                } else {
                    categoryHasProductsWarning.style.display = 'none';
                    confirmDeleteBtn.disabled = false;
                }

                deleteModal.show();
            });
        });

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (categoryIdToDelete && !this.disabled) {
                const formData = new FormData();
                formData.append('id', categoryIdToDelete);

                fetch('<?php echo SITE_URL; ?>admin/delete-category', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message and reload page
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                        deleteModal.hide();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the category');
                        deleteModal.hide();
                    });
            }
        });
    });
</script>