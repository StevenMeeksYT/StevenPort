<?php
// Start output buffering to catch any unwanted output
ob_start();

require_once("db.php");
require_once("func.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$error = '';
$token = $_GET['token'] ?? '';

// Verify token
if (empty($token)) {
    header("Location: forgot_password.php");
    exit();
}

$db = new DBFunc();
$user = $db->getUserByResetToken($token);

if (!$user || strtotime($user['password_reset_expires']) < time()) {
    $error = "Invalid or expired reset token.";
    $token = '';
}

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif (strlen($newPassword) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($user && $db->resetPassword($user['username'], $newPassword)) {
        $message = "Password reset successfully! You can now log in with your new password.";
        $token = '';
    } else {
        $error = "Failed to reset password.";
    }
}

// Clear any unwanted output that might have been generated
ob_clean();

include("includes/header.php");
?>

<style>
.reset-password-container {
    max-width: 500px;
    margin: 50px auto;
    padding: 20px;
}

.reset-password-card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 40px;
    box-shadow: var(--shadow);
    text-align: center;
}

.reset-password-title {
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 10px;
    color: var(--text);
}

.reset-password-subtitle {
    color: var(--text-secondary);
    margin-bottom: 30px;
    font-size: 16px;
}

.form-group {
    margin-bottom: 20px;
    text-align: left;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--text);
}

.form-group input {
    width: 100%;
    padding: 12px;
    border: 2px solid var(--border);
    border-radius: 8px;
    background: var(--bg);
    color: var(--text);
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: var(--primary);
}

.btn-reset {
    width: 100%;
    padding: 12px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-reset:hover {
    background: var(--primary-dark);
}

.back-to-login {
    margin-top: 20px;
    text-align: center;
}

.back-to-login a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
}

.back-to-login a:hover {
    text-decoration: underline;
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert-success {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.alert-error {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.2);
}

.password-strength {
    margin-top: 5px;
    font-size: 12px;
}

.password-strength.weak {
    color: #dc3545;
}

.password-strength.medium {
    color: #ffc107;
}

.password-strength.strong {
    color: #28a745;
}
</style>

<section class="page-container">
    <?php if ($message): ?>
        <div class="alert alert-success" style="margin: 20px auto; max-width: 500px;">
            <span class="alert-icon">✅</span>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error" style="margin: 20px auto; max-width: 500px;">
            <span class="alert-icon">⚠️</span>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="reset-password-container">
        <div class="reset-password-card">
            <h1 class="reset-password-title">Reset Password</h1>
            <p class="reset-password-subtitle">Enter your new password below.</p>
            
            <?php if ($token && $user): ?>
                <form method="post" action="">
                    <input type="hidden" name="action" value="reset_password">
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required minlength="8" placeholder="Enter new password">
                        <div id="password-strength" class="password-strength"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="8" placeholder="Confirm new password">
                    </div>
                    
                    <button type="submit" class="btn-reset">Reset Password</button>
                </form>
            <?php endif; ?>
            
            <div class="back-to-login">
                <a href="login.php">← Back to Login</a>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const strengthIndicator = document.getElementById('password-strength');
    
    // Password strength checker
    function checkPasswordStrength(password) {
        let strength = 0;
        let feedback = [];
        
        if (password.length >= 8) strength++;
        else feedback.push('at least 8 characters');
        
        if (/[a-z]/.test(password)) strength++;
        else feedback.push('lowercase letters');
        
        if (/[A-Z]/.test(password)) strength++;
        else feedback.push('uppercase letters');
        
        if (/[0-9]/.test(password)) strength++;
        else feedback.push('numbers');
        
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        else feedback.push('special characters');
        
        return { strength, feedback };
    }
    
    // Update password strength indicator
    newPassword.addEventListener('input', function() {
        const password = this.value;
        const { strength, feedback } = checkPasswordStrength(password);
        
        if (password.length === 0) {
            strengthIndicator.textContent = '';
            strengthIndicator.className = 'password-strength';
        } else if (strength < 3) {
            strengthIndicator.textContent = 'Weak password. Add: ' + feedback.slice(0, 2).join(', ');
            strengthIndicator.className = 'password-strength weak';
        } else if (strength < 4) {
            strengthIndicator.textContent = 'Medium strength password';
            strengthIndicator.className = 'password-strength medium';
        } else {
            strengthIndicator.textContent = 'Strong password';
            strengthIndicator.className = 'password-strength strong';
        }
    });
    
    // Password confirmation validation
    function validatePassword() {
        if (newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Passwords don't match");
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    newPassword.addEventListener('change', validatePassword);
    confirmPassword.addEventListener('keyup', validatePassword);
});
</script>

<?php include("includes/footer.php"); ?>
