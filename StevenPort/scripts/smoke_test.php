<?php
// Simple smoke test script for StevenPort pages.
// Usage: php scripts\smoke_test.php [base_url]
$base = $argv[1] ?? 'http://localhost/StevenPort';
$pages = [
    '/',
    '/index.php',
    '/anime_gallery.php',
    '/anime_admin.php',
    '/cms_builder.php',
    '/tc_database.php',
    '/tornado_db.php',
    '/research_papers.php'
];

function fetch($url) {
    $opts = ["http" => ["method" => "GET", "timeout" => 5]];
    $context = stream_context_create($opts);
    $start = microtime(true);
    $content = @file_get_contents($url, false, $context);
    $time = round((microtime(true) - $start) * 1000);
    $meta = $http_response_header[0] ?? '';
    preg_match('#HTTP/\d+\.\d+\s+(\d+)#', $meta, $m);
    $code = $m[1] ?? '0';
    return ['code' => (int)$code, 'content' => $content, 'time_ms' => $time];
}

echo "Running smoke tests against: $base\n\n";
$results = [];
foreach ($pages as $p) {
    $url = rtrim($base, '/') . $p;
    echo "Checking: $url ... ";
    $r = fetch($url);
    $ok = $r['code'] >= 200 && $r['code'] < 400 && !empty($r['content']);
    echo ($ok ? "OK" : "FAIL") . " ({$r['code']}, {$r['time_ms']}ms)\n";
    if (!$ok) {
        echo "  -> Response length: " . strlen($r['content']) . "\n";
    } else {
        // Basic content checks: look for <html> and MasterUI/js
        $checks = [];
        if (stripos($r['content'], '<html') === false) $checks[] = 'missing_html';
        if (stripos($r['content'], 'MasterUI') !== false) $checks[] = 'has_MasterUI';
        echo "  -> Checks: " . (empty($checks) ? 'basic' : implode(',', $checks)) . "\n";
    }
    $results[$url] = $r;
}

// Summary
$failed = array_filter($results, fn($r)=> !($r['code']>=200 && $r['code']<400 && !empty($r['content'])));

echo "\nSmoke test complete. " . (count($failed) ? count($failed) . " failures" : "All pages OK") . "\n";

if (count($failed)) {
    foreach ($failed as $url => $r) {
        echo "- $url => HTTP {$r['code']}, length=" . strlen($r['content']) . "\n";
    }
}

exit(count($failed) ? 2 : 0);
