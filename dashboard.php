<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';
require_once 'func.php';

// Check both session and cookie for authentication
if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();
}

// Use session if available, else fallback to cookie
$username = $_SESSION['username'] ?? $_COOKIE['username'];
$role = $_SESSION['role'] ?? $_COOKIE['role'] ?? 'user';

$db = new DBFunc();
$conn = $db->getConnection();

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Count totals
$totalUsers = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$totalTC = $conn->query("SELECT COUNT(*) AS c FROM tcdatabase")->fetch_assoc()['c'];
$totalProjects = $conn->query("SELECT COUNT(*) AS c FROM projects")->fetch_assoc()['c'];

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {
            case 'add_user':
                $username = trim($_POST['username']);
                $email = trim($_POST['email']);
                $password = $_POST['password'];
                $role = 'user'; // Default role for admin-created users
                $firstName = trim($_POST['first_name']);
                $lastName = trim($_POST['last_name']);

                if ($db->registerUser($username, $email, $role, $password)) {
                    $message = "User added successfully!";
                } else {
                    $error = "Failed to add user.";
                }
                break;

            case 'delete_user':
                $userId = (int)$_POST['user_id'];
                if ($db->deleteUser($userId)) {
                    $message = "User deleted successfully!";
                } else {
                    $error = "Failed to delete user.";
                }
                break;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get recent users and tropical cyclones
$recentUsers = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$recentTC = $conn->query("SELECT * FROM tcdatabase ORDER BY id DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<main class="page-container">
    <h1 class="page-title">Admin Dashboard</h1>

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

    <section class="cards-grid">
        <div class="card stat-card">
            <h2><?= $totalUsers ?></h2>
            <p>Registered Users</p>
        </div>

        <div class="card stat-card">
            <h2><?= $totalTC ?></h2>
            <p>Tropical Cyclone Records</p>
        </div>

        <div class="card stat-card">
            <h2><?= $totalProjects ?></h2>
            <p>Portfolio Projects</p>
        </div>
    </section>

    <!-- User Management Section -->
    <section class="admin-section">
        <div class="section-header">
            <h2>User Management</h2>
            <button class="btn btn-primary" onclick="toggleUserForm()">Add New User</button>
        </div>

        <!-- Add User Form -->
        <div id="user-form" class="card" style="display: none; margin-bottom: 20px;">
            <h3>Add New User</h3>
            <p class="form-note">
                <strong>Note:</strong> Users created by admin will have "user" role by default. Role selection is only available during registration.
            </p>
            <form method="post" action="">
                <input type="hidden" name="action" value="add_user">

                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required minlength="8">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Add User</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleUserForm()">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="card">
            <h3>Recent Users</h3>
            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentUsers)): ?>
                            <tr class="no-results">
                                <td colspan="6">No users found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="role-badge role-<?= htmlspecialchars($user['role']) ?>">
                                            <?= ucfirst(htmlspecialchars($user['role'])) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <a href="account_settings.php?user=<?= $user['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                        <?php if ($user['id'] != 1): // Don't allow deleting the first admin 
                                        ?>
                                            <button class="btn btn-danger btn-sm delete-user-btn" data-id="<?= $user['id'] ?>" data-username="<?= htmlspecialchars($user['username']) ?>">Delete</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Tropical Cyclone Management Section -->
    <section class="admin-section">
        <div class="section-header">
            <h2>Tropical Cyclone Management</h2>
            <a href="tc_database.php" class="btn btn-primary">Manage Database</a>
        </div>

        <div class="card">
            <h3>Recent Tropical Cyclones</h3>
            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Basin</th>
                            <th>Formed</th>
                            <th>MSW (mph)</th>
                            <th>MSLP (mbar)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentTC)): ?>
                            <tr class="no-results">
                                <td colspan="6">No tropical cyclones found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentTC as $tc): ?>
                                <tr>
                                    <td><?= htmlspecialchars($tc['id']) ?></td>
                                    <td><?= htmlspecialchars($tc['name'] ?? 'Unknown') ?></td>
                                    <td><?= htmlspecialchars($tc['basin'] ?? 'Unknown') ?></td>
                                    <td><?= htmlspecialchars($tc['formed'] ?? 'Unknown') ?></td>
                                    <td><?= htmlspecialchars($tc['msw'] ?? 'Unknown') ?></td>
                                    <td><?= htmlspecialchars($tc['mslp'] ?? 'Unknown') ?></td>
                                    <td>
                                        <a href="tc_database.php?view=<?= $tc['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                                        <a href="tc_database.php?edit=<?= $tc['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Tornado Management Section -->
    <section class="admin-section">
        <div class="section-header">
            <h2>Tornado Management</h2>
            <a href="tornado_db.php" class="btn btn-primary">Manage Database</a>
        </div>

        <div class="card">
            <h3>Recent Tornado Events</h3>
            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name / Location</th>
                            <th>Date</th>
                            <th>State</th>
                            <th>EF/F/IF Scale</th>
                            <th>Width (m)</th>
                            <th>Fatalities</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recentTornado = $db->getConnection()->query("
                    SELECT id, name, date, state, intensity_scale, path_width, fatalities 
                    FROM tornado_db 
                    ORDER BY date DESC 
                    LIMIT 5
                ")->fetch_all(MYSQLI_ASSOC);
                        ?>

                        <?php if (empty($recentTornado)): ?>
                            <tr class="no-results">
                                <td colspan="8">No tornado records found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentTornado as $t): ?>
                                <tr>
                                    <td><?= htmlspecialchars($t['id']) ?></td>
                                    <td><?= htmlspecialchars($t['name'] ?? 'Unknown') ?></td>
                                    <td><?= htmlspecialchars($t['date'] ?? 'Unknown') ?></td>
                                    <td><?= htmlspecialchars($t['state'] ?? 'Unknown') ?></td>
                                    <td><?= htmlspecialchars($t['intensity_scale'] ?? 'U') ?></td>
                                    <td><?= htmlspecialchars($t['path_width'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($t['fatalities'] ?? '0') ?></td>
                                    <td>
                                        <a href="tornado_db.php?view=<?= $t['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                                        <a href="tornado_db.php?edit=<?= $t['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Anime Art Gallery Management Section -->
    <section class="admin-section">
        <div class="section-header">
            <h2>Anime Art Gallery Management</h2>
            <a href="anime_admin.php" class="btn btn-primary">Manage Gallery</a>
        </div>

        <div class="card">
            <h3>Recent Anime Artworks</h3>
            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Artist</th>
                            <th>Tags</th>
                            <th>Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recentArt = $db->getConnection()->query("
                        SELECT id, title, artist, tags, created_at 
                        FROM anime_gallery 
                        ORDER BY created_at DESC 
                        LIMIT 5
                    ")->fetch_all(MYSQLI_ASSOC);
                        ?>

                        <?php if (empty($recentArt)): ?>
                            <tr class="no-results">
                                <td colspan="6">No artworks found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentArt as $art): ?>
                                <tr>
                                    <td><?= htmlspecialchars($art['id']) ?></td>
                                    <td><?= htmlspecialchars($art['title']) ?></td>
                                    <td><?= htmlspecialchars($art['artist'] ?: 'Unknown') ?></td>
                                    <td><?= htmlspecialchars($art['tags'] ?: 'None') ?></td>
                                    <td><?= date('M j, Y', strtotime($art['created_at'])) ?></td>
                                    <td>
                                        <a href="anime_admin.php?edit=<?= $art['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                        <a href="anime_gallery.php?view=<?= $art['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Portfolio CMS Management Section -->
    <section class="admin-section">
        <div class="section-header">
            <h2>Portfolio CMS Management</h2>
            <a href="cms_builder.php" class="btn btn-primary">Open CMS Builder</a>
        </div>

        <div class="card">
            <h3>Recent Portfolio Projects</h3>
            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Project Title</th>
                            <th>Category</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recentProjects = [];
                        if ($conn && $conn->connect_errno === 0) {
                            $res = $conn->query("SELECT id, title, category, created_at FROM projects ORDER BY created_at DESC LIMIT 5");
                            if ($res && $res->num_rows > 0) {
                                $recentProjects = $res->fetch_all(MYSQLI_ASSOC);
                            }
                        }
                        ?>

                        <?php if (empty($recentProjects)): ?>
                            <tr class="no-results">
                                <td colspan="5">No projects found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentProjects as $proj): ?>
                                <tr>
                                    <td><?= htmlspecialchars($proj['id']) ?></td>
                                    <td><?= htmlspecialchars($proj['title']) ?></td>
                                    <td><?= htmlspecialchars($proj['category'] ?? 'Portfolio') ?></td>
                                    <td><?= date('M j, Y', strtotime($proj['created_at'])) ?></td>
                                    <td>
                                        <a href="cms_builder.php?edit=<?= $proj['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                        <a href="cms_builder.php?view=<?= $proj['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Research Papers Archive Management Section -->
    <section class="admin-section">
        <div class="section-header">
            <h2>Research Papers Archive</h2>
            <a href="research_papers.php" class="btn btn-primary">Manage Papers</a>
        </div>

        <div class="card">
            <h3>Recent Research Papers</h3>
            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Authors</th>
                            <th>Year</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recentPapers = $conn->query("SELECT id, title, authors, year, category FROM research_papers ORDER BY year DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
                        ?>
                        <?php if (empty($recentPapers)): ?>
                            <tr class="no-results">
                                <td colspan="6">No research papers found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentPapers as $paper): ?>
                                <tr>
                                    <td><?= htmlspecialchars($paper['id']) ?></td>
                                    <td><?= htmlspecialchars($paper['title'] ?? 'Untitled') ?></td>
                                    <td><?= htmlspecialchars($paper['authors'] ?? 'Unknown') ?></td>
                                    <td><?= htmlspecialchars($paper['year'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($paper['category'] ?? 'General') ?></td>
                                    <td>
                                        <a href="research_papers.php?view=<?= $paper['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                                        <a href="research_papers.php?edit=<?= $paper['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Quick Actions -->
    <section class="admin-section">
        <h2>Quick Actions</h2>
        <div class="cards-grid">
            <div class="card action-card">
                <h3>CMS Builder</h3>
                <p>Manage portfolio projects and content.</p>
                <a href="cms_builder.php" class="btn btn-primary">Open CMS</a>
            </div>

            <div class="card action-card">
                <h3>System Monitor</h3>
                <p>Monitor system performance and logs.</p>
                <a href="sys_monitor.php" class="btn btn-primary">Open Monitor</a>
            </div>

            <div class="card action-card">
                <h3>Back to Home</h3>
                <p>Return to user-facing index page.</p>
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </section>
</main>

<!-- Delete User Confirmation Modal -->
<div id="deleteUserModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Delete User</h3>
        <p>Are you sure you want to delete user "<span id="deleteUsername"></span>"? This action cannot be undone.</p>
        <form id="deleteUserForm" method="post" action="">
            <input type="hidden" name="action" value="delete_user">
            <input type="hidden" name="user_id" id="deleteUserId">
            <div class="form-actions">
                <button type="submit" class="btn btn-danger">Delete</button>
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
    .admin-section {
        margin: 30px 0;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .section-header h2 {
        margin: 0;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .form-note {
        color: #666;
        font-size: 14px;
        margin-bottom: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        border-radius: 4px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .role-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }

    .role-user {
        background: #e3f2fd;
        color: #1976d2;
    }

    .role-admin {
        background: #fff3e0;
        color: #f57c00;
    }

    .role-superadmin {
        background: #fce4ec;
        color: #c2185b;
    }

    .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
    }

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        max-width: 400px;
        width: 90%;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
    }
</style>

<script>
    function toggleUserForm() {
        const form = document.getElementById('user-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    // Delete user functionality
    document.querySelectorAll('.delete-user-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.id;
            const username = this.dataset.username;

            document.getElementById('deleteUserId').value = userId;
            document.getElementById('deleteUsername').textContent = username;
            document.getElementById('deleteUserModal').style.display = 'flex';
        });
    });

    function closeDeleteModal() {
        document.getElementById('deleteUserModal').style.display = 'none';
    }

    // Close modal when clicking outside
    document.getElementById('deleteUserModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>

<?php include 'includes/footer.php'; ?>