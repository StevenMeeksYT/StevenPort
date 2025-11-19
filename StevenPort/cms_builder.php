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

$db = new DBFunc();
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'add_project':
                $title = trim($_POST['title']);
                $description = trim($_POST['desc']);
                $image = $_FILES['img'] ?? null;
                $category = 'Portfolio'; // Default category
                $link = ''; // Default empty link
                
                if ($db->addProject($title, $category, $description, $image, $link)) {
                    $message = "Project added successfully!";
                } else {
                    $error = "Failed to add project.";
                }
                break;
                
            case 'update_project':
                $id = (int)$_POST['project_id'];
                $title = trim($_POST['title']);
                $description = trim($_POST['desc']);
                $image = $_FILES['img'] ?? null;
                $category = 'Portfolio'; // Default category
                $link = ''; // Default empty link
                
                if ($db->updateProject($id, $title, $category, $description, $image, $link)) {
                    $message = "Project updated successfully!";
                } else {
                    $error = "Failed to update project.";
                }
                break;
                
            case 'delete_project':
                $id = (int)$_POST['project_id'];
                if ($db->deleteProject($id)) {
                    $message = "Project deleted successfully!";
                } else {
                    $error = "Failed to delete project.";
                }
                break;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Fetch all projects
$projects = $db->getAllProjects();
?>
<?php include("includes/header.php"); ?>

<section class="page-container fade-in">
  <h1 class="page-title">Portfolio CMS Builder</h1>
  <p class="subtitle">Add or edit projects dynamically.</p>
  <a href="index.php" class="btn">Back</a>

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

  <div class="card" style="margin-top: 20px;">
    <h2 id="form-title">Add New Project</h2>
    <form id="project-form" method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" id="form-action" value="add_project">
      <input type="hidden" name="project_id" id="project-id" value="">
      
      <div class="form-group">
        <label for="title">Project Title</label>
        <input type="text" id="title" name="title" required>
      </div>

      <div class="form-group">
        <label for="desc">Description</label>
        <textarea id="desc" name="desc" rows="3" required></textarea>
      </div>

      <div class="form-group">
        <label for="img">Project Image</label>
        <input type="file" id="img" name="img" accept="image/*">
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary" id="submit-btn">Add Project</button>
        <button type="button" class="btn btn-secondary" id="cancel-btn" style="display: none;">Cancel</button>
      </div>
    </form>
  </div>

  <div class="card" style="margin-top: 30px;">
    <h2>Existing Projects</h2>
    <div class="table-container">
      <table class="styled-table">
        <thead>
          <tr><th>ID</th><th>Title</th><th>Category</th><th>Description</th><th>Image</th><th>Actions</th></tr>
        </thead>
        <tbody>
      <?php if (empty($projects)): ?>
        <tr class="no-results"><td colspan="6">No projects found. Add your first project above!</td></tr>
          <?php else: ?>
            <?php foreach ($projects as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['id'] ?? 'N/A') ?></td>
              <td><?= htmlspecialchars($p['title'] ?? 'Untitled') ?></td>
              <td><?= htmlspecialchars($p['category'] ?? 'Portfolio') ?></td>
              <td><?= htmlspecialchars(substr($p['description'] ?? 'No description', 0, 50)) ?><?= strlen($p['description'] ?? '') > 50 ? '...' : '' ?></td>
              <td>
                <?php if (!empty($p['image'])): ?>
                  <img loading="lazy" src="images/<?= htmlspecialchars($p['image']) ?>" alt="Project Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                <?php else: ?>
                  <span class="muted">No image</span>
                <?php endif; ?>
              </td>
              <td>
                <button class="btn btn-secondary edit-btn" data-id="<?= htmlspecialchars($p['id'] ?? '') ?>" data-title="<?= htmlspecialchars($p['title'] ?? '') ?>" data-desc="<?= htmlspecialchars($p['description'] ?? '') ?>">Edit</button>
                <button class="btn btn-danger delete-btn" data-id="<?= htmlspecialchars($p['id'] ?? '') ?>" data-title="<?= htmlspecialchars($p['title'] ?? '') ?>">Delete</button>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('project-form');
    const formTitle = document.getElementById('form-title');
    const formAction = document.getElementById('form-action');
    const projectId = document.getElementById('project-id');
    const titleInput = document.getElementById('title');
    const descInput = document.getElementById('desc');
    const submitBtn = document.getElementById('submit-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    
    // Edit button functionality
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            const desc = this.dataset.desc;
            
            // Populate form
            projectId.value = id;
            titleInput.value = title;
            descInput.value = desc;
            
            // Update form for edit mode
            formTitle.textContent = 'Edit Project';
            formAction.value = 'update_project';
            submitBtn.textContent = 'Update Project';
            cancelBtn.style.display = 'inline-block';
            
            // Scroll to form
            form.scrollIntoView({ behavior: 'smooth' });
        });
    });
    
    // Delete button functionality
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const title = this.dataset.title;
            
            if (confirm(`Are you sure you want to delete "${title}"? This action cannot be undone.`)) {
                // Create a form to submit delete request
                const deleteForm = document.createElement('form');
                deleteForm.method = 'POST';
                deleteForm.style.display = 'none';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_project';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'project_id';
                idInput.value = id;
                
                deleteForm.appendChild(actionInput);
                deleteForm.appendChild(idInput);
                document.body.appendChild(deleteForm);
                deleteForm.submit();
            }
        });
    });
    
    // Cancel button functionality
    cancelBtn.addEventListener('click', function() {
        // Reset form to add mode
        formTitle.textContent = 'Add New Project';
        formAction.value = 'add_project';
        projectId.value = '';
        titleInput.value = '';
        descInput.value = '';
        document.getElementById('img').value = '';
        submitBtn.textContent = 'Add Project';
        cancelBtn.style.display = 'none';
    });
});
</script>

<?php include("includes/footer.php"); ?>
