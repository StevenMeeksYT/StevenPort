/* ============================================================
   STEVENPORT MASTER JS - Modern Responsive UI System v3.2
   Features: Dark/Light Theme, Mobile Menu, Modals, Buttons,
   Progress Updates, Smooth Animations, Utility Functions,
   Matrix Rain (Worldwide Unicode), Particle Network
   Author: Steven Weathers
============================================================ */

// Performance-sensitive animation configuration (top-level)
const _prefersReducedMotion = typeof window !== 'undefined' && window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const _hardwareConcurrency = typeof navigator !== 'undefined' ? (navigator.hardwareConcurrency || 4) : 4;
const _lowPowerDevice = _hardwareConcurrency <= 2;
const _ANIMATIONS_ENABLED = !_prefersReducedMotion && !_lowPowerDevice;

document.addEventListener("DOMContentLoaded", () => {

    /* ============================================================
       THEME TOGGLE - Light/Dark Mode
    ============================================================ */
    const themeToggle = document.querySelectorAll('[data-theme-toggle]');

    function setTheme(theme) {
        document.documentElement.setAttribute("data-theme", theme);
        localStorage.setItem("theme", theme);
    }

    // small lock to avoid double-toggles from multiple event handlers
    let _themeToggleLock = false;
    function toggleTheme() {
        if (_themeToggleLock) return;
        _themeToggleLock = true;
        setTimeout(() => { _themeToggleLock = false; }, 220);

        const currentTheme = document.documentElement.getAttribute("data-theme") || "light";
        const newTheme = currentTheme === "light" ? "dark" : "light";
        setTheme(newTheme);
        updateMatrixColor();
        // keep UI buttons/icons in sync
        updateThemeIcons();
    }

    // Robust binding: support click, pointer events and keyboard activation.
    function bindThemeToggles() {
        const nodes = document.querySelectorAll('[data-theme-toggle]');
        nodes.forEach(btn => {
            // avoid double-binding
            if (btn.__themeBound) return;
            btn.__themeBound = true;
            // mouse/keyboard/touch events
            btn.addEventListener('click', toggleTheme);
            btn.addEventListener('pointerup', (e) => { e.preventDefault(); toggleTheme(); });
            btn.addEventListener('keyup', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleTheme(); } });
        });
    }

    bindThemeToggles();
    // Re-bind when layout changes (new toggles added or DOM changes on resize)
    window.addEventListener('resize', debounce(() => bindThemeToggles(), 120));

    // Delegated handler: if clicking/tapping an element with data-theme-toggle doesn't trigger
    // due to binding order or layering, this catches pointerup events at document level.
    document.addEventListener('pointerup', (e) => {
        const b = e.target.closest && e.target.closest('[data-theme-toggle]');
        if (b) {
            e.preventDefault();
            toggleTheme();
        }
    });

    const savedTheme = localStorage.getItem("theme");
    if (savedTheme) setTheme(savedTheme);
    else if (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches) setTheme("dark");

    // Update all theme-toggle buttons/icons to reflect current state
    function updateThemeIcons() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const icon = isDark ? '🌞' : '🌙';
        const pressed = isDark ? 'true' : 'false';
        // elements using data-theme-toggle
        document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
            try { btn.textContent = icon; btn.setAttribute('aria-pressed', pressed); } catch (e) { }
        });
    }

    // Sync icons on load
    updateThemeIcons();
    // Ensure icons stay in sync after a resize (some UI may re-render)
    window.addEventListener('resize', debounce(updateThemeIcons, 120));

    /* ============================================================
       MOBILE MENU TOGGLE
    ============================================================ */
    const mobileToggle = document.querySelector(".mobile-menu-toggle");
    const mobileNav = document.querySelector(".mobile-nav");

    if (mobileToggle && mobileNav) {
        mobileToggle.addEventListener("click", () => {
            mobileToggle.classList.toggle("active");
            mobileNav.classList.toggle("active");
        });
        const mobileLinks = mobileNav.querySelectorAll(".mobile-nav-link");
        mobileLinks.forEach(link => link.addEventListener("click", () => {
            mobileToggle.classList.remove("active");
            mobileNav.classList.remove("active");
        }));
    }

    /* ============================================================
       MODAL FUNCTIONALITY
    ============================================================ */
    const modalTriggers = document.querySelectorAll("[data-modal-target]");
    const modals = document.querySelectorAll(".modal");
    const modalCloses = document.querySelectorAll(".modal-close");

    modalTriggers.forEach(trigger => {
        trigger.addEventListener("click", () => {
            const target = document.querySelector(trigger.dataset.modalTarget);
            if (target) target.classList.add("active");
        });
    });

    modalCloses.forEach(btn => {
        btn.addEventListener("click", () => {
            const modal = btn.closest(".modal");
            modal.classList.remove("active");
        });
    });

    modals.forEach(modal => {
        modal.addEventListener("click", e => {
            if (e.target === modal) modal.classList.remove("active");
        });
    });

    /* ============================================================
       SMOOTH SCROLL FOR ANCHORS
    ============================================================ */
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener("click", e => {
            e.preventDefault();
            const target = document.querySelector(link.getAttribute("href"));
            if (target) target.scrollIntoView({ behavior: "smooth", block: "start" });
        });
    });

    /* ============================================================
       PROGRESS CONTAINER DYNAMIC UPDATE
    ============================================================ */
    const progressContainer = document.querySelector(".progress-container");

    function addProgress(message) {
        if (!progressContainer) return;
        const item = document.createElement("div");
        item.classList.add("progress-item");
        item.innerHTML = message;
        progressContainer.appendChild(item);
        item.scrollIntoView({ behavior: "smooth", block: "end" });
    }

    window.addProgress = addProgress;

    /* ============================================================
       BUTTON RIPPLE EFFECT
    ============================================================ */
    document.querySelectorAll(".btn").forEach(btn => {
        btn.addEventListener("click", function (e) {
            // MATRIX RAIN: create only when allowed (reduces CPU on low-power / reduced-motion)
            let matrixCanvas, mCtx;
            if (_ANIMATIONS_ENABLED) {
                try {
                    matrixCanvas = document.createElement("canvas");
                    matrixCanvas.id = "matrix-canvas";
                    matrixCanvas.style.position = "fixed";
                    matrixCanvas.style.top = "0";
                    matrixCanvas.style.left = "0";
                    matrixCanvas.style.width = "100%";
                    matrixCanvas.style.height = "100%";
                    matrixCanvas.style.zIndex = "-2"; // behind content & particles
                    matrixCanvas.style.pointerEvents = "none";
                    document.body.appendChild(matrixCanvas);
                    mCtx = matrixCanvas.getContext("2d");
                } catch (err) {
                    console.warn('Matrix canvas init failed, skipping animations.', err);
                    matrixCanvas = null; mCtx = null;
                }
            }
            const ripple = document.createElement("span");
            ripple.className = "ripple";
            this.appendChild(ripple);
            const rect = this.getBoundingClientRect();
            ripple.style.left = `${e.clientX - rect.left}px`;
            ripple.style.top = `${e.clientY - rect.top}px`;
            setTimeout(() => ripple.remove(), 600);
        });
    });

    /* ============================================================
       FLOAT ANIMATION FOR CARDS
    ============================================================ */
    document.querySelectorAll(".card").forEach((card, index) => {
        card.style.animationDelay = `${index % 2 === 0 ? 0 : 3}s`;
    });

    /* ============================================================
       INPUT/FORM FOCUS GLOW
    ============================================================ */
    document.querySelectorAll("input, select, textarea").forEach(input => {
        input.addEventListener("focus", () => input.classList.add("focused"));
        input.addEventListener("blur", () => input.classList.remove("focused"));
    });

    /* ============================================================
       SHIMMER LOADING EFFECT
    ============================================================ */
    const loadingElements = document.querySelectorAll(".loading");
    function startShimmer() { loadingElements.forEach(el => el.classList.add("shimmer")); }
    function stopShimmer() { loadingElements.forEach(el => el.classList.remove("shimmer")); }
    window.startShimmer = startShimmer;
    window.stopShimmer = stopShimmer;

    /* ============================================================
       UTILITY FUNCTIONS
    ============================================================ */
    function debounce(func, wait = 100) {
        let timeout;
        return function (...args) { clearTimeout(timeout); timeout = setTimeout(() => func.apply(this, args), wait); };
    }
    function throttle(func, limit = 100) {
        let lastFunc, lastRan;
        return function (...args) {
            if (!lastRan) { func.apply(this, args); lastRan = Date.now(); }
            else { clearTimeout(lastFunc); lastFunc = setTimeout(() => {
                if ((Date.now() - lastRan) >= limit) { func.apply(this, args); lastRan = Date.now(); }
            }, limit - (Date.now() - lastRan)); }
        };
    }
    window.debounce = debounce;
    window.throttle = throttle;

    /* ============================================================
       SCROLL ANIMATIONS (APPEAR ON SCROLL)
    ============================================================ */
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add("fade-in"); });
    }, { threshold: 0.1 });
    document.querySelectorAll(".fade-in, .slide-up, .float").forEach(el => observer.observe(el));

    /* ============================================================
       ADVANCED SCROLL PROGRESS BAR
    ============================================================ */
    const progressBar = document.querySelector(".scroll-progress-bar");
    if (progressBar) {
        window.addEventListener("scroll", throttle(() => {
            const scrollTop = window.scrollY;
            const docHeight = document.body.scrollHeight - window.innerHeight;
            const scrollPercent = (scrollTop / docHeight) * 100;
            progressBar.style.width = `${scrollPercent}%`;
        }, 20));
    }

    /* ============================================================
       MATRIX RAIN & PARTICLE NETWORK (performance-safe)
       - Only initialize heavy canvases when animations allowed
       - Throttle matrix to low FPS, cap canvas resolution
       - Scale particle count to viewport and hardware
       - Pause when document hidden
    ============================================================ */
    if (_ANIMATIONS_ENABLED) {
        // MATRIX RAIN
        try {
            const matrixCanvas = document.createElement("canvas");
            matrixCanvas.id = "matrix-canvas";
            matrixCanvas.style.position = "fixed";
            matrixCanvas.style.top = "0";
            matrixCanvas.style.left = "0";
            matrixCanvas.style.width = "100%";
            matrixCanvas.style.height = "100%";
            matrixCanvas.style.zIndex = "-2";
            matrixCanvas.style.pointerEvents = "none";
            document.body.appendChild(matrixCanvas);

            const mCtx = matrixCanvas.getContext("2d");
            const unicodeRanges = [
                [0x0020, 0x007E],[0x00A0, 0x00FF],[0x0370, 0x03FF],[0x0400, 0x04FF],
                [0x0590, 0x05FF],[0x0600, 0x06FF],[0x0900, 0x097F],[0x0E00, 0x0E7F],
                [0x3040, 0x309F],[0x30A0, 0x30FF],[0x4E00, 0x9FFF],[0x1F300, 0x1F6FF]
            ];
            const letters = unicodeRanges.map(r => { let s=''; for(let i=r[0];i<=r[1];i++){try{s+=String.fromCodePoint(i);}catch{} } return s; }).join('').split('');
            const fontSize = 16;
            let columns = Math.floor(window.innerWidth / fontSize);
            let drops = Array(columns).fill(1);

            function resizeMatrix() {
                matrixCanvas.width = Math.min(window.innerWidth, 1600);
                matrixCanvas.height = Math.min(window.innerHeight, 1200);
                columns = Math.floor(matrixCanvas.width / fontSize);
                drops = Array(columns).fill(1);
            }
            resizeMatrix();
            window.addEventListener('resize', debounce(resizeMatrix, 200));

            function updateMatrixColor() {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                mCtx.fillStyle = isDark ? '#0F0' : '#008000';
            }

            function drawMatrix() {
                try {
                    mCtx.fillStyle = 'rgba(0,0,0,0.08)';
                    mCtx.fillRect(0,0,matrixCanvas.width,matrixCanvas.height);
                    updateMatrixColor();
                    mCtx.font = fontSize + 'px monospace';
                    for (let i = 0; i < drops.length; i++) {
                        const text = letters[Math.floor(Math.random()*letters.length)];
                        mCtx.fillText(text, i*fontSize, drops[i]*fontSize);
                        if (drops[i]*fontSize > matrixCanvas.height && Math.random() > 0.975) drops[i] = 0;
                        drops[i]++;
                    }
                } catch (err) { /* ignore drawing errors */ }
            }

            let matrixTimer = setInterval(drawMatrix, 80); // ~12.5 FPS
            document.addEventListener('visibilitychange', () => { if (document.hidden) clearInterval(matrixTimer); else matrixTimer = setInterval(drawMatrix, 80); });
        } catch (err) { console.warn('Matrix failed to initialize', err); }

        // PARTICLES
        try {
            const particleCanvas = document.createElement('canvas');
            particleCanvas.id = 'particle-canvas';
            particleCanvas.style.position = 'fixed';
            particleCanvas.style.top = '0';
            particleCanvas.style.left = '0';
            particleCanvas.style.width = '100%';
            particleCanvas.style.height = '100%';
            particleCanvas.style.zIndex = '-3';
            particleCanvas.style.pointerEvents = 'none';
            document.body.appendChild(particleCanvas);
            const pCtx = particleCanvas.getContext('2d');

            function resizeParticles() {
                particleCanvas.width = Math.min(window.innerWidth, 1600);
                particleCanvas.height = Math.min(window.innerHeight, 1200);
            }
            resizeParticles();
            window.addEventListener('resize', debounce(resizeParticles, 200));

            const particleCount = Math.min(120, Math.max(30, Math.floor(window.innerWidth / 15)));
            const particles = [];
            for (let i = 0; i < particleCount; i++) {
                particles.push({ x: Math.random()*particleCanvas.width, y: Math.random()*particleCanvas.height, vx: (Math.random()-0.5)*0.6, vy: (Math.random()-0.5)*0.6, r: Math.random()*2+1 });
            }

            let particleRaf = null;
            function drawParticles() {
                try {
                    pCtx.clearRect(0,0,particleCanvas.width,particleCanvas.height);
                    pCtx.fillStyle = 'rgba(255,255,255,0.45)';
                    particles.forEach(p=>{ p.x+=p.vx; p.y+=p.vy; if(p.x<0||p.x>particleCanvas.width)p.vx*=-1; if(p.y<0||p.y>particleCanvas.height)p.vy*=-1; pCtx.beginPath(); pCtx.arc(p.x,p.y,p.r,0,Math.PI*2); pCtx.fill(); });
                    for (let i=0;i<particles.length;i++){
                        for (let j=i+1;j<particles.length;j++){
                            const dx = particles[i].x-particles[j].x; const dy = particles[i].y-particles[j].y; const dist = Math.sqrt(dx*dx+dy*dy);
                            if (dist<100) { pCtx.strokeStyle = 'rgba(255,255,255,'+ (1-dist/100)*0.18 +')'; pCtx.beginPath(); pCtx.moveTo(particles[i].x,particles[i].y); pCtx.lineTo(particles[j].x,particles[j].y); pCtx.stroke(); }
                        }
                    }
                } catch (err) { }
                // schedule next with a small timeout to reduce churn on low-power devices
                particleRaf = requestAnimationFrame(() => setTimeout(drawParticles, _lowPowerDevice ? 80 : 40));
            }
            drawParticles();
            document.addEventListener('visibilitychange', () => { if (document.hidden && particleRaf) { cancelAnimationFrame(particleRaf); particleRaf = null; } else if (!particleRaf) drawParticles(); });
        } catch (err) { console.warn('Particles failed to initialize', err); }
    } // end if _ANIMATIONS_ENABLED

    /* ============================================================
       GLOBAL EXPORT
    ============================================================ */
    window.MasterUI = { setTheme, toggleTheme, addProgress, startShimmer, stopShimmer };

}); // DOMContentLoaded

