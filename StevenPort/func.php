<?php
// $dev_mode = true; // Change to false on live server

// if ($dev_mode) {
//     ini_set('display_errors', 1);
//     ini_set('display_startup_errors', 1);
//     error_reporting(E_ALL);
// } else {
//     ini_set('display_errors', 0);
//     ini_set('log_errors', 1);
//     ini_set('error_log', __DIR__ . '/php_errors.log');
//     error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
// }

require_once("db.php");

// ini_set('session.cookie_lifetime', 86400 * 30);
// ini_set('session.gc_maxlifetime', 86400 * 30);

if (session_status() === PHP_SESSION_NONE) {
    // Extend session lifetime
    ini_set('session.cookie_lifetime', 86400 * 30);
    ini_set('session.gc_maxlifetime', 86400 * 30);
    session_start();
}

class DBFunc
{
    private $conn;
    private $imgDir;

    /* =======================================================
     * ACCESS CONTROL HELPERS
     * ======================================================= */
    private function requireAdmin()
    {
        $role = $_SESSION['role'] ?? $_COOKIE['role'] ?? 'user';
        if (!in_array(strtolower($role), ['admin', 'superadmin'])) {
            http_response_code(403);
            die("🚫 Access denied. Admin privileges required.");
        }
    }

    public function __construct()
    {
        $db = new DBConn();
        $this->conn = $db->getConnection();

        $this->imgDir = __DIR__ . "/images/";
        if (!is_dir($this->imgDir)) {
            mkdir($this->imgDir, 0777, true);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    /* =======================================================
     * USER MANAGEMENT
     * ======================================================= */

    public function registerUser($name, $email, $role, $pass)
    {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            throw new Exception("❌ This email is already registered.");
        }

        $validRoles = ['user', 'admin', 'superadmin'];
        $role = in_array(strtolower($role), $validRoles) ? strtolower($role) : 'user';

        $hashedPassword = password_hash($pass, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare("INSERT INTO users (username, email, role, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $role, $hashedPassword);

        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }

        $_SESSION['username'] = $name;
        $_SESSION['role'] = $role;
        setcookie("username", $name, time() + (365 * 24 * 60 * 60), "/", "", false, true);
        setcookie("role", $role, time() + (365 * 24 * 60 * 60), "/", "", false, true);

        $this->updateLastLogin($name);

        if ($role === 'admin' || $role === 'superadmin') {
            header("Location: dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    }

    public function loginUser($email, $pwd)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($pwd, $row['password'])) {
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];

                setcookie("username", $row['username'], time() + (365 * 24 * 60 * 60), "/", "", false, true);
                setcookie("role", $row['role'], time() + (365 * 24 * 60 * 60), "/", "", false, true);

                $this->updateLastLogin($row['username']);

                $role = strtolower($row['role']);
                if ($role === 'admin' || $role === 'superadmin') {
                    header("Location: dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                throw new Exception("❌ Invalid password.");
            }
        } else {
            throw new Exception("❌ User not found.");
        }
    }

    public function getUserRole()
    {
        return strtolower($_SESSION['role'] ?? $_COOKIE['role'] ?? 'user');
    }

    public function updateLastLogin($username)
    {
        $stmt = $this->conn->prepare("UPDATE users SET last_login = NOW() WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
    }
    
    public function deleteUser($id)
    {
        $this->requireAdmin();

        // Prevent deleting the superadmin (ID 1)
        if ($id == 1) {
            throw new Exception("🚫 You cannot delete the main admin account.");
        }

        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();

        if (!$result) {
            throw new Exception("❌ Database error while deleting user: " . $stmt->error);
        }

        return true;
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['username']) || isset($_COOKIE['username']);
    }

    /* =======================================================
     * PROJECTS MANAGEMENT (CRUD)
     * ======================================================= */

    public function getAllProjects()
    {
        $result = $this->conn->query("SELECT * FROM projects ORDER BY id DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProject($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function addProject($title, $description, $image = null)
    {
        $this->requireAdmin();
        $imageFile = $image && isset($image['error']) && $image['error'] === 0 ? $this->handleUpload($image, "project_") : null;
        $stmt = $this->conn->prepare("INSERT INTO projects (title, description, image, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $title, $description, $imageFile);
        return $stmt->execute();
    }

    public function updateProject($id, $title, $description, $image = null)
    {
        $this->requireAdmin();
        $current = $this->getProject($id);
        if (!$current) return false;

        $imageFile = $current['image'];
        if ($image && isset($image['error']) && $image['error'] === 0) {
            $imageFile = $this->handleUpload($image, "project_");
            if ($current['image']) @unlink($this->imgDir . $current['image']);
        }

        $stmt = $this->conn->prepare("UPDATE projects SET title=?, description=?, image=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("sssi", $title, $description, $imageFile, $id);
        return $stmt->execute();
    }

    public function deleteProject($id)
    {
        $this->requireAdmin();
        $project = $this->getProject($id);
        if (!$project) return false;
        if ($project['image']) @unlink($this->imgDir . $project['image']);

        $stmt = $this->conn->prepare("DELETE FROM projects WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* =======================================================
     * TROPICAL CYCLONE DATABASE (CRUD)
     * ======================================================= */

    public function addTC($storm_id, $storm_img, $track_img, $name, $basin, $msw, $mslp, $formed, $dissipated, $ace, $damage, $fatalities, $desc)
    {
        $this->requireAdmin();
        $stormFile = ($storm_img && isset($storm_img['error']) && $storm_img['error'] === UPLOAD_ERR_OK) ? $this->handleUpload($storm_img, "storm_") : null;
        $trackFile = ($track_img && isset($track_img['error']) && $track_img['error'] === UPLOAD_ERR_OK) ? $this->handleUpload($track_img, "track_") : null;

        $stmt = $this->conn->prepare("INSERT INTO tcdatabase 
            (storm_id, storm_img, track_img, name, basin, msw, mslp, formed, dissipated, ace_value, damage, fatalities, `desc`)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");

        $stmt->bind_param("sssssssssdsss", $storm_id, $stormFile, $trackFile, $name, $basin, $msw, $mslp, $formed, $dissipated, $ace, $damage, $fatalities, $desc);
        return $stmt->execute();
    }

    public function getAllTC()
    {
        $result = $this->conn->query("SELECT * FROM tcdatabase ORDER BY id DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function showTC($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tcdatabase WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateTC($id, $storm_id, $storm_img, $track_img, $name, $basin, $msw, $mslp, $formed, $dissipated, $ace, $damage, $fatalities, $desc)
    {
        $this->requireAdmin();
        $current = $this->showTC($id);

        $stormFile = ($storm_img && isset($storm_img['name']) && $storm_img['name']) ? $this->handleUpload($storm_img, "storm_") : $current['storm_img'];
        $trackFile = ($track_img && isset($track_img['name']) && $track_img['name']) ? $this->handleUpload($track_img, "track_") : $current['track_img'];

        if ($storm_img && isset($storm_img['name']) && $storm_img['name'] && $current['storm_img']) @unlink($this->imgDir . $current['storm_img']);
        if ($track_img && isset($track_img['name']) && $track_img['name'] && $current['track_img']) @unlink($this->imgDir . $current['track_img']);

        $stmt = $this->conn->prepare("UPDATE tcdatabase 
            SET storm_id=?, storm_img=?, track_img=?, name=?, basin=?, msw=?, mslp=?, formed=?, dissipated=?, ace_value=?, damage=?, fatalities=?, `desc`=? WHERE id=?");
        $stmt->bind_param("sssssssssdsssi", $storm_id, $stormFile, $trackFile, $name, $basin, $msw, $mslp, $formed, $dissipated, $ace, $damage, $fatalities, $desc, $id);
        return $stmt->execute();
    }

    public function deleteTC($id)
    {
        $this->requireAdmin();
        $storm = $this->showTC($id);
        if ($storm['storm_img']) @unlink($this->imgDir . $storm['storm_img']);
        if ($storm['track_img']) @unlink($this->imgDir . $storm['track_img']);
        $stmt = $this->conn->prepare("DELETE FROM tcdatabase WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* =======================================================
     * ANIME GALLERY (CRUD)
     * ======================================================= */

    public function addAnimeArt($title, $artist, $tags, $imageFile, $description)
    {
        $this->requireAdmin();
        $imgFile = $this->handleUpload($imageFile, "anime_");
        $stmt = $this->conn->prepare("INSERT INTO anime_gallery (title, artist, tags, image, description) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $title, $artist, $tags, $imgFile, $description);
        return $stmt->execute();
    }

    public function getAllAnimeArt()
    {
        $result = $this->conn->query("SELECT * FROM anime_gallery ORDER BY id DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function showAnimeArt($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM anime_gallery WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateAnimeArt($id, $title, $artist, $tags, $imageFile, $description)
    {
        $this->requireAdmin();
        $current = $this->showAnimeArt($id);

        $imgFile = ($imageFile && isset($imageFile['name']) && $imageFile['name']) ? $this->handleUpload($imageFile, "anime_") : $current['image'];
        if ($imageFile && isset($imageFile['name']) && $imageFile['name'] && $current['image']) @unlink($this->imgDir . $current['image']);

        $stmt = $this->conn->prepare("UPDATE anime_gallery SET title=?, artist=?, tags=?, image=?, description=? WHERE id=?");
        $stmt->bind_param("sssssi", $title, $artist, $tags, $imgFile, $description, $id);
        return $stmt->execute();
    }

    public function deleteAnimeArt($id)
    {
        $this->requireAdmin();
        $art = $this->showAnimeArt($id);
        if ($art['image']) @unlink($this->imgDir . $art['image']);
        $stmt = $this->conn->prepare("DELETE FROM anime_gallery WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* =======================================================
     * ANIME GALLERY FILTER + PAGINATION
     * ======================================================= */

    public function countFilteredAnimeArt($search = '')
    {
        $sql = "SELECT COUNT(*) AS total FROM anime_gallery WHERE 1";
        $params = [];
        $types = "";

        if ($search !== '') {
            $sql .= " AND (title LIKE ? OR tags LIKE ? OR artist LIKE ?)";
            $params = ["%$search%", "%$search%", "%$search%"];
            $types = "sss";
        }

        $stmt = $this->conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return (int)($res['total'] ?? 0);
    }

    public function getFilteredAnimeArt($search = '', $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM anime_gallery WHERE 1";
        $params = [];
        $types = "";

        if ($search !== '') {
            $sql .= " AND (title LIKE ? OR tags LIKE ? OR artist LIKE ?)";
            $params = ["%$search%", "%$search%", "%$search%"];
            $types = "sss";
        }

        $sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    /* =======================================================
     * TROPICAL CYCLONE DATABASE HELPERS (FILTERS, BASINS)
     * ======================================================= */

    // Return array of distinct basins (strings)
    public function getAllBasins()
    {
        $result = $this->conn->query("SELECT DISTINCT basin FROM tcdatabase ORDER BY basin ASC");
        if (!$result) return [];
        $basins = [];
        while ($row = $result->fetch_assoc()) {
            $basins[] = $row['basin'];
        }
        return $basins;
    }

    // Get filtered TC rows (id, storm_id, name, basin, msw, mslp, formed)
    public function getFilteredTCs($search = '', $basin = '', $year = '', $wind = '', $limit = 10, $offset = 0)
    {
        $where = [];
        $params = [];
        $types = "";

        if (!empty($search)) {
            $where[] = "(name LIKE ? OR storm_id LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $types .= "ss";
        }
        if (!empty($basin)) {
            $where[] = "basin = ?";
            $params[] = $basin;
            $types .= "s";
        }
        if (!empty($year)) {
            $where[] = "YEAR(formed) = ?";
            $params[] = (int)$year;
            $types .= "i";
        }
        if (!empty($wind)) {
            if ($wind === ">=100") {
                $where[] = "msw >= 100";
            } elseif ($wind === ">=64") {
                $where[] = "msw >= 64";
            } elseif ($wind === "<64") {
                $where[] = "msw < 64";
            }
        }

        $sql = "SELECT id, storm_id, name, basin, msw, mslp, formed FROM tcdatabase";
        if ($where) $sql .= " WHERE " . implode(" AND ", $where);

        $sql .= " ORDER BY formed DESC, name ASC LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function countFilteredTCs($search = '', $basin = '', $year = '', $wind = '')
    {
        $where = [];
        $params = [];
        $types = "";

        if (!empty($search)) {
            $where[] = "(name LIKE ? OR storm_id LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $types .= "ss";
        }
        if (!empty($basin)) {
            $where[] = "basin = ?";
            $params[] = $basin;
            $types .= "s";
        }
        if (!empty($year)) {
            $where[] = "YEAR(formed) = ?";
            $params[] = (int)$year;
            $types .= "i";
        }
        if (!empty($wind)) {
            if ($wind === ">=100") {
                $where[] = "msw >= 100";
            } elseif ($wind === ">=64") {
                $where[] = "msw >= 64";
            } elseif ($wind === "<64") {
                $where[] = "msw < 64";
            }
        }

        $sql = "SELECT COUNT(*) AS total FROM tcdatabase";
        if ($where) $sql .= " WHERE " . implode(" AND ", $where);

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return (int)($res['total'] ?? 0);
    }

    /* =======================================================
     * PRIVATE HELPERS
     * ======================================================= */
    private function handleUpload($file, $prefix)
    {
        if (!$file || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) return null;
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "gif", "webp", "svg"];
        if (!in_array($ext, $allowed)) return null;
        $filename = uniqid($prefix) . "." . $ext;
        $targetFile = $this->imgDir . $filename;
        move_uploaded_file($file["tmp_name"], $targetFile);
        return $filename;
    }

    /* =======================================================
     * 🌪️ TORNADO DATABASE (CRUD)
     * ======================================================= */

    public function addTornado($tornado_id, $name, $date, $state, $location, $intensity_scale, $width, $length, $fatalities, $injuries, $damage, $desc)
    {
        $this->requireAdmin();

        $stmt = $this->conn->prepare("INSERT INTO tornado_db 
            (tornado_id, name, date, state, location, intensity_scale, width, length, fatalities, injuries, damage, `desc`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "sssssssdddis",
            $tornado_id,
            $name,
            $date,
            $state,
            $location,
            $intensity_scale,
            $width,
            $length,
            $fatalities,
            $injuries,
            $damage,
            $desc
        );
        return $stmt->execute();
    }

    public function getAllTornadoes()
    {
        $result = $this->conn->query("SELECT * FROM tornado_db ORDER BY date DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function showTornado($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM tornado_db WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateTornado($id, $tornado_id, $name, $date, $state, $location, $intensity_scale, $width, $length, $fatalities, $injuries, $damage, $desc)
    {
        $this->requireAdmin();

        $stmt = $this->conn->prepare("UPDATE tornado_db 
            SET tornado_id=?, name=?, date=?, state=?, location=?, intensity_scale=?, width=?, length=?, fatalities=?, injuries=?, damage=?, `desc`=? 
            WHERE id=?");

        $stmt->bind_param(
            "sssssssdddisi",
            $tornado_id,
            $name,
            $date,
            $state,
            $location,
            $intensity_scale,
            $width,
            $length,
            $fatalities,
            $injuries,
            $damage,
            $desc,
            $id
        );
        return $stmt->execute();
    }

    public function deleteTornado($id)
    {
        $this->requireAdmin();
        $stmt = $this->conn->prepare("DELETE FROM tornado_db WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* =======================================================
     * 🌪️ FILTERED SEARCH + PAGINATION
     * ======================================================= */

    public function getFilteredTornadoes($search = '', $state = '', $year = '', $intensity = '', $limit = 10, $offset = 0)
    {
        $where = [];
        $params = [];
        $types = "";

        if (!empty($search)) {
            $where[] = "(name LIKE ? OR location LIKE ? OR tornado_id LIKE ?)";
            $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
            $types .= "sss";
        }
        if (!empty($state)) {
            $where[] = "state = ?";
            $params[] = $state;
            $types .= "s";
        }
        if (!empty($year)) {
            $where[] = "YEAR(date) = ?";
            $params[] = (int)$year;
            $types .= "i";
        }
        if (!empty($intensity)) {
            $where[] = "intensity_scale = ?";
            $params[] = $intensity;
            $types .= "s";
        }

        $sql = "SELECT * FROM tornado_db";
        if ($where) $sql .= " WHERE " . implode(" AND ", $where);
        $sql .= " ORDER BY date DESC LIMIT ? OFFSET ?";

        $params[] = (int)$limit;
        $params[] = (int)$offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function countFilteredTornadoes($search = '', $state = '', $year = '', $intensity = '')
    {
        $where = [];
        $params = [];
        $types = "";

        if (!empty($search)) {
            $where[] = "(name LIKE ? OR location LIKE ? OR tornado_id LIKE ?)";
            $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
            $types .= "sss";
        }
        if (!empty($state)) {
            $where[] = "state = ?";
            $params[] = $state;
            $types .= "s";
        }
        if (!empty($year)) {
            $where[] = "YEAR(date) = ?";
            $params[] = (int)$year;
            $types .= "i";
        }
        if (!empty($intensity)) {
            $where[] = "intensity_scale = ?";
            $params[] = $intensity;
            $types .= "s";
        }

        $sql = "SELECT COUNT(*) AS total FROM tornado_db";
        if ($where) $sql .= " WHERE " . implode(" AND ", $where);

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return (int)($res['total'] ?? 0);
    }

    /* =======================================================
     * 📚 RESEARCH PAPERS DATABASE (CRUD + FILTER)
     * ======================================================= */

    public function addResearchPaper($title, $authors, $year, $category, $abstract, $file)
    {
        $this->requireAdmin();

        $fileName = null;
        if ($file && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
            $allowed = ["pdf", "docx", "txt"];
            if (in_array($ext, $allowed)) {
                $fileName = uniqid("paper_") . "." . $ext;
                $targetPath = __DIR__ . "/papers/" . $fileName;
                if (!is_dir(__DIR__ . "/papers/")) mkdir(__DIR__ . "/papers/", 0777, true);
                move_uploaded_file($file["tmp_name"], $targetPath);
            }
        }

        $stmt = $this->conn->prepare("INSERT INTO research_papers (title, authors, year, category, abstract, file_name, uploaded_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssisss", $title, $authors, $year, $category, $abstract, $fileName);
        return $stmt->execute();
    }

    public function getAllResearchPapers()
    {
        $result = $this->conn->query("SELECT * FROM research_papers ORDER BY year DESC, id DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function showResearchPaper($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM research_papers WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateResearchPaper($id, $title, $authors, $year, $category, $abstract, $file = null)
    {
        $this->requireAdmin();
        $current = $this->showResearchPaper($id);
        $fileName = $current['file_name'];

        if ($file && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
            $allowed = ["pdf", "docx", "txt"];
            if (in_array($ext, $allowed)) {
                if ($fileName && file_exists(__DIR__ . "/papers/" . $fileName)) unlink(__DIR__ . "/papers/" . $fileName);
                $fileName = uniqid("paper_") . "." . $ext;
                move_uploaded_file($file["tmp_name"], __DIR__ . "/papers/" . $fileName);
            }
        }

        $stmt = $this->conn->prepare("UPDATE research_papers SET title=?, authors=?, year=?, category=?, abstract=?, file_name=? WHERE id=?");
        $stmt->bind_param("ssisssi", $title, $authors, $year, $category, $abstract, $fileName, $id);
        return $stmt->execute();
    }

    public function deleteResearchPaper($id)
    {
        $this->requireAdmin();
        $paper = $this->showResearchPaper($id);
        if ($paper && $paper['file_name'] && file_exists(__DIR__ . "/papers/" . $paper['file_name'])) {
            unlink(__DIR__ . "/papers/" . $paper['file_name']);
        }
        $stmt = $this->conn->prepare("DELETE FROM research_papers WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getFilteredPapers($search = '', $category = '', $year = '', $limit = 10, $offset = 0)
    {
        $where = [];
        $params = [];
        $types = "";

        if (!empty($search)) {
            $where[] = "(title LIKE ? OR authors LIKE ? OR abstract LIKE ?)";
            $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
            $types .= "sss";
        }
        if (!empty($category)) {
            $where[] = "category = ?";
            $params[] = $category;
            $types .= "s";
        }
        if (!empty($year)) {
            $where[] = "year = ?";
            $params[] = (int)$year;
            $types .= "i";
        }

        $sql = "SELECT * FROM research_papers";
        if ($where) $sql .= " WHERE " . implode(" AND ", $where);
        $sql .= " ORDER BY year DESC, id DESC LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function countFilteredPapers($search = '', $category = '', $year = '')
    {
        $where = [];
        $params = [];
        $types = "";

        if (!empty($search)) {
            $where[] = "(title LIKE ? OR authors LIKE ? OR abstract LIKE ?)";
            $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
            $types .= "sss";
        }
        if (!empty($category)) {
            $where[] = "category = ?";
            $params[] = $category;
            $types .= "s";
        }
        if (!empty($year)) {
            $where[] = "year = ?";
            $params[] = (int)$year;
            $types .= "i";
        }

        $sql = "SELECT COUNT(*) AS total FROM research_papers";
        if ($where) $sql .= " WHERE " . implode(" AND ", $where);
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return (int)($res['total'] ?? 0);
    }
} // end class DBFunc