<?php
include("db.php");
include("func.php");

$db = new DBFunc();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require login
if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();
}

// User role
$role = $_SESSION['role'] ?? $_COOKIE['role'] ?? 'user';

// --- Filters ---
$search = $_GET['search'] ?? '';
$limit = $_GET['limit'] ?? 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

// --- Handle form submissions (admin only) ---
if (in_array(strtolower($role), ['admin', 'superadmin']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_art'])) {
        $db->addAnimeArt($_POST['title'], $_POST['artist'], $_POST['tags'], $_FILES['image'], $_POST['description']);
    }
    if (isset($_POST['update_art'])) {
        $db->updateAnimeArt($_POST['id'], $_POST['title'], $_POST['artist'], $_POST['tags'], $_FILES['image'], $_POST['description']);
    }
    if (isset($_POST['delete_art'])) {
        $db->deleteAnimeArt($_POST['id']);
    }
}

// --- Fetch filtered artworks ---
$total = $db->countFilteredAnimeArt($search);
$artworks = $db->getFilteredAnimeArt($search, $limit, $offset);
$totalPages = ceil($total / $limit);
?>

<?php include("includes/header.php"); ?>

<section class="page-container fade-in">
  <h1 class="page-title">🎨 Anime Art Gallery</h1>
  <p class="subtitle">Browse artworks. Admins can also manage entries.</p>
  <a href="index.php" class="btn">Back</a>

  <!-- Filter/Search Bar -->
  <form method="get" class="filter-bar" style="margin:15px 0;">
      <input type="text" name="search" placeholder="Search by title, tag, or artist..." 
             value="<?= htmlspecialchars($search) ?>">
      <select name="limit">
        <?php foreach ([10, 30, 50] as $opt): ?>
          <option value="<?= $opt ?>" <?= $limit == $opt ? 'selected' : '' ?>><?= $opt ?> per page</option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-blue">Filter</button>
  </form>

  <!-- Add / Update Form (admin only) -->
  <?php if (in_array(strtolower($role), ['admin', 'superadmin'])): ?>
  <form method="POST" enctype="multipart/form-data" class="form-card">
    <h2>Add / Update Artwork</h2>
    <input type="hidden" name="id" id="art_id">
    <label>Title<input type="text" name="title" required></label>
    <label>Artist<input type="text" name="artist" placeholder="Optional"></label>
    <label>Tags<input type="text" name="tags" placeholder="e.g., fantasy, ai-art, waifu"></label>
    <label>Description<textarea name="description" rows="3"></textarea></label>
    <label>Image<input type="file" name="image" accept="image/*" required></label>
    <button type="submit" name="add_art" class="btn btn-primary">Add Artwork</button>
  </form>
  <hr style="margin:30px 0;">
  <?php endif; ?>

  <!-- Artworks Grid -->
  <div class="dashboard-grid">
    <?php if (!empty($artworks)): ?>
      <?php foreach ($artworks as $a): ?>
        <div class="card fade-in">
          <img loading="lazy" src="images/<?= htmlspecialchars($a['image']) ?>" alt="<?= htmlspecialchars($a['title']) ?>">
          <div class="info">
            <h3><?= htmlspecialchars($a['title']) ?></h3>
            <?php if (!empty($a['artist'])): ?>
              <p><strong>Artist:</strong> <?= htmlspecialchars($a['artist']) ?></p>
            <?php endif; ?>
            <?php if (!empty($a['tags'])): ?>
              <p><strong>Tags:</strong> <?= htmlspecialchars($a['tags']) ?></p>
            <?php endif; ?>
            <?php if (!empty($a['description'])): ?>
              <p><?= htmlspecialchars($a['description']) ?></p>
            <?php endif; ?>

            <!-- Delete button (admin only) -->
            <?php if (in_array(strtolower($role), ['admin', 'superadmin'])): ?>
            <form method="POST" style="margin-top:10px;">
              <input type="hidden" name="id" value="<?= $a['id'] ?>">
              <button type="submit" name="delete_art" class="btn btn-danger" onclick="return confirm('Delete this artwork?');">Delete</button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="text-align:center; margin-top:40px;">No artworks found.</p>
    <?php endif; ?>
  </div>

  <!-- Pagination -->
  <div class="pagination" style="margin-top:20px;text-align:center;">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>

</section>

<?php include("includes/footer.php"); ?>
