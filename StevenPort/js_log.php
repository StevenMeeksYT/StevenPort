<?php
// js_log.php - simple server-side logger for client JS errors
// Writes JSONL entries to logs/js_errors.log (one JSON object per line)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'POST only']);
    exit;
}

// Read raw body (client may send JSON)
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    // Fallback to form-encoded
    $data = $_POST;
}

$entry = [
    'ts' => date('c'),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'ua' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'referer' => $_SERVER['HTTP_REFERER'] ?? '',
    'data' => $data,
];

$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) mkdir($logDir, 0755, true);
$logFile = $logDir . '/js_errors.log';

// Append as JSON line
file_put_contents($logFile, json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);

header('Content-Type: application/json');
echo json_encode(['ok' => true]);
exit;
