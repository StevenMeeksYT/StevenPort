 <?php
// tornado_db.php
// Single-file Tornado Database page (CRUD + export + filters + pagination)

include("db.php");
include("func.php");
require_once("php/fpdf.php");

$db = new DBFunc(); // your DB helper
$conn = $db->getConnection();

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', 86400 * 30);
    ini_set('session.gc_maxlifetime', 86400 * 30);
    session_start();
}

// Require login - for AJAX actions return JSON 401 instead of redirecting to login page
$authenticated = isset($_SESSION['username']) || isset($_COOKIE['username']);
if (!$authenticated) {
    if (isset($_REQUEST['action'])) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['ok' => false, 'error' => 'Not authenticated']);
        exit();
    }
    header("Location: login.php");
    exit();
}

// User info
$username = $_SESSION['username'] ?? $_COOKIE['username'] ?? null;
$role = strtolower($_SESSION['role'] ?? $_COOKIE['role'] ?? 'user');

function isAdmin($r) {
    return in_array($r, ['admin', 'superadmin']);
}

/* -------------------------
   Input / Filters (GET)
   ------------------------- */
$search = $_GET['search'] ?? '';
$year   = $_GET['year'] ?? '';
$scale  = $_GET['scale'] ?? '';
$state  = $_GET['state'] ?? '';
$damage = $_GET['damage'] ?? '';
$limit  = (int)($_GET['limit'] ?? 10);
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

/* -------------------------
   Export Handler (GET export=csv|pdf)
   ------------------------- */
