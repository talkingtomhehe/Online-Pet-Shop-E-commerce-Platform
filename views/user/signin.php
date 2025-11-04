<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="auth-card card border-0 shadow-sm">
                    <div class="card-header text-center border-0 bg-transparent pt-4">
                        <p class="auth-title mb-0 login-message">Login to your Woof-woof account</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form id="loginForm" method="post" action="<?php echo SITE_URL; ?>user/login">
                            <div class="mb-3">
                                <label for="login_username" class="form-label">Username</label>
                                <div class="input-group">
                                    <input type="text" class="form-control auth-input border-start-0" 
                                        id="login_username" name="username" 
                                        placeholder="Enter your username" required>
                                </div>
                                <div id="login_username_error" class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label for="login_password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control auth-input border-start-0" 
                                        id="login_password" name="password" 
                                        placeholder="Enter your password" required>
                                </div>
                                <div id="login_password_error" class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="login_remember_me" name="remember_me">
                                <label class="form-check-label" for="login_remember_me">Keep me signed in</label>
                                <small class="form-text text-muted d-block">
                                    Not recommended for shared computers
                                </small>
                            </div>
                            <div class="d-flex justify-content-end mb-4">
                                <a href="#" class="forgot-link">Forgot password?</a>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary auth-btn">Sign In</button>
                            </div>

                            <!-- Divider -->
                            <div class="position-relative my-4">
                                <hr>
                                <p class="position-absolute top-0 start-50 translate-middle bg-white px-3 text-muted small">or continue with</p>
                            </div>

                            <!-- Social Login Buttons - Enhanced Visibility -->
                            <div class="social-login mb-4">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <a href="<?php echo SITE_URL; ?>user/google-login" class="btn w-100 social-box" onclick="selectSocialBox(this, event)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48" class="me-2">
                                                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                                                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                                                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                            </svg>
                                            Google
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="<?php echo SITE_URL; ?>user/facebook-login" class="btn w-100 social-box" onclick="selectSocialBox(this, event)">
                                            <i class="bi bi-facebook me-2 text-primary"></i> Facebook
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <p class="mt-4 text-center">
                            Don't have an account? <a href="<?php echo SITE_URL; ?>user/signup" class="auth-link">Create Account</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>