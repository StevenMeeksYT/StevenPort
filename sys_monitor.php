<?php
include("db.php");
include("func.php");
if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();
}
?>
<?php include("includes/header.php"); ?>

<section class="page-container fade-in">
  <h1 class="page-title">System Monitor Dashboard</h1>
  <p class="subtitle">Live charts for simulated CPU, RAM, Disk, and more system metrics.</p>

  <a href="index.php" class="btn">Back</a>

  <div class="dashboard-grid">
    <div class="card"><canvas id="cpuChart"></canvas></div>
    <div class="card"><canvas id="ramChart"></canvas></div>
    <div class="card"><canvas id="diskChart"></canvas></div>
    <div class="card"><canvas id="gpuChart"></canvas></div>
    <div class="card"><canvas id="netChart"></canvas></div>
    <div class="card"><canvas id="tempChart"></canvas></div>
    <div class="card"><canvas id="procChart"></canvas></div>
        <!-- Additional monitoring cards -->
        <div class="card" id="internetCard">
            <h3>Internet</h3>
            <div id="internetStatus">Checking…</div>
            <div id="latency">Latency: — ms</div>
            <div id="httpCheck">HTTP: —</div>
            <div id="dnsCheck">External resource: —</div>
            <div class="mt-4">
                <canvas id="internetLatencyChart" class="w-full"></canvas>
            </div>
            <div class="mt-4">
                <canvas id="externalLoadChart" class="w-full"></canvas>
            </div>
        </div>

        <div class="card" id="mobileCard">
            <h3>Mobile / Device</h3>
            <div id="onlineStatus">Online: —</div>
            <div id="connectionInfo">Connection: —</div>
            <div id="batteryInfo">Battery: —</div>
            <div id="geoInfo">Geo: —</div>
            <div class="mt-4 action-bar">
                <label class="inline-block"><input type="checkbox" id="enableGeo" /> Enable Geolocation</label>
                <label class="inline-block"><input type="checkbox" id="enableBattery" /> Enable Battery</label>
            </div>
        </div>
        <div class="card" id="serverCard">
            <h3>Server Checks</h3>
            <div class="action-bar items-center mt-2">
                <input type="text" id="probeUrl" placeholder="https://example.com">
                <input type="text" id="probeHost" placeholder="host (example.com)">
                <input type="number" id="probePort" placeholder="port">
                <button class="btn" id="runHttpProbe">HTTP Probe</button>
                <button class="btn" id="runPortCheck">Port Check</button>
                <button class="btn" id="runDns">DNS Lookup</button>
            </div>
            <div id="serverResults" class="progress-container mt-2"></div>
            <div class="mt-4"><canvas id="serverLatencyChart" class="w-full"></canvas></div>
        </div>
  </div>
</section>

<!-- ✅ Load Chart.js properly before using it -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* =========================================================
   Chart.js System Monitor Dashboard
   ========================================================= */

function createLineChart(id, label, color) {
    const ctx = document.getElementById(id);
    if (!ctx) return null;
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: Array.from({length: 10}, (_, i) => ''),
            datasets: [{
                label,
                data: Array(10).fill(0),
                borderColor: color,
                backgroundColor: color.replace('1)', '0.2)'),
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            animation: false,
            responsive: true,
            plugins: { legend: { display: true, position: 'top' } },
            scales: { y: { min: 0, max: 100 } }
        }
    });
}

// ✅ Define all charts with unique colors
const charts = {
    cpu:  createLineChart('cpuChart',  'CPU Usage (%)',     'rgba(255,99,132,1)'),
    ram:  createLineChart('ramChart',  'RAM Usage (%)',     'rgba(54,162,235,1)'),
    disk: createLineChart('diskChart', 'Disk Usage (%)',    'rgba(255,206,86,1)'),
    gpu:  createLineChart('gpuChart',  'GPU Usage (%)',     'rgba(153,102,255,1)'),
    net:  createLineChart('netChart',  'Network Load (%)',  'rgba(75,192,192,1)'),
    temp: createLineChart('tempChart', 'CPU Temperature (°C)', 'rgba(255,159,64,1)'),
    proc: createLineChart('procChart', 'Processes Load (%)', 'rgba(99,255,132,1)')
};

