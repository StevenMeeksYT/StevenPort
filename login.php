<?php
// Start output buffering
ob_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/func.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new DBFunc();
$error = '';

// Redirect already logged-in users
if ($db->isLoggedIn()) {
    $role = $db->getUserRole();
    header("Location: " . ($role === 'admin' || $role === 'superadmin' ? "dashboard.php" : "index.php"));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        try {
            $db->loginUser($email, $password);
            // loginUser handles redirect after success
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } else {
        $error = "⚠️ Please fill in all fields.";
    }
}

// Clear any unwanted output
ob_clean();

// Include header
include("includes/header.php");
?>

<!-- Login Hero Section -->
<section class="auth-hero">
    <div class="container">
        <div class="auth-hero-content">
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Sign in to access your portfolio dashboard</p>
        </div>
    </div>
</section>

<?php if (!empty($error)): ?>
    <div class="alert alert-error" style="margin: 20px auto; max-width: 600px;">
        <span class="alert-icon">⚠️</span>
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Login Form Section -->
<section class="auth-form-section">
    <div class="container">
        <div class="auth-form-container">
            <div class="auth-form-card">
                <div class="form-header">
                    <h2>Sign In</h2>
                    <p>Enter your credentials to access your account</p>
                </div>

                <form method="post" class="auth-form" id="loginForm">
                <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-group">
                            <span class="input-icon">📧</span>
                            <input type="email" id="email" name="email" required 
                                   value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                                   placeholder="Enter your email address">
                        </div>
                </div>

                <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="password" name="password" required
                                   placeholder="Enter your password">
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <span class="toggle-icon">👁️</span>
                            </button>
                        </div>
                </div>

                    <button type="submit" class="btn btn-primary auth-submit">
                        <span class="btn-text">Sign In</span>
                        <span class="btn-loading">
                            <span class="spinner"></span>
                            Signing In...
                        </span>
                    </button>
            </form>

                <div class="form-footer">
                    <p>Don't have an account? <a href="register.php" class="auth-link">Create one here</a></p>
                    <p><a href="forgot_password.php" class="auth-link">Forgot your password?</a></p>
                </div>
            </div>
            
            <!-- Login Features -->
            <div class="auth-features">
                <div class="feature-card">
                    <div class="feature-icon">🚀</div>
                    <h3>Fast Access</h3>
                    <p>Quick and secure login to your portfolio dashboard</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🔐</div>
                    <h3>Secure</h3>
                    <p>Your data is protected with industry-standard security</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Dashboard</h3>
                    <p>Access all your projects and tools from one place</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional CSS for Login Page -->
<style>
.auth-hero {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    color: white;
    padding: var(--space-20) 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.auth-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 30% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    animation: float 20s ease-in-out infinite;
}

.auth-hero-content {
    position: relative;
    z-index: 2;
}

.auth-title {
    font-size: var(--font-size-5xl);
    font-weight: 700;
    margin-bottom: var(--space-4);
}

.auth-subtitle {
    font-size: var(--font-size-xl);
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

.auth-form-section {
    padding: var(--space-16) 0;
}

.auth-form-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: var(--space-12);
    align-items: start;
    max-width: 1000px;
    margin: 0 auto;
}

.auth-form-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-xl);
    padding: var(--space-8);
    box-shadow: var(--shadow-large);
    position: relative;
    overflow: hidden;
}

.auth-form-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--primary-light), var(--accent-color));
}

.form-header {
    text-align: center;
    margin-bottom: var(--space-8);
}

.form-header h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--space-3);
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.form-header p {
    color: var(--text-secondary);
    font-size: var(--font-size-lg);
}

.alert {
    padding: var(--space-4);
    border-radius: var(--radius-md);
    margin-bottom: var(--space-6);
    display: flex;
    align-items: center;
    gap: var(--space-3);
    font-weight: 500;
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger-color);
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.alert-icon {
    font-size: var(--font-size-lg);
}

.auth-form .form-group {
    margin-bottom: var(--space-6);
}

.auth-form label {
    display: block;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--space-2);
    font-size: var(--font-size-sm);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.input-group {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: var(--space-4);
    font-size: var(--font-size-lg);
    color: var(--text-muted);
    z-index: 2;
}

.auth-form input,
.auth-form select {
    width: 100%;
    padding: var(--space-4) var(--space-4) var(--space-4) var(--space-12);
    font-size: var(--font-size-base);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    background: var(--bg-secondary);
    color: var(--text-primary);
    transition: all var(--transition-fast);
    font-family: var(--font-family);
}

.auth-form input:focus,
.auth-form select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    background: var(--bg-primary);
}

.password-toggle {
    position: absolute;
    right: var(--space-4);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-muted);
    font-size: var(--font-size-lg);
    z-index: 2;
    padding: var(--space-2);
    border-radius: var(--radius-sm);
    transition: color var(--transition-fast);
}

.password-toggle:hover {
    color: var(--primary-color);
}

.auth-submit {
    width: 100%;
    padding: var(--space-4);
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin-top: var(--space-6);
    position: relative;
    overflow: hidden;
}

.auth-submit .btn-loading {
    display: none;
}

.auth-submit.loading .btn-text {
    display: none;
}

.auth-submit.loading .btn-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
}

.spinner {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top: 2px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.form-footer {
    text-align: center;
    margin-top: var(--space-6);
    padding-top: var(--space-6);
    border-top: 1px solid var(--border-color);
}

.form-footer p {
    color: var(--text-secondary);
    margin: 0;
}

.auth-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    transition: color var(--transition-fast);
}

.auth-link:hover {
    color: var(--primary-dark);
    text-decoration: underline;
}

.auth-features {
    display: flex;
    flex-direction: column;
    gap: var(--space-6);
}

.feature-card {
    background: var(--bg-secondary);
    padding: var(--space-6);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-color);
    text-align: center;
    transition: all var(--transition-normal);
}

.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-medium);
    border-color: var(--primary-color);
}

.feature-icon {
    font-size: var(--font-size-4xl);
    margin-bottom: var(--space-4);
}

.feature-card h3 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--space-3);
    color: var(--text-primary);
}

.feature-card p {
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0;
}

/* Animations */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .auth-form-container {
        grid-template-columns: 1fr;
        gap: var(--space-8);
    }
    
    .auth-features {
        flex-direction: row;
        overflow-x: auto;
        gap: var(--space-4);
    }
    
    .feature-card {
        min-width: 200px;
        flex-shrink: 0;
    }
    
    .auth-title {
        font-size: var(--font-size-3xl);
    }
}

@media (max-width: 480px) {
    .auth-form-card {
        padding: var(--space-6);
    }
    
    .auth-features {
        flex-direction: column;
    }
}
</style>

<!-- JavaScript for Login Page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission handling
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.querySelector('.auth-submit');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            // Show loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });
    }
    
    // Form validation
    const inputs = document.querySelectorAll('.auth-form input, .auth-form select');
    
    inputs.forEach(input => {
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
    inputs.forEach(input => {
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

function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.toggle-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.textContent = '🙈';
    } else {
        passwordInput.type = 'password';
        toggleIcon.textContent = '👁️';
    }
}
</script>

<?php include("includes/footer.php"); ?>
