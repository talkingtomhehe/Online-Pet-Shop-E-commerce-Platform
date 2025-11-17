<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-center border-0 bg-transparent pt-4">
                    <h3 class="mb-0">Admin Login</h3>
                    <p class="text-muted">Access the Woof-woof admin panel</p>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?php echo SITE_URL; ?>admin/login">
                        <div class="mb-3">
                            <label for="admin_username" class="form-label">Admin Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="bi bi-person-lock text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" 
                                    id="admin_username" name="admin_username" 
                                    placeholder="Enter admin username" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="admin_password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                                <input type="password" class="form-control border-start-0" 
                                    id="admin_password" name="admin_password" 
                                    placeholder="Enter admin password" required>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                            <label class="form-check-label" for="remember_me">Keep me signed in</label>
                            <small class="form-text text-muted d-block">
                                Not recommended for shared computers
                            </small>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Sign In to Admin</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="<?php echo SITE_URL; ?>" class="text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Return to Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>