// ✅ Update every 1 second with simulated random data
setInterval(() => {
    for (let key in charts) {
        const chart = charts[key];
        if (!chart) continue;
        chart.data.datasets[0].data.shift();
        let nextVal = Math.floor(Math.random() * 100);

        // special simulation tweaks
        if (key === 'temp') nextVal = 30 + Math.floor(Math.random() * 50);
        if (key === 'net')  nextVal = Math.floor(Math.random() * 80);

        chart.data.datasets[0].data.push(nextVal);
        chart.update();
    }
}, 1000);

/* -------------------------
   Extended monitors (client-side checks)
   - Connectivity / latency to same-origin
   - External image load latency (acts as HTTP/DNS test)
   - Navigator.onLine, Network Information API
   - Battery API
   - Geolocation (if permitted by user)
 ------------------------- */

function updateStatusText(id, text, ok) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = text;
    el.classList.toggle('status-ok', !!ok);
}

async function measureLatency(url = '/'){
    const start = performance.now();
    try{
        // use fetch with cache-bypass
        const res = await fetch(url + '?_mon=' + Date.now(), {cache: 'no-store', method: 'HEAD'});
        const ms = Math.round(performance.now() - start);
        return {ok: res && res.ok, ms};
    }catch(e){
        return {ok:false, ms: null};
    }
}

function measureImageLoad(url, timeout = 8000){
    return new Promise(resolve => {
        const img = new Image();
        const start = performance.now();
        let done = false;
        const tid = setTimeout(() => { if (!done) { done = true; resolve({ok:false, ms:null}); } }, timeout);
        img.onload = function(){ if (done) return; done = true; clearTimeout(tid); resolve({ok:true, ms: Math.round(performance.now() - start)}); };
        img.onerror = function(){ if (done) return; done = true; clearTimeout(tid); resolve({ok:false, ms:null}); };
        img.src = url + '?_mon=' + Date.now();
    });
}

async function runExtendedChecks(){
    // online status
    updateStatusText('onlineStatus', 'Online: ' + (navigator.onLine ? 'Yes' : 'No'), navigator.onLine);

    // measure same-origin latency
    const latency = await measureLatency('/');
    updateStatusText('latency', 'Latency: ' + (latency.ms !== null ? latency.ms + ' ms' : 'failed'), latency.ok);
    updateStatusText('internetStatus', 'Status: ' + (latency.ok ? 'Reachable' : 'Unreachable'), latency.ok);

    // HTTP check via image load to external CDN (avoids CORS for status)
    // Use a small Google-hosted image (public) — image loads are allowed cross-origin
    const imgUrl = 'https://www.gstatic.com/images/branding/product/1x/drive_2020q4_48dp.png';
    const imgRes = await measureImageLoad(imgUrl, 5000);
    updateStatusText('dnsCheck', 'External resource load: ' + (imgRes.ok ? (imgRes.ms + ' ms') : 'failed'), imgRes.ok);

    // Network Information API (may be undefined)
    const conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
    if (conn) {
        const info = `type=${conn.effectiveType || conn.type || 'unknown'} downlink=${conn.downlink || 'n/a'} rtt=${conn.rtt || 'n/a'}`;
        updateStatusText('connectionInfo', 'Connection: ' + info, true);
    } else {
        updateStatusText('connectionInfo', 'Connection: not available', false);
    }

    // Battery API
    if (navigator.getBattery) {
        try{
            const batt = await navigator.getBattery();
            const level = Math.round(batt.level * 100);
            const charging = batt.charging ? 'charging' : 'discharging';
            updateStatusText('batteryInfo', `Battery: ${level}% (${charging})`, level > 20);
        }catch(e){ updateStatusText('batteryInfo', 'Battery: unavailable', false); }
    } else {
        updateStatusText('batteryInfo', 'Battery: not supported', false);
    }

    // Geolocation (one-shot - may prompt user)
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            updateStatusText('geoInfo', `Geo: ${pos.coords.latitude.toFixed(4)}, ${pos.coords.longitude.toFixed(4)}`, true);
        }, err => {
            updateStatusText('geoInfo', 'Geo: permission denied or unavailable', false);
        }, {timeout: 5000});
    } else {
        updateStatusText('geoInfo', 'Geo: not supported', false);
    }
}

