<?php
include("db.php");
include("func.php");

if (session_status() === PHP_SESSION_NONE) session_start();

// Require login
if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'] ?? $_COOKIE['username'];
$role = $_SESSION['role'] ?? 'user';

$db = new DBFunc();

// --- Handle Form Submissions (Admin Only) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'admin') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $db->addResearchPaper(
                $_POST['title'],
                $_POST['authors'],
                $_POST['year'],
                $_POST['category'],
                $_POST['abstract'],
                $_FILES['file'] ?? null
            );
            break;

        case 'edit':
            $db->updateResearchPaper(
                $_POST['id'],
                $_POST['title'],
                $_POST['authors'],
                $_POST['year'],
                $_POST['category'],
                $_POST['abstract'],
                $_FILES['file'] ?? null
            );
            break;

        case 'delete':
            $db->deleteResearchPaper($_POST['id']);
            break;
    }

    header("Location: research_papers.php");
    exit();
}

// --- Filters ---
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$year = $_GET['year'] ?? '';
$limit = 10;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$total = $db->countFilteredPapers($search, $category, $year);
$papers = $db->getFilteredPapers($search, $category, $year, $limit, $offset);
$totalPages = ceil($total / $limit);

include("includes/header.php");
?>

<div class="container">
    <h2 class="page-title">📚 Research Papers Database</h2>

    <!-- Search & Filters -->
    <form method="get" class="search-bar">
        <input type="text" name="search" placeholder="Search title, authors, or abstract..." value="<?= htmlspecialchars($search) ?>">
        <input type="text" name="category" placeholder="Category" value="<?= htmlspecialchars($category) ?>">
        <input type="number" name="year" placeholder="Year" value="<?= htmlspecialchars($year) ?>">
        <button type="submit" class="btn">Search</button>

        <?php if ($role === 'admin'): ?>
            <button type="button" class="btn add-btn" onclick="toggleForm('formContainer')">+ Add Paper</button>
        <?php endif; ?>
    </form>

    <!-- Add/Edit Form (Admin Only) -->
    <?php if ($role === 'admin'): ?>
    <div id="formContainer" class="form-container" style="display:none;">
        <h3 id="formTitle">Add Research Paper</h3>
        <form method="post" enctype="multipart/form-data" class="styled-form">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" id="id">

            <label>Title</label>
            <input type="text" name="title" id="title" required>

            <label>Authors</label>
            <input type="text" name="authors" id="authors" required>

            <label>Year</label>
            <input type="number" name="year" id="year" min="1900" max="2100" required>

            <label>Category</label>
            <input type="text" name="category" id="category">

            <label>Abstract</label>
            <textarea name="abstract" id="abstract" rows="4"></textarea>

            <label>File Upload (PDF/DOCX/TXT)</label>
            <input type="file" name="file" id="file" accept=".pdf,.docx,.txt">

            <button type="submit" class="btn">Save</button>
            <button type="button" class="btn cancel-btn" onclick="toggleForm('formContainer')">Cancel</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Research Papers Table -->
    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Authors</th>
                    <th>Year</th>
                    <th>Category</th>
                    <th>File</th>
                    <?php if ($role === 'admin'): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($papers)): ?>
                    <tr class="no-results"><td colspan="<?= $role === 'admin' ? 7 : 6 ?>">No research papers found.</td></tr>
                <?php else: ?>
                    <?php foreach ($papers as $paper): ?>
                        <tr>
                            <td><?= htmlspecialchars($paper['id']) ?></td>
                            <td><?= htmlspecialchars($paper['title']) ?></td>
                            <td><?= htmlspecialchars($paper['authors']) ?></td>
                            <td><?= htmlspecialchars($paper['year']) ?></td>
                            <td><?= htmlspecialchars($paper['category']) ?></td>
                            <td>
                                <?php if (!empty($paper['file_name'])): ?>
                                    <div class="btn-group">
                                        <a href="papers/<?= htmlspecialchars($paper['file_name']) ?>" target="_blank" class="btn small-btn">View</a>
                                        <a href="papers/<?= htmlspecialchars($paper['file_name']) ?>" download class="btn small-btn btn-success">Download</a>
                                    </div>
                                <?php else: ?>
                                    <span class="muted">No file</span>
                                <?php endif; ?>
                            </td>

                            <?php if ($role === 'admin'): ?>
                            <td>
                                <button class="btn small-btn" onclick='editPaper(<?= json_encode($paper) ?>)'>Edit</button>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $paper['id'] ?>">
                                    <button type="submit" class="btn danger-btn" onclick="return confirm('Delete this paper?')">Delete</button>
                                </form>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&year=<?= urlencode($year) ?>" 
               class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function toggleForm(id) {
    const form = document.getElementById(id);
    form.style.display = form.style.display === "none" ? "block" : "none";
}

function editPaper(paper) {
    toggleForm('formContainer');
    document.getElementById('formTitle').innerText = "Edit Research Paper";
    document.querySelector('input[name="action"]').value = "edit";
    document.getElementById('id').value = paper.id;
    document.getElementById('title').value = paper.title;
    document.getElementById('authors').value = paper.authors;
    document.getElementById('year').value = paper.year;
    document.getElementById('category').value = paper.category;
    document.getElementById('abstract').value = paper.abstract;
}
</script>

<?php include("includes/footer.php"); ?>
