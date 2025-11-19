<?php
require_once("db.php");
require_once("func.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'] ?? $_COOKIE['username'];
$db = new DBFunc();
$user = $db->getUserByUsername($username);

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'update_profile':
                $newUsername = trim($_POST['username']);
                $newEmail = trim($_POST['email']);
                $firstName = trim($_POST['first_name']);
                $lastName = trim($_POST['last_name']);
                $bio = trim($_POST['bio']);
                
                if ($db->updateUserProfile($username, $newUsername, $newEmail, $firstName, $lastName, $bio)) {
                    $_SESSION['username'] = $newUsername;
                    setcookie("username", $newUsername, time() + (365 * 24 * 60 * 60), "/", "", false, true);
                    $message = "Profile updated successfully!";
                    $user = $db->getUserByUsername($newUsername);
                }
                break;
                
            case 'change_password':
                $currentPassword = $_POST['current_password'];
                $newPassword = $_POST['new_password'];
                $confirmPassword = $_POST['confirm_password'];
                
                if ($newPassword !== $confirmPassword) {
                    $error = "New passwords do not match.";
                } elseif (strlen($newPassword) < 8) {
                    $error = "Password must be at least 8 characters long.";
                } elseif ($db->changePassword($username, $currentPassword, $newPassword)) {
                    $message = "Password changed successfully!";
                } else {
                    $error = "Current password is incorrect.";
                }
                break;
                
            case 'update_preferences':
                $theme = $_POST['theme'] ?? 'light';
                $notifications = isset($_POST['notifications']) ? 1 : 0;
                $emailNotifications = isset($_POST['email_notifications']) ? 1 : 0;
                $language = $_POST['language'] ?? 'en';
                $timezone = $_POST['timezone'] ?? 'UTC';
                
                if ($db->updateUserPreferences($username, $theme, $notifications, $emailNotifications, $language, $timezone)) {
                    $message = "Preferences updated successfully!";
                }
                break;
                
            case 'delete_account':
                $confirmPassword = $_POST['confirm_password'];
                if ($db->deleteAccount($username, $confirmPassword)) {
                    session_destroy();
                    setcookie("username", "", time() - 3600, "/");
                    header("Location: login.php");
                    exit();
                } else {
                    $error = "Password confirmation failed.";
                }
                break;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

include("includes/header.php");
?>

<style>
.settings-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.settings-nav {
    display: flex;
    background: var(--card-bg);
    border-radius: 12px;
    padding: 8px;
    margin-bottom: 30px;
    box-shadow: var(--shadow);
}

.settings-nav button {
    flex: 1;
    padding: 12px 20px;
    border: none;
    background: transparent;
    color: var(--text);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.settings-nav button.active {
    background: var(--primary);
    color: white;
}

.settings-nav button:hover:not(.active) {
    background: var(--hover-bg);
}

.settings-section {
    display: none;
    background: var(--card-bg);
    border-radius: 12px;
    padding: 30px;
    box-shadow: var(--shadow);
}

.settings-section.active {
    display: block;
}

.section-title {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 20px;
    color: var(--text);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--text);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid var(--border);
    border-radius: 8px;
    background: var(--bg);
    color: var(--text);
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.checkbox-group input[type="checkbox"] {
    width: auto;
}

.danger-zone {
    border: 2px solid #dc3545;
    border-radius: 12px;
    padding: 20px;
    background: rgba(220, 53, 69, 0.05);
}

.danger-zone h3 {
    color: #dc3545;
    margin-bottom: 15px;
}

.btn-danger {
    background: #dc3545;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.btn-danger:hover {
    background: #c82333;
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

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .settings-nav {
        flex-direction: column;
    }
    
    .settings-nav button {
        margin-bottom: 5px;
    }
}
</style>

<section class="page-container">
    <div class="settings-container">
        <h1 class="page-title">Account Settings</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success">
                <span class="alert-icon">✅</span>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <span class="alert-icon">⚠️</span>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <nav class="settings-nav">
            <button class="nav-btn active" data-section="profile">👤 Profile</button>
            <button class="nav-btn" data-section="security">🔒 Security</button>
            <button class="nav-btn" data-section="preferences">⚙️ Preferences</button>
            <button class="nav-btn" data-section="danger">⚠️ Danger Zone</button>
        </nav>
        
        <!-- Profile Section -->
        <div id="profile" class="settings-section active">
            <h2 class="section-title">Profile Information</h2>
            <form method="post" action="">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" rows="4" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
        
        <!-- Security Section -->
        <div id="security" class="settings-section">
            <h2 class="section-title">Security Settings</h2>
            
            <form method="post" action="" style="margin-bottom: 30px;">
                <input type="hidden" name="action" value="change_password">
                
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
            
            <div class="security-info">
                <h3>Security Information</h3>
                <p><strong>Account Created:</strong> <?php echo date('F j, Y', strtotime($user['created_at'] ?? 'now')); ?></p>
                <p><strong>Last Login:</strong> <?php echo date('F j, Y g:i A', strtotime($user['last_login'] ?? 'now')); ?></p>
                <p><strong>Role:</strong> <?php echo ucfirst($user['role'] ?? 'user'); ?></p>
            </div>
        </div>
        
        <!-- Preferences Section -->
        <div id="preferences" class="settings-section">
            <h2 class="section-title">Preferences</h2>
            <form method="post" action="">
                <input type="hidden" name="action" value="update_preferences">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="theme">Theme</label>
                        <select id="theme" name="theme">
                            <option value="light" <?php echo ($user['theme'] ?? 'light') === 'light' ? 'selected' : ''; ?>>Light</option>
                            <option value="dark" <?php echo ($user['theme'] ?? 'light') === 'dark' ? 'selected' : ''; ?>>Dark</option>
                            <option value="auto" <?php echo ($user['theme'] ?? 'light') === 'auto' ? 'selected' : ''; ?>>Auto</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="language">Language</label>
                        <select id="language" name="language">
                            <option value="en" <?php echo ($user['language'] ?? 'en') === 'en' ? 'selected' : ''; ?>>English</option>
                            <option value="es" <?php echo ($user['language'] ?? 'en') === 'es' ? 'selected' : ''; ?>>Spanish</option>
                            <option value="fr" <?php echo ($user['language'] ?? 'en') === 'fr' ? 'selected' : ''; ?>>French</option>
                            <option value="de" <?php echo ($user['language'] ?? 'en') === 'de' ? 'selected' : ''; ?>>German</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="timezone">Timezone</label>
                    <select id="timezone" name="timezone">
                        <option value="UTC" <?php echo ($user['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                        <option value="America/New_York" <?php echo ($user['timezone'] ?? 'UTC') === 'America/New_York' ? 'selected' : ''; ?>>Eastern Time</option>
                        <option value="America/Chicago" <?php echo ($user['timezone'] ?? 'UTC') === 'America/Chicago' ? 'selected' : ''; ?>>Central Time</option>
                        <option value="America/Denver" <?php echo ($user['timezone'] ?? 'UTC') === 'America/Denver' ? 'selected' : ''; ?>>Mountain Time</option>
                        <option value="America/Los_Angeles" <?php echo ($user['timezone'] ?? 'UTC') === 'America/Los_Angeles' ? 'selected' : ''; ?>>Pacific Time</option>
                        <option value="Europe/London" <?php echo ($user['timezone'] ?? 'UTC') === 'Europe/London' ? 'selected' : ''; ?>>London</option>
                        <option value="Europe/Paris" <?php echo ($user['timezone'] ?? 'UTC') === 'Europe/Paris' ? 'selected' : ''; ?>>Paris</option>
                        <option value="Asia/Tokyo" <?php echo ($user['timezone'] ?? 'UTC') === 'Asia/Tokyo' ? 'selected' : ''; ?>>Tokyo</option>
                        <option value="Asia/Shanghai" <?php echo ($user['timezone'] ?? 'UTC') === 'Asia/Shanghai' ? 'selected' : ''; ?>>Shanghai</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <h3>Notifications</h3>
                    <div class="checkbox-group">
                        <input type="checkbox" id="notifications" name="notifications" <?php echo ($user['notifications'] ?? 1) ? 'checked' : ''; ?>>
                        <label for="notifications">Enable notifications</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="email_notifications" name="email_notifications" <?php echo ($user['email_notifications'] ?? 1) ? 'checked' : ''; ?>>
                        <label for="email_notifications">Email notifications</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Preferences</button>
            </form>
        </div>
        
        <!-- Danger Zone Section -->
        <div id="danger" class="settings-section">
            <h2 class="section-title">Danger Zone</h2>
            
            <div class="danger-zone">
                <h3>Delete Account</h3>
                <p>Once you delete your account, there is no going back. Please be certain.</p>
                <form method="post" action="" onsubmit="return confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.');">
                    <input type="hidden" name="action" value="delete_account">
                    
                    <div class="form-group">
                        <label for="confirm_password_delete">Confirm your password to delete account</label>
                        <input type="password" id="confirm_password_delete" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn-danger">Delete Account</button>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navButtons = document.querySelectorAll('.nav-btn');
    const sections = document.querySelectorAll('.settings-section');
    
    navButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetSection = this.getAttribute('data-section');
            
            // Remove active class from all buttons and sections
            navButtons.forEach(btn => btn.classList.remove('active'));
            sections.forEach(section => section.classList.remove('active'));
            
            // Add active class to clicked button and corresponding section
            this.classList.add('active');
            document.getElementById(targetSection).classList.add('active');
        });
    });
    
    // Password confirmation validation
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
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