// initial run
runExtendedChecks();
// periodic runs
setInterval(runExtendedChecks, 10000);

/* Use master.css for styling; add a tiny status-ok helper that aligns with master variables */
(function(){
    const s = document.createElement('style');
    s.innerHTML = '.status-ok{color:var(--success-color,#10b981);font-weight:600;} .status-fail{color:var(--danger-color,#dc2626);font-weight:600;}';
    document.head.appendChild(s);
})();

/* -------------------------
   Latency charts & history
------------------------- */
const MAX_SAMPLES = 60;
const internetLatencySamples = JSON.parse(localStorage.getItem('internetLatencySamples') || '[]');
const externalLoadSamples = JSON.parse(localStorage.getItem('externalLoadSamples') || '[]');

function pushSample(arr, v){ arr.push(v); while(arr.length>MAX_SAMPLES) arr.shift(); }

// create charts (if canvas exists)
let internetLatencyChart = null, externalLoadChart = null;
function createSmallChart(canvasId, label, borderColor){
    const el = document.getElementById(canvasId);
    if(!el) return null;
    return new Chart(el, {
        type: 'line', data: { labels: Array(MAX_SAMPLES).fill(''), datasets:[{ label, data: Array.from({length:MAX_SAMPLES},()=>null), borderColor, backgroundColor: borderColor.replace('1)','0.15)'), fill:true, tension:0.35 }] },
        options: { animation:false, responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
    });
}

internetLatencyChart = createSmallChart('internetLatencyChart','Latency (ms)','rgba(54,162,235,1)');
externalLoadChart = createSmallChart('externalLoadChart','External load (ms)','rgba(255,159,64,1)');

function updateCharts(){
    if(internetLatencyChart){
        internetLatencyChart.data.datasets[0].data = internetLatencySamples.slice(-MAX_SAMPLES).map(v=>v==null?null:v);
        internetLatencyChart.update();
    }
    if(externalLoadChart){
        externalLoadChart.data.datasets[0].data = externalLoadSamples.slice(-MAX_SAMPLES).map(v=>v==null?null:v);
        externalLoadChart.update();
    }
}

// integrate with runExtendedChecks results by wrapping updateStatusText calls
const _updateStatusText = updateStatusText;
function updateStatusText(id, text, ok){
    _updateStatusText(id, text, ok);
    if(id === 'latency'){
        const ms = parseInt((text.match(/(\d+)\s?ms/) || [])[1] || '0',10);
        pushSample(internetLatencySamples, isNaN(ms)?null:ms);
        localStorage.setItem('internetLatencySamples', JSON.stringify(internetLatencySamples));
    }
    if(id === 'dnsCheck'){
        const ms = parseInt((text.match(/(\d+)\s?ms/) || [])[1] || '0',10);
        pushSample(externalLoadSamples, isNaN(ms)?null:ms);
        localStorage.setItem('externalLoadSamples', JSON.stringify(externalLoadSamples));
    }
    updateCharts();
}

// alert banner
function showAlert(msg, level='warn'){
    let banner = document.getElementById('sysAlertBanner');
    if(!banner){ banner = document.createElement('div'); banner.id = 'sysAlertBanner'; banner.style.position='fixed'; banner.style.top='12px'; banner.style.right='12px'; banner.style.zIndex=9999; banner.style.minWidth='200px'; document.body.appendChild(banner); }
    // use master.css alert classes for styling
    const item = document.createElement('div');
    item.className = 'alert ' + (level === 'error' ? 'alert-danger' : 'alert-warning');
    item.textContent = msg;
    banner.appendChild(item);
    setTimeout(()=>{ item.remove(); if(!banner.hasChildNodes()) banner.remove(); }, 6000);
}

// respect user settings for geo/battery
const enableGeoEl = document.getElementById('enableGeo');
const enableBatteryEl = document.getElementById('enableBattery');
if(enableGeoEl) {
    enableGeoEl.checked = localStorage.getItem('enableGeo') === '1';
    enableGeoEl.addEventListener('change', ()=> localStorage.setItem('enableGeo', enableGeoEl.checked ? '1' : '0'));
}
if(enableBatteryEl) {
    enableBatteryEl.checked = localStorage.getItem('enableBattery') === '1';
    enableBatteryEl.addEventListener('change', ()=> localStorage.setItem('enableBattery', enableBatteryEl.checked ? '1' : '0'));
}

// wrap geolocation and battery usage in runExtendedChecks to respect settings
const _runExtendedChecks = runExtendedChecks;
async function runExtendedChecks(){
    // online status
    updateStatusText('onlineStatus', 'Online: ' + (navigator.onLine ? 'Yes' : 'No'), navigator.onLine);

    // measure same-origin latency
    const latency = await measureLatency('/');
    updateStatusText('latency', 'Latency: ' + (latency.ms !== null ? latency.ms + ' ms' : 'failed'), latency.ok);
    updateStatusText('internetStatus', 'Status: ' + (latency.ok ? 'Reachable' : 'Unreachable'), latency.ok);

    // HTTP check via image load to external CDN
    const imgUrl = 'https://www.gstatic.com/images/branding/product/1x/drive_2020q4_48dp.png';
    const imgRes = await measureImageLoad(imgUrl, 5000);
    updateStatusText('dnsCheck', 'External resource load: ' + (imgRes.ok ? (imgRes.ms + ' ms') : 'failed'), imgRes.ok);

    // Network Information API
    const conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
    if (conn) {
        const info = `type=${conn.effectiveType || conn.type || 'unknown'} downlink=${conn.downlink || 'n/a'} rtt=${conn.rtt || 'n/a'}`;
        updateStatusText('connectionInfo', 'Connection: ' + info, true);
    } else {
        updateStatusText('connectionInfo', 'Connection: not available', false);
    }

    // Battery API (respect setting)
    const wantBattery = (localStorage.getItem('enableBattery') === '1');
    if (wantBattery && navigator.getBattery) {
        try{
            const batt = await navigator.getBattery();
            const level = Math.round(batt.level * 100);
            const charging = batt.charging ? 'charging' : 'discharging';
            updateStatusText('batteryInfo', `Battery: ${level}% (${charging})`, level > 20);
        }catch(e){ updateStatusText('batteryInfo', 'Battery: unavailable', false); }
    } else if (!wantBattery) {
        updateStatusText('batteryInfo', 'Battery: disabled', false);
    } else {
        updateStatusText('batteryInfo', 'Battery: not supported', false);
    }

    // Geolocation (respect setting)
    const wantGeo = (localStorage.getItem('enableGeo') === '1');
    if (wantGeo && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            updateStatusText('geoInfo', `Geo: ${pos.coords.latitude.toFixed(4)}, ${pos.coords.longitude.toFixed(4)}`, true);
        }, err => {
            updateStatusText('geoInfo', 'Geo: permission denied or unavailable', false);
        }, {timeout: 5000});
    } else if (!wantGeo) {
        updateStatusText('geoInfo', 'Geo: disabled', false);
    } else {
        updateStatusText('geoInfo', 'Geo: not supported', false);
    }

    // alerts when thresholds crossed
    if (!latency.ok || !imgRes.ok || !navigator.onLine) {
        showAlert('Connectivity issue detected', 'warn');
    }
    if (latency.ms !== null && latency.ms > 800) showAlert('High latency: ' + latency.ms + 'ms', 'warn');
    if (imgRes.ms !== null && imgRes.ms > 2000) showAlert('Slow external resource load: ' + imgRes.ms + 'ms', 'warn');
}

