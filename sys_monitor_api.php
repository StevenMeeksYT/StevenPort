<?php
// sys_monitor_api.php
// Simple server-side probes for the System Monitor page.

header('Content-Type: application/json');

function json_out($data){ echo json_encode($data); exit; }

$action = $_GET['action'] ?? '';

// Basic whitelist for URLs for safety (avoid local file/schema probes).
function sanitize_url($url){
    $url = trim($url);
    if ($url === '') return null;
    // allow http/https only
    if (!preg_match('#^https?://#i', $url)) $url = 'http://' . $url;
    // parse host only
    $p = parse_url($url);
    if (!$p || empty($p['host'])) return null;
    return $url;
}

// Append a JSON line to server log (optional)
function append_log($entry){
    $logFile = __DIR__ . '/sys_monitor_logs.jsonl';
    $line = json_encode($entry, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    @file_put_contents($logFile, $line . "\n", FILE_APPEND | LOCK_EX);
}

try{
    switch($action){
        case 'http_probe': {
            $url = sanitize_url($_GET['url'] ?? '');
            if (!$url) json_out(['ok'=>false,'error'=>'Invalid URL']);
            $result = ['ok'=>false,'url'=>$url,'http_code'=>null,'time_ms'=>null,'error'=>null];
            // Prefer cURL
            if (function_exists('curl_init')){
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_NOBODY => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_FOLLOWLOCATION => true,
                ]);
                $t1 = microtime(true);
                $exec = curl_exec($ch);
                $t2 = microtime(true);
                $result['time_ms'] = (int)(($t2-$t1)*1000);
                $result['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $err = curl_error($ch);
                curl_close($ch);
                $result['ok'] = ($result['http_code'] >= 200 && $result['http_code'] < 400);
                $result['error'] = $err ?: null;
            } else {
                // fallback to file_get_contents
                $opts = ['http'=>['method'=>'HEAD','timeout'=>8]];
                $ctx = stream_context_create($opts);
                $t1 = microtime(true);
                $res = @fopen($url, 'rb', false, $ctx);
                $t2 = microtime(true);
                $result['time_ms'] = (int)(($t2-$t1)*1000);
                if ($res){
                    $meta = stream_get_meta_data($res);
                    $headers = $meta['wrapper_data'] ?? [];
                    foreach($headers as $h){ if (preg_match('#HTTP/\d\.\d\s+(\d+)#i',$h,$m)){ $result['http_code']=(int)$m[1]; break; } }
                    fclose($res);
                    $result['ok'] = ($result['http_code']>=200 && $result['http_code']<400);
                } else {
                    $result['error'] = 'request failed';
                }
            }
            if (isset($_GET['log']) && $_GET['log']=='1') append_log(['action'=>'http_probe','ts'=>time(),'result'=>$result]);
            json_out(['ok'=>true,'data'=>$result]);
        }

        case 'port_check': {
            $host = $_GET['host'] ?? '';
            $port = intval($_GET['port'] ?? 0);
            if (!$host || !$port) json_out(['ok'=>false,'error'=>'Invalid host/port']);
            $start = microtime(true);
            $conn = @fsockopen($host, $port, $errno, $errstr, 5);
            $time_ms = (int)((microtime(true)-$start)*1000);
            if ($conn){ fclose($conn); $ok = true; } else { $ok = false; }
            $res = ['host'=>$host,'port'=>$port,'ok'=>$ok,'time_ms'=>$time_ms,'error'=>$errstr?:null];
            if (isset($_GET['log']) && $_GET['log']=='1') append_log(['action'=>'port_check','ts'=>time(),'result'=>$res]);
            json_out(['ok'=>true,'data'=>$res]);
        }

        case 'dns_lookup': {
            $host = $_GET['host'] ?? '';
            if (!$host) json_out(['ok'=>false,'error'=>'Invalid host']);
            $records = @dns_get_record($host, DNS_A + DNS_AAAA + DNS_CNAME + DNS_MX);
            $ok = !empty($records);
            $res = ['host'=>$host,'ok'=>$ok,'records'=>$records ?: []];
            if (isset($_GET['log']) && $_GET['log']=='1') append_log(['action'=>'dns_lookup','ts'=>time(),'result'=>$res]);
            json_out(['ok'=>true,'data'=>$res]);
        }

        case 'server_uptime': {
            $res = ['ok'=>true,'php_uname'=>php_uname(),'load'=>null,'uptime'=>null];
            if (function_exists('sys_getloadavg')) $res['load'] = sys_getloadavg();
            // try /proc/uptime (linux)
            if (is_readable('/proc/uptime')){
                $u = @file_get_contents('/proc/uptime');
                if ($u){ $parts = explode(' ', trim($u)); $res['uptime'] = (float)$parts[0]; }
            } else if (function_exists('exec')){
                $out = @shell_exec('uptime');
                if ($out) $res['uptime_raw'] = trim($out);
            }
            json_out($res);
        }

        default:
            json_out(['ok'=>false,'error'=>'Unknown action']);
    }
} catch (Throwable $e){
    json_out(['ok'=>false,'error'=>$e->getMessage()]);
}

?>
