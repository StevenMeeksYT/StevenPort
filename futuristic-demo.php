<?php
include("db.php");
include("func.php");

$db = new DBFunc();

// Require login
if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'] ?? $_COOKIE['username'];
$role = $_SESSION['role'] ?? $_COOKIE['role'] ?? 'user';

include("includes/header.php");
?>

<!-- Futuristic Demo Hero -->
<section class="demo-hero">
    <div class="container">
        <div class="demo-hero-content">
            <h1 class="holographic neon-glow">Futuristic Features Demo</h1>
            <p class="demo-subtitle">Experience the next generation of web interfaces</p>
            <div class="demo-controls">
                <button class="cyber-btn" onclick="toggleParticles()">Toggle Particles</button>
                <button class="cyber-btn" onclick="toggleMatrix()">Toggle Matrix</button>
                <button class="cyber-btn" onclick="toggleNeural()">Toggle Neural Net</button>
                <button class="cyber-btn" onclick="switchTheme()">Switch Theme</button>
            </div>
        </div>
    </div>
    <canvas id="matrixCanvas" style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;display:none;"></canvas>
</section>

<!-- Interactive Components Demo -->
<section class="demo-section">
    <div class="container">
        <h2 class="section-title holographic">Interactive Components</h2>
        <div class="demo-grid">
            <div class="demo-card glass-card">
                <h3>Glassmorphism Effects</h3>
                <p>Beautiful glass-like transparency with backdrop blur effects</p>
                <div class="glass-controls">
                    <button class="btn btn-primary">Glass Button</button>
                    <button class="btn btn-outline">Outline Button</button>
                </div>
            </div>

            <div class="demo-card card-3d" onclick="flipCard(this)">
                <div class="card-face">
                    <h4>Front Side</h4>
                    <p>Click to flip!</p>
                </div>
                <div class="card-back">
                    <h4>Back Side</h4>
                    <p>Amazing 3D effect!</p>
                </div>
            </div>

            <div class="demo-card">
                <h3>Holographic Text</h3>
                <h4 class="holographic">Holographic Title</h4>
                <p class="holographic">This text has a holographic effect with color morphing</p>
            </div>

            <div class="demo-card">
                <h3>AI Terminal Interface</h3>
                <div class="ai-terminal" id="demoTerminal">
                    <div class="terminal-line">> System initialized</div>
                    <div class="terminal-line">> Loading modules...</div>
                    <div class="terminal-line">> Welcome!</div>
                </div>
            </div>

            <div class="demo-card">
                <h3>Quantum Loader</h3>
                <div id="quantumDemo" class="quantum-loader"></div>
                <button class="btn btn-primary" onclick="startQuantumLoader()">Start Quantum Load</button>
            </div>

            <div class="demo-card">
                <h3>Neural Network</h3>
                <div id="neuralDemo" class="neural-viz"></div>
                <p>Interactive neural network visualization</p>
            </div>
        </div>
    </div>
</section>

<!-- Interactive Controls -->
<section class="controls-section">
    <div class="container">
        <h2 class="section-title holographic">Interactive Controls</h2>
        <div class="controls-grid">
            <div class="control-panel">
                <h3>Theme Controls</h3>
                <div class="control-group">
                    <button class="cyber-btn" onclick="setTheme('light')">Light Theme</button>
                    <button class="cyber-btn" onclick="setTheme('dark')">Dark Theme</button>
                    <button class="cyber-btn" onclick="setTheme('cyberpunk')">Cyberpunk Mode</button>
                </div>
            </div>
            <div class="control-panel">
                <h3>Effect Controls</h3>
                <div class="control-group">
                    <button class="cyber-btn" onclick="toggleGlow()">Toggle Glow</button>
                    <button class="cyber-btn" onclick="toggleShimmer()">Toggle Shimmer</button>
                    <button class="cyber-btn" onclick="toggleFloat()">Toggle Float</button>
                </div>
            </div>
            <div class="control-panel">
                <h3>Performance</h3>
                <div class="control-group">
                    <button class="cyber-btn" onclick="enableReducedMotion()">Reduce Motion</button>
                    <button class="cyber-btn" onclick="disableEffects()">Disable Effects</button>
                    <button class="cyber-btn" onclick="resetEffects()">Reset All</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // --------------------------
