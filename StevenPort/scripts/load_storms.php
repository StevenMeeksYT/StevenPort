<?php
// scripts/load_storms.php
// Simple CLI helper to inspect or import static storm JSON/CSV into DB (optional insert)
// Usage: php scripts\load_storms.php [--file=path] [--import]

$options = [];
foreach ($argv as $a) {
    if (strpos($a, '--file=') === 0) $options['file'] = substr($a,7);
    if ($a === '--import') $options['import'] = true;
}

$defaultFiles = [
    __DIR__ . '/../data/tornadoes.json',
    __DIR__ . '/../data/tc_storms.json'
];

$files = [];
if (!empty($options['file'])) {
    $files[] = $options['file'];
} else {
    foreach ($defaultFiles as $f) if (file_exists($f)) $files[] = $f;
}

if (empty($files)) {
    echo "No data files found. Place JSON files under data/ or pass --file=path\n";
    exit(1);
}

foreach ($files as $f) {
    echo "Inspecting: $f\n";
    $txt = file_get_contents($f);
    $data = json_decode($txt, true);
    if ($data === null) {
        echo "  ERROR: invalid JSON (".json_last_error_msg().")\n";
        continue;
    }
    echo "  Records: " . count($data) . "\n";
    // show first record keys
    if (count($data) > 0) {
        $first = $data[0];
        echo "  Sample keys: " . implode(', ', array_keys($first)) . "\n";
    }

    if (!empty($options['import'])) {
        // Attempt to import into DB if DB config exists
        @include __DIR__ . '/../db.php';
        if (!class_exists('DBFunc')) { echo "  DBFunc not available; cannot import.\n"; continue; }
        $db = new DBFunc(); $conn = $db->getConnection();
        $table = strpos(basename($f),'tornado')!==false ? 'tornado_db' : 'TCDatabase';
        echo "  Importing to table: $table\n";
        $count = 0;
        foreach ($data as $row) {
            // Build insert dynamically - simple mapping (may need adjustment)
            $cols = array_keys($row);
            $placeholders = array_fill(0, count($cols), '?');
            $sql = 'INSERT INTO ' . $table . ' (`' . implode('`,`', $cols) . '`) VALUES (' . implode(',', $placeholders) . ')';
            $stmt = $conn->prepare($sql);
            if (!$stmt) { echo "    Prepare failed: " . $conn->error . "\n"; break; }
            // bind everything as strings for simplicity
            $types = str_repeat('s', count($cols));
            $vals = array_values($row);
            $refs = [];
            foreach ($vals as $k => $v) $refs[$k] = &$vals[$k];
            array_unshift($refs, $types);
            call_user_func_array([$stmt, 'bind_param'], $refs);
            if ($stmt->execute()) $count++; else echo "    Insert failed: " . $stmt->error . "\n";
        }
        echo "  Imported: $count records into $table\n";
    }
}

echo "Done.\n";
