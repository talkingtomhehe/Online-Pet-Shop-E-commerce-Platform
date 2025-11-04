<?php
// Footer for all pages
?>
    </main>
    
    <button onclick="topFunction()" id="myBtn" title="Go to top">
        <i class="bi bi-arrow-up"></i>
    </button>

    <style>
        .footer-link {
            transition: color 0.3s ease;
        }
        .footer-link:hover {
            color: #b77c52 !important;
        }
        .social-link:hover {
            color: #b77c52 !important;
        }
    </style>
    
    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row">
                <!-- Contact Information -->
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0 text-start">
                    <h5 class="text-uppercase mb-4">Contact Us</h5>
                    <div class="mb-3">
                        <p class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i> 269 Đ. Lý Thường Kiệt, Phường 15, Quận 11, Hồ Chí Minh, Việt Nam</p>
                    </div>
                    <div class="mb-3">
                        <p class="mb-0"><i class="bi bi-telephone-fill me-2"></i> +66 2 123 4567</p>
                    </div>
                    <div class="mb-3">
                        <p class="mb-0"><i class="bi bi-envelope-fill me-2"></i> contact@woofwoofpetshop.com</p>
                    </div>
                    <div class="mb-3">
                        <p class="mb-0"><i class="bi bi-clock-fill me-2"></i> Mon - Sat: 9:00 AM - 7:00 PM</p>
                        <p class="mb-0 ps-4">Sun: 10:00 AM - 6:00 PM</p>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0 text-start">
                    <h5 class="text-uppercase mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?php echo SITE_URL; ?>" class="text-white text-decoration-none footer-link">
                                <i class="bi bi-house-door me-2"></i>Home
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo SITE_URL; ?>products" class="text-white text-decoration-none footer-link">
                                <i class="bi bi-shop me-2"></i>Products
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo SITE_URL; ?>contact" class="text-white text-decoration-none footer-link">
                                <i class="bi bi-envelope me-2"></i>Contact Us
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo SITE_URL; ?>user/profile" class="text-white text-decoration-none footer-link">
                                <i class="bi bi-person me-2"></i>My Account
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Social Media and Newsletter -->
                <div class="col-lg-4 col-md-12 text-start">
                    <h5 class="text-uppercase mb-4">Connect With Us</h5>
                    <div class="mb-4">
                        <a href="https://facebook.com/woofwoofpetshop" target="_blank" class="text-white me-3 fs-4 social-link">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="https://instagram.com/woofwoofpetshop" target="_blank" class="text-white me-3 fs-4 social-link">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="https://twitter.com/woofwoofpetshop" target="_blank" class="text-white me-3 fs-4 social-link">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="https://line.me/woofwoofpetshop" target="_blank" class="text-white me-3 fs-4 social-link">
                            <i class="bi bi-line"></i>
                        </a>
                    </div>
                    
                    <!-- Newsletter Subscription -->
                    <h5 class="text-uppercase mb-3">Subscribe to Our Newsletter</h5>
                    <form action="<?php echo SITE_URL; ?>newsletter/subscribe" method="post" class="mb-3">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Enter your email" name="email" required>
                            <button class="btn btn-primary" type="submit">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Copyright -->
            <div class="row mt-4">
                <div class="col-md-12 text-center">
                    <hr class="mb-4">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Back to top button
        var mybutton = document.getElementById("myBtn");
        
        window.onscroll = function() {scrollFunction()};
        
        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                mybutton.style.display = "block";
            } else {
                mybutton.style.display = "none";
            }
        }
        
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
    </script>
    
    <script src="<?php echo SITE_URL; ?>public/js/script.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the offcanvas element
    const offcanvasNavbar = document.getElementById('offcanvasNavbar');
    
    if (offcanvasNavbar) {
        // Add event listeners for offcanvas show/hide
        offcanvasNavbar.addEventListener('show.bs.offcanvas', function () {
            document.body.classList.add('offcanvas-active');
        });
        
        offcanvasNavbar.addEventListener('hidden.bs.offcanvas', function () {
            document.body.classList.remove('offcanvas-active');
        });
    }
});
</script>
</body>
</html>