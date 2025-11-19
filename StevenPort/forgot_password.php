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

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_reset') {
    $email = trim($_POST['email']);
    
    if (!empty($email)) {
        $db = new DBFunc();
        $user = $db->getUserByEmail($email);
        
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            if ($db->setPasswordResetToken($user['username'], $token, $expires)) {
                // In a real application, you would send an email here
                // For demo purposes, we'll just show the reset link
                $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
                $message = "Password reset link: <a href='" . $resetLink . "'>" . $resetLink . "</a><br><small>This link expires in 1 hour.</small>";
            } else {
                $error = "Failed to generate reset token.";
            }
        } else {
            $message = "If an account with that email exists, a password reset link has been sent.";
        }
    } else {
        $error = "Please enter your email address.";
    }
}

// Clear any unwanted output that might have been generated
ob_clean();

include("includes/header.php");
?>

<style>
.forgot-password-container {
    max-width: 500px;
    margin: 50px auto;
    padding: 20px;
}

.forgot-password-card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 40px;
    box-shadow: var(--shadow);
    text-align: center;
}

.forgot-password-title {
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 10px;
    color: var(--text);
}

.forgot-password-subtitle {
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

.reset-link {
    background: rgba(0, 123, 255, 0.1);
    border: 1px solid rgba(0, 123, 255, 0.2);
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
    word-break: break-all;
}

.reset-link a {
    color: #007bff;
    text-decoration: none;
}

.reset-link a:hover {
    text-decoration: underline;
}
</style>

<section class="page-container">
    <?php if ($message): ?>
        <div class="alert alert-success" style="margin: 20px auto; max-width: 500px;">
            <span class="alert-icon">✅</span>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error" style="margin: 20px auto; max-width: 500px;">
            <span class="alert-icon">⚠️</span>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="forgot-password-container">
        <div class="forgot-password-card">
            <h1 class="forgot-password-title">Forgot Password?</h1>
            <p class="forgot-password-subtitle">Enter your email address and we'll send you a link to reset your password.</p>
            
            <form method="post" action="">
                <input type="hidden" name="action" value="request_reset">
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email address">
                </div>
                
                <button type="submit" class="btn-reset">Send Reset Link</button>
            </form>
            
            <div class="back-to-login">
                <a href="login.php">← Back to Login</a>
            </div>
        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>
