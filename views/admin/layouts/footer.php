<?php if (isset($_SESSION['admin_id'])): ?>
            </main>
        </div>
    </div>
<?php else: ?>
    </div>
<?php endif; ?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom scripts for admin panel -->
<script>
    // Handle sidebar toggle for mobile
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarMenu = document.getElementById('sidebarMenu');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebarMenu.classList.toggle('show');
                sidebarBackdrop.classList.toggle('show');
            });
        }
        
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', function() {
                sidebarMenu.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
            });
        }
        
        // Close sidebar when clicking a nav item on mobile
        const navLinks = document.querySelectorAll('#sidebarMenu .nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    sidebarMenu.classList.remove('show');
                    sidebarBackdrop.classList.remove('show');
                }
            });
        });
    });
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>

<footer class="text-center mt-4 py-3 text-muted">
    <p class="mb-0">&copy; <?php echo date('Y'); ?> Woof-woof Admin Panel. All rights reserved.</p>
</footer>

</body>
</html>