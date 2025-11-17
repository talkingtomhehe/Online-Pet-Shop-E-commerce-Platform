<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">All Users</h5>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users && $users->num_rows > 0): ?>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-user"
                                        data-id="<?php echo $user['id']; ?>"
                                        data-username="<?php echo htmlspecialchars($user['username']); ?>">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No users found</td>
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
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the user: <strong id="userName"></strong>?</p>
                <p class="text-danger">This action cannot be undone and will delete all associated data including orders and cart items.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Delete user confirmation
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
        let userIdToDelete = null;

        document.querySelectorAll('.delete-user').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const username = this.dataset.username;

                userIdToDelete = id;
                document.getElementById('userName').textContent = username;
                deleteModal.show();
            });
        });

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (userIdToDelete) {
                const formData = new FormData();
                formData.append('id', userIdToDelete);

                fetch('<?php echo SITE_URL; ?>admin/delete-user', {
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
                        alert('An error occurred while deleting the user');
                        deleteModal.hide();
                    });
            }
        });
    });
</script>