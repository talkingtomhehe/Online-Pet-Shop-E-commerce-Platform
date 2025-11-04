// Password validation for profile page
document.addEventListener('DOMContentLoaded', function() {
    // Only run the script if we're on the profile page with password fields
    const currentPasswordField = document.getElementById('current_password');
    const newPasswordField = document.getElementById('new_password');
    const confirmPasswordField = document.getElementById('confirm_password');
    
    if (!currentPasswordField || !newPasswordField || !confirmPasswordField) return;
    
    // Create error containers if they don't exist
    let newPasswordError = document.getElementById('new_password_error');
    let confirmPasswordError = document.getElementById('confirm_password_error');
    
    if (!newPasswordError) {
        newPasswordError = document.createElement('div');
        newPasswordError.id = 'new_password_error';
        newPasswordError.className = 'invalid-feedback';
        newPasswordField.parentNode.appendChild(newPasswordError);
    }
    
    if (!confirmPasswordError) {
        confirmPasswordError = document.createElement('div');
        confirmPasswordError.id = 'confirm_password_error';
        confirmPasswordError.className = 'invalid-feedback';
        confirmPasswordField.parentNode.appendChild(confirmPasswordError);
    }
    
    // Add password strength indicator
    addPasswordStrengthIndicator();
    
    // Regular expressions for validation
    const passwordPatterns = {
        length: /.{8,}/,
        lowercase: /[a-z]/,
        uppercase: /[A-Z]/,
        number: /[0-9]/,
        special: /[!@#$%^&*(),.?":{}|<>]/
    };
    
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
    newPasswordField.addEventListener('input', function() {
        validateNewPassword(this.value);
        if (confirmPasswordField.value) {
            validateConfirmPassword(confirmPasswordField.value, this.value);
        }
        updatePasswordStrength(this.value);
    });
    
    confirmPasswordField.addEventListener('input', function() {
        validateConfirmPassword(this.value, newPasswordField.value);
    });
    
    // Form submission validation
    const profileForm = document.querySelector('form[action*="user/profile"]');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            // Only validate password fields if the user is trying to change password
            if (currentPasswordField.value || newPasswordField.value || confirmPasswordField.value) {
                // Check if current password is filled
                if (!currentPasswordField.value) {
                    e.preventDefault();
                    currentPasswordField.classList.add('is-invalid');
                    
                    // Create error message if it doesn't exist
                    let currentPasswordError = document.getElementById('current_password_error');
                    if (!currentPasswordError) {
                        currentPasswordError = document.createElement('div');
                        currentPasswordError.id = 'current_password_error';
                        currentPasswordError.className = 'invalid-feedback';
                        currentPasswordField.parentNode.appendChild(currentPasswordError);
                    }
                    currentPasswordError.textContent = 'Please enter your current password';
                    currentPasswordError.style.display = 'block';
                    return;
                }
                
                // Validate new password and confirmation
                const isNewPasswordValid = validateNewPassword(newPasswordField.value);
                const isConfirmValid = validateConfirmPassword(confirmPasswordField.value, newPasswordField.value);
                
                if (!isNewPasswordValid || !isConfirmValid) {
                    e.preventDefault();
                }
            }
        });
    }
    
    /**
     * Validate new password
     */
    function validateNewPassword(password) {
        // Reset error message
        newPasswordError.textContent = '';
        newPasswordError.style.display = 'none';
        
        // Remove existing validation classes
        newPasswordField.classList.remove('is-invalid');
        newPasswordField.classList.remove('is-valid');
        
        if (password === '') {
            // If password is empty and current password is also empty, assume user doesn't want to change password
            if (currentPasswordField.value === '') {
                return true;
            }
            newPasswordError.textContent = 'New password is required';
            newPasswordError.style.display = 'block';
            newPasswordField.classList.add('is-invalid');
            return false;
        }
        
        if (!passwordPatterns.length.test(password)) {
            newPasswordError.textContent = 'Password must be at least 8 characters long';
            newPasswordError.style.display = 'block';
            newPasswordField.classList.add('is-invalid');
            return false;
        }
        
        // Count criteria met for complexity
        let criteriaCount = 0;
        if (passwordPatterns.lowercase.test(password)) criteriaCount++;
        if (passwordPatterns.uppercase.test(password)) criteriaCount++;
        if (passwordPatterns.number.test(password)) criteriaCount++;
        if (passwordPatterns.special.test(password)) criteriaCount++;
        
        if (criteriaCount < 3) {
            newPasswordError.textContent = 'Password must contain at least 3 of the following: lowercase, uppercase, number, special character';
            newPasswordError.style.display = 'block';
            newPasswordField.classList.add('is-invalid');
            return false;
        }
        
        // If we got here, password is valid
        newPasswordField.classList.add('is-valid');
        return true;
    }
    
    /**
     * Validate confirm password
     */
    function validateConfirmPassword(confirmPassword, password) {
        // Reset error message
        confirmPasswordError.textContent = '';
        confirmPasswordError.style.display = 'none';
        
        // Remove existing validation classes
        confirmPasswordField.classList.remove('is-invalid');
        confirmPasswordField.classList.remove('is-valid');
        
        // If password is empty, assume user doesn't want to change password
        if (password === '' && confirmPassword === '' && currentPasswordField.value === '') {
            return true;
        }
        
        if (confirmPassword === '') {
            confirmPasswordError.textContent = 'Please confirm your new password';
            confirmPasswordError.style.display = 'block';
            confirmPasswordField.classList.add('is-invalid');
            return false;
        }
        
        if (confirmPassword !== password) {
            confirmPasswordError.textContent = 'Passwords do not match';
            confirmPasswordError.style.display = 'block';
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
        // Check if it already exists
        if (document.querySelector('.password-strength')) return;
        
        // Create container for strength indicator
        const strengthContainer = document.createElement('div');
        strengthContainer.className = 'password-strength mt-2';
        strengthContainer.innerHTML = `
            <div class="progress" style="height: 5px;">
                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <small class="strength-text text-muted mt-1 d-inline-block">Password strength: none</small>
        `;
        
        // Insert after new password field
        newPasswordField.parentNode.appendChild(strengthContainer);
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
        if (passwordPatterns.lowercase.test(password)) strength += 15;
        if (passwordPatterns.uppercase.test(password)) strength += 15;
        if (passwordPatterns.number.test(password)) strength += 15;
        if (passwordPatterns.special.test(password)) strength += 30;
        
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