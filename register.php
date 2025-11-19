<?php
// Start output buffering
ob_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/func.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new DBFunc();
$error = "";

// Prevent logged-in users from accessing registration
if ($db->isLoggedIn()) {
    header("Location: " . ($db->getUserRole() === 'admin' ? "dashboard.php" : "index.php"));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'] ?? 'user';
    $pass = $_POST['password'];
    $confirmPass = $_POST['confirmPassword'];

    if (!empty($name) && !empty($email) && !empty($pass) && !empty($confirmPass)) {
        if ($pass !== $confirmPass) {
            $error = "❌ Passwords do not match!";
        } else {
            try {
                $db->registerUser($name, $email, $role, $pass);
                // registerUser handles redirect after success
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    } else {
        $error = "⚠️ All fields are required.";
    }
}

// Clear any unwanted output
ob_clean();

// Include header
include("includes/header.php");
?>

<!-- Register Hero Section -->
<section class="auth-hero">
    <div class="container">
        <div class="auth-hero-content">
            <h1 class="auth-title">Join StevenPort</h1>
            <p class="auth-subtitle">Create your account to access the portfolio dashboard</p>
        </div>
    </div>
</section>

<?php if (!empty($error)): ?>
    <div class="alert alert-error" style="margin: 20px auto; max-width: 600px;">
        <span class="alert-icon">⚠️</span>
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Register Form Section -->
<section class="auth-form-section">
    <div class="container">
        <div class="auth-form-container">
            <div class="auth-form-card">
                <div class="form-header">
                    <h2>Create Account</h2>
                    <p>Fill in your details to get started</p>
                </div>

                <form method="post" class="auth-form" id="registerForm">
                    <div class="form-group">
                        <label for="username">Full Name</label>
                        <div class="input-group">
                            <span class="input-icon">👤</span>
                            <input type="text" id="username" name="username" required 
                                   value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
                                   placeholder="Enter your full name">
                        </div>
                    </div>

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
                                   placeholder="Create a strong password">
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <span class="toggle-icon">👁️</span>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strengthFill"></div>
                            </div>
                            <span class="strength-text" id="strengthText">Password strength</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="confirmPassword" name="confirmPassword" required
                                   placeholder="Confirm your password">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="role">Account Type</label>
                        <div class="input-group">
                            <span class="input-icon">⚙️</span>
                            <select id="role" name="role" required>
                                <option value="user" <?php echo (isset($role) && $role === 'user') ? 'selected' : ''; ?>>User</option>
                                <option value="admin" <?php echo (isset($role) && $role === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="agreeTerms" required>
                            <span class="checkmark"></span>
                            I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary auth-submit">
                        <span class="btn-text">Create Account</span>
                        <span class="btn-loading">
                            <span class="spinner"></span>
                            Creating Account...
                        </span>
                    </button>
                </form>

                <div class="form-footer">
                    <p>Already have an account? <a href="login.php" class="auth-link">Sign in here</a></p>
                </div>
            </div>
            
            <!-- Register Features -->
            <div class="auth-features">
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3>Access Projects</h3>
                    <p>View and interact with all portfolio projects and tools</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Dashboard Access</h3>
                    <p>Get personalized access to your portfolio dashboard</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🚀</div>
                    <h3>Stay Updated</h3>
                    <p>Receive notifications about new projects and updates</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional CSS for Register Page -->
<style>
/* Password Strength Indicator */
.password-strength {
    margin-top: var(--space-2);
}

.strength-bar {
    width: 100%;
    height: 4px;
    background: var(--bg-tertiary);
    border-radius: var(--radius-full);
    overflow: hidden;
    margin-bottom: var(--space-1);
}

.strength-fill {
    height: 100%;
    width: 0%;
    transition: all var(--transition-normal);
    border-radius: var(--radius-full);
}

.strength-fill.weak {
    width: 25%;
    background: var(--danger-color);
}

.strength-fill.fair {
    width: 50%;
    background: var(--warning-color);
}

.strength-fill.good {
    width: 75%;
    background: var(--accent-color);
}

.strength-fill.strong {
    width: 100%;
    background: var(--success-color);
}

.strength-text {
    font-size: var(--font-size-xs);
    color: var(--text-muted);
    font-weight: 500;
}

/* Custom Checkbox */
.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: var(--space-3);
    cursor: pointer;
    font-size: var(--font-size-sm);
    line-height: 1.5;
    color: var(--text-secondary);
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-sm);
    background: var(--bg-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition-fast);
    flex-shrink: 0;
    margin-top: 2px;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.checkbox-label input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    color: white;
    font-size: var(--font-size-sm);
    font-weight: bold;
}

.checkbox-label:hover .checkmark {
    border-color: var(--primary-color);
}

.terms-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    transition: color var(--transition-fast);
}

.terms-link:hover {
    color: var(--primary-dark);
    text-decoration: underline;
}

/* Password confirmation validation */
.password-match {
    border-color: var(--success-color) !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
}

.password-mismatch {
    border-color: var(--danger-color) !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}
</style>

<!-- JavaScript for Register Page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission handling
    const registerForm = document.getElementById('registerForm');
    const submitBtn = document.querySelector('.auth-submit');
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            // Validate password match
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return;
            }
            
            // Show loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });
    }
    
    // Password strength checker
    const passwordInput = document.getElementById('password');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = checkPasswordStrength(password);
        
        strengthFill.className = 'strength-fill ' + strength.level;
        strengthText.textContent = strength.text;
    });
    
    function checkPasswordStrength(password) {
        let score = 0;
        let feedback = [];
        
        if (password.length >= 8) score += 1;
        else feedback.push('at least 8 characters');
        
        if (/[a-z]/.test(password)) score += 1;
        else feedback.push('lowercase letters');
        
        if (/[A-Z]/.test(password)) score += 1;
        else feedback.push('uppercase letters');
        
        if (/[0-9]/.test(password)) score += 1;
        else feedback.push('numbers');
        
        if (/[^A-Za-z0-9]/.test(password)) score += 1;
        else feedback.push('special characters');
        
        if (score <= 1) {
            return { level: 'weak', text: 'Weak password' };
        } else if (score <= 2) {
            return { level: 'fair', text: 'Fair password' };
        } else if (score <= 3) {
            return { level: 'good', text: 'Good password' };
        } else {
            return { level: 'strong', text: 'Strong password' };
        }
    }
    
    // Password confirmation validation
    const confirmPasswordInput = document.getElementById('confirmPassword');
    
    function validatePasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (confirmPassword.length > 0) {
            if (password === confirmPassword) {
                confirmPasswordInput.classList.remove('password-mismatch');
                confirmPasswordInput.classList.add('password-match');
            } else {
                confirmPasswordInput.classList.remove('password-match');
                confirmPasswordInput.classList.add('password-mismatch');
            }
        } else {
            confirmPasswordInput.classList.remove('password-match', 'password-mismatch');
        }
    }
    
    confirmPasswordInput.addEventListener('input', validatePasswordMatch);
    passwordInput.addEventListener('input', validatePasswordMatch);
    
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
        
        // Username validation
        if (fieldName === 'username' && value.length < 2) {
            field.classList.add('error');
            return false;
        }
        
        // Password validation
        if (fieldName === 'password' && value.length < 8) {
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
