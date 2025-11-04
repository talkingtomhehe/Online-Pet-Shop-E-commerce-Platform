document.addEventListener('DOMContentLoaded', function() {
    // Initialize registration validation only if registration form exists
    const registerForm = document.getElementById('registerForm');
    if (!registerForm) return;
    
    // Form fields with unique IDs
    const usernameField = document.getElementById('register_username');
    const emailField = document.getElementById('register_email');
    const passwordField = document.getElementById('register_password');
    const confirmPasswordField = document.getElementById('register_confirm_password');
    
    // Get error elements with unique IDs
    const usernameError = document.getElementById('register_username_error');
    const emailError = document.getElementById('register_email_error');
    const passwordError = document.getElementById('register_password_error');
    const confirmError = document.getElementById('register_confirm_password_error');
    
    // Regular expressions for validation
    const patterns = {
        username: /^[a-zA-Z0-9_]{5,20}$/,
        email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
        password: {
            length: /.{8,}/,
            lowercase: /[a-z]/,
            uppercase: /[A-Z]/,
            number: /[0-9]/,
            special: /[!@#$%^&*(),.?":{}|<>]/
        }
    };
    
    // Track if server validation is in progress
    let checkingUsername = false;
    let checkingEmail = false;
    
    // Debounce function
    function debounce(func, delay) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }
    
    // Add input event listeners
    usernameField.addEventListener('input', debounce(function() {
        validateUsername(this.value);
    }, 300));
    
    emailField.addEventListener('input', debounce(function() {
        validateEmail(this.value);
    }, 300));
    
    passwordField.addEventListener('input', function() {
        validatePassword(this.value);
        if (confirmPasswordField.value) {
            validateConfirmPassword(confirmPasswordField.value, this.value);
        }
        updatePasswordStrength(this.value);
    });
    
    confirmPasswordField.addEventListener('input', function() {
        validateConfirmPassword(this.value, passwordField.value);
    });
    
    // Form submission
    registerForm.addEventListener('submit', function(e) {
        // First, check if there are any ongoing validation checks
        if (checkingUsername || checkingEmail) {
            e.preventDefault();
            alert('Please wait while we validate your username and email.');
            return;
        }
        
        // Check if fields with error messages exist
        const usernameHasError = usernameField.classList.contains('is-invalid');
        const emailHasError = emailField.classList.contains('is-invalid');
        
        // Validate all fields
        const isPasswordValid = validatePassword(passwordField.value);
        const isConfirmValid = validateConfirmPassword(confirmPasswordField.value, passwordField.value);
        
        // Prevent form submission if validation fails
        if (usernameHasError || emailHasError || !isPasswordValid || !isConfirmValid) {
            e.preventDefault();
            
            // Show a message indicating which fields have errors
            if (usernameHasError) {
                usernameError.style.display = 'block';
            }
            
            if (emailHasError) {
                emailError.style.display = 'block';
            }
        }
    });
    
    // Add password strength indicator
    addPasswordStrengthIndicator();
    
    /**
     * Validate username
     */
    function validateUsername(username) {
        // Reset error message
        usernameError.textContent = '';
        usernameError.style.display = 'none';
        
        // Remove existing validation classes
        usernameField.classList.remove('is-invalid');
        usernameField.classList.remove('is-valid');
        
        if (username.trim() === '') {
            usernameError.textContent = 'Username is required';
            usernameError.style.display = 'block';
            usernameField.classList.add('is-invalid');
            return false;
        }
        
        if (username.length < 5) {
            usernameError.textContent = 'Username must be at least 5 characters';
            usernameError.style.display = 'block';
            usernameField.classList.add('is-invalid');
            return false;
        }
        
        if (!patterns.username.test(username)) {
            usernameError.textContent = 'Username can only contain letters, numbers and underscore';
            usernameError.style.display = 'block';
            usernameField.classList.add('is-invalid');
            return false;
        }
        
        // Check if username already exists
        checkUsernameExists(username);
        
        // Return true for now, the ajax callback will handle the validation result
        return !checkingUsername;
    }
    
    /**
     * Validate email
     */
    function validateEmail(email) {
        // Reset error message
        emailError.textContent = '';
        emailError.style.display = 'none';
        
        // Remove existing validation classes
        emailField.classList.remove('is-invalid');
        emailField.classList.remove('is-valid');
        
        if (email.trim() === '') {
            emailError.textContent = 'Email is required';
            emailError.style.display = 'block';
            emailField.classList.add('is-invalid');
            return false;
        }
        
        if (!patterns.email.test(email)) {
            emailError.textContent = 'Please enter a valid email address';
            emailError.style.display = 'block';
            emailField.classList.add('is-invalid');
            return false;
        }
        
        // Check if email already exists
        checkEmailExists(email);
        
        // Return true for now, the ajax callback will handle the validation result
        return !checkingEmail;
    }
    
    /**
     * Check if username already exists via AJAX
     */
    function checkUsernameExists(username) {
        // Only check if the username is valid according to pattern
        if (!patterns.username.test(username)) return;
        
        checkingUsername = true;
        
        // Add spinner to indicate checking
        usernameField.classList.add('is-checking');
        
        // Create URL with query parameter
        const url = new URL(window.location.origin + '/petshop/ajax/check-username');
        url.searchParams.append('username', username);
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                checkingUsername = false;
                usernameField.classList.remove('is-checking');
                
                if (data.exists) {
                    // Username already exists
                    usernameError.textContent = 'This username is already taken';
                    usernameError.style.display = 'block';
                    usernameField.classList.add('is-invalid');
                } else {
                    // Username is available
                    usernameField.classList.add('is-valid');
                }
            })
            .catch(error => {
                console.error('Error checking username:', error);
                checkingUsername = false;
                usernameField.classList.remove('is-checking');
            });
    }
    
    /**
     * Check if email already exists via AJAX
     */
    function checkEmailExists(email) {
        // Only check if the email is valid according to pattern
        if (!patterns.email.test(email)) return;
        
        checkingEmail = true;
        
        // Add spinner to indicate checking
        emailField.classList.add('is-checking');
        
        // Create URL with query parameter
        const url = new URL(window.location.origin + '/petshop/ajax/check-email');
        url.searchParams.append('email', email);
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                checkingEmail = false;
                emailField.classList.remove('is-checking');
                
                if (data.exists) {
                    // Email already exists
                    emailError.textContent = 'This email is already registered';
                    emailError.style.display = 'block';
                    emailField.classList.add('is-invalid');
                } else {
                    // Email is available
                    emailField.classList.add('is-valid');
                }
            })
            .catch(error => {
                console.error('Error checking email:', error);
                checkingEmail = false;
                emailField.classList.remove('is-checking');
            });
    }
    
    /**
     * Validate password
     */
    function validatePassword(password) {
        // Reset error message
        passwordError.textContent = '';
        passwordError.style.display = 'none';
        
        // Remove existing validation classes
        passwordField.classList.remove('is-invalid');
        passwordField.classList.remove('is-valid');
        
        if (password === '') {
            passwordError.textContent = 'Password is required';
            passwordError.style.display = 'block';
            passwordField.classList.add('is-invalid');
            return false;
        }
        
        if (!patterns.password.length.test(password)) {
            passwordError.textContent = 'Password must be at least 8 characters long';
            passwordError.style.display = 'block';
            passwordField.classList.add('is-invalid');
            return false;
        }
        
        // Count criteria met for complexity
        let criteriaCount = 0;
        if (patterns.password.lowercase.test(password)) criteriaCount++;
        if (patterns.password.uppercase.test(password)) criteriaCount++;
        if (patterns.password.number.test(password)) criteriaCount++;
        if (patterns.password.special.test(password)) criteriaCount++;
        
        if (criteriaCount < 3) {
            passwordError.textContent = 'Password must contain at least 3 of the following: lowercase, uppercase, number, special character';
            passwordError.style.display = 'block';
            passwordField.classList.add('is-invalid');
            return false;
        }
        
        // If we got here, password is valid
        passwordField.classList.add('is-valid');
        return true;
    }
    
    /**
     * Validate confirm password
     */
    function validateConfirmPassword(confirmPassword, password) {
        // Reset error message
        confirmError.textContent = '';
        confirmError.style.display = 'none';
        
        // Remove existing validation classes
        confirmPasswordField.classList.remove('is-invalid');
        confirmPasswordField.classList.remove('is-valid');
        
        if (confirmPassword === '') {
            confirmError.textContent = 'Please confirm your password';
            confirmError.style.display = 'block';
            confirmPasswordField.classList.add('is-invalid');
            return false;
        }
        
        if (confirmPassword !== password) {
            confirmError.textContent = 'Passwords do not match';
            confirmError.style.display = 'block';
            confirmPasswordField.classList.add('is-invalid');
            return false;
        }
        
        // If we got here, confirmation is valid
        confirmPasswordField.classList.add('is-valid');
        return true;
    }
    
    /**
     * Add password strength indicator
     */
    function addPasswordStrengthIndicator() {
        // Create container for strength indicator
        const strengthContainer = document.createElement('div');
        strengthContainer.className = 'password-strength mt-2';
        strengthContainer.innerHTML = `
            <div class="progress" style="height: 5px;">
                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <small class="strength-text text-muted mt-1 d-inline-block">Password strength: none</small>
        `;
        
        // Insert after password field parent (input group)
        passwordField.parentNode.parentNode.insertAdjacentElement('afterend', strengthContainer);
    }
    
    /**
     * Update password strength indicator
     */
    function updatePasswordStrength(password) {
        const strengthContainer = document.querySelector('.password-strength');
        if (!strengthContainer) return;
        
        const progressBar = strengthContainer.querySelector('.progress-bar');
        const strengthText = strengthContainer.querySelector('.strength-text');
        
        // Reset
        progressBar.style.width = '0%';
        progressBar.className = 'progress-bar';
        strengthText.textContent = 'Password strength: none';
        strengthText.className = 'strength-text text-muted mt-1 d-inline-block';
        
        if (password.length === 0) return;
        
        // Calculate strength score (0-100)
        let strength = 0;
        
        // Length
        if (password.length >= 8) strength += 25;
        
        // Character types
        if (patterns.password.lowercase.test(password)) strength += 15;
        if (patterns.password.uppercase.test(password)) strength += 15;
        if (patterns.password.number.test(password)) strength += 15;
        if (patterns.password.special.test(password)) strength += 30;
        
        // Update UI
        progressBar.style.width = `${strength}%`;
        
        if (strength < 30) {
            progressBar.className = 'progress-bar bg-danger';
            strengthText.textContent = 'Password strength: very weak';
            strengthText.className = 'strength-text text-danger mt-1 d-inline-block';
        } else if (strength < 50) {
            progressBar.className = 'progress-bar bg-warning';
            strengthText.textContent = 'Password strength: weak';
            strengthText.className = 'strength-text text-warning mt-1 d-inline-block';
        } else if (strength < 75) {
            progressBar.className = 'progress-bar bg-info';
            strengthText.textContent = 'Password strength: medium';
            strengthText.className = 'strength-text text-info mt-1 d-inline-block';
        } else {
            progressBar.className = 'progress-bar bg-success';
            strengthText.textContent = 'Password strength: strong';
            strengthText.className = 'strength-text text-success mt-1 d-inline-block';
        }
    }
});