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
  <h1 class="page-title">Media Studio Tools</h1>
  <p class="subtitle">Utility collection — clock, calendar, converter</p>
  <a href="index.php" class="btn">Back</a>

  <div class="dashboard-grid">
    <div class="card">
      <h2>Digital Clock</h2>
      <div id="clock" style="font-size:2em;margin:10px 0;"></div>
    </div>

    <div class="card">
      <h2>Calendar</h2>
      <div id="calendar"></div>
    </div>

    <div class="card">
      <h2>Unit Converter</h2>
      <input type="number" id="km" placeholder="Kilometers">
      <button class="btn" id="convertBtn">Convert</button>
      <p id="result"></p>
    </div>
  </div>
<script>
function updateClock(){
  const now = new Date();
  document.getElementById("clock").textContent = now.toLocaleTimeString();
}
setInterval(updateClock, 1000);
updateClock();

const cal = document.getElementById("calendar");
const date = new Date();
cal.innerHTML = `<p>${date.toLocaleDateString(undefined,{month:"long",year:"numeric"})}</p>`;

document.getElementById("convertBtn").onclick = () => {
  const km = parseFloat(document.getElementById("km").value);
  if (isNaN(km)) return alert("Enter valid number!");
  document.getElementById("result").textContent = `${km} km = ${(km*0.621371).toFixed(2)} miles`;
};

// theme handled globally by header/footer includes
</script>

<?php include("includes/footer.php"); ?>
