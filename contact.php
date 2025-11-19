<?php
include("db.php");
include("func.php");

$db = new DBFunc();

// Require login
if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'] ?? $_COOKIE['username'];
$role = $_SESSION['role'] ?? $_COOKIE['role'] ?? 'user';

// Handle form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message_text = trim($_POST['message'] ?? '');
    
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message_text)) {
        // Here you would typically save to database or send email
        // For now, we'll just show a success message
        $message = "Thank you for your message! I'll get back to you soon.";
        $messageType = 'success';
        
        // Clear form data
        $_POST = array();
    } else {
        $message = "Please fill in all required fields.";
        $messageType = 'error';
    }
}

// Include header
include("includes/header.php");
?>

<!-- Contact Hero Section -->
<section class="contact-hero">
    <div class="container">
        <div class="contact-hero-content">
            <h1 class="hero-title">Get In Touch</h1>
            <p class="hero-subtitle">
                Ready to start your next project? Let's discuss how I can help bring your ideas to life.
            </p>
        </div>
    </div>
</section>

<!-- Contact Content -->
<section class="contact-content">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="contact-form-section">
                <div class="form-header">
                    <h2>Send me a Message</h2>
                    <p>Fill out the form below and I'll get back to you as soon as possible.</p>
                </div>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="contact-form" id="contactForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" required 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                   placeholder="Enter your full name">
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   placeholder="Enter your email address">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" required 
                               value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>"
                               placeholder="What's this about?">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" required rows="6" 
                                  placeholder="Tell me about your project or question..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary submit-btn">
                        <span class="btn-text">Send Message</span>
                        <span class="btn-loading">Sending...</span>
                    </button>
                </form>
            </div>
            
            <!-- Contact Information -->
            <div class="contact-info-section">
                <div class="contact-info-card">
                    <h3>Contact Information</h3>
                    <p>Feel free to reach out through any of these channels:</p>
                    
                    <div class="contact-methods">
                        <div class="contact-method">
                            <div class="method-icon">📧</div>
                            <div class="method-details">
                                <h4>Email</h4>
                                <p>slewsteven2@gmail.com</p>
                                <a href="mailto:slewsteven2@gmail.com" class="method-link">Send Email</a>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <div class="method-icon">📱</div>
                            <div class="method-details">
                                <h4>Phone</h4>
                                <p>+(60) 11 5682 8699</p>
                                <a href="tel:+601156828699" class="method-link">Call Now</a>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <div class="method-icon">📍</div>
                            <div class="method-details">
                                <h4>Location</h4>
                                <p>Malaysia</p>
                                <a href="#" class="method-link">View on Map</a>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <div class="method-icon">⏰</div>
                            <div class="method-details">
                                <h4>Availability</h4>
                                <p>Monday - Friday</p>
                                <p>9:00 AM - 6:00 PM (GMT+8)</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Response Card -->
                <div class="response-time-card">
                    <div class="response-icon">⚡</div>
                    <h4>Quick Response</h4>
                    <p>I typically respond to messages within 24 hours. For urgent matters, please call directly.</p>
                </div>
                
                <!-- Social Links Card -->
                <div class="social-links-card">
                    <h4>Follow Me</h4>
                    <div class="social-links">
                        <a href="#" class="social-link" title="GitHub">
                            <span class="social-icon">📱</span>
                            <span class="social-name">GitHub</span>
                        </a>
                        <a href="#" class="social-link" title="LinkedIn">
                            <span class="social-icon">💼</span>
                            <span class="social-name">LinkedIn</span>
                        </a>
                        <a href="#" class="social-link" title="Twitter">
                            <span class="social-icon">🐦</span>
                            <span class="social-name">Twitter</span>
                        </a>
                        <a href="#" class="social-link" title="Portfolio">
                            <span class="social-icon">🌐</span>
                            <span class="social-name">Portfolio</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle">Common questions about my services and availability</p>
        </div>
        
        <div class="faq-grid">
            <div class="faq-item">
                <div class="faq-question">
                    <h3>What services do you offer?</h3>
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                    <p>I specialize in full-stack web development, including custom web applications, database design, API development, and responsive website design. I also offer consulting services for technical architecture and system optimization.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>What's your typical project timeline?</h3>
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                    <p>Project timelines vary depending on complexity and scope. Simple websites typically take 1-2 weeks, while complex web applications can take 1-3 months. I'll provide a detailed timeline during our initial consultation.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Do you provide ongoing support?</h3>
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                    <p>Yes! I offer ongoing maintenance and support packages for all projects. This includes bug fixes, security updates, feature enhancements, and technical support.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>What technologies do you work with?</h3>
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                    <p>My core technologies include PHP, HTML5, CSS3, JavaScript, MySQL, and various frameworks and libraries. I'm also experienced with API integration, responsive design, and performance optimization.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>How do you handle project communication?</h3>
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                    <p>I maintain regular communication throughout the project via email, phone calls, and video meetings. I provide progress updates and welcome client feedback at every stage of development.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <h3>Do you work with clients internationally?</h3>
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                    <p>Absolutely! I work with clients worldwide and am comfortable with remote collaboration. I'm available during Malaysian business hours (GMT+8) but can accommodate different time zones when needed.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional CSS for Contact Page -->
<style>
.contact-hero {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    color: white;
    padding: var(--space-20) 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.contact-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 30% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    animation: float 20s ease-in-out infinite;
}

.contact-hero-content {
    position: relative;
    z-index: 2;
}

.hero-title {
    font-size: var(--font-size-5xl);
    font-weight: 700;
    margin-bottom: var(--space-4);
}

.hero-subtitle {
    font-size: var(--font-size-xl);
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

.contact-content {
    padding: var(--space-16) 0;
}

.contact-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: var(--space-12);
    align-items: start;
}

