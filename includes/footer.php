</main>

    <!-- Enhanced Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>StevenPort</h3>
                    <p>A modern portfolio showcasing web development projects, creative tools, and technical solutions.</p>
                    <div class="social-links">
                        <a href="#" class="social-link" title="GitHub">📱</a>
                        <a href="#" class="social-link" title="LinkedIn">💼</a>
                        <a href="#" class="social-link" title="Email">📧</a>
                        <a href="StevenFJCombineFull.pdf" target="_blank" rel="noopener" class="btn btn-secondary" style="margin-left:8px;">Review Portfolio</a>
                        <a href="StevenFJCombineFull.pdf" download class="btn btn-primary" style="margin-left:8px;">Download</a>
                    </div>
                </div>
                
                   <div class="footer-section">
                       <h3>Quick Links</h3>
                       <ul class="footer-links">
                           <li><a href="index.php">Home</a></li>
                           <li><a href="about.php">About</a></li>
                           <li><a href="projects.php">Projects</a></li>
                           <li><a href="contact.php">Contact</a></li>
                           <li><a href="https://wx24yt.wixsite.com/fr24/tropical-update-1" target="_blank" rel="noopener">🌪️ StevenWx</a></li>
                       </ul>
                   </div>
                
                <div class="footer-section">
                    <h3>Projects</h3>
                    <ul class="footer-links">
                        <li><a href="tc_database.php">TC Database</a></li>
                        <li><a href="weather_forecast.php">Weather Tracker</a></li>
                        <li><a href="anime_gallery.php">Anime Gallery</a></li>
                        <li><a href="media_tools.php">Media Tools</a></li>
                    </ul>
                </div>
                
                   <div class="footer-section">
                       <h3>Contact Info</h3>
                       <p>📧 slewsteven2@gmail.com</p>
                       <p>📱 +(60) 11 5682 8699</p>
                       <p>📍 Malaysia</p>
                   </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> StevenPort by Ng Yi Thern. All rights reserved.</p>
                <p class="footer-note">Built with ❤️ using PHP, HTML, CSS & JavaScript</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Enhanced Functionality -->
    <script>
        // Theme Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('themeToggle');
            const savedTheme = localStorage.getItem('theme') || 'light';
            
            // Apply saved theme
            document.documentElement.setAttribute('data-theme', savedTheme);
            updateThemeIcon(savedTheme);
            
            // Theme toggle event
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    const currentTheme = document.documentElement.getAttribute('data-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    
                    document.documentElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    updateThemeIcon(newTheme);
                });
            }
            
            function updateThemeIcon(theme) {
                if (themeToggle) {
                    themeToggle.textContent = theme === 'dark' ? '☀️' : '🌙';
                }
            }
            
            // Mobile Menu Toggle
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mobileNav = document.getElementById('mobileNav');
            
            if (mobileMenuToggle && mobileNav) {
                mobileMenuToggle.addEventListener('click', function() {
                    mobileMenuToggle.classList.toggle('active');
                    mobileNav.classList.toggle('active');
                    document.body.classList.toggle('menu-open');
                });
                
                // Close mobile menu when clicking on links
                const mobileNavLinks = mobileNav.querySelectorAll('.mobile-nav-link');
                mobileNavLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        mobileMenuToggle.classList.remove('active');
                        mobileNav.classList.remove('active');
                        document.body.classList.remove('menu-open');
                    });
                });
                
                // Close mobile menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!mobileMenuToggle.contains(e.target) && !mobileNav.contains(e.target)) {
                        mobileMenuToggle.classList.remove('active');
                        mobileNav.classList.remove('active');
                        document.body.classList.remove('menu-open');
                    }
                });
            }
            
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Add loading animation to buttons
            document.querySelectorAll('.btn').forEach(button => {
                button.addEventListener('click', function() {
                    if (this.type !== 'submit') {
                        this.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            this.style.transform = '';
                        }, 150);
                    }
                });
            });
            
            // Intersection Observer for fade-in animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            }, observerOptions);
            
            // Observe all cards and sections
            document.querySelectorAll('.card, .footer-section').forEach(el => {
                observer.observe(el);
            });
        });
        
        // Add floating animation to brand icon
        document.addEventListener('DOMContentLoaded', function() {
            const brandIcon = document.querySelector('.brand-icon');
            if (brandIcon) {
                setInterval(() => {
                    brandIcon.classList.add('float');
                    setTimeout(() => {
                        brandIcon.classList.remove('float');
                    }, 3000);
                }, 10000);
            }
        });
        
        // Enhanced form validation
        function validateForm(form) {
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('error');
                    isValid = false;
                } else {
                    input.classList.remove('error');
                }
            });
            
            return isValid;
        }
        
        // Add error styles for invalid inputs
        const footerStyle = document.createElement('style');
        footerStyle.textContent = `
            input.error, select.error, textarea.error {
                border-color: var(--danger-color) !important;
                box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
            }
            
            body.menu-open {
                overflow: hidden;
            }
            
            .social-links {
                display: flex;
                gap: var(--space-3);
                margin-top: var(--space-4);
            }
            
            .social-link {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                background: var(--bg-tertiary);
                border-radius: var(--radius-md);
                text-decoration: none;
                font-size: var(--font-size-lg);
                transition: all var(--transition-fast);
            }
            
            .social-link:hover {
                background: var(--primary-color);
                color: white;
                transform: translateY(-2px);
                text-decoration: none;
            }
            
            .footer-links {
                list-style: none;
                padding: 0;
            }
            
            .footer-links li {
                margin-bottom: var(--space-2);
            }
            
            .footer-note {
                margin-top: var(--space-2);
                font-size: var(--font-size-sm);
                opacity: 0.8;
            }
        `;
        document.head.appendChild(footerStyle);
    </script>
</body>
</html>
