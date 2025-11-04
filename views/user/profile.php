<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">My Account</h5>
                    <div class="list-group list-group-flush">
                        <a href="<?php echo SITE_URL; ?>user/profile" class="list-group-item list-group-item-action active">Profile</a>
                        <a href="<?php echo SITE_URL; ?>user/orders" class="list-group-item list-group-item-action">Orders</a>
                        <a href="<?php echo SITE_URL; ?>user/logout" class="list-group-item list-group-item-action text-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">My Profile</h5>
                </div>
                <div class="card-body">
                    <!-- Show messages -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <!-- Avatar Section -->
                    <div class="text-center mb-4">
                        <?php
                        // Check if avatar is an external URL or local path
                        if (!empty($userData['avatar'])) {
                            if (strpos($userData['avatar'], 'http') === 0) {
                                // It's already a full URL (like from Google), use as is
                                $avatarUrl = $userData['avatar'];
                            } else {
                                // It's a local path, prepend SITE_URL
                                $avatarUrl = SITE_URL . str_replace('\\', '/', $userData['avatar']);
                            }
                        } else {
                            // No avatar, use default
                            $avatarUrl = SITE_URL . 'public/images/avatars/default.png';
                        }
                        ?>
                        <div class="position-relative d-inline-block">
                            <img src="<?php echo $avatarUrl; ?>" alt="Profile Avatar"
                                class="rounded-circle img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                            <button type="button" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle"
                                data-bs-toggle="modal" data-bs-target="#avatarModal" style="width: 30px; height: 30px; padding: 0;">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                        <h5 class="mt-3"><?php echo htmlspecialchars($userData['full_name'] ?? $userData['username']); ?></h5>
                    </div>

                    <form method="post" action="<?php echo SITE_URL; ?>user/profile">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?php echo htmlspecialchars($userData['username']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name"
                                value="<?php echo htmlspecialchars($userData['full_name'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                        </div>

                        <h5 class="mt-4 mb-3">Address Information</h5>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address"
                                value="<?php echo htmlspecialchars($userData['address'] ?? ''); ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city"
                                    value="<?php echo htmlspecialchars($userData['city'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code"
                                    value="<?php echo htmlspecialchars($userData['postal_code'] ?? ''); ?>">
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3">Change Password</h5>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                            <small class="form-text text-muted">Leave blank if you don't want to change your password.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Avatar Upload Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="avatarModalLabel">Change Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Update the avatar preview in modal if needed -->
            <div class="modal-body">
                <form id="avatar-form" action="<?php echo SITE_URL; ?>user/update-avatar" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Select an image file (JPG, PNG)</label>
                        <input class="form-control" type="file" id="avatar" name="avatar" accept=".jpg,.jpeg,.png">
                        <div class="form-text">
                            Maximum file size: 2MB. Recommended size: 300x300 pixels.
                        </div>
                    </div>
                    <div class="mb-3">
                        <div id="image-preview" class="text-center d-none">
                            <img id="preview" src="#" alt="Preview" class="rounded-circle img-thumbnail"
                                style="max-width: 150px; max-height: 150px; object-fit: cover;">
                        </div>
                    </div>
                    <!-- Add note about external avatar if user logged in with Google -->
                    <?php if (strpos($avatarUrl, 'http') === 0): ?>
                        <div class="alert alert-warning" role="alert">
                            You are using a Google account. To change your avatar, please update it in your Google account settings.
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo SITE_URL; ?>public/js/profile-validation.js"></script>

<script>
    // Image preview script
    document.addEventListener('DOMContentLoaded', function() {
        const avatarInput = document.getElementById('avatar');
        const imagePreview = document.getElementById('image-preview');
        const preview = document.getElementById('preview');

        avatarInput.addEventListener('change', function() {
            const file = this.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    imagePreview.classList.remove('d-none');
                }

                reader.readAsDataURL(file);
            } else {
                imagePreview.classList.add('d-none');
            }
        });
    });
</script>