if (isset($_GET['export']) && in_array($_GET['export'], ['csv', 'pdf'])) {
    // Build same filter SQL used for listing
    $sql = "SELECT id, tornado_id, name, date, state, intensity_scale, wind_speed, path_length, path_width, fatalities, injuries, damage, latitude, longitude, description
            FROM tornado_db WHERE 1=1";
    $params = [];
    $types = '';

    if ($search !== '') {
        $sql .= " AND (tornado_id LIKE ? OR name LIKE ? OR description LIKE ?)";
        $types .= 'sss';
        $q = "%$search%";
        $params[] = $q; $params[] = $q; $params[] = $q;
    }
    if ($year !== '') {
        $sql .= " AND YEAR(date) = ?";
        $types .= 'i';
        $params[] = (int)$year;
    }
    if ($scale !== '') {
        $sql .= " AND intensity_scale = ?";
        $types .= 's';
        $params[] = $scale;
    }
    if ($state !== '') {
        $sql .= " AND state = ?";
        $types .= 's';
        $params[] = $state;
    }
    if ($damage !== '') {
        $sql .= " AND damage = ?";
        $types .= 's';
        $params[] = $damage;
    }

    $sql .= " ORDER BY date DESC";

    $stmt = $conn->prepare($sql);
    if ($types !== '') $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;

    $filename = 'tornado_export_' . date('Ymd_His');

    if ($_GET['export'] === 'csv') {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename={$filename}.csv");
        $out = fopen('php://output', 'w');
        if (!empty($rows)) fputcsv($out, array_keys($rows[0]));
        foreach ($rows as $r) fputcsv($out, $r);
        fclose($out);
        exit();
    }

    if ($_GET['export'] === 'pdf') {
        $pdf = new FPDF('L','mm','A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(0,10,'Tornado Database Export',0,1,'C');
        $pdf->Ln(4);
        $pdf->SetFont('Arial','',9);
        if (!empty($rows)) {
            // headers
            foreach (array_keys($rows[0]) as $h) $pdf->Cell(40,8,substr($h,0,18),1);
            $pdf->Ln();
            // rows
            foreach ($rows as $r) {
                foreach ($r as $c) $pdf->Cell(40,8,substr((string)$c,0,18),1);
                $pdf->Ln();
            }
        } else {
            $pdf->Cell(0,8,'No records',0,1,'L');
        }
        $pdf->Output("D","{$filename}.pdf");
        exit();
    }
}

/* -------------------------
   Page: Fetch listing (with filters + pagination)
   ------------------------- */
// Build filter clauses once and reuse for both list and count queries (avoid SQL_CALC_FOUND_ROWS)
$baseWhere = " WHERE 1=1";
$filterParams = [];
$filterTypes = '';

if ($search !== '') {
    $baseWhere .= " AND (tornado_id LIKE ? OR name LIKE ? OR description LIKE ?)";
    $filterTypes .= 'sss';
    $q = "%$search%";
    $filterParams[] = $q; $filterParams[] = $q; $filterParams[] = $q;
}
if ($year !== '') {
    $baseWhere .= " AND YEAR(date) = ?";
    $filterTypes .= 'i';
    $filterParams[] = (int)$year;
}
if ($scale !== '') {
    $baseWhere .= " AND intensity_scale = ?";
    $filterTypes .= 's';
    $filterParams[] = $scale;
}
if ($state !== '') {
    $baseWhere .= " AND state = ?";
    $filterTypes .= 's';
    $filterParams[] = $state;
}
if ($damage !== '') {
    $baseWhere .= " AND damage = ?";
    $filterTypes .= 's';
    $filterParams[] = $damage;
}

$listSql = "SELECT id, tornado_id, name, date, state, intensity_scale, wind_speed, path_length, path_width, fatalities, injuries, damage FROM tornado_db" . $baseWhere . " ORDER BY date DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($listSql);
// Bind filter params then limit/offset
if ($filterTypes !== '') {
    $bindTypes = $filterTypes . 'ii';
    $bindParams = array_merge($filterParams, [$limit, $offset]);
    // create references for bind_param
    $refs = [];
    foreach ($bindParams as $k => $v) $refs[$k] = &$bindParams[$k];
    array_unshift($refs, $bindTypes);
    call_user_func_array([$stmt, 'bind_param'], $refs);
} else {
    $stmt->bind_param('ii', $limit, $offset);
}

$stmt->execute();
$res = $stmt->get_result();
$tornadoes = [];
while ($r = $res->fetch_assoc()) $tornadoes[] = $r;

// Count total rows matching same filters (for pagination)
$countSql = "SELECT COUNT(*) AS total FROM tornado_db" . $baseWhere;
$countStmt = $conn->prepare($countSql);
if ($filterTypes !== '') {
    $refs2 = [];
    foreach ($filterParams as $k => $v) $refs2[$k] = &$filterParams[$k];
    array_unshift($refs2, $filterTypes);
    call_user_func_array([$countStmt, 'bind_param'], $refs2);
}
$countStmt->execute();
$total = (int)($countStmt->get_result()->fetch_assoc()['total'] ?? 0);
$totalPages = max(1, ceil($total / $limit));

/* -------------------------
   Helper: Upload handling
   ------------------------- */
function saveUploadedFile($fileField, $subdir = 'tornado_files') {
    if (empty($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) return null;
    $uploadsDir = __DIR__ . '/uploads/' . $subdir;
    if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
    $orig = basename($_FILES[$fileField]['name']);
    $ext = pathinfo($orig, PATHINFO_EXTENSION);
    $safeName = bin2hex(random_bytes(8)) . ($ext ? '.' . $ext : '');
    $dest = $uploadsDir . '/' . $safeName;
    if (move_uploaded_file($_FILES[$fileField]['tmp_name'], $dest)) {
        // return web-accessible relative path (adjust if your app serves uploads from /uploads/)
        return 'uploads/' . $subdir . '/' . $safeName;
    }
    return null;
}

/* -------------------------
   AJAX ROUTER (moved up)
   Handles actions:
     - get_tornado (GET id)
     - save_tornado (POST)
     - delete_tornado (POST id)
   Moved before any output so AJAX endpoints return clean JSON (fixes JSON parse errors
   when the page previously emitted HTML before the JSON payload).
   ------------------------- */

if (php_sapi_name() !== 'cli' && isset($_REQUEST['action'])) {
    header('Cache-Control: no-store');
    header('Content-Type: application/json');

    $action = $_REQUEST['action'];
    $role = strtolower($_SESSION['role'] ?? 'user');

    try {
        switch ($action) {

            /* Get single tornado */
            case 'get_tornado': {
                $id = (int)($_GET['id'] ?? 0);
                if ($id <= 0) { echo json_encode(['ok'=>false,'error'=>'Invalid id']); exit; }
                // If static dataset exists under data/tornadoes.json, serve from there for dev/testing
                $staticFile = __DIR__ . '/data/tornadoes.json';
                if (file_exists($staticFile)) {
                    $json = json_decode(file_get_contents($staticFile), true);
                    foreach ($json as $r) {
                        if ((int)($r['id'] ?? 0) === $id || ($r['tornado_id'] ?? '') == $_GET['id']) {
                            echo json_encode(['ok'=>true,'data'=>$r]);
                            exit;
                        }
                    }
                    echo json_encode(['ok'=>false,'error'=>'Not found']);
                    exit;
                }
                $stmt = $conn->prepare("SELECT * FROM tornado_db WHERE id = ? LIMIT 1");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $res = $stmt->get_result()->fetch_assoc();
                if ($res) echo json_encode(['ok'=>true,'data'=>$res]);
                else echo json_encode(['ok'=>false,'error'=>'Not found']);
                break;
            }

            /* Delete tornado */
            case 'delete_tornado': {
                if (!isAdmin($role)) {
                    http_response_code(403);
                    echo json_encode(['ok'=>false,'error'=>'Access denied']);
                    exit;
                }
                $id = (int)($_POST['id'] ?? 0);
                if ($id <= 0) { echo json_encode(['ok'=>false,'error'=>'Invalid id']); exit; }
                // Optionally remove images (attempt)
                $row = $conn->query("SELECT tornado_img, track_img FROM tornado_db WHERE id = " . intval($id))->fetch_assoc();
                if ($row) {
                    foreach (['tornado_img','track_img'] as $f) {
                        if(!empty($row[$f]) && file_exists(__DIR__ . '/' . $row[$f])) @unlink(__DIR__ . '/' . $row[$f]);
                    }
                }
                $stmt = $conn->prepare("DELETE FROM tornado_db WHERE id = ? LIMIT 1");
                $stmt->bind_param('i',$id);
                $ok = $stmt->execute();
                echo json_encode(['ok'=>(bool)$ok]);
                break;
            }

            /* Save tornado (insert or update) */
            case 'save_tornado': {
                if (!isAdmin($role)) {
                    http_response_code(403);
                    echo json_encode(['ok'=>false,'error'=>'Access denied']);
                    exit;
                }

                // Collect fields
                $id = (int)($_POST['id'] ?? 0);
                $tornado_id     = trim($_POST['tornado_id'] ?? '');
                $name           = trim($_POST['name'] ?? '');
                $date           = $_POST['date'] ?? null;
                $intensity_scale= $_POST['intensity_scale'] ?? '';
                // If an EF-specific scale was provided, prefer it (adds back EF input without changing DB schema)
                $ef_scale = trim($_POST['ef_scale'] ?? '');
                if ($ef_scale !== '') {
                    $intensity_scale = $ef_scale;
                }
                $wind_speed     = ($_POST['wind_speed'] !== '') ? (int)$_POST['wind_speed'] : null;
                $path_length    = ($_POST['path_length'] !== '') ? (float)$_POST['path_length'] : null;
                $path_width     = ($_POST['path_width'] !== '') ? (float)$_POST['path_width'] : null;
                $fatalities     = (int)($_POST['fatalities'] ?? 0);
                $injuries       = (int)($_POST['injuries'] ?? 0);
                $damage         = trim($_POST['damage'] ?? '');
                $stateField     = trim($_POST['state'] ?? '');
                $latitude       = ($_POST['latitude'] !== '') ? (float)$_POST['latitude'] : null;
                $longitude      = ($_POST['longitude'] !== '') ? (float)$_POST['longitude'] : null;
                $description    = trim($_POST['description'] ?? '');

                // Handle uploads
                $tornado_img_path = null;
                $track_img_path = null;
                if (!empty($_FILES['tornado_img']) && $_FILES['tornado_img']['error'] === UPLOAD_ERR_OK) {
                    $tornado_img_path = saveUploadedFile('tornado_img', 'tornado_images');
                }
                if (!empty($_FILES['track_img']) && $_FILES['track_img']['error'] === UPLOAD_ERR_OK) {
                    $track_img_path = saveUploadedFile('track_img', 'tornado_tracks');
                }

                if ($id > 0) {
                    // update existing
                    // if a new upload exists, set it; otherwise keep existing file path
                    $existing = $conn->query("SELECT tornado_img, track_img FROM tornado_db WHERE id = " . intval($id))->fetch_assoc();

                    $sql = "UPDATE tornado_db SET
                            tornado_id = ?, name = ?, date = ?, intensity_scale = ?, wind_speed = ?, path_length = ?, path_width = ?, fatalities = ?, injuries = ?, damage = ?, state = ?, latitude = ?, longitude = ?, description = ?";

                    $params = [];
                    $types = 'ssssddiiissddss'; // placeholder types, will adjust below

                    // We'll build dynamic types/params to handle nullable numbers properly
                    $types = '';
                    $params = [];

                    $sql .= ", tornado_img = ?, track_img = ? WHERE id = ?";

                    // prepare values (if not uploaded, reuse existing)
                    $timg = $tornado_img_path ?? ($existing['tornado_img'] ?? null);
                    $trimg = $track_img_path ?? ($existing['track_img'] ?? null);

                    // bind all values in consistent order
                    $types = 'ssssddiiissddsssi';
                    $params = [
                        $tornado_id,
                        $name,
                        $date,
                        $intensity_scale,
                        $wind_speed,
                        $path_length,
                        $path_width,
                        $fatalities,
                        $injuries,
                        $damage,
                        $stateField,
                        $latitude,
                        $longitude,
                        $description,
                        $timg,
                        $trimg,
                        $id
                    ];

                    $stmt = $conn->prepare($sql);
                    // bind params dynamically - must convert nulls to appropriate types
                    // create references
                    $refs = [];
                    foreach ($params as $k => $v) $refs[$k] = &$params[$k];
                    array_unshift($refs, $types);
                    call_user_func_array([$stmt, 'bind_param'], $refs);
                    $ok = $stmt->execute();
                    echo json_encode(['ok'=> (bool)$ok]);
                } else {
                    // insert new
                    $sql = "INSERT INTO tornado_db
                        (tornado_id, name, date, intensity_scale, wind_speed, path_length, path_width, fatalities, injuries, damage, state, latitude, longitude, description, tornado_img, track_img)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $types = 'ssssddiiissddss';
                    // normalize nulls to null (mysqli bind_param will convert non-string numbers automatically)
                    $p1 = $tornado_id;
                    $p2 = $name;
                    $p3 = $date;
                    $p4 = $intensity_scale;
                    $p5 = $wind_speed !== null ? $wind_speed : null;
                    $p6 = $path_length !== null ? $path_length : null;
                    $p7 = $path_width !== null ? $path_width : null;
                    $p8 = $fatalities;
                    $p9 = $injuries;
                    $p10 = $damage;
                    $p11 = $stateField;
                    $p12 = $latitude !== null ? $latitude : null;
                    $p13 = $longitude !== null ? $longitude : null;
                    $p14 = $description;
                    $p15 = $tornado_img_path;
                    $p16 = $track_img_path;

                    // Because bind_param doesn't accept null for numeric types directly in some versions,
                    // we'll cast numbers to strings if null to avoid type mismatch; DB will accept null if we pass null
                    $stmt->bind_param(
                        'ssssddiiissddss',
                        $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9, $p10, $p11, $p12, $p13, $p14, $p15
                    );
                    // Note: Because the above types/params count mismatch is tricky across PHP versions,
                    // we'll fall back to a safer approach for insert using prepared statement with explicit types:
                    // Use a PDO-like fallback: build a parameterized query and use mysqli_stmt::bind_param properly.

                    // Simpler safe insertion block (re-prepare correctly)
                    $stmt = $conn->prepare($sql);
                    // convert floats to strings or null
                    $bindVals = [
                        $tornado_id,
                        $name,
                        $date,
                        $intensity_scale,
                        $wind_speed !== null ? $wind_speed : null,
                        $path_length !== null ? $path_length : null,
                        $path_width !== null ? $path_width : null,
                        $fatalities,
                        $injuries,
                        $damage,
                        $stateField,
                        $latitude !== null ? $latitude : null,
                        $longitude !== null ? $longitude : null,
                        $description,
                        $tornado_img_path,
                        $track_img_path
                    ];
                    // We will create a dynamic types string: s = string, d = double, i = integer
                    $dynamicTypes = '';
                    foreach ($bindVals as $v) {
                        if (is_int($v)) $dynamicTypes .= 'i';
                        elseif (is_float($v)) $dynamicTypes .= 'd';
                        else $dynamicTypes .= 's';
                    }
                    $refs = [];
                    foreach ($bindVals as $k => $v) $refs[$k] = &$bindVals[$k];
                    array_unshift($refs, $dynamicTypes);
                    call_user_func_array([$stmt, 'bind_param'], $refs);
                    $ok = $stmt->execute();
                    echo json_encode(['ok'=> (bool)$ok, 'insert_id' => $stmt->insert_id ?? null]);
                }

                break;
            }

            default:
                echo json_encode(['ok'=>false,'error'=>'Unknown action']);
        }
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
    }
    exit;
}

/* -------------------------
   Include header (UI)
   ------------------------- */
include("includes/header.php");
?>

<style>
/* minor modal styling override - master.css remains primary */
.modal-box { background-color: var(--bg-secondary); color: var(--text-primary); padding:1.25rem; border-radius:8px; }
.modal-box input, .modal-box select, .modal-box textarea { box-sizing:border-box; width:100%; margin-bottom:.5rem; }
.modal.show { display:flex !important; opacity:1; }
.table-wrapper { overflow-x:auto; }
.styled-table td, .styled-table th { white-space:nowrap; }
</style>

<section class="page-container fade-in">
    <h1>Tornado Database</h1>
    <p class="subtitle">Tornado records — search, add, edit, delete, and export</p>

    <div class="latest-storm-marquee">Latest Tornado: <span id="latest-storm">
        <?= htmlspecialchars($tornadoes[0]['name'] ?? 'N/A') ?>
    </span></div>

    <div class="button-container">
        <button class="btn btn-back" onclick="location.href='index.php'">Back</button>
        <?php if (isAdmin($role)): ?>
            <button class="btn btn-add" onclick="openAddModal()">+ Add Tornado</button>
        <?php endif; ?>
        <button class="btn btn-export" onclick="doExport('csv')">Export CSV</button>
        <button class="btn btn-export" onclick="doExport('pdf')">Export PDF</button>
    </div>

    <!-- Filter bar -->
    <form method="get" class="filter-bar" style="gap:.5rem;display:flex;flex-wrap:wrap;">
        <input type="text" name="search" placeholder="Search ID / Name / Description" value="<?= htmlspecialchars($search) ?>">
        <select name="year">
            <option value="">All Years</option>
            <?php for ($y = date('Y'); $y >= 1900; $y--): ?>
                <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>

        <select name="scale">
            <option value="">All Scales</option>
            <?php $scales = ['EF0','EF1','EF2','EF3','EF4','EF5','F0','F1','F2','F3','F4','F5','IF0','IF1','IF2','IF3','IF4','IF5','U']; ?>
            <?php foreach ($scales as $s): ?>
                <option value="<?= $s ?>" <?= ($scale == $s) ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>

        <select name="state">
            <option value="">All States</option>
            <?php
            $states = ['AL','AR','FL','GA','IA','IL','IN','KS','KY','LA','MO','MS','NE','OK','SD','TN','TX','WI','Other'];
            foreach ($states as $st): ?>
                <option value="<?= $st ?>" <?= ($state == $st) ? 'selected' : '' ?>><?= $st ?></option>
            <?php endforeach; ?>
        </select>

        <select name="damage">
            <option value="">All Damage</option>
            <option value="minor" <?= $damage=='minor'? 'selected':'' ?>>Minor</option>
            <option value="moderate" <?= $damage=='moderate'? 'selected':'' ?>>Moderate</option>
            <option value="major" <?= $damage=='major'? 'selected':'' ?>>Major</option>
            <option value="catastrophic" <?= $damage=='catastrophic'? 'selected':'' ?>>Catastrophic</option>
        </select>

        <select name="limit">
            <?php foreach ([10,30,50] as $opt): ?>
                <option value="<?= $opt ?>" <?= $limit==$opt? 'selected':'' ?>><?= $opt ?> per page</option>
            <?php endforeach; ?>
        </select>

    <button type="submit" class="btn btn-blue">Filter</button>
        <a class="btn btn-secondary" href="tornado_db.php">Reset</a>
    </form>

    <!-- Table -->
    <div class="table-wrapper fade-in" style="margin-top:1rem;">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tornado ID</th>
                    <th>Name / Location</th>
                    <th>Date</th>
                    <th>Scale</th>
                    <th>Wind (mph)</th>
                    <th>Length (mi)</th>
                    <th>Width (m)</th>
                    <th>Fatalities</th>
                    <th>Damage</th>
                    <th>State</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tornadoes)): foreach ($tornadoes as $t): ?>
                    <tr>
                        <td><?= (int)$t['id'] ?></td>
                        <td><?= htmlspecialchars($t['tornado_id'] ?? '') ?></td>
                        <td><?= htmlspecialchars($t['name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($t['date'] ?? '') ?></td>
                        <td><?= htmlspecialchars($t['intensity_scale'] ?? '') ?></td>
                        <td><?= htmlspecialchars($t['wind_speed'] ?? '') ?></td>
                        <td><?= htmlspecialchars($t['path_length'] ?? '') ?></td>
                        <td><?= htmlspecialchars($t['path_width'] ?? '') ?></td>
                        <td><?= htmlspecialchars($t['fatalities'] ?? '') ?></td>
                        <td><?= htmlspecialchars($t['damage'] ?? '') ?></td>
                        <td><?= htmlspecialchars($t['state'] ?? '') ?></td>
                        <td>
                            <button class="btn btn-blue btn-sm" onclick="viewTornado(<?= (int)$t['id'] ?>)">View</button>
                            <?php if (isAdmin($role)): ?>
                                <button class="btn btn-green btn-sm" onclick="editTornado(<?= (int)$t['id'] ?>)">Edit</button>
                                <button class="btn btn-red btn-sm" onclick="deleteTornado(<?= (int)$t['id'] ?>)">Delete</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="12">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination" style="margin-top:1rem;">
        <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</section>

<!-- ADD / EDIT Modal -->
<div id="tornadoModal" class="modal">
    <div class="modal-content modal-box">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Add Tornado</h2>
        <form id="tornadoForm" enctype="multipart/form-data" onsubmit="submitTornado(event)">
                <input type="hidden" name="id" id="tornado_hidden_id">

                <!-- Image inputs first (preview + file inputs) -->
                <div id="imagePreviews" style="display:flex;gap:1rem;margin-bottom:0.75rem;flex-wrap:wrap;align-items:flex-start;">
                    <!-- previews will be injected here when editing -->
                </div>

                <label>Image (tornado)
                    <input type="file" name="tornado_img" id="tornado_img_input">
                </label>

                <label>Track Image
                    <input type="file" name="track_img" id="track_img_input">
                </label>

                <!-- Properties follow after images -->
                <label>Tornado ID
                    <input type="text" name="tornado_id" id="tornado_tornado_id" required>
                </label>

                <label>Name / Location
                    <input type="text" name="name" id="tornado_name" required>
                </label>

                <label>Date
                    <input type="date" name="date" id="tornado_date" required>
                </label>

                <label>Intensity Scale
                    <!-- EF select (separate input) -->
                    <select name="ef_scale" id="tornado_ef">
                        <option value="">-- EF Scale (optional) --</option>
                        <option value="EF0">EF0</option>
                        <option value="EF1">EF1</option>
                        <option value="EF2">EF2</option>
                        <option value="EF3">EF3</option>
                        <option value="EF4">EF4</option>
                        <option value="EF5">EF5</option>
                    </select>

                    <select name="intensity_scale" id="tornado_intensity">
                        <?php foreach ($scales as $s): ?>
                            <option value="<?= $s ?>"><?= $s ?></option>
                        <?php endforeach; ?>
                        <option value="U">U</option>
                    </select>
                </label>

                <label>Wind Speed (mph)
                    <input type="number" name="wind_speed" id="tornado_wind">
                </label>

                <label>Path Length (mi)
                    <input type="number" step="0.01" name="path_length" id="tornado_length">
                </label>

                <label>Path Width (m)
                    <input type="number" step="0.01" name="path_width" id="tornado_width">
                </label>

                <label>Fatalities
                    <input type="number" name="fatalities" id="tornado_fatalities" value="0">
                </label>

                <label>Injuries
                    <input type="number" name="injuries" id="tornado_injuries" value="0">
                </label>

                <label>Damage
                    <input type="text" name="damage" id="tornado_damage">
                </label>

                <label>State
                    <input type="text" name="state" id="tornado_state">
                </label>

                <label>Latitude
                    <input type="number" step="0.000001" name="latitude" id="tornado_lat">
                </label>

                <label>Longitude
                    <input type="number" step="0.000001" name="longitude" id="tornado_lon">
                </label>

                <label>Description
                    <textarea name="description" id="tornado_desc"></textarea>
                </label>

            <div class="modal-actions" style="display:flex;gap:.5rem;">
                <button type="submit" class="btn btn-green" id="modalSubmit">Add Record</button>
                <button type="button" class="btn" onclick="closeModal()">Back</button>
            </div>
        </form>
    </div>
</div>

<!-- VIEW Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content modal-box">
        <span class="close-btn" onclick="closeViewModal()">&times;</span>
        <h2>Tornado Details</h2>
        <div id="viewContent" style="max-height:60vh; overflow:auto;"></div>
        <div class="modal-actions">
            <button class="btn" onclick="closeViewModal()">Back</button>
        </div>
    </div>
</div>

<script>
/* ----- Utilities ----- */
function doExport(fmt){
    const p = new URLSearchParams(window.location.search);
    p.set('export', fmt);
    window.location = window.location.pathname + '?' + p.toString();
}

function openAddModal(){
    openModal('add', null);
}
function openModal(mode='add', data=null){
    const m = document.getElementById('tornadoModal');
    m.classList.add('show');
    document.getElementById('modalTitle').innerText = mode==='add' ? 'Add Tornado' : 'Edit Tornado';
    document.getElementById('modalSubmit').innerText = mode==='add' ? 'Add Record' : 'Save Changes';
    const form = document.getElementById('tornadoForm');
    form.reset();
    const previews = document.getElementById('imagePreviews');
    if(mode==='edit' && data){
        document.getElementById('tornado_hidden_id').value = data.id || '';

        // populate image previews first
        if(previews) previews.innerHTML = '';
                if (previews && data.tornado_img) {
            const d = document.createElement('div'); d.style.maxWidth = '180px';
            const l = document.createElement('div'); l.textContent = 'Current Image';
            const i = document.createElement('img'); i.loading = 'lazy'; i.src = data.tornado_img; i.style.maxWidth = '100%'; i.style.height = 'auto'; i.style.borderRadius = '6px';
            d.appendChild(l); d.appendChild(i); previews.appendChild(d);
        }
        if(previews && data.track_img){
            const d2 = document.createElement('div'); d2.style.maxWidth = '180px';
            const l2 = document.createElement('div'); l2.textContent = 'Current Track';
            const i2 = document.createElement('img'); i2.loading = 'lazy'; i2.src = data.track_img; i2.style.maxWidth = '100%'; i2.style.height = 'auto'; i2.style.borderRadius = '6px';
            d2.appendChild(l2); d2.appendChild(i2); previews.appendChild(d2);
        }

        // populate form fields (properties)
        ['tornado_id','name','date','intensity','wind','length','width','fatalities','injuries','damage','state','latitude','longitude','description'].forEach(k=>{
            const el = document.getElementById('tornado_'+k);
            if(!el) return;
            // map differences in keys
            if(k==='intensity') el.value = data.intensity_scale || data.ef_scale || '';
            else if(k==='tornado_id') el.value = data.tornado_id || data.id || '';
            else if(k==='length') el.value = data.path_length || '';
            else if(k==='width') el.value = data.path_width || '';
            else el.value = data[k.replace('tornado_','')] ?? data[k] ?? data[k.replace('tornado_','')] ?? '';
        });
        // set EF select explicitly when editing (prefer explicit ef_scale, otherwise derive from intensity_scale)
        const efEl = document.getElementById('tornado_ef');
        if (efEl) {
            if (data.ef_scale) efEl.value = data.ef_scale;
            else if (data.intensity_scale && /^EF[0-5]/i.test(data.intensity_scale)) efEl.value = data.intensity_scale;
            else if (data.intensity_scale && /^F[0-5]/i.test(data.intensity_scale)) efEl.value = 'EF' + data.intensity_scale.replace(/[^0-9]/g,'');
            else efEl.value = '';
        }
    } else {
        document.getElementById('tornado_hidden_id').value = '';
        if(previews) previews.innerHTML = '';
    }
}
function closeModal(){ document.getElementById('tornadoModal').classList.remove('show'); }

function viewTornado(id){
    // Show loading indicator
    const viewContent = document.getElementById('viewContent');
    if (viewContent) viewContent.innerHTML = '<div style="text-align:center;padding:20px;">Loading tornado data...</div>';
    document.getElementById('viewModal').classList.add('show');
    
    fetch('tornado_db.php?action=get_tornado&id='+encodeURIComponent(id))
    .then(r => {
        if (!r.ok) throw new Error('Network response was not ok');
        return r.json();
    })
    .then(res=>{
        if(!res.ok){ 
            alert(res.error || 'Not found'); 
            closeViewModal();
            return; 
        }
        const d = res.data;
        // show images first (if present)
        let html = '';
        if (d.tornado_img || d.track_img) {
            html += '<div style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:0.75rem;">';
            if (d.tornado_img) {
                html += `<div style="max-width:320px;">\n                    <div style="font-weight:600;margin-bottom:6px">Image</div>\n                    <img loading="lazy" src="${d.tornado_img}" style="width:100%;height:auto;border-radius:6px">\n                </div>`;
            }
            if(d.track_img){
                html += `<div style="max-width:320px;">\n                    <div style="font-weight:600;margin-bottom:6px">Track</div>\n                    <img loading="lazy" src="${d.track_img}" style="width:100%;height:auto;border-radius:6px">\n                </div>`;
            }
            html += '</div>';
        }

        html += '<table style="width:100%;">';
        html += `<tr><td><strong>ID</strong></td><td>${d.tornado_id}</td></tr>`;
        html += `<tr><td><strong>Name / Location</strong></td><td>${d.name}</td></tr>`;
        html += `<tr><td><strong>Date</strong></td><td>${d.date}</td></tr>`;
    // derive EF display if available in intensity_scale or stored separately
    let ef_display = '';
    if (d.ef_scale) ef_display = d.ef_scale;
    else if (d.intensity_scale && (/^EF[0-5]/i).test(d.intensity_scale)) ef_display = d.intensity_scale;
    else if (d.intensity_scale && (/^F[0-5]/i).test(d.intensity_scale)) ef_display = 'EF' + d.intensity_scale.replace(/[^0-9]/g,'');
    if (ef_display) html += `<tr><td><strong>EF Scale</strong></td><td>${ef_display}</td></tr>`;
    html += `<tr><td><strong>Scale</strong></td><td>${d.intensity_scale}</td></tr>`;
    html += `<tr><td><strong>Wind (mph)</strong></td><td>${d.wind_speed}</td></tr>`;
        html += `<tr><td><strong>Path Length (mi)</strong></td><td>${d.path_length}</td></tr>`;
        html += `<tr><td><strong>Path Width (m)</strong></td><td>${d.path_width}</td></tr>`;
        html += `<tr><td><strong>Fatalities</strong></td><td>${d.fatalities}</td></tr>`;
        html += `<tr><td><strong>Damage</strong></td><td>${d.damage}</td></tr>`;
        html += `<tr><td><strong>State</strong></td><td>${d.state}</td></tr>`;
        html += `<tr><td><strong>Lat / Lon</strong></td><td>${d.latitude}, ${d.longitude}</td></tr>`;
        html += `<tr><td><strong>Description</strong></td><td>${d.description}</td></tr>`;
        html += '</table>';
        document.getElementById('viewContent').innerHTML = html;
    })
    .catch(e=>{
        alert('Error loading: '+e);
        closeViewModal();
    });
}
function closeViewModal(){ document.getElementById('viewModal').classList.remove('show'); }

function editTornado(id){
    // Show loading indicator in modal
    const modalBody = document.querySelector('#tornadoModal .modal-body');
    if (modalBody) modalBody.innerHTML = '<div style="text-align:center;padding:20px;">Loading tornado data for editing...</div>';
    document.getElementById('tornadoModal').classList.add('show');
    
    fetch('tornado_db.php?action=get_tornado&id='+encodeURIComponent(id))
    .then(r => {
        if (!r.ok) throw new Error('Network response was not ok');
        return r.json();
    })
    .then(res=>{
        if(!res.ok){ 
            alert(res.error || 'Not found'); 
            closeModal();
            return; 
        }
        openModal('edit', res.data);
    })
    .catch(e=>{
        alert('Error loading for edit: '+e);
        closeModal();
    });
}

function deleteTornado(id){
    if(!confirm('Delete this tornado record?')) return;
    
    // Show loading state
    const deleteBtn = event.target;
    const originalText = deleteBtn.textContent;
    deleteBtn.textContent = 'Deleting...';
    deleteBtn.disabled = true;
    
    fetch('tornado_db.php?action=delete_tornado', {
        method: 'POST',
        headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
        body: new URLSearchParams({id:id})
    })
    .then(r => {
        if (!r.ok) throw new Error('Network response was not ok');
        return r.json();
    })
    .then(res=>{
        if(res.ok) {
            alert('Tornado record deleted successfully');
            location.reload();
        } else {
            alert(res.error||'Delete failed');
            deleteBtn.textContent = originalText;
            deleteBtn.disabled = false;
        }
    })
    .catch(e=>{
        alert('Error: '+e);
        deleteBtn.textContent = originalText;
        deleteBtn.disabled = false;
    });
}

function submitTornado(e){
    e.preventDefault();
    const submitBtn = document.getElementById('modalSubmit');
    if(submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';
    }
    const form = document.getElementById('tornadoForm');
    const fd = new FormData(form);
    // map intensity select id
    const intensityEl = document.getElementById('tornado_intensity');
    if(intensityEl) fd.set('intensity_scale', intensityEl.value);
    // include ef_scale if supplied (backend will prioritize ef_scale when present)
    const efSelect = document.getElementById('tornado_ef');
    if (efSelect) fd.set('ef_scale', efSelect.value);
    fetch('tornado_db.php?action=save_tornado', {
        method:'POST',
        body: fd
    })
    .then(r => {
        if (!r.ok) throw new Error('Network response was not ok');
        return r.json();
    })
    .then(res=>{
        if(res.ok){ 
            alert('Tornado record saved successfully'); 
            location.reload(); 
        }
        else {
            alert(res.error || 'Save failed');
            if(submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Changes';
            }
        }
    })
    .catch(e=>{
        alert('Error saving: '+e);
        if(submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Changes';
        }
    });
}
</script>

<?php include("includes/footer.php"); ?>
