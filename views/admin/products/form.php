<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4"><?php echo isset($product['id']) ? 'Edit Product' : 'Add New Product'; ?></h5>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo $product['price'] ?? ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="stock" name="stock" value="<?php echo $product['stock'] ?? ''; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php if ($categories && $categories->num_rows > 0): ?>
                                <?php while($category = $categories->fetch_assoc()): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo (isset($product['category_id']) && $product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="featured" name="featured" <?php echo (isset($product['featured']) && $product['featured']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="featured">
                                Featured Product
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="image" class="form-label">Product Image</label>
                        
                        <!-- Image Preview Container -->
                        <div class="image-preview-container mb-3">
                            <!-- Default preview box with placeholder or current image -->
                            <div id="imagePreview" class="image-preview border rounded p-2 text-center">
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="<?php echo SITE_URL . $product['image_url']; ?>" class="img-fluid preview-image" alt="Product Image">
                                    <input type="hidden" name="current_image" value="<?php echo $product['image_url']; ?>">
                                <?php else: ?>
                                    <div class="placeholder-text text-muted">
                                        <i class="bi bi-image fs-1"></i><br>
                                        Image preview will appear here
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- File Input -->
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="form-text mt-2">Recommended size: 600x600px. Max size: 2MB</div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <?php echo isset($product['id']) ? 'Update Product' : 'Add Product'; ?>
                </button>
                <a href="<?php echo SITE_URL; ?>admin/products" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        
        // Function to handle image preview
        imageInput.addEventListener('change', function() {
            // Clear the preview
            imagePreview.innerHTML = '';
            
            // Check if a file is selected
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Check file type
                const validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                if (!validImageTypes.includes(file.type)) {
                    imagePreview.innerHTML = '<div class="alert alert-danger">Please select a valid image file (JPEG, PNG, or GIF)</div>';
                    return;
                }
                
                // Check file size (max 2MB)
                const maxSize = 2 * 1024 * 1024; // 2MB in bytes
                if (file.size > maxSize) {
                    imagePreview.innerHTML = '<div class="alert alert-danger">File size exceeds 2MB. Please choose a smaller image.</div>';
                    return;
                }
                
                // Create preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('img-fluid', 'preview-image');
                    img.alt = 'Product Image Preview';
                    imagePreview.appendChild(img);
                }
                reader.readAsDataURL(file);
            } else {
                // No file selected, show placeholder
                imagePreview.innerHTML = `
                    <div class="placeholder-text text-muted">
                        <i class="bi bi-image fs-1"></i><br>
                        Image preview will appear here
                    </div>
                `;
            }
        });
    });
</script>

<style>
    .image-preview {
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        margin-bottom: 10px;
        overflow: hidden;
    }
    
    .preview-image {
        max-height: 250px;
        max-width: 100%;
        object-fit: contain;
    }
    
    .placeholder-text {
        padding: 20px;
    }
    
    /* Add animation for preview changes */
    .image-preview img {
        animation: fadeIn 0.5s;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>