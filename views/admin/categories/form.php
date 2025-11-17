<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4"><?php echo isset($category['id']) ? 'Edit Category' : 'Add New Category'; ?></h5>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($category['name'] ?? ''); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <?php echo isset($category['id']) ? 'Update Category' : 'Add Category'; ?>
                </button>
                <a href="<?php echo SITE_URL; ?>admin/categories" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>