document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    
    alerts.forEach(function(alert) {
        // Create a progress bar for visual countdown
        const progressBar = document.createElement('div');
        progressBar.className = 'alert-progress';
        progressBar.style.cssText = `
            height: 3px;
            background-color: rgba(255, 255, 255, 0.7);
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            transition: width 5s linear;
        `;
        alert.style.position = 'relative';
        alert.appendChild(progressBar);
        
        // Start the animation
        setTimeout(() => {
            progressBar.style.width = '0%';
        }, 50);
        
        // Set timeout to remove the alert
        setTimeout(function() {
            // Only dismiss if alert still exists in DOM
            if (alert && alert.parentNode) {
                // Create fade-out effect
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                
                // Remove element after fade completes
                setTimeout(function() {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 500);
            }
        }, 5000);
    });
    
    // Manual dismiss for alerts with close buttons
    document.addEventListener('click', function(e) {
        // Find closest button with close role or btn-close class
        const closeButton = e.target.closest('[data-dismiss="alert"], .btn-close');
        
        if (closeButton) {
            const alert = closeButton.closest('.alert');
            if (alert) {
                // Create fade-out effect
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                
                // Remove element after fade completes
                setTimeout(function() {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 500);
            }
        }
    });
});