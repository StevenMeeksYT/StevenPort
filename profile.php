<?php
include("db.php");
include("func.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'] ?? $_COOKIE['username'];
$role = $_SESSION['role'] ?? $_COOKIE['role'] ?? 'user';

include("includes/header.php");
?>

<section class="page-container">
	<div class="content-box">
		<h1 class="mb-6">Profile</h1>
		<p class="mb-4">Signed in as <strong><?php echo htmlspecialchars($username); ?></strong><?php if ($role === 'admin') echo ' (admin)'; ?>.</p>
		<div class="dashboard-grid">
			<div class="card">
				<h3>Quick Actions</h3>
				<div class="mt-4">
					<a href="StevenFJCombineFull.pdf" target="_blank" rel="noopener" class="btn btn-secondary">Review Portfolio</a>
					<a href="StevenFJCombineFull.pdf" download class="btn btn-primary">Download Portfolio</a>
				</div>
			</div>
			<div class="card">
				<h3>Account</h3>
				<p class="mb-4">Manage your account settings and security.</p>
				<div class="mt-4">
					<a href="account_settings.php" class="btn btn-primary">Account Settings</a>
					<a href="logout.php" class="btn btn-outline">Logout</a>
				</div>
			</div>
			<?php if ($role === 'admin'): ?>
			<div class="card">
				<h3>Admin</h3>
				<p class="mb-4">Access administrative dashboard and tools.</p>
				<a href="dashboard.php" class="btn btn-primary">Open Dashboard</a>
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php include("includes/footer.php"); ?>


