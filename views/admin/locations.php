<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-3">
    <h1 class="h2">Store Locations</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#locationModal">
        <i class="bi bi-plus-lg"></i> Add New Location
    </button>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<!-- Store Locations Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if ($storeLocations && $storeLocations->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($location = $storeLocations->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $location['id']; ?></td>
                                <td><?php echo htmlspecialchars($location['name']); ?></td>
                                <td><?php echo htmlspecialchars($location['address']); ?></td>
                                <td><?php echo htmlspecialchars($location['phone']); ?></td>
                                <td><?php echo htmlspecialchars($location['email']); ?></td>
                                <td>
                                    <?php if ($location['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?edit=<?php echo $location['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="?delete=<?php echo $location['id']; ?>" class="btn btn-sm btn-outline-danger ms-1" 
                                       onclick="return confirm('Are you sure you want to delete this location?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">No store locations found. Add your first location using the button above.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">
                    <?php echo $locationToEdit ? 'Edit Location' : 'Add New Location'; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <?php if ($locationToEdit): ?>
                        <input type="hidden" name="location_id" value="<?php echo $locationToEdit['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Store Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required 
                                   value="<?php echo $locationToEdit ? htmlspecialchars($locationToEdit['name']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo $locationToEdit ? htmlspecialchars($locationToEdit['email']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address *</label>
                        <textarea class="form-control" id="address" name="address" rows="2" required><?php 
                            echo $locationToEdit ? htmlspecialchars($locationToEdit['address']) : '';
                        ?></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="latitude" class="form-label">Latitude *</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" required
                                   value="<?php echo $locationToEdit ? $locationToEdit['latitude'] : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="longitude" class="form-label">Longitude *</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" required
                                   value="<?php echo $locationToEdit ? $locationToEdit['longitude'] : ''; ?>">
                        </div>
                        <div class="col-12 mt-2">
                            <small class="text-muted">
                                Tip: Find coordinates by right-clicking on a location in Google Maps and selecting "What's here?"
                            </small>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                   value="<?php echo $locationToEdit ? htmlspecialchars($locationToEdit['phone']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="is_active" class="form-check-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                       <?php echo (!$locationToEdit || $locationToEdit['is_active']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="hours" class="form-label">Business Hours</label>
                        <textarea class="form-control" id="hours" name="hours" rows="3"><?php 
                            echo $locationToEdit ? htmlspecialchars($locationToEdit['hours']) : '';
                        ?></textarea>
                        <small class="text-muted">You can use HTML tags like &lt;br&gt; for formatting</small>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <?php echo $locationToEdit ? 'Update Location' : 'Add Location'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-open the edit modal if editing
<?php if ($locationToEdit): ?>
document.addEventListener('DOMContentLoaded', function() {
    const locationModal = new bootstrap.Modal(document.getElementById('locationModal'));
    locationModal.show();
});
<?php endif; ?>
</script>