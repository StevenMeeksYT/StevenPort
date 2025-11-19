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

include("db.php");
include("func.php");

$db = new DBFunc();

// Require login
if (!isset($_SESSION['username']) && !isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'] ?? $_COOKIE['username'];
$role = $_SESSION['role'] ?? $_COOKIE['role'] ?? 'user'; // Default to 'user'

// Include header
include("includes/header.php");
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title fade-in">
                    Welcome back, <span class="highlight"><?php echo htmlspecialchars($username); ?></span>!
                </h1>
                <h6 class="hero-subtitle fade-in">
                    This website is still under development. Please pardon the dust! 🚧
                </h6>
                <p class="hero-subtitle fade-in">
                    Ng Yi Thern — Full-Stack Developer & Creative Problem Solver
                </p>
                <p class="hero-description fade-in">
                    Explore my portfolio featuring web systems, creative projects, and technical tools
                    built with modern technologies including PHP, HTML, CSS, SQL, and JavaScript.
                </p>
                <div class="hero-actions fade-in">
                    <a href="about.php" class="btn btn-primary">Learn More</a>
                    <a href="contact.php" class="btn btn-outline">Get In Touch</a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="floating-cards">
                    <div class="floating-card card-1 holographic">🌐</div>
                    <div class="floating-card card-2 holographic">⚡</div>
                    <div class="floating-card card-3 holographic">🎨</div>
                    <div class="floating-card card-4 holographic">📊</div>
                </div>

                <!-- AI Terminal Display -->
                <div class="ai-terminal-display">
                    <div class="terminal-header">
                        <span class="terminal-dot red"></span>
                        <span class="terminal-dot yellow"></span>
                        <span class="terminal-dot green"></span>
                        <span class="terminal-title">AI_PORTFOLIO.exe</span>
                    </div>
                    <div class="terminal-content">
                        <div class="terminal-line">> Initializing portfolio systems...</div>
                        <div class="terminal-line">> Loading neural networks...</div>
                        <div class="terminal-line">> Analyzing user data...</div>
                        <div class="terminal-line">> Portfolio ready. Welcome, <?php echo htmlspecialchars($username); ?>!</div>
                    </div>
                </div>

                <!-- Neural Network Visualization -->
                <div class="neural-viz-container">
                    <div class="neural-viz" id="neuralViz"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🚀</div>
                <div class="stat-number">15+</div>
                <div class="stat-label">Projects Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💻</div>
                <div class="stat-number">5+</div>
                <div class="stat-label">Technologies Mastered</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🎯</div>
                <div class="stat-number">100%</div>
                <div class="stat-label">Client Satisfaction</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⚡</div>
                <div class="stat-number">24/7</div>
                <div class="stat-label">Available Support</div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Projects Section -->
<section class="projects-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Featured Projects</h2>
            <p class="section-subtitle">Handpicked projects showcasing my skills and expertise</p>
        </div>

        <div class="dashboard-grid">
            <!-- Project 1 -->
            <div class="card project-card">
                <div class="project-icon">🌪️</div>
                <h3>Tropical Cyclone Database</h3>
                <p>Comprehensive database management system for archiving and analyzing tropical cyclone records and climatological data with advanced filtering and visualization.</p>
                <div class="project-tech">
                    <span class="tech-tag">PHP</span>
                    <span class="tech-tag">MySQL</span>
                    <span class="tech-tag">JavaScript</span>
                </div>
                <a href="tc_database.php" class="btn btn-primary">
                    <?php echo $role === 'admin' ? "Manage Database" : "View Database"; ?>
                </a>
            </div>

            <!-- Project 2 -->
            <div class="card project-card">
                <div class="project-icon">🌤️</div>
                <h3>Weather Tracker</h3>
                <p>Real-time tropical cyclone monitoring system with live storm tracking, detailed meteorological data, and interactive maps using KnackWx ATCF API.</p>
                <div class="project-tech">
                    <span class="tech-tag">API Integration</span>
                    <span class="tech-tag">Real-time Data</span>
                    <span class="tech-tag">Interactive Maps</span>
                </div>
                <a href="weather_forecast.php" class="btn btn-primary">
                    <?php echo $role === 'admin' ? "Monitor Storms" : "View Tracker"; ?>
                </a>
            </div>

            <!-- Project 3 -->
            <div class="card project-card">
                <div class="project-icon">📊</div>
                <h3>System Monitor</h3>
                <p>Advanced system resource monitoring dashboard with live CPU, memory, and disk usage graphs, alerts, and performance analytics.</p>
                <div class="project-tech">
                    <span class="tech-tag">PHP</span>
                    <span class="tech-tag">Charts.js</span>
                    <span class="tech-tag">Real-time</span>
                </div>
                <a href="sys_monitor.php" class="btn btn-primary">
                    <?php echo $role === 'admin' ? "Monitor System" : "View Dashboard"; ?>
                </a>
            </div>

            <!-- Project 4 -->
            <div class="card project-card">
                <div class="project-icon">🎨</div>
                <h3>Anime Art Gallery</h3>
                <p>Creative web gallery showcasing AI-generated anime artwork with advanced filtering, search functionality, and high-resolution downloads.</p>
                <div class="project-tech">
                    <span class="tech-tag">Image Gallery</span>
                    <span class="tech-tag">Search & Filter</span>
                    <span class="tech-tag">Responsive Design</span>
                </div>
                <a href="anime_gallery.php" class="btn btn-primary">
                    <?php echo $role === 'admin' ? "Manage Gallery" : "Browse Gallery"; ?>
                </a>
            </div>

            <!-- Project 5 -->
            <div class="card project-card">
                <div class="project-icon">🛠️</div>
                <h3>Media Tools Suite</h3>
                <p>Collection of practical web utilities including digital clock, calendar widget, unit converter, and productivity tools for daily use.</p>
                <div class="project-tech">
                    <span class="tech-tag">JavaScript</span>
                    <span class="tech-tag">CSS3</span>
                    <span class="tech-tag">Utilities</span>
                </div>
                <a href="media_tools.php" class="btn btn-primary">
                    <?php echo $role === 'admin' ? "Manage Tools" : "Use Tools"; ?>
                </a>
            </div>

            <!-- Project 6 -->
            <div class="card project-card">
                <div class="project-icon">⚙️</div>
                <h3>Portfolio CMS</h3>
                <p>Custom content management system for dynamic project editing, image uploads, content publishing, and portfolio maintenance.</p>
                <div class="project-tech">
                    <span class="tech-tag">CMS</span>
                    <span class="tech-tag">File Upload</span>
                    <span class="tech-tag">Admin Panel</span>
                </div>
                <a href="cms_builder.php" class="btn btn-primary">
                    <?php echo $role === 'admin' ? "Manage Content" : "View CMS"; ?>
                </a>
            </div>
        </div>

        <!-- Project 7 -->
        <div class="card project-card">
            <div class="project-icon">🌪️</div>
            <h3>Tornado Database</h3>
            <p>Comprehensive tornado records database for tracking, analyzing, and visualizing historical and modern tornado events with advanced filters and statistics.</p>
            <div class="project-tech">
                <span class="tech-tag">PHP</span>
                <span class="tech-tag">MySQL</span>
                <span class="tech-tag">Data Visualization</span>
            </div>
            <a href="tornado_db.php" class="btn btn-primary">
                <?php echo $role === 'admin' ? "Manage Tornado DB" : "View Tornado DB"; ?>
            </a>
        </div>

        <!-- Project 8 -->
        <div class="card project-card">
            <div class="project-icon">📚</div>
            <h3>Research Papers Archive</h3>
            <p>Centralized library for uploading, managing, and accessing meteorological and scientific research papers with search and tagging features.</p>
            <div class="project-tech">
                <span class="tech-tag">PHP</span>
                <span class="tech-tag">File Upload</span>
                <span class="tech-tag">Search Engine</span>
            </div>
            <a href="research_papers.php" class="btn btn-primary">
                <?php echo $role === 'admin' ? "Manage Papers" : "Browse Archive"; ?>
            </a>
        </div>
    </div>
</section>

<!-- Quick Actions Section -->
<section class="quick-actions-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Quick Actions</h2>
            <p class="section-subtitle">Frequently used tools and resources</p>
        </div>

        <div class="quick-actions-grid">
            <a href="./StevenFJCombineFull.pdf" target="_blank" class="quick-action-card">
                <div class="action-icon">📄</div>
                <h3>Download Portfolio</h3>
                <p>Get my complete portfolio in PDF format</p>
            </a>

            <a href="about.php" class="quick-action-card">
                <div class="action-icon">👨‍💻</div>
                <h3>About Me</h3>
                <p>Learn more about my background and skills</p>
            </a>

            <a href="contact.php" class="quick-action-card">
                <div class="action-icon">📧</div>
                <h3>Contact</h3>
                <p>Get in touch for collaborations</p>
            </a>

            <?php if ($role === 'admin'): ?>
                <a href="dashboard.php" class="quick-action-card admin-only">
                    <div class="action-icon">⚙️</div>
                    <h3>Admin Dashboard</h3>
                    <p>Access administrative controls</p>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Additional CSS for Enhanced Index Page -->
<style>
    .hero-section {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        padding: var(--space-20) 0;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        animation: float 20s ease-in-out infinite;
    }

    .hero-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-12);
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .hero-title {
        font-size: var(--font-size-5xl);
        font-weight: 700;
        margin-bottom: var(--space-4);
        line-height: 1.2;
    }

    [data-theme="light"] .hero-title .highlight {
        color: #000000;
        background: none;
        background-clip: initial;
        -webkit-background-clip: initial;
        -webkit-text-fill-color: currentColor;
    }

    [data-theme="dark"] .hero-title .highlight {
        color: #ffffff;
        background: none;
        background-clip: initial;
        -webkit-background-clip: initial;
        -webkit-text-fill-color: currentColor;
    }

    .hero-subtitle {
        font-size: var(--font-size-xl);
        font-weight: 500;
        margin-bottom: var(--space-6);
        opacity: 0.9;
    }

    .hero-description {
        font-size: var(--font-size-lg);
        margin-bottom: var(--space-8);
        opacity: 0.8;
        line-height: 1.7;
    }

    .hero-actions {
        display: flex;
        gap: var(--space-4);
        flex-wrap: wrap;
    }

    .hero-visual {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    .floating-cards {
        position: relative;
        width: 300px;
        height: 300px;
    }

    .floating-card {
        position: absolute;
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-xl);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: var(--font-size-2xl);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .card-1 {
        top: 20%;
        left: 10%;
        animation: float 3s ease-in-out infinite;
    }

    .card-2 {
        top: 60%;
        right: 10%;
        animation: float 3s ease-in-out infinite 1s;
    }

    .card-3 {
        bottom: 20%;
        left: 30%;
        animation: float 3s ease-in-out infinite 2s;
    }

    .card-4 {
        top: 10%;
        right: 30%;
        animation: float 3s ease-in-out infinite 0.5s;
    }

    /* AI Terminal Display */
    .ai-terminal-display {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 300px;
        background: rgba(0, 0, 0, 0.8);
        border: 1px solid #ffffff;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
        z-index: 10;
        font-family: 'Courier New', monospace;
        color: #ffffff;
        backdrop-filter: blur(10px);
    }

    .terminal-header {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        background: rgba(255, 255, 255, 0.1);
        border-bottom: 1px solid #ffffff;
    }

    .terminal-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .terminal-dot.red {
        background: #ff5f56;
    }

    .terminal-dot.yellow {
        background: #ffbd2e;
    }

    .terminal-dot.green {
        background: #27ca3f;
    }

    .terminal-title {
        font-size: 12px;
        font-weight: bold;
        margin-left: 8px;
    }

    .terminal-content {
        padding: 12px;
        font-size: 11px;
        line-height: 1.4;
    }

    .terminal-line {
        margin: 4px 0;
        opacity: 0;
        animation: terminal-type 0.5s ease forwards;
    }

    .terminal-line:nth-child(1) {
        animation-delay: 1s;
    }

    .terminal-line:nth-child(2) {
        animation-delay: 2s;
    }

    .terminal-line:nth-child(3) {
        animation-delay: 3s;
    }

    .terminal-line:nth-child(4) {
        animation-delay: 4s;
    }

    /* Neural Network Container */
    .neural-viz-container {
        position: absolute;
        bottom: -50px;
        right: -50px;
        width: 200px;
        height: 150px;
        opacity: 0.3;
    }

    .neural-viz {
        width: 100%;
        height: 100%;
        position: relative;
    }

    .stats-section {
        padding: var(--space-16) 0;
        background: var(--bg-secondary);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--space-6);
    }

    .stat-card {
        text-align: center;
        padding: var(--space-6);
        background: var(--bg-primary);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-light);
        transition: all var(--transition-normal);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-medium);
    }

    .stat-icon {
        font-size: var(--font-size-4xl);
        margin-bottom: var(--space-4);
    }

    .stat-number {
        font-size: var(--font-size-3xl);
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: var(--space-2);
    }

    .stat-label {
        color: var(--text-secondary);
        font-weight: 500;
    }

    .projects-section {
        padding: var(--space-16) 0;
    }

    .section-header {
        text-align: center;
        margin-bottom: var(--space-12);
    }

    .section-title {
        font-size: var(--font-size-4xl);
        font-weight: 700;
        margin-bottom: var(--space-4);
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .section-subtitle {
        font-size: var(--font-size-lg);
        color: var(--text-secondary);
        max-width: 600px;
        margin: 0 auto;
    }

    .project-card {
        text-align: center;
        transition: all var(--transition-normal);
    }

    .project-card:hover {
        transform: translateY(-8px);
    }

    .project-icon {
        font-size: var(--font-size-5xl);
        margin-bottom: var(--space-4);
        display: block;
    }

    .project-tech {
        display: flex;
        flex-wrap: wrap;
        gap: var(--space-2);
        justify-content: center;
        margin: var(--space-4) 0;
    }

    .tech-tag {
        background: var(--bg-tertiary);
        color: var(--text-primary);
        padding: var(--space-1) var(--space-3);
        border-radius: var(--radius-full);
        font-size: var(--font-size-xs);
        font-weight: 600;
    }

    .quick-actions-section {
        padding: var(--space-16) 0;
        background: var(--bg-secondary);
    }

    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--space-6);
    }

    .quick-action-card {
        background: var(--bg-primary);
        border: 2px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: var(--space-6);
        text-align: center;
        text-decoration: none;
        color: var(--text-primary);
        transition: all var(--transition-normal);
    }

    .quick-action-card:hover {
        border-color: var(--primary-color);
        transform: translateY(-4px);
        box-shadow: var(--shadow-medium);
        text-decoration: none;
        color: var(--text-primary);
    }

    .quick-action-card.admin-only {
        border-color: var(--accent-color);
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.05), rgba(245, 158, 11, 0.1));
    }

    .action-icon {
        font-size: var(--font-size-4xl);
        margin-bottom: var(--space-4);
        display: block;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-content {
            grid-template-columns: 1fr;
            text-align: center;
            gap: var(--space-8);
        }

        .hero-title {
            font-size: var(--font-size-3xl);
        }

        .hero-actions {
            justify-content: center;
        }

        .floating-cards {
            width: 200px;
            height: 200px;
        }

        .floating-card {
            width: 40px;
            height: 40px;
            font-size: var(--font-size-lg);
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .quick-actions-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .hero-actions {
            flex-direction: column;
            align-items: center;
        }

        .hero-actions .btn {
            width: 100%;
            max-width: 300px;
        }

        .ai-terminal-display {
            width: 250px;
        }

        .neural-viz-container {
            width: 150px;
            height: 100px;
        }
    }
</style>

<!-- Advanced JavaScript for Index Page -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize neural network visualization
        if (window.futuristicEffects) {
            const neuralContainer = document.getElementById('neuralViz');
            if (neuralContainer) {
                window.futuristicEffects.createNeuralViz(neuralContainer);
            }
        }

        // Add futuristic hover effects to project cards
        const projectCards = document.querySelectorAll('.project-card');
        projectCards.forEach(card => {
            if (window.futuristicEffects) {
                window.futuristicEffects.addFuturisticHover(card);
            }

            // Add 3D flip effect
            card.addEventListener('click', function() {
                if (window.futuristicEffects) {
                    window.futuristicEffects.flipCard(this);
                }
            });
        });

        // Add glassmorphism to stats cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            card.classList.add('glass-card');
        });

        // Add quantum loader to buttons on hover
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                if (!this.querySelector('.quantum-loader')) {
                    const loader = document.createElement('div');
                    loader.className = 'quantum-loader';
                    loader.style.position = 'absolute';
                    loader.style.top = '50%';
                    loader.style.left = '50%';
                    loader.style.transform = 'translate(-50%, -50%)';
                    loader.style.opacity = '0';
                    this.style.position = 'relative';
                    this.appendChild(loader);

                    setTimeout(() => {
                        loader.style.opacity = '0.3';
                    }, 100);
                }
            });

            button.addEventListener('mouseleave', function() {
                const loader = this.querySelector('.quantum-loader');
                if (loader) {
                    loader.remove();
                }
            });
        });

        // Add holographic effect to floating cards
        const floatingCards = document.querySelectorAll('.floating-card');
        floatingCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.animation = 'hologram-flicker 0.3s infinite, float 3s ease-in-out infinite';
            });

            card.addEventListener('mouseleave', function() {
                this.style.animation = 'float 3s ease-in-out infinite';
            });
        });

        // Add data stream effects to project cards
        const projectCardsWithStream = document.querySelectorAll('.project-card');
        projectCardsWithStream.forEach(card => {
            const stream = document.createElement('div');
            stream.className = 'data-stream';
            stream.style.position = 'absolute';
            stream.style.top = '0';
            stream.style.left = '0';
            stream.style.width = '100%';
            stream.style.height = '2px';
            card.style.position = 'relative';
            card.appendChild(stream);
        });

        // Add cyberpunk button effects
        const cyberButtons = document.querySelectorAll('.btn-primary');
        cyberButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Create ripple effect
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;

                this.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Add morphing background to hero section
        const heroSection = document.querySelector('.hero-section');
        if (heroSection && window.futuristicEffects) {
            window.futuristicEffects.createMorphingBackground(heroSection);
        }

        // Initialize quantum loaders for loading states
        const submitButtons = document.querySelectorAll('button[type="submit"]');
        submitButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (window.futuristicEffects) {
                    const loader = window.futuristicEffects.createQuantumLoader(this);
                    setTimeout(() => {
                        if (loader.parentNode) {
                            loader.remove();
                        }
                    }, 2000);
                }
            });
        });
    });

    // Add ripple animation keyframes
    const rippleStyle = document.createElement('style');
    rippleStyle.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
    document.head.appendChild(rippleStyle);
</script>

<?php include("includes/footer.php"); ?>