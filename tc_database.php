<?php
include("db.php");
include("func.php");
require_once("php/fpdf.php");

$db = new DBFunc(); // Must be created early

if (session_status() === PHP_SESSION_NONE) {
    // Extend session lifetime
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

// Read username & role, prefer session then cookie
$username = $_SESSION['username'] ?? $_COOKIE['username'] ?? null;
$role = strtolower($_SESSION['role'] ?? $_COOKIE['role'] ?? 'user');

// Filters
$search = $_GET['search'] ?? '';
$basin = $_GET['basin'] ?? '';
$year = $_GET['year'] ?? '';
$wind = $_GET['wind'] ?? '';
$pressure = $_GET['pressure'] ?? ''; // ✅ Fix: define pressure
$sshws = $_GET['sshws'] ?? '';
$limit = (int)($_GET['limit'] ?? 10);
$page = (int)($_GET['page'] ?? 1);
$offset = ($page - 1) * $limit;

// Basin list
$basins = [
    ['basin_code' => 'NATL', 'basin_name' => 'North Atlantic'],
    ['basin_code' => 'NEPAC', 'basin_name' => 'Northeast Pacific'],
    ['basin_code' => 'CPAC', 'basin_name' => 'Central Pacific'],
    ['basin_code' => 'NWPAC', 'basin_name' => 'Northwest Pacific'],
    ['basin_code' => 'NIO', 'basin_name' => 'North Indian Ocean'],
    ['basin_code' => 'SWIO', 'basin_name' => 'Southwest Indian Ocean'],
    ['basin_code' => 'AU', 'basin_name' => 'Australian Region'],
    ['basin_code' => 'SPAC', 'basin_name' => 'South Pacific'],
    ['basin_code' => 'SEPAC', 'basin_name' => 'Southeast Pacific'],
    ['basin_code' => 'SATL', 'basin_name' => 'South Atlantic'],
    ['basin_code' => 'MED', 'basin_name' => 'Mediterranean']
];

// Fetch storms and counts
$storms = $db->getFilteredTCs($search, $basin, $year, $wind, $limit, $offset);
$total = $db->countFilteredTCs($search, $basin, $year, $wind);
$totalPages = max(1, ceil($total / $limit));

// Latest storm
$latestStorm = $db->getFilteredTCs('', '', '', '', 1, 0);
$latestStormName = $latestStorm[0]['name'] ?? 'N/A';

function isAdmin($r)
{
    return in_array($r, ['admin', 'superadmin']);
}

// --- EXPORT HANDLER ---
$export = $_GET['export'] ?? null;
if ($export) {
    $data = $db->getFilteredTCs($search, $basin, $year, $wind, 9999, 0);
    $filename = 'tropical_cyclone_export_' . date('Ymd_His');

    if (!$data || count($data) === 0) {
        header("Location: tc_database.php");
        exit();
    }

    if ($export === 'csv') {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename={$filename}.csv");
        $output = fopen("php://output", "w");
        fputcsv($output, array_keys($data[0]));
        foreach ($data as $row) fputcsv($output, $row);
        fclose($output);
        exit();
    }

    if ($export === 'pdf') {
        require_once __DIR__ . "/php/fpdf.php";
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Tropical Cyclone Database Export', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);

        foreach (array_keys($data[0]) as $header) {
            $pdf->Cell(40, 8, $header, 1);
        }
        $pdf->Ln();

        foreach ($data as $row) {
            foreach ($row as $cell) {
                $pdf->Cell(40, 8, substr($cell, 0, 25), 1);
            }
            $pdf->Ln();
        }

        $pdf->Output("D", "{$filename}.pdf");
        exit();
    }
}

/* --------------------------
   AJAX ROUTER (moved up)
   Handles JSON action endpoints before any HTML is emitted so AJAX callers receive
   pure JSON responses (prevents HTML/login pages being returned to fetch().json()).
   -------------------------- */
if (php_sapi_name() !== 'cli' && isset($_REQUEST['action'])) {
    header('Cache-Control: no-store');
    header('Content-Type: application/json');

    $db = new DBFunc();
    $conn = $db->getConnection();
    $action = $_REQUEST['action'];

    try {
        $role = $_SESSION['role'] ?? 'user';

        switch ($action) {

            case 'getFilteredTCs':
                $search = $_REQUEST['search'] ?? '';
                $basin = $_REQUEST['basin'] ?? '';
                $year = $_REQUEST['year'] ?? '';
                $wind = $_REQUEST['wind'] ?? '';
                $pressure = $_REQUEST['pressure'] ?? ''; // <- added to avoid undefined variable
                // (no sshws server-side handling in this view - UI-only selector)
                $limit = intval($_REQUEST['limit'] ?? 10);
                $offset = intval($_REQUEST['offset'] ?? 0);

                $sql = "SELECT * FROM TCDatabase WHERE 1=1";
                $types = '';
                $params = [];

                if ($search !== '') {
                    $sql .= " AND (storm_name LIKE ? OR storm_id LIKE ?)";
                    $types .= 'ss';
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                }
                if ($basin !== '') {
                    $sql .= " AND Basin = ?";
                    $types .= 's';
                    $params[] = $basin;
                }
                if ($year !== '') {
                    // year may be BCE negative in other parts of app; here we assume formed is a DATE so use YEAR()
                    $sql .= " AND YEAR(formed) = ?";
                    $types .= 'i';
                    $params[] = (int)$year;
                }

                // Keep previous numeric-wind behavior (if provided as numeric threshold)
                if ($wind !== '' && is_numeric($wind)) {
                    $sql .= " AND msw >= ?";
                    $types .= 'i';
                    $params[] = (int)$wind;
                }

                // SSHWS filter intentionally omitted here (UI-only for now)

                // --- Pressure filter (matches dropdown values) ---
                if ($pressure !== '') {
                    // use intval on numeric pieces to ensure numeric literals are safe
                    switch ($pressure) {
                        case '>1000':
                            $sql .= " AND mslp > 1000";
                            break;
                        case '980-1000':
                            $sql .= " AND mslp BETWEEN 980 AND 1000";
                            break;
                        case '965-979':
                            $sql .= " AND mslp BETWEEN 965 AND 979";
                            break;
                        case '945-964':
                            $sql .= " AND mslp BETWEEN 945 AND 964";
                            break;
                        case '920-944':
                            $sql .= " AND mslp BETWEEN 920 AND 944";
                            break;
                        case '900-919':
                            $sql .= " AND mslp BETWEEN 900 AND 919";
                            break;
                        case '<=899':
                            $sql .= " AND mslp <= 899";
                            break;
                    }
                }

                $sql .= " ORDER BY formed DESC LIMIT ? OFFSET ?";
                $types .= 'ii';
                $params[] = $limit;
                $params[] = $offset;

                $stmt = $conn->prepare($sql);
                if (!empty($params)) $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = [];
                while ($row = $result->fetch_assoc()) $data[] = $row;

                // Count total (mirror same filters)
                $countSql = "SELECT COUNT(*) AS total FROM TCDatabase WHERE 1=1";
                $countTypes = '';
                $countParams = [];

                if ($search !== '') {
                    $countSql .= " AND (storm_name LIKE ? OR storm_id LIKE ?)";
                    $countTypes .= 'ss';
                    $countParams[] = "%$search%";
                    $countParams[] = "%$search%";
                }
                if ($basin !== '') {
                    $countSql .= " AND Basin = ?";
                    $countTypes .= 's';
                    $countParams[] = $basin;
                }
                if ($year !== '') {
                    $countSql .= " AND YEAR(formed) = ?";
                    $countTypes .= 'i';
                    $countParams[] = (int)$year;
                }
                if ($wind !== '' && is_numeric($wind)) {
                    $countSql .= " AND msw >= ?";
                    $countTypes .= 'i';
                    $countParams[] = (int)$wind;
                }

                // SSHWS count filtering intentionally omitted here

                if ($pressure !== '') {
                    switch ($pressure) {
                        case '>1000':
                            $countSql .= " AND mslp > 1000";
                            break;
                        case '980-1000':
                            $countSql .= " AND mslp BETWEEN 980 AND 1000";
                            break;
                        case '965-979':
                            $countSql .= " AND mslp BETWEEN 965 AND 979";
                            break;
                        case '945-964':
                            $countSql .= " AND mslp BETWEEN 945 AND 964";
                            break;
                        case '920-944':
                            $countSql .= " AND mslp BETWEEN 920 AND 944";
                            break;
                        case '900-919':
                            $countSql .= " AND mslp BETWEEN 900 AND 919";
                            break;
                        case '<=899':
                            $countSql .= " AND mslp <= 899";
                            break;
                    }
                }

                $countStmt = $conn->prepare($countSql);
                if (!empty($countParams)) $countStmt->bind_param($countTypes, ...$countParams);
                $countStmt->execute();
                $total = $countStmt->get_result()->fetch_assoc()['total'] ?? 0;

                echo json_encode(['ok' => true, 'data' => $data, 'total' => $total]);
                break;

            case 'get_storm':
                $id = (int)($_GET['id'] ?? 0);
                if ($id <= 0) { echo json_encode(['ok' => false, 'error' => 'Invalid id']); break; }
                // Support static dataset for dev: data/tc_storms.json
                $static = __DIR__ . '/data/tc_storms.json';
                if (file_exists($static)) {
                    $arr = json_decode(file_get_contents($static), true);
                    foreach ($arr as $s) {
                        if ((int)($s['id'] ?? 0) === $id || ($s['storm_id'] ?? '') == $_GET['id']) {
                            echo json_encode(['ok' => true, 'data' => $s]);
                            break 2;
                        }
                    }
                    echo json_encode(['ok' => false, 'error' => 'Not found']);
                    break;
                }
                $storm = $db->showTC($id);
                echo json_encode($storm ? ['ok' => true, 'data' => $storm] : ['ok' => false, 'error' => 'Not found']);
                break;

            case 'delete_storm':
                if (!in_array($role, ['admin', 'superadmin'])) {
                    http_response_code(403);
                    echo json_encode(['ok' => false, 'error' => 'Access denied']);
                    exit;
                }
                $id = (int)($_POST['id'] ?? 0);
                $ok = $db->deleteTC($id);
                echo json_encode(['ok' => (bool)$ok]);
                break;

            case 'save_storm':
                if (!in_array($role, ['admin', 'superadmin'])) {
                    http_response_code(403);
                    echo json_encode(['ok' => false, 'error' => 'Access denied']);
                    exit;
                }

                $id = $_POST['id'] ?? null;
                $storm_id = $_POST['storm_id'] ?? '';
                $name = $_POST['storm_name'] ?? ($_POST['name'] ?? '');
                $basin = $_POST['basin'] ?? '';
                $msw = $_POST['msw'] ?? '';
                $sshws = trim($_POST['sshws'] ?? '');
                // If SSHWS classification provided but no numeric msw, map to a representative msw value
                if ($sshws !== '' && ($msw === '' || $msw === null)) {
                    switch (strtoupper($sshws)) {
                        case 'TD': $msw = 30; break; // depression
                        case 'TS': $msw = 50; break; // tropical storm mid-range
                        case 'C1': $msw = 85; break;
                        case 'C2': $msw = 103; break;
                        case 'C3': $msw = 120; break;
                        case 'C4': $msw = 143; break;
                        case 'C5': $msw = 165; break;
                        default: $msw = $msw; break;
                    }
                }
                $mslp = $_POST['mslp'] ?? '';
                $formed = $_POST['formed'] ?? '';
                $dissipated = $_POST['dissipated'] ?? '';
                $ace = $_POST['ace_value'] ?? null;
                $damage = $_POST['damage'] ?? '';
                $fatalities = $_POST['fatalities'] ?? '';
                $desc = $_POST['history'] ?? ($_POST['desc'] ?? '');
                $storm_img = $_FILES['storm_img'] ?? null;
                $track_img = $_FILES['track_img'] ?? null;

                if ($id) {
                    $ok = $db->updateTC($id, $storm_id, $storm_img, $track_img, $name, $basin, $msw, $mslp, $formed, $dissipated, $ace, $damage, $fatalities, $desc);
                } else {
                    $ok = $db->addTC($storm_id, $storm_img, $track_img, $name, $basin, $msw, $mslp, $formed, $dissipated, $ace, $damage, $fatalities, $desc);
                }

                echo json_encode(['ok' => (bool)$ok]);
                break;

            default:
                echo json_encode(['ok' => false, 'error' => 'Unknown action']);
        }
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }

    exit; // prevent HTML output for AJAX
}

include("includes/header.php");
?>

<style>
    /* Small inline styles only for modal color toggle wrapper (master.css still primary) */
    .modal-box {
        background-color: var(--bg-secondary);
        color: var(--text-primary);
        padding: 1.5rem;
        border-radius: 0.5rem;
    }

    /* header text (h2) will use master.css styles; we do not override */
    .modal-box h2 {
        color: inherit;
    }

    /* Provide a simple dark-mode override if the page uses body.dark-mode */
    body.dark-mode .modal-box {
        background-color: #000;
        color: #fff;
    }

    /* Ensure modal-inputs inherit modal text color */
    .modal-box input,
    .modal-box select,
    .modal-box textarea {
        background-color: transparent;
        color: inherit;
        border: 1px solid var(--border-color);
        border-radius: 0.25rem;
        padding: 0.5rem;
        width: 100%;
        box-sizing: border-box;
    }

    /* Modal show class */
    .modal.show {
        display: flex !important;
        opacity: 1;
    }
</style>

<section class="page-container fade-in">
    <h1>Tropical Cyclone Database</h1>
    <p class="subtitle">Comprehensive storm record viewer & manager</p>

    <div class="latest-storm-marquee">
        Latest Storm: <span id="latest-storm"><?= htmlspecialchars($latestStormName) ?></span>
    </div>

    <div class="button-container">
        <button class="btn btn-back" onclick="window.location.href='index.php'">Back</button>

        <?php if (isAdmin($role)): ?>
            <button class="btn btn-add" onclick="openAddModal()">Add Storm</button>
        <?php endif; ?>

        <button class="btn btn-export" onclick="exportCSV()">Export CSV</button>
        <button class="btn btn-export" onclick="exportPDF()">Export PDF</button>
    </div>

    <!-- Filter bar -->
    <form method="get" class="filter-bar">
        <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
        <select name="basin">
            <option value="">All Basins</option>
            <?php foreach ($basins as $b): ?>
                <option value="<?= htmlspecialchars($b['basin_code']) ?>" <?= $basin == $b['basin_code'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($b['basin_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- SSHWS classification filter (optional) -->
        <select name="sshws">
            <option value="">All Classifications</option>
            <option value="TD" <?= ($sshws ?? '') == 'TD' ? 'selected' : '' ?>>TD (Depression)</option>
            <option value="TS" <?= ($sshws ?? '') == 'TS' ? 'selected' : '' ?>>TS (Tropical Storm)</option>
            <option value="C1" <?= ($sshws ?? '') == 'C1' ? 'selected' : '' ?>>Category 1</option>
            <option value="C2" <?= ($sshws ?? '') == 'C2' ? 'selected' : '' ?>>Category 2</option>
            <option value="C3" <?= ($sshws ?? '') == 'C3' ? 'selected' : '' ?>>Category 3</option>
            <option value="C4" <?= ($sshws ?? '') == 'C4' ? 'selected' : '' ?>>Category 4</option>
            <option value="C5" <?= ($sshws ?? '') == 'C5' ? 'selected' : '' ?>>Category 5</option>
        </select>

        <select name="year">
            <option value="">All Years</option>
            <?php for ($y = date('Y'); $y >= -1000; $y--): ?>
                <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>>
                    <?= $y > 0 ? $y : abs($y) . ' BCE' ?>
                </option>
            <?php endfor; ?>
        </select>

        <select name="wind">
            <option value="">All Wind</option>
            <option value="TD" <?= $wind == 'TD' ? 'selected' : '' ?>>Tropical/Subtropical Depression (TD/SD)</option>
            <option value="TS" <?= $wind == 'TS' ? 'selected' : '' ?>>Tropical/Subtropical Storm (TS/SS)</option>
            <option value="C1" <?= $wind == 'C1' ? 'selected' : '' ?>>Category 1 (74–95 mph)</option>
            <option value="C2" <?= $wind == 'C2' ? 'selected' : '' ?>>Category 2 (96–110 mph)</option>
            <option value="C3" <?= $wind == 'C3' ? 'selected' : '' ?>>Category 3 (111–129 mph)</option>
            <option value="C4" <?= $wind == 'C4' ? 'selected' : '' ?>>Category 4 (130–156 mph)</option>
            <option value="C5" <?= $wind == 'C5' ? 'selected' : '' ?>>Category 5 (≥157 mph)</option>
        </select>

        <select name="pressure">
            <option value="">All Pressure</option>
            <option value=">1000" <?= $pressure == '>1000' ? 'selected' : '' ?>>&gt;1000 mbar (TD/SD)</option>
            <option value="980-1000" <?= $pressure == '980-1000' ? 'selected' : '' ?>>980–1000 mbar (TS/SS)</option>
            <option value="965-979" <?= $pressure == '965-979' ? 'selected' : '' ?>>965–979 mbar (Category 1)</option>
            <option value="945-964" <?= $pressure == '945-964' ? 'selected' : '' ?>>945–964 mbar (Category 2)</option>
            <option value="920-944" <?= $pressure == '920-944' ? 'selected' : '' ?>>920–944 mbar (Category 3)</option>
            <option value="900-919" <?= $pressure == '900-919' ? 'selected' : '' ?>>900–919 mbar (Category 4)</option>
            <option value="<=899" <?= $pressure == '<=899' ? 'selected' : '' ?>>≤899 mbar (Category 5)</option>
        </select>

        <select name="limit">
            <?php foreach ([10, 30, 50] as $opt): ?>
                <option value="<?= $opt ?>" <?= $limit == $opt ? 'selected' : '' ?>><?= $opt ?> per page</option>
            <?php endforeach; ?>
        </select>

    <button type="submit" class="btn btn-blue">Filter</button>
    </form>

    <!-- Table -->
    <div class="table-wrapper fade-in">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Storm ID</th>
                    <th>Name</th>
                    <th>Basin</th>
                    <th>Formed</th>
                    <th>Dissipated</th>
                    <th>Wind (mph)</th>
                    <th>Pressure (mbar)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($storms)): foreach ($storms as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['storm_id'] ?? '') ?></td>
                            <td><?= htmlspecialchars($s['name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($s['basin'] ?? '') ?></td>
                            <td><?= htmlspecialchars($s['formed'] ?? '') ?></td>
                            <td><?= htmlspecialchars($s['dissipated'] ?? '') ?></td>
                            <td><?= htmlspecialchars($s['msw'] ?? '') ?></td>
                            <td><?= htmlspecialchars($s['mslp'] ?? '') ?></td>
                            <td>
                                <button class="btn btn-blue btn-sm" onclick="viewStorm(<?= (int)$s['id'] ?>)">View</button>
                                <?php if (isAdmin($role)): ?>
                                    <button class="btn btn-green btn-sm" onclick="editStorm(<?= (int)$s['id'] ?>)">Edit</button>
                                    <button class="btn btn-red btn-sm" onclick="deleteStorm(<?= (int)$s['id'] ?>)">Delete</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="8">No results found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i === $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</section>

<!-- ADD / EDIT Modal (admin only inputs) -->
<div id="stormModal" class="modal">
    <div class="modal-content modal-box">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Add Storm</h2>
        <form id="stormForm" enctype="multipart/form-data" onsubmit="submitStorm(event)">
            <input type="hidden" name="id" id="storm_hidden_id">
            <!-- Image previews and file inputs first -->
            <div id="imagePreviewsTC" style="display:flex;gap:1rem;margin-bottom:0.75rem;flex-wrap:wrap;align-items:flex-start;"></div>

            <label>Storm Image
                <input type="file" name="storm_img" id="storm_img_input">
            </label>

            <label>Track Image
                <input type="file" name="track_img" id="track_img_input">
            </label>

            <label>Storm ID
                <input type="text" name="storm_id" id="storm_id_input" required>
            </label>

            <label>Name
                <input type="text" name="storm_name" id="storm_name" required>
            </label>

            <label>Basin
                <select name="basin" id="storm_basin" required>
                    <?php foreach ($basins as $b): ?>
                        <option value="<?= htmlspecialchars($b['basin_code']) ?>"><?= htmlspecialchars($b['basin_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>Max Wind (mph)
                <!-- SSHWS selector (optional) -->
                <select name="sshws" id="storm_sshws">
                    <option value="">-- SSHWS / Classification (optional) --</option>
                    <option value="TD">TD (Depression)</option>
                    <option value="TS">TS (Tropical Storm)</option>
                    <option value="C1">C1</option>
                    <option value="C2">C2</option>
                    <option value="C3">C3</option>
                    <option value="C4">C4</option>
                    <option value="C5">C5</option>
                </select>

                <input type="number" name="msw" id="storm_msw">
            </label>

            <label>Min Pressure (mbar)
                <input type="number" name="mslp" id="storm_mslp">
            </label>

            <label>Formed
                <input type="date" name="formed" id="storm_formed">
            </label>

            <label>Dissipated
                <input type="date" name="dissipated" id="storm_dissipated">
            </label>

            <label>ACE Value
                <input type="number" step="0.0001" name="ace_value" id="storm_ace">
            </label>

            <label>Damage
                <input type="text" name="damage" id="storm_damage">
            </label>

            <label>Fatalities
                <input type="text" name="fatalities" id="storm_fatalities">
            </label>

            <label>Description
                <textarea name="desc" id="storm_desc"></textarea>
            </label>

            <!-- (moved image inputs to top) -->

            <div class="modal-actions">
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
        <h2>Storm Details</h2>
        <div id="viewContent"></div>
        <div class="modal-actions">
            <button class="btn" onclick="closeViewModal()">Back</button>
        </div>
    </div>
</div>

<script>
    /* ----- Helper handlers ----- */
    function openAddModal() {
        openModal('add');
    }

    function openModal(mode = 'add', data = null) {
        const modal = document.getElementById('stormModal');
        modal.classList.add('show');
        document.getElementById('modalTitle').innerText = mode === 'add' ? 'Add Storm' : 'Edit Storm';
        document.getElementById('modalSubmit').innerText = mode === 'add' ? 'Add Record' : 'Save Changes';
        const previews = document.getElementById('imagePreviewsTC');
        if (mode === 'edit' && data) {
            // show existing images in preview area first
            if (previews) previews.innerHTML = '';
            if (previews && data.storm_img) {
                const d = document.createElement('div'); d.style.maxWidth = '240px';
                const lbl = document.createElement('div'); lbl.textContent = 'Current Image'; lbl.style.fontWeight = '600'; lbl.style.marginBottom = '6px';
                const img = document.createElement('img'); img.loading = 'lazy'; img.src = data.storm_img; img.style.width = '100%'; img.style.height = 'auto'; img.style.borderRadius = '6px';
                d.appendChild(lbl); d.appendChild(img); previews.appendChild(d);
            }
            if (previews && data.track_img) {
                const d2 = document.createElement('div'); d2.style.maxWidth = '240px';
                const lbl2 = document.createElement('div'); lbl2.textContent = 'Current Track'; lbl2.style.fontWeight = '600'; lbl2.style.marginBottom = '6px';
                const img2 = document.createElement('img'); img2.loading = 'lazy'; img2.src = data.track_img; img2.style.width = '100%'; img2.style.height = 'auto'; img2.style.borderRadius = '6px';
                d2.appendChild(lbl2); d2.appendChild(img2); previews.appendChild(d2);
            }

            // populate inputs if present (properties)
            for (const k in data) {
                const el = document.getElementById('storm_' + k);
                if (el) el.value = data[k];
            }
            // populate sshws selector: prefer explicit value, otherwise derive from msw
            const sshEl = document.getElementById('storm_sshws');
            if (sshEl) {
                if (data.sshws) {
                    sshEl.value = data.sshws;
                } else {
                    const mswNum = parseInt(data.msw || '') || null;
                    if (mswNum) {
                        if (mswNum < 39) sshEl.value = 'TD';
                        else if (mswNum >= 39 && mswNum <= 73) sshEl.value = 'TS';
                        else if (mswNum >= 74 && mswNum <= 95) sshEl.value = 'C1';
                        else if (mswNum >= 96 && mswNum <= 110) sshEl.value = 'C2';
                        else if (mswNum >= 111 && mswNum <= 129) sshEl.value = 'C3';
                        else if (mswNum >= 130 && mswNum <= 156) sshEl.value = 'C4';
                        else if (mswNum >= 157) sshEl.value = 'C5';
                    } else {
                        sshEl.value = '';
                    }
                }
            }
            document.getElementById('storm_hidden_id').value = data.id;
        } else {
            // reset form for add
            const form = document.getElementById('stormForm');
            if (form) form.reset();
            document.getElementById('storm_hidden_id').value = '';
            if (previews) previews.innerHTML = '';
        }
    }

    function closeModal() {
        const modal = document.getElementById('stormModal');
        modal.classList.remove('show');
    }

    /* View modal */
    function viewStorm(id) {
        // Show loading indicator
        const viewContent = document.getElementById('viewContent');
        if (viewContent) viewContent.innerHTML = '<div style="text-align:center;padding:20px;">Loading storm data...</div>';
        document.getElementById('viewModal').classList.add('show');
        
        fetch('tc_database.php?action=get_storm&id=' + encodeURIComponent(id))
            .then(r => {
                if (!r.ok) throw new Error('Network response was not ok');
                return r.json();
            })
            .then(res => {
                if (!res.ok) { 
                    alert(res.error || 'Not found'); 
                    closeViewModal();
                    return; 
                }
                const d = res.data;
                let html = '';
                if (d.storm_img || d.track_img) {
                    html += '<div style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:0.75rem;">';
                    if (d.storm_img) html += `<div style="max-width:360px;"><div style="font-weight:600;margin-bottom:6px">Image</div><img loading="lazy" src="${d.storm_img}" style="width:100%;height:auto;border-radius:6px"></div>`;
                    if (d.track_img) html += `<div style="max-width:360px;"><div style="font-weight:600;margin-bottom:6px">Track</div><img loading="lazy" src="${d.track_img}" style="width:100%;height:auto;border-radius:6px"></div>`;
                    html += '</div>';
                }
                html += '<table style="width:100%;">';
                html += `<tr><td><strong>Storm ID</strong></td><td>${d.storm_id || ''}</td></tr>`;
                html += `<tr><td><strong>Name</strong></td><td>${d.name || ''}</td></tr>`;
                html += `<tr><td><strong>Basin</strong></td><td>${d.basin || ''}</td></tr>`;
                html += `<tr><td><strong>Formed</strong></td><td>${d.formed || ''}</td></tr>`;
                html += `<tr><td><strong>Dissipated</strong></td><td>${d.dissipated || ''}</td></tr>`;
                html += `<tr><td><strong>Wind (mph)</strong></td><td>${d.msw || ''}</td></tr>`;
                        // derive SSHWS classification from msw for display
                        let sshws_display = '';
                        const mswVal = parseInt(d.msw || '') || null;
                        if (mswVal) {
                            if (mswVal < 39) sshws_display = 'TD';
                            else if (mswVal >= 39 && mswVal <= 73) sshws_display = 'TS';
                            else if (mswVal >= 74 && mswVal <= 95) sshws_display = 'C1';
                            else if (mswVal >= 96 && mswVal <= 110) sshws_display = 'C2';
                            else if (mswVal >= 111 && mswVal <= 129) sshws_display = 'C3';
                            else if (mswVal >= 130 && mswVal <= 156) sshws_display = 'C4';
                            else if (mswVal >= 157) sshws_display = 'C5';
                        }
                        html += `<tr><td><strong>SSHWS</strong></td><td>${sshws_display}</td></tr>`;
                        html += `<tr><td><strong>Wind (mph)</strong></td><td>${d.msw || ''}</td></tr>`;
                html += `<tr><td><strong>Pressure (mbar)</strong></td><td>${d.mslp || ''}</td></tr>`;
                html += `<tr><td><strong>ACE</strong></td><td>${d.ace_value || ''}</td></tr>`;
                html += `<tr><td><strong>Damage</strong></td><td>${d.damage || ''}</td></tr>`;
                html += `<tr><td><strong>Fatalities</strong></td><td>${d.fatalities || ''}</td></tr>`;
                html += `<tr><td><strong>Description</strong></td><td>${d.desc || ''}</td></tr>`;
                html += '</table>';

                document.getElementById('viewContent').innerHTML = html;
            })
            .catch(err => {
                alert('Error loading storm: ' + err);
                closeViewModal();
            });
    }

    function closeViewModal() {
        document.getElementById('viewModal').classList.remove('show');
    }

    /* Edit */
    function editStorm(id) {
        fetch('tc_database.php?action=get_storm&id=' + encodeURIComponent(id))
            .then(r => r.json())
            .then(data => {
                if (data && data.data) openModal('edit', data.data);
                else alert('Could not load storm data');
            })
            .catch(err => alert('Error loading storm for edit: ' + err));
    }

    /* Delete */
    function deleteStorm(id) {
        if (!confirm('Delete this storm?')) return;
        fetch('tc_database.php?action=delete_storm', {
                method: 'POST',
                headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
                body: new URLSearchParams({id:id})
            })
            .then(r => r.json())
            .then(res => {
                if (res.ok) {
                    location.reload();
                } else {
                    alert(res.error || 'Delete failed');
                }
            })
            .catch(err => alert('Error deleting: ' + err));
    }

    /* Submit add/edit form */
    function submitStorm(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('modalSubmit');
        if (submitBtn) submitBtn.disabled = true;
    const form = new FormData(document.getElementById('stormForm'));
    // include sshws selector value so backend can map class -> msw if numeric not provided
    const sshwsEl = document.getElementById('storm_sshws');
    if (sshwsEl) form.set('sshws', sshwsEl.value);
        fetch('tc_database.php?action=save_storm', {
                method: 'POST',
                body: form
            })
            .then(r => r.json())
            .then(res => {
                if (res.ok) {
                    alert('Saved successfully');
                    location.reload();
                } else {
                    alert(res.error || 'Error saving storm');
                }
            })
            .catch(err => alert('Save error: ' + err))
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
            });
    }

    /* Export handlers (use same query param approach as older links) */
    function exportCSV() {
        const params = new URLSearchParams(window.location.search);
        params.set('export', 'csv');
        window.location = window.location.pathname + '?' + params.toString();
    }

    function exportPDF() {
        const params = new URLSearchParams(window.location.search);
        params.set('export', 'pdf');
        window.location = window.location.pathname + '?' + params.toString();
    }
</script>

<?php include("includes/footer.php"); ?>

<?php
// (router moved earlier in file)
?>