// initial charts update
updateCharts();

/* -------------------------
   Server-side probes integration
------------------------- */
const serverLatencySamples = JSON.parse(localStorage.getItem('serverLatencySamples') || '[]');
function pushServerSample(v){ serverLatencySamples.push(v); while(serverLatencySamples.length>MAX_SAMPLES) serverLatencySamples.shift(); localStorage.setItem('serverLatencySamples', JSON.stringify(serverLatencySamples)); }

let serverLatencyChart = null;
function createServerChart(){
    const el = document.getElementById('serverLatencyChart');
    if(!el) return null;
    return new Chart(el, { type:'line', data:{ labels:Array(MAX_SAMPLES).fill(''), datasets:[{label:'Server Latency', data:Array.from({length:MAX_SAMPLES},()=>null), borderColor:'rgba(75,192,192,1)', backgroundColor:'rgba(75,192,192,0.15)', fill:true}]}, options:{animation:false,responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}} });
}
serverLatencyChart = createServerChart();

function updateServerChart(){ if(serverLatencyChart){ serverLatencyChart.data.datasets[0].data = serverLatencySamples.slice(-MAX_SAMPLES).map(v=>v==null?null:v); serverLatencyChart.update(); } }

async function callApi(action, params={}){
    const qs = new URLSearchParams(Object.assign({action}, params));
    try{
        const res = await fetch('sys_monitor_api.php?' + qs.toString());
        return await res.json();
    }catch(e){ return {ok:false,error:e.message}; }
}

