<?php
include("func.php");

$db = new DBFunc();

// Require login
if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_project'])) {
        $db->addProject(
            $_POST['title'],
            $_POST['category'],
            $_POST['description'],
            $_FILES['image'],
            $_POST['link']
        );
    }

    if (isset($_POST['update_project'])) {
        $db->updateProject(
            $_POST['id'],
            $_POST['title'],
            $_POST['category'],
            $_POST['description'],
            $_FILES['image'],
            $_POST['link']
        );
    }

    if (isset($_POST['delete_project'])) {
        $db->deleteProject($_POST['id']);
    }
}

$projects = $db->getAllProjects();
?>
<?php include("includes/header.php"); ?>

<section class="page-container fade-in">
  <h1 class="page-title">Portfolio Projects Manager</h1>
  <p class="subtitle">Add, edit, or delete projects dynamically.</p>
  <a href="index.php" class="btn">Back</a>

  <form method="POST" enctype="multipart/form-data" class="form-card">
    <h2>Add / Update Project</h2>
    <input type="hidden" name="id" id="project_id">
    <label>Title</label>
    <input type="text" name="title" required>

    <label>Category</label>
    <input type="text" name="category" placeholder="e.g., Web App, Creative, System">

    <label>Description</label>
    <textarea name="description" rows="3"></textarea>

    <label>Image</label>
    <input type="file" name="image" accept="image/*">

    <label>Link</label>
    <input type="text" name="link" placeholder="https://...">

    <button type="submit" name="add_project" class="btn">Add Project</button>
  </form>

  <h2>All Projects</h2>
  <table class="styled-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Category</th>
        <th>Image</th>
        <th>Link</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($projects as $p): ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td><?= htmlspecialchars($p['title']) ?></td>
          <td><?= htmlspecialchars($p['category']) ?></td>
          <td>
            <?php if ($p['image']): ?>
              <img src="images/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
            <?php endif; ?>
          </td>
          <td>
            <?php if ($p['link']): ?>
              <a href="<?= htmlspecialchars($p['link']) ?>" target="_blank">View</a>
            <?php endif; ?>
          </td>
          <td>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button name="delete_project" class="btn btn-danger" onclick="return confirm('Delete this project?');">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php include("includes/footer.php"); ?>
