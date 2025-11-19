<?php include("includes/header.php"); ?>

<section class="page-container fade-in">
  <h1 class="page-title">Live Cyclones (KnackWx)</h1>
  <p class="subtitle">Powered by KnackWx ATCF API</p>
  <a href="index.php" class="btn">Back</a>

  <div id="knackWxResult" class="card" style="text-align:left; padding:15px;">
    <p>Loading cyclone data...</p>
  </div>
<script>

// Fetch KnackWx ATCF data
async function loadKnackWx() {
    const container = document.getElementById("knackWxResult");
    try {
        const res = await fetch("https://api.knackwx.com/atcf/v2");
        if (!res.ok) throw new Error(`KnackWx API error: ${res.status}`);
        const data = await res.json();

        if (data.length === 0) {
            container.innerHTML = "<p>No active cyclones currently.</p>";
            return;
        }

        let html = "<ul style='list-style:none; padding-left:0;'>";
        data.forEach(storm => {
            html += `<li style="margin-bottom:15px; border-bottom:1px solid #ccc; padding-bottom:10px;">
                <strong>${storm.storm_name}</strong> (${storm.atcf_id})<br>
                Winds: ${storm.winds} kt | Pressure: ${storm.pressure} hPa<br>
                Lat/Lon: ${storm.latitude.toFixed(2)}, ${storm.longitude.toFixed(2)}<br>
                Cyclone Nature: ${storm.cyclone_nature}<br>
                Last Updated: ${new Date(storm.last_updated).toLocaleString()}<br>
                ATCF File: <a href="${storm.atcf_sector_file}" target="_blank">Sector File</a> |
                Interp File: <a href="${storm.interp_sector_file}" target="_blank">Interp File</a>
            </li>`;
        });
        html += "</ul>";
        container.innerHTML = html;

    } catch (err) {
        container.innerHTML = `<p style='color:red;'>${err.message}</p>`;
    }
}

// Load data on page load
loadKnackWx();
</script>

<?php include("includes/footer.php"); ?>