.contact-form-section {
    background: var(--bg-secondary);
    padding: var(--space-8);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-medium);
}

.form-header {
    margin-bottom: var(--space-8);
}

.form-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--space-4);
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-6);
}

.contact-form .form-group {
    margin-bottom: var(--space-6);
}

.contact-form label {
    display: block;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--space-2);
}

.contact-form input,
.contact-form textarea {
    width: 100%;
    padding: var(--space-4);
    font-size: var(--font-size-base);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    background: var(--bg-primary);
    color: var(--text-primary);
    transition: all var(--transition-fast);
    font-family: var(--font-family);
}

.contact-form input:focus,
.contact-form textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.contact-form textarea {
    resize: vertical;
    min-height: 120px;
}

.submit-btn {
    position: relative;
    width: 100%;
    padding: var(--space-4) var(--space-6);
    font-size: var(--font-size-lg);
    font-weight: 600;
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all var(--transition-fast);
    overflow: hidden;
}

.submit-btn .btn-loading {
    display: none;
}

.submit-btn.loading .btn-text {
    display: none;
}

.submit-btn.loading .btn-loading {
    display: inline;
}

.contact-info-section {
    display: flex;
    flex-direction: column;
    gap: var(--space-6);
}

.contact-info-card,
.response-time-card,
.social-links-card {
    background: var(--bg-primary);
    padding: var(--space-6);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-light);
    transition: all var(--transition-normal);
}

.contact-info-card:hover,
.response-time-card:hover,
.social-links-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-medium);
}

.contact-info-card h3 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--space-4);
    color: var(--primary-color);
}

.contact-methods {
    display: flex;
    flex-direction: column;
    gap: var(--space-6);
}

.contact-method {
    display: flex;
    gap: var(--space-4);
    align-items: flex-start;
}

.method-icon {
    font-size: var(--font-size-2xl);
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    flex-shrink: 0;
}

.method-details h4 {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin-bottom: var(--space-1);
    color: var(--text-primary);
}

.method-details p {
    color: var(--text-secondary);
    margin-bottom: var(--space-2);
}

.method-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    transition: color var(--transition-fast);
}

.method-link:hover {
    color: var(--primary-dark);
    text-decoration: underline;
}

.response-time-card {
    text-align: center;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
    border-color: var(--success-color);
}

.response-icon {
    font-size: var(--font-size-4xl);
    margin-bottom: var(--space-4);
}

.response-time-card h4 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--space-3);
    color: var(--success-color);
}

.social-links-card h4 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--space-4);
    color: var(--text-primary);
}

.social-links {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-3);
}

.social-link {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    padding: var(--space-3);
    background: var(--bg-secondary);
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--text-primary);
    transition: all var(--transition-fast);
    border: 1px solid var(--border-color);
}

.social-link:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    text-decoration: none;
    border-color: var(--primary-color);
}

.social-icon {
    font-size: var(--font-size-lg);
}

.social-name {
    font-weight: 500;
}

.faq-section {
    padding: var(--space-16) 0;
    background: var(--bg-secondary);
}

.faq-grid {
    display: grid;
    gap: var(--space-4);
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: all var(--transition-normal);
}

.faq-item:hover {
    box-shadow: var(--shadow-light);
}

.faq-question {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-6);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.faq-question:hover {
    background: var(--bg-tertiary);
}

.faq-question h3 {
    font-size: var(--font-size-lg);
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.faq-toggle {
    font-size: var(--font-size-xl);
    font-weight: 300;
    color: var(--primary-color);
    transition: transform var(--transition-fast);
}

.faq-item.active .faq-toggle {
    transform: rotate(45deg);
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height var(--transition-normal) ease-out;
}

.faq-item.active .faq-answer {
    max-height: 200px;
}

.faq-answer p {
    padding: 0 var(--space-6) var(--space-6);
    margin: 0;
    color: var(--text-secondary);
    line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
        gap: var(--space-8);
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: var(--space-4);
    }
    
    .contact-methods {
        gap: var(--space-4);
    }
    
    .social-links {
        grid-template-columns: 1fr;
    }
    
    .hero-title {
        font-size: var(--font-size-3xl);
    }
}

@media (max-width: 480px) {
    .contact-form-section {
        padding: var(--space-6);
    }
    
    .contact-info-section {
        gap: var(--space-4);
    }
    
    .contact-info-card,
    .response-time-card,
    .social-links-card {
        padding: var(--space-4);
    }
}
</style>

<!-- JavaScript for Contact Page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission handling
    const contactForm = document.getElementById('contactForm');
    const submitBtn = document.querySelector('.submit-btn');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            
            // Show loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            // Simulate form processing (remove this in production)
            setTimeout(() => {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            }, 2000);
        });
    }
    
    // FAQ Toggle functionality
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', function() {
            const isActive = item.classList.contains('active');
            
            // Close all other FAQ items
            faqItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                }
            });
            
            // Toggle current item
            item.classList.toggle('active', !isActive);
        });
    });
    
    // Form validation
    const formInputs = document.querySelectorAll('.contact-form input, .contact-form textarea');
    
    formInputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                validateField(this);
            }
        });
    });
    
    function validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        
        // Remove existing error state
        field.classList.remove('error');
        
        // Basic validation
        if (!value) {
            field.classList.add('error');
            return false;
        }
        
        // Email validation
        if (fieldName === 'email' && !isValidEmail(value)) {
            field.classList.add('error');
            return false;
        }
        
        return true;
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Add floating labels effect
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // Check if input has value on page load
        if (input.value) {
            input.parentElement.classList.add('focused');
        }
    });
});
</script>

<?php include("includes/footer.php"); ?>