// Futuristic Effects Object
// --------------------------
window.futuristicEffects = {
    flipCard: function(card) { card.classList.toggle('flipped'); },
    createNeuralViz: function(container) {
        container.innerHTML = "<p style='text-align:center; color: var(--neon-cyan)'>Neural Network Active</p>";
    },
    createQuantumLoader: function(container) {
        container.innerHTML = "";
        for (let i=0;i<10;i++){
            const dot = document.createElement('div');
            dot.style.cssText = `
                width:15px; height:15px; border-radius:50%;
                background:var(--neon-pink); display:inline-block; margin:2px;
                animation: quantum-dot 1s infinite ease-in-out ${i*0.1}s;
            `;
            container.appendChild(dot);
        }
    },
    addFuturisticHover: function(card) {
        card.addEventListener('mouseenter', ()=> card.style.transform="translateY(-6px) scale(1.02)");
        card.addEventListener('mouseleave', ()=> card.style.transform="translateY(0) scale(1)");
    },
    addCyberpunkEffects: function() {
        document.body.style.backgroundColor="#0f0f0f";
        document.body.style.color="#00f0ff";
    }
};

// --------------------------
// Functions
// --------------------------
function flipCard(card){ window.futuristicEffects.flipCard(card); }
function setTheme(theme){
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    if(theme==='cyberpunk') window.futuristicEffects.addCyberpunkEffects();
}
function toggleGlow(){ document.querySelectorAll('.demo-card, .btn').forEach(el=>el.classList.toggle('neon-glow')); }
function toggleShimmer(){ document.querySelectorAll('.demo-card').forEach(el=>el.classList.toggle('glass-card')); }
function toggleFloat(){ document.querySelectorAll('.demo-card').forEach(el=>el.style.animation? el.style.animation="" : el.style.animation="float-card 3s ease-in-out infinite"); }
function enableReducedMotion(){ document.documentElement.style.setProperty('--transition-fast','0.01ms'); document.documentElement.style.setProperty('--transition-normal','0.01ms'); document.documentElement.style.setProperty('--transition-slow','0.01ms'); }
function disableEffects(){ document.querySelectorAll('.particle-container, .neural-viz, .card-3d, #matrixCanvas').forEach(el=>el.style.display='none'); }
function resetEffects(){ location.reload(); }

// --------------------------
// Particle Burst
// --------------------------
function createParticleBurst(){
    const container=document.getElementById('particleDemo');
    for(let i=0;i<20;i++){
        const p=document.createElement('div');
        p.style.cssText=`width:4px;height:4px;border-radius:50%;position:absolute;left:50%;top:50%;background:var(--neon-cyan);animation:burst 1s forwards;transform:translate(0,0)`;
        const angle=(2*Math.PI*i)/20;
        const distance=100+Math.random()*50;
        p.style.setProperty('--end-x', Math.cos(angle)*distance+'px');
        p.style.setProperty('--end-y', Math.sin(angle)*distance+'px');
        container.appendChild(p);
        setTimeout(()=>p.remove(),1000);
    }
}

// --------------------------
// Matrix Rain
// --------------------------
let matrixActive=false;
function toggleMatrix(){
    const canvas=document.getElementById('matrixCanvas');
    if(!matrixActive){
        canvas.style.display='block';
        matrixActive=true;
        startMatrixRain(canvas);
    } else {
        canvas.style.display='none';
        matrixActive=false;
    }
}

function startMatrixRain(canvas){
    const ctx=canvas.getContext('2d');
    canvas.width=canvas.offsetWidth;
    canvas.height=canvas.offsetHeight;
    const letters='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    const fontSize=14;
    const columns=Math.floor(canvas.width/fontSize);
    const drops=[];
    for(let x=0;x<columns;x++){ drops[x]=Math.random()*canvas.height; }
    
    function draw(){
        if(!matrixActive) return;
        ctx.fillStyle='rgba(0,0,0,0.05)';
        ctx.fillRect(0,0,canvas.width,canvas.height);
        ctx.fillStyle='#0F0';
        ctx.font=fontSize+'px monospace';
        for(let i=0;i<drops.length;i++){
            const text=letters.charAt(Math.floor(Math.random()*letters.length));
            ctx.fillText(text,i*fontSize,drops[i]);
            drops[i]+=fontSize;
            if(drops[i]>canvas.height && Math.random()>0.975) drops[i]=0;
        }
        requestAnimationFrame(draw);
    }
    draw();
}

// --------------------------
// Quantum Loader Start
// --------------------------
function startQuantumLoader(){ window.futuristicEffects.createQuantumLoader(document.getElementById('quantumDemo')); }

// --------------------------
// DOMContentLoaded Initialization
// --------------------------
document.addEventListener('DOMContentLoaded',()=>{
    const neuralDemo=document.getElementById('neuralDemo');
    if(neuralDemo) window.futuristicEffects.createNeuralViz(neuralDemo);
});

</script>
<?php include("includes/footer.php"); ?>
