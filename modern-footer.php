    </div><!-- End of content-container -->

    <!-- Footer -->
    <footer class="footer mt-5 py-4 bg-white shadow-sm">
        <div class="container">
            <div class="row">
                <!-- Copyright -->
                <div class="col-md-4 mb-4 mb-md-0">
                    <h6 class="text-primary font-semibold mb-3">Flipoo Medical</h6>
                    <p class="text-muted mb-2 small">Comprehensive medical management system for healthcare professionals.</p>
                    <p class="text-muted small">&copy; <?php echo date('Y'); ?> Flipoo Medical. All rights reserved.</p>
                </div>
                
                <!-- Quick Links -->
                <div class="col-md-4 mb-4 mb-md-0">
                    <h6 class="text-primary font-semibold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-decoration-none text-muted small"><i class="fas fa-chevron-right me-1 small"></i> Dashboard</a></li>
                        <li class="mb-2"><a href="patients.php" class="text-decoration-none text-muted small"><i class="fas fa-chevron-right me-1 small"></i> Patients</a></li>
                        <li class="mb-2"><a href="visitRecord.php" class="text-decoration-none text-muted small"><i class="fas fa-chevron-right me-1 small"></i> Visit Records</a></li>
                        <li class="mb-2"><a href="medicineDashboard.php" class="text-decoration-none text-muted small"><i class="fas fa-chevron-right me-1 small"></i> Medicines</a></li>
                        <li><a href="misDashboard.php" class="text-decoration-none text-muted small"><i class="fas fa-chevron-right me-1 small"></i> Reports</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div class="col-md-4">
                    <h6 class="text-primary font-semibold mb-3">Contact</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2 d-flex align-items-center text-muted small">
                            <i class="fas fa-map-marker-alt me-2"></i> 
                            <span>123 Medical Center, Healthcare Avenue</span>
                        </li>
                        <li class="mb-2 d-flex align-items-center text-muted small">
                            <i class="fas fa-phone me-2"></i> 
                            <span>+1 (555) 123-4567</span>
                        </li>
                        <li class="mb-2 d-flex align-items-center text-muted small">
                            <i class="fas fa-envelope me-2"></i> 
                            <span>support@flipoomedical.com</span>
                        </li>
                    </ul>
                    
                    <!-- Social Media -->
                    <div class="mt-3">
                        <a href="#" class="text-decoration-none me-2">
                            <i class="fab fa-facebook-f text-primary"></i>
                        </a>
                        <a href="#" class="text-decoration-none me-2">
                            <i class="fab fa-twitter text-primary"></i>
                        </a>
                        <a href="#" class="text-decoration-none me-2">
                            <i class="fab fa-linkedin-in text-primary"></i>
                        </a>
                        <a href="#" class="text-decoration-none">
                            <i class="fab fa-instagram text-primary"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top Button -->
    <a href="#" id="backToTop" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </a>
    
    <!-- JavaScript Libraries -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js for data visualization (if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="assets/js/modern-app.js"></script>
    
    <!-- Page-specific JavaScript -->
    <?php if (isset($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        // Initialize back to top button
        document.addEventListener('DOMContentLoaded', function() {
            const backToTopButton = document.getElementById('backToTop');
            
            // Show/hide button based on scroll position
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTopButton.classList.add('show');
                } else {
                    backToTopButton.classList.remove('show');
                }
            });
            
            // Scroll to top when clicked
            backToTopButton.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            
            // Show welcome toast on first visit
            if (!localStorage.getItem('welcomeShown')) {
                setTimeout(() => {
                    showToast('Welcome to the new Flipoo Medical interface!', 'info', 5000);
                    localStorage.setItem('welcomeShown', 'true');
                }, 2000);
            }
        });
    </script>
</body>
</html> 