async function runServerHttpProbe(url){
    const res = await callApi('http_probe', {url});
    const outEl = document.getElementById('serverResults');
    if(!res.ok){ outEl.innerText = 'Error: ' + (res.error||'unknown'); return; }
    const d = res.data;
    outEl.innerHTML = `<div>HTTP probe to <strong>${d.url}</strong>: code=${d.http_code}, time=${d.time_ms}ms ${d.ok?'<span class="status-ok">OK</span>':'<span class="status-fail">FAIL</span>'}</div>`;
    pushServerSample(d.time_ms !== null ? d.time_ms : null);
    updateServerChart();
}

async function runServerPortCheck(host, port){
    const res = await callApi('port_check', {host, port});
    const outEl = document.getElementById('serverResults');
    if(!res.ok){ outEl.innerText = 'Error: ' + (res.error||'unknown'); return; }
    const d = res.data;
    outEl.innerHTML = `<div>Port ${d.port} on ${d.host}: ${d.ok?'<span class="status-ok">open</span>':'<span class="status-fail">closed</span>'} (${d.time_ms} ms)</div>`;
}

async function runServerDns(host){
    const res = await callApi('dns_lookup', {host});
    const outEl = document.getElementById('serverResults');
    if(!res.ok){ outEl.innerText = 'Error: ' + (res.error||'unknown'); return; }
    const d = res.data;
    outEl.innerHTML = `<div>DNS lookup for ${d.host}: ${d.ok?'<span class="status-ok">found</span>':'<span class="status-fail">no records</span>'}</div>`;
    const wrap = document.createElement('div');
    wrap.className = 'progress-container';
    wrap.style.maxHeight = '220px';
    wrap.style.overflow = 'auto';
    const pre = document.createElement('pre'); pre.textContent = JSON.stringify(d.records,null,2); pre.style.whiteSpace = 'pre-wrap'; pre.style.padding = '12px';
    wrap.appendChild(pre);
    outEl.appendChild(wrap);
}

document.getElementById('runHttpProbe')?.addEventListener('click', ()=>{
    const url = document.getElementById('probeUrl').value || window.location.origin;
    runServerHttpProbe(url);
});
document.getElementById('runPortCheck')?.addEventListener('click', ()=>{
    const host = document.getElementById('probeHost').value || window.location.hostname;
    const port = document.getElementById('probePort').value || 80;
    runServerPortCheck(host, port);
});
document.getElementById('runDns')?.addEventListener('click', ()=>{
    const host = document.getElementById('probeHost').value || window.location.hostname;
    runServerDns(host);
});

// run an initial server probe
setTimeout(()=>{ runServerHttpProbe(window.location.origin); updateServerChart(); }, 1000);
// periodic server probes
setInterval(()=>{ runServerHttpProbe(window.location.origin); }, 30000);
</script>

<?php include("includes/footer.php"); ?>
