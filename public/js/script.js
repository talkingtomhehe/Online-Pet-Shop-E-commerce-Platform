// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('navbar-scrolled');
    } else {
        navbar.classList.remove('navbar-scrolled');
    }
});

// Social login box selection
function selectSocialBox(element, event) {
    // Prevent immediate navigation
    event.preventDefault();
    
    // Remove selected class from all boxes
    document.querySelectorAll('.social-box').forEach(box => {
        box.classList.remove('selected');
    });
    
    // Add selected class to clicked box
    element.classList.add('selected');
    
    // Navigate after short delay to show selection
    setTimeout(function() {
        window.location = element.href;
    }, 300);
}