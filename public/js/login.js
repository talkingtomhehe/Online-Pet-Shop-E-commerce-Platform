/**
 * Form validation script for login form
 * Woof-woof PetShop
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize login validation only if login form exists
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;
    
    // Form fields with unique IDs
    const usernameField = document.getElementById('login_username');
    const passwordField = document.getElementById('login_password');
    const usernameError = document.getElementById('login_username_error');
    const passwordError = document.getElementById('login_password_error');
    
    // Add input event listeners
    usernameField.addEventListener('input', function() {
        validateUsername(this.value);
    });
    
    passwordField.addEventListener('input', function() {
        validatePassword(this.value);
    });
    
    // Form submission
    loginForm.addEventListener('submit', function(e) {
        // Validate all fields
        const isUsernameValid = validateUsername(usernameField.value);
        const isPasswordValid = validatePassword(passwordField.value);
        
        // Prevent form submission if validation fails
        if (!isUsernameValid || !isPasswordValid) {
            e.preventDefault();
        }
    });
    
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
        
        usernameField.classList.add('is-valid');
        return true;
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
        
        passwordField.classList.add('is-valid');
        return true;
    }
});