/* ============================================================
   Defensive submit fallback + global JS error handlers
   - If a click on a submit button does not trigger a submit event (e.g., blocked by overlay or errant preventDefault),
     the fallback will call form.submit() after a short timeout. This only runs when no submit event occurred.
   - Global error handlers log uncaught exceptions and unhandled promise rejections to console to avoid silent failures.
============================================================ */

// Mark forms as submitted when a submit event fires (capture phase)
document.addEventListener('submit', function (e) {
    try { e.target.dataset.__submitted = '1'; } catch (err) { /* ignore */ }
}, true);

// Click capture on submit buttons: if no submit occurs shortly after click, fallback to native submit
document.addEventListener('click', function (e) {
    try {
        const btn = e.target.closest && e.target.closest('button, input[type="submit"]');
        if (!btn) return;
        const type = (btn.getAttribute && (btn.getAttribute('type') || btn.type) || '').toLowerCase();
        // Only interested in submit buttons (exclude buttons used for other JS-only actions)
        if (type && type !== 'submit') return;
        const form = btn.closest && btn.closest('form');
        if (!form) return;

        // Reset flag then wait briefly — if submit event didn't fire, call native form.submit()
        form.dataset.__submitted = '0';
        setTimeout(() => {
            if (form.dataset.__submitted !== '1') {
                try {
                    // attempt native submit as a last-resort fallback
                    form.submit();
                } catch (err) {
                    console.warn('Fallback form.submit() failed', err);
                }
            }
            // cleanup marker
            try { delete form.dataset.__submitted; } catch (e) { form.dataset.__submitted = '0'; }
        }, 300);
    } catch (err) { /* swallow */ }
}, true);

// Global JS error handler
window.addEventListener('error', function (e) {
    try {
        console.error('Uncaught JS error:', e.message || e.error || e);
        // Non-blocking: could also POST to an internal logging endpoint if desired
    } catch (err) { /* ignore */ }
});

// Global unhandled rejection handler
window.addEventListener('unhandledrejection', function (e) {
    try {
        console.error('Unhandled promise rejection:', e.reason);
    } catch (err) { /* ignore */ }
});

/* ============================================================
   RIPPLES CSS CLASS
============================================================ */
const rippleStyle = document.createElement("style");
rippleStyle.innerHTML = `
.ripple { position:absolute; border-radius:50%; transform:scale(0);
  animation:ripple-effect 0.6s linear; background:rgba(255,255,255,0.3); pointer-events:none; }
@keyframes ripple-effect { to { transform:scale(4); opacity:0; } }`;
document.head.appendChild(rippleStyle);

/* ============================================================
   NEURAL NETWORK PARTICLE SIMULATION
============================================================ */
document.addEventListener("DOMContentLoaded", () => {
        // Only initialize neural network particle canvas if animations allowed
        if (_ANIMATIONS_ENABLED) {
                try {
                        const nnCanvas = document.createElement("canvas");
                        nnCanvas.id = "neural-net-canvas";
                        nnCanvas.style.position = "fixed";
                        nnCanvas.style.top = "0";
                        nnCanvas.style.left = "0";
                        nnCanvas.style.width = "100%";
                        nnCanvas.style.height = "100%";
                        nnCanvas.style.zIndex = "-2";
                        nnCanvas.style.pointerEvents = "none";
                        document.body.appendChild(nnCanvas);
                        const nnCtx = nnCanvas.getContext("2d");

                        function resizeCanvas() { nnCanvas.width = Math.min(window.innerWidth, 1600); nnCanvas.height = Math.min(window.innerHeight, 1200); }
                        resizeCanvas();
                        window.addEventListener('resize', debounce(resizeCanvas, 200));

                        const NUM_PARTICLES = Math.min(120, Math.max(20, Math.floor(window.innerWidth / 15)));
                        const particles = [];
                        for (let i = 0; i < NUM_PARTICLES; i++) particles.push({ x: Math.random() * nnCanvas.width, y: Math.random() * nnCanvas.height, vx: (Math.random() - 0.5) * 1.2, vy: (Math.random() - 0.5) * 1.2, radius: Math.random() * 2 + 1 });

                        const mouse = { x: null, y: null };
                        window.addEventListener('mousemove', e => { mouse.x = e.clientX; mouse.y = e.clientY; });
                        window.addEventListener('mouseout', () => { mouse.x = null; mouse.y = null; });

                        let nnRaf = null;
                        function drawParticles() {
                                try {
                                        nnCtx.clearRect(0, 0, nnCanvas.width, nnCanvas.height);
                                        for (let i = 0; i < particles.length; i++) {
                                                for (let j = i + 1; j < particles.length; j++) {
                                                        const dx = particles[i].x - particles[j].x;
                                                        const dy = particles[i].y - particles[j].y;
                                                        const dist = Math.sqrt(dx * dx + dy * dy);
                                                        if (dist < 120) {
                                                                nnCtx.strokeStyle = `rgba(0,255,255,${0.3 - dist / 400})`;
                                                                nnCtx.lineWidth = 1; nnCtx.beginPath(); nnCtx.moveTo(particles[i].x, particles[i].y); nnCtx.lineTo(particles[j].x, particles[j].y); nnCtx.stroke();
                                                        }
                                                }
                                                if (mouse.x !== null && mouse.y !== null) {
                                                        const dxm = particles[i].x - mouse.x, dym = particles[i].y - mouse.y; const distM = Math.sqrt(dxm * dxm + dym * dym);
                                                        if (distM < 150) { nnCtx.strokeStyle = `rgba(0,255,255,${0.4 - distM / 400})`; nnCtx.lineWidth = 1; nnCtx.beginPath(); nnCtx.moveTo(particles[i].x, particles[i].y); nnCtx.lineTo(mouse.x, mouse.y); nnCtx.stroke(); }
                                                }
                                        }
                                        particles.forEach(p => { nnCtx.fillStyle = '#0ff'; nnCtx.beginPath(); nnCtx.arc(p.x, p.y, p.radius, 0, Math.PI * 2); nnCtx.fill(); p.x += p.vx; p.y += p.vy; if (p.x < 0 || p.x > nnCanvas.width) p.vx *= -1; if (p.y < 0 || p.y > nnCanvas.height) p.vy *= -1; });
                                } catch (err) { }
                                nnRaf = requestAnimationFrame(() => setTimeout(drawParticles, _lowPowerDevice ? 80 : 40));
                        }
                        drawParticles();
                        document.addEventListener('visibilitychange', () => { if (document.hidden && nnRaf) { cancelAnimationFrame(nnRaf); nnRaf = null; } else if (!nnRaf) drawParticles(); });
                } catch (err) { console.warn('Neural canvas init failed', err); }
        }
  });
// End of master.js
