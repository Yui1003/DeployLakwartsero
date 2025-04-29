</main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="footer-heading"><i class="fas fa-globe-americas"></i> Lakwartsero</h5>
                    <p>Your ultimate travel companion. We help you discover the world's most amazing destinations and create unforgettable experiences.</p>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-pinterest"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-md-2 mb-4 mb-md-0">
                    <h5 class="footer-heading">Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="destinations.php">Destinations</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <?php if (!isLoggedIn()): ?>
                            <li><a href="login.php">Login</a></li>
                            <li><a href="register.php">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5 class="footer-heading">Top Destinations</h5>
                    <ul class="footer-links">
                        <li><a href="destinations.php">Boracay</a></li>
                        <li><a href="destinations.php">Palawan</a></li>
                        <li><a href="destinations.php">Cebu</a></li>
                        <li><a href="destinations.php">Bohol</a></li>
                        <li><a href="destinations.php">Siargao</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3">
                    <h5 class="footer-heading">Contact Us</h5>
                    <address>
                        <p><i class="fas fa-map-marker-alt me-2"></i>CV38+C99, Rosario, Cavite</p>
                        <p><i class="fas fa-phone-alt me-2"></i> +63999-791-4791</p>
                        <p><i class="fas fa-envelope me-2"></i> rc.jomarie.avecilla@cvsu.edu.ph</p>
                    </address>
                    <div class="newsletter">
                        <h6>Subscribe to our Newsletter</h6>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Your Email">
                            <button class="btn btn-primary" type="button">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="footer-divider">
            
            <div class="row footer-bottom">
                <div class="col-md-6">
                    <p>&copy; <?php echo date('Y'); ?> Lakwartsero. All Rights Reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <ul class="footer-bottom-links">
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JS -->
    <script src="js/script.js"></script>
    <script src="js/animations.js"></script>
    
    <script>
        // Initialize AOS animations
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
        
        // Navbar transparency toggle on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    </script>
</body>
</html>