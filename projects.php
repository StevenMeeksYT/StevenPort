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

// Include header
include("includes/header.php");
?>

<!-- Projects Hero Section -->
<section class="projects-hero">
    <div class="container">
        <div class="projects-hero-content">
            <h1 class="hero-title">My Projects</h1>
            <p class="hero-subtitle">
                A showcase of web applications, tools, and systems I've developed
            </p>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number">15+</span>
                    <span class="stat-label">Projects</span>
                </div>
                <div class="stat">
                    <span class="stat-number">6</span>
                    <span class="stat-label">Categories</span>
                </div>
                <div class="stat">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Success Rate</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Project Categories Filter -->
<section class="project-filter">
    <div class="container">
        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">All Projects</button>
            <button class="filter-btn" data-filter="web-apps">Web Applications</button>
            <button class="filter-btn" data-filter="tools">Development Tools</button>
            <button class="filter-btn" data-filter="databases">Database Systems</button>
            <button class="filter-btn" data-filter="creative">Creative Projects</button>
            <button class="filter-btn" data-filter="monitoring">Monitoring Systems</button>
        </div>
    </div>
</section>

<!-- Projects Grid -->
<section class="projects-grid-section">
    <div class="container">
        <div class="projects-grid">
            <!-- Project 1: Tropical Cyclone Database -->
            <div class="project-card" data-category="web-apps databases">
                <div class="project-image">
                    <div class="project-placeholder">
                        <span class="project-icon">🌪️</span>
                    </div>
                    <div class="project-overlay">
                        <div class="project-actions">
                            <a href="tc_database.php" class="btn btn-primary">
                                <?php echo $role === 'admin' ? "Manage" : "View"; ?>
                            </a>
                            <button class="btn btn-secondary project-info-btn" data-project="tc-database">Info</button>
                        </div>
                    </div>
                </div>
                <div class="project-content">
                    <div class="project-category">Web Application • Database</div>
                    <h3 class="project-title">Tropical Cyclone Database</h3>
                    <p class="project-description">
                        Comprehensive database management system for archiving and analyzing tropical cyclone records with advanced filtering and data visualization.
                    </p>
                    <div class="project-tech">
                        <span class="tech-tag">PHP</span>
                        <span class="tech-tag">MySQL</span>
                        <span class="tech-tag">JavaScript</span>
                        <span class="tech-tag">Data Analysis</span>
                    </div>
                    <div class="project-features">
                        <span class="feature">📊 Advanced Analytics</span>
                        <span class="feature">🔍 Search & Filter</span>
                        <span class="feature">📈 Data Visualization</span>
                    </div>
                </div>
            </div>

            <!-- Project 2: Weather Tracker -->
            <div class="project-card" data-category="web-apps monitoring">
                <div class="project-image">
                    <div class="project-placeholder">
                        <span class="project-icon">🌤️</span>
                    </div>
                    <div class="project-overlay">
                        <div class="project-actions">
                            <a href="weather_forecast.php" class="btn btn-primary">
                                <?php echo $role === 'admin' ? "Monitor" : "View"; ?>
                            </a>
                            <button class="btn btn-secondary project-info-btn" data-project="weather-tracker">Info</button>
                        </div>
                    </div>
                </div>
                <div class="project-content">
                    <div class="project-category">Real-time System • API Integration</div>
                    <h3 class="project-title">Weather Tracker</h3>
                    <p class="project-description">
                        Real-time tropical cyclone monitoring system with live storm tracking and meteorological data using KnackWx ATCF API.
                    </p>
                    <div class="project-tech">
                        <span class="tech-tag">API Integration</span>
                        <span class="tech-tag">Real-time Data</span>
                        <span class="tech-tag">Interactive Maps</span>
                        <span class="tech-tag">Weather Data</span>
                    </div>
                    <div class="project-features">
                        <span class="feature">🌍 Live Tracking</span>
                        <span class="feature">📡 API Integration</span>
                        <span class="feature">🗺️ Interactive Maps</span>
                    </div>
                </div>
            </div>

            <!-- Project 3: System Monitor -->
            <div class="project-card" data-category="monitoring tools">
                <div class="project-image">
                    <div class="project-placeholder">
                        <span class="project-icon">📊</span>
                    </div>
                    <div class="project-overlay">
                        <div class="project-actions">
                            <a href="sys_monitor.php" class="btn btn-primary">
                                <?php echo $role === 'admin' ? "Monitor" : "View"; ?>
                            </a>
                            <button class="btn btn-secondary project-info-btn" data-project="system-monitor">Info</button>
                        </div>
                    </div>
                </div>
                <div class="project-content">
                    <div class="project-category">System Monitoring • Analytics</div>
                    <h3 class="project-title">System Monitor Dashboard</h3>
                    <p class="project-description">
                        Advanced system resource monitoring with live CPU, memory, and disk usage graphs, alerts, and performance analytics.
                    </p>
                    <div class="project-tech">
                        <span class="tech-tag">PHP</span>
                        <span class="tech-tag">Charts.js</span>
                        <span class="tech-tag">Real-time</span>
                        <span class="tech-tag">System Metrics</span>
                    </div>
                    <div class="project-features">
                        <span class="feature">📈 Live Charts</span>
                        <span class="feature">⚠️ Alerts</span>
                        <span class="feature">📊 Analytics</span>
                    </div>
                </div>
            </div>

            <!-- Project 4: Anime Gallery -->
            <div class="project-card" data-category="creative web-apps">
                <div class="project-image">
                    <div class="project-placeholder">
                        <span class="project-icon">🎨</span>
                    </div>
                    <div class="project-overlay">
                        <div class="project-actions">
                            <a href="anime_gallery.php" class="btn btn-primary">
                                <?php echo $role === 'admin' ? "Manage" : "Browse"; ?>
                            </a>
                            <button class="btn btn-secondary project-info-btn" data-project="anime-gallery">Info</button>
                        </div>
                    </div>
                </div>
                <div class="project-content">
                    <div class="project-category">Creative Gallery • Media</div>
                    <h3 class="project-title">Anime Art Gallery</h3>
                    <p class="project-description">
                        Creative web gallery showcasing AI-generated anime artwork with advanced filtering, search functionality, and high-resolution downloads.
                    </p>
                    <div class="project-tech">
                        <span class="tech-tag">Image Gallery</span>
                        <span class="tech-tag">Search & Filter</span>
                        <span class="tech-tag">Responsive Design</span>
                        <span class="tech-tag">Media Management</span>
                    </div>
                    <div class="project-features">
                        <span class="feature">🖼️ High-res Images</span>
                        <span class="feature">🔍 Advanced Search</span>
                        <span class="feature">📱 Mobile Friendly</span>
                    </div>
                </div>
            </div>

            <!-- Project 5: Media Tools -->
            <div class="project-card" data-category="tools">
                <div class="project-image">
                    <div class="project-placeholder">
                        <span class="project-icon">🛠️</span>
                    </div>
                    <div class="project-overlay">
                        <div class="project-actions">
                            <a href="media_tools.php" class="btn btn-primary">
                                <?php echo $role === 'admin' ? "Manage" : "Use Tools"; ?>
                            </a>
                            <button class="btn btn-secondary project-info-btn" data-project="media-tools">Info</button>
                        </div>
                    </div>
                </div>
                <div class="project-content">
                    <div class="project-category">Utility Tools • Productivity</div>
                    <h3 class="project-title">Media Tools Suite</h3>
                    <p class="project-description">
                        Collection of practical web utilities including digital clock, calendar widget, unit converter, and productivity tools for daily use.
                    </p>
                    <div class="project-tech">
                        <span class="tech-tag">JavaScript</span>
                        <span class="tech-tag">CSS3</span>
                        <span class="tech-tag">Utilities</span>
                        <span class="tech-tag">Widgets</span>
                    </div>
                    <div class="project-features">
                        <span class="feature">⏰ Digital Clock</span>
                        <span class="feature">📅 Calendar Widget</span>
                        <span class="feature">🔢 Unit Converter</span>
                    </div>
                </div>
            </div>

            <!-- Project 6: Portfolio CMS -->
            <div class="project-card" data-category="web-apps tools">
                <div class="project-image">
                    <div class="project-placeholder">
                        <span class="project-icon">⚙️</span>
                    </div>
                    <div class="project-overlay">
                        <div class="project-actions">
                            <a href="cms_builder.php" class="btn btn-primary">
                                <?php echo $role === 'admin' ? "Manage" : "View CMS"; ?>
                            </a>
                            <button class="btn btn-secondary project-info-btn" data-project="portfolio-cms">Info</button>
                        </div>
                    </div>
                </div>
                <div class="project-content">
                    <div class="project-category">Content Management • CMS</div>
                    <h3 class="project-title">Portfolio CMS Builder</h3>
                    <p class="project-description">
                        Custom content management system for dynamic project editing, image uploads, content publishing, and portfolio maintenance.
                    </p>
                    <div class="project-tech">
                        <span class="tech-tag">CMS</span>
                        <span class="tech-tag">File Upload</span>
                        <span class="tech-tag">Admin Panel</span>
                        <span class="tech-tag">Content Management</span>
                    </div>
                    <div class="project-features">
                        <span class="feature">📝 Content Editor</span>
                        <span class="feature">📁 File Management</span>
                        <span class="feature">👤 User Management</span>
                    </div>
                </div>
            </div>

            <!-- Project 7: Tornado Database -->
            <div class="project-card" data-category="web-apps databases">
                <div class="project-image">
                    <div class="project-placeholder">
                        <span class="project-icon">🌪️</span>
                    </div>
                    <div class="project-overlay">
                        <div class="project-actions">
                            <a href="tornado_db.php" class="btn btn-primary">
                                <?php echo $role === 'admin' ? "Manage" : "View"; ?>
                            </a>
                            <button class="btn btn-secondary project-info-btn" data-project="tornado-database">Info</button>
                        </div>
                    </div>
                </div>
                <div class="project-content">
                    <div class="project-category">Web Application • Database</div>
                    <h3 class="project-title">Tornado Database</h3>
                    <p class="project-description">
                        Comprehensive database for storing, tracking, and analyzing tornado records with data visualization and meteorological insights.
                    </p>
                    <div class="project-tech">
                        <span class="tech-tag">PHP</span>
                        <span class="tech-tag">MySQL</span>
                        <span class="tech-tag">Data Visualization</span>
                        <span class="tech-tag">Analytics</span>
                    </div>
                    <div class="project-features">
                        <span class="feature">🌪️ Tornado Records</span>
                        <span class="feature">📊 Data Charts</span>
                        <span class="feature">🔍 Search & Filter</span>
                    </div>
                </div>
            </div>

            <!-- Project 8: Research Papers Archive -->
            <div class="project-card" data-category="tools databases">
                <div class="project-image">
                    <div class="project-placeholder">
                        <span class="project-icon">📚</span>
                    </div>
                    <div class="project-overlay">
                        <div class="project-actions">
                            <a href="research_papers.php" class="btn btn-primary">
                                <?php echo $role === 'admin' ? "Manage" : "Browse"; ?>
                            </a>
                            <button class="btn btn-secondary project-info-btn" data-project="research-papers">Info</button>
                        </div>
                    </div>
                </div>
                <div class="project-content">
                    <div class="project-category">Research • Documentation</div>
                    <h3 class="project-title">Research Papers Archive</h3>
                    <p class="project-description">
                        Centralized repository for uploading, organizing, and reading meteorological and scientific research papers with keyword search and filters.
                    </p>
                    <div class="project-tech">
                        <span class="tech-tag">PHP</span>
                        <span class="tech-tag">File Upload</span>
                        <span class="tech-tag">Search Engine</span>
                        <span class="tech-tag">Database</span>
                    </div>
                    <div class="project-features">
                        <span class="feature">📄 Paper Uploads</span>
                        <span class="feature">🔎 Keyword Search</span>
                        <span class="feature">📚 Tag-based Filters</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Project Details Modal -->
<div class="modal" id="projectModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Project Details</h2>
            <span class="modal-close">&times;</span>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Project details will be loaded here -->
        </div>
    </div>
</div>

<!-- Additional CSS for Projects Page -->
<style>
    .projects-hero {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        padding: var(--space-20) 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .projects-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: float 25s ease-in-out infinite;
    }

    .projects-hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-title {
        font-size: var(--font-size-5xl);
        font-weight: 700;
        margin-bottom: var(--space-4);
    }

    .hero-subtitle {
        font-size: var(--font-size-xl);
        opacity: 0.9;
        margin-bottom: var(--space-8);
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }

    .hero-stats {
        display: flex;
        justify-content: center;
        gap: var(--space-8);
        flex-wrap: wrap;
    }

    .stat {
        text-align: center;
    }

    .stat-number {
        display: block;
        font-size: var(--font-size-3xl);
        font-weight: 700;
        margin-bottom: var(--space-2);
    }

    .stat-label {
        font-size: var(--font-size-lg);
        opacity: 0.8;
    }

    .project-filter {
        padding: var(--space-8) 0;
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .filter-buttons {
        display: flex;
        justify-content: center;
        gap: var(--space-4);
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: var(--space-3) var(--space-6);
        background: var(--bg-primary);
        border: 2px solid var(--border-color);
        border-radius: var(--radius-full);
        color: var(--text-primary);
        font-weight: 500;
        cursor: pointer;
        transition: all var(--transition-fast);
        position: relative;
        overflow: hidden;
    }

    .filter-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.1), transparent);
        transition: left 0.5s;
    }

    .filter-btn:hover::before {
        left: 100%;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        transform: translateY(-2px);
    }

    .projects-grid-section {
        padding: var(--space-16) 0;
    }

    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: var(--space-8);
    }

    .project-card {
        background: var(--bg-primary);
        border-radius: var(--radius-xl);
        overflow: hidden;
        box-shadow: var(--shadow-light);
        transition: all var(--transition-normal);
        border: 1px solid var(--border-color);
        opacity: 1;
        transform: scale(1);
    }

    .project-card.hidden {
        opacity: 0;
        transform: scale(0.8);
        pointer-events: none;
    }

    .project-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-large);
        border-color: var(--primary-color);
    }

    .project-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .project-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .project-placeholder::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 30% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        animation: float 15s ease-in-out infinite;
    }

    .project-icon {
        font-size: var(--font-size-5xl);
        z-index: 2;
        color: white;
    }

    .project-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity var(--transition-normal);
    }

    .project-card:hover .project-overlay {
        opacity: 1;
    }

    .project-actions {
        display: flex;
        gap: var(--space-3);
    }

    .project-content {
        padding: var(--space-6);
    }

    .project-category {
        font-size: var(--font-size-sm);
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: var(--space-3);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .project-title {
        font-size: var(--font-size-xl);
        font-weight: 700;
        margin-bottom: var(--space-3);
        color: var(--text-primary);
    }

    .project-description {
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: var(--space-4);
    }

    .project-tech {
        display: flex;
        flex-wrap: wrap;
        gap: var(--space-2);
        margin-bottom: var(--space-4);
    }

    .tech-tag {
        background: var(--bg-tertiary);
        color: var(--text-primary);
        padding: var(--space-1) var(--space-3);
        border-radius: var(--radius-full);
        font-size: var(--font-size-xs);
        font-weight: 600;
        border: 1px solid var(--border-color);
    }

    .project-features {
        display: flex;
        flex-direction: column;
        gap: var(--space-2);
    }

    .feature {
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.8);
        z-index: var(--z-modal);
        align-items: center;
        justify-content: center;
        padding: var(--space-4);
    }

    .modal.active {
        display: flex;
        animation: fadeIn var(--transition-normal) ease-out;
    }

    .modal-content {
        background: var(--bg-primary);
        border-radius: var(--radius-xl);
        padding: var(--space-8);
        max-width: 800px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: var(--shadow-xl);
        animation: slideUp var(--transition-normal) ease-out;
        border: 1px solid var(--border-color);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--space-6);
        padding-bottom: var(--space-4);
        border-bottom: 1px solid var(--border-color);
    }

    .modal-header h2 {
        color: var(--text-primary);
        margin: 0;
    }

    .modal-close {
        font-size: var(--font-size-2xl);
        cursor: pointer;
        color: var(--text-muted);
        transition: color var(--transition-fast);
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius-md);
    }

    .modal-close:hover {
        color: var(--danger-color);
        background: var(--bg-tertiary);
    }

    .modal-body {
        color: var(--text-secondary);
        line-height: 1.7;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-stats {
            gap: var(--space-6);
        }

        .filter-buttons {
            gap: var(--space-2);
        }

        .filter-btn {
            padding: var(--space-2) var(--space-4);
            font-size: var(--font-size-sm);
        }

        .projects-grid {
            grid-template-columns: 1fr;
            gap: var(--space-6);
        }

        .project-actions {
            flex-direction: column;
        }

        .modal-content {
            padding: var(--space-6);
            margin: var(--space-4);
        }
    }

    @media (max-width: 480px) {
        .hero-title {
            font-size: var(--font-size-3xl);
        }

        .hero-stats {
            flex-direction: column;
            gap: var(--space-4);
        }

        .filter-buttons {
            flex-direction: column;
            align-items: center;
        }

        .filter-btn {
            width: 200px;
        }
    }
</style>

<!-- JavaScript for Projects Page -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Project filtering
        const filterButtons = document.querySelectorAll('.filter-btn');
        const projectCards = document.querySelectorAll('.project-card');

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');

                // Update active button
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Filter projects
                projectCards.forEach(card => {
                    const categories = card.getAttribute('data-category');
                    if (filter === 'all' || categories.includes(filter)) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });
            });
        });

        // Project info modals
        const projectInfoButtons = document.querySelectorAll('.project-info-btn');
        const modal = document.getElementById('projectModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        const modalClose = document.querySelector('.modal-close');

        // Project details data
        const projectDetails = {
            'tc-database': {
                title: 'Tropical Cyclone Database',
                content: `
                <h3>Overview</h3>
                <p>This comprehensive database management system was designed to archive and analyze tropical cyclone records with advanced filtering and data visualization capabilities.</p>
                
                <h3>Key Features</h3>
                <ul>
                    <li>Advanced data filtering and search functionality</li>
                    <li>Interactive data visualization with charts and graphs</li>
                    <li>Comprehensive cyclone record management</li>
                    <li>Export capabilities for data analysis</li>
                    <li>Responsive design for all devices</li>
                    <li>Admin panel for data management</li>
                </ul>
                
                <h3>Technologies Used</h3>
                <p><strong>Backend:</strong> PHP, MySQL<br>
                <strong>Frontend:</strong> HTML5, CSS3, JavaScript<br>
                <strong>Features:</strong> Data visualization, Search & filter, Admin controls</p>
                
                <h3>Project Impact</h3>
                <p>This system provides meteorologists and researchers with an efficient tool for managing and analyzing tropical cyclone data, improving data accessibility and research capabilities.</p>
            `
            },
            'weather-tracker': {
                title: 'Weather Tracker',
                content: `
                <h3>Overview</h3>
                <p>A real-time tropical cyclone monitoring system that provides live storm tracking and meteorological data using the KnackWx ATCF API.</p>
                
                <h3>Key Features</h3>
                <ul>
                    <li>Real-time storm tracking and monitoring</li>
                    <li>Interactive maps with storm paths</li>
                    <li>Detailed meteorological data display</li>
                    <li>API integration with KnackWx ATCF</li>
                    <li>Historical storm data access</li>
                    <li>Alert system for significant storms</li>
                </ul>
                
                <h3>Technologies Used</h3>
                <p><strong>API Integration:</strong> KnackWx ATCF API<br>
                <strong>Frontend:</strong> HTML5, CSS3, JavaScript<br>
                <strong>Features:</strong> Real-time data, Interactive maps, Weather data</p>
                
                <h3>Project Impact</h3>
                <p>Provides accurate and timely information about tropical cyclones, helping meteorologists and the public stay informed about weather conditions and potential threats.</p>
            `
            },
            'system-monitor': {
                title: 'System Monitor Dashboard',
                content: `
                <h3>Overview</h3>
                <p>An advanced system resource monitoring dashboard with live CPU, memory, and disk usage graphs, alerts, and performance analytics.</p>
                
                <h3>Key Features</h3>
                <ul>
                    <li>Live system resource monitoring</li>
                    <li>Interactive charts and graphs</li>
                    <li>Performance analytics and trends</li>
                    <li>Alert system for resource thresholds</li>
                    <li>Historical data tracking</li>
                    <li>Real-time updates</li>
                </ul>
                
                <h3>Technologies Used</h3>
                <p><strong>Backend:</strong> PHP<br>
                <strong>Frontend:</strong> Charts.js, HTML5, CSS3<br>
                <strong>Features:</strong> Real-time monitoring, Data visualization, System metrics</p>
                
                <h3>Project Impact</h3>
                <p>Enables system administrators to monitor server performance in real-time, identify potential issues early, and optimize system resources effectively.</p>
            `
            },
            'anime-gallery': {
                title: 'Anime Art Gallery',
                content: `
                <h3>Overview</h3>
                <p>A creative web gallery showcasing AI-generated anime artwork with advanced filtering, search functionality, and high-resolution downloads.</p>
                
                <h3>Key Features</h3>
                <ul>
                    <li>High-resolution image gallery</li>
                    <li>Advanced search and filtering</li>
                    <li>Category-based organization</li>
                    <li>Download functionality</li>
                    <li>Responsive design</li>
                    <li>Image optimization</li>
                </ul>
                
                <h3>Technologies Used</h3>
                <p><strong>Backend:</strong> PHP<br>
                <strong>Frontend:</strong> HTML5, CSS3, JavaScript<br>
                <strong>Features:</strong> Image gallery, Search & filter, Media management</p>
                
                <h3>Project Impact</h3>
                <p>Provides a platform for showcasing creative artwork with an intuitive user experience, making it easy for users to browse and discover content.</p>
            `
            },
            'media-tools': {
                title: 'Media Tools Suite',
                content: `
                <h3>Overview</h3>
                <p>A collection of practical web utilities including digital clock, calendar widget, unit converter, and productivity tools for daily use.</p>
                
                <h3>Key Features</h3>
                <ul>
                    <li>Digital clock with timezone support</li>
                    <li>Interactive calendar widget</li>
                    <li>Unit conversion tools</li>
                    <li>Productivity utilities</li>
                    <li>Responsive design</li>
                    <li>Easy-to-use interface</li>
                </ul>
                
                <h3>Technologies Used</h3>
                <p><strong>Frontend:</strong> JavaScript, CSS3, HTML5<br>
                <strong>Features:</strong> Utility tools, Widgets, Productivity tools</p>
                
                <h3>Project Impact</h3>
                <p>Provides users with essential tools for daily productivity, combining multiple utilities in one convenient location with a clean, intuitive interface.</p>
            `
            },
            'portfolio-cms': {
                title: 'Portfolio CMS Builder',
                content: `
                <h3>Overview</h3>
                <p>A custom content management system for dynamic project editing, image uploads, content publishing, and portfolio maintenance.</p>
                
                <h3>Key Features</h3>
                <ul>
                    <li>Dynamic content editing</li>
                    <li>Image and file upload management</li>
                    <li>User authentication and roles</li>
                    <li>Content publishing system</li>
                    <li>Admin dashboard</li>
                    <li>Database management</li>
                </ul>
                
                <h3>Technologies Used</h3>
                <p><strong>Backend:</strong> PHP, MySQL<br>
                <strong>Frontend:</strong> HTML5, CSS3, JavaScript<br>
                <strong>Features:</strong> CMS, File management, Admin panel</p>
                
                <h3>Project Impact</h3>
                <p>Enables easy management of portfolio content without technical knowledge, providing a streamlined workflow for content creators and administrators.</p>
            `
            },

            'tornado-database': {
                title: 'Tornado Database',
                content: `
        <h3>Overview</h3>
        <p>This project is a comprehensive database designed to manage tornado event data, allowing meteorologists and researchers to analyze historical patterns, frequency, and severity of tornadoes.</p>

        <h3>Key Features</h3>
        <ul>
            <li>Detailed tornado records with customizable filters</li>
            <li>Interactive data visualization using charts and graphs</li>
            <li>Import and export of datasets (CSV, JSON)</li>
            <li>Search by date, location, or intensity</li>
            <li>Responsive interface with admin management tools</li>
        </ul>

        <h3>Technologies Used</h3>
        <p><strong>Backend:</strong> PHP, MySQL<br>
        <strong>Frontend:</strong> HTML5, CSS3, JavaScript<br>
        <strong>Features:</strong> Data visualization, Filtering, Analytics, Admin controls</p>

        <h3>Project Impact</h3>
        <p>Supports meteorological research by providing structured access to tornado data and visualization tools for analysis and trend discovery.</p>
    `
            },
            'research-papers': {
                title: 'Research Papers Archive',
                content: `
        <h3>Overview</h3>
        <p>A digital archive for storing, organizing, and browsing scientific and meteorological research papers. Designed to streamline document management for researchers and students.</p>

        <h3>Key Features</h3>
        <ul>
            <li>Upload and manage PDF research papers</li>
            <li>Advanced keyword search and tag filtering</li>
            <li>Categorization by topic, author, or year</li>
            <li>Admin moderation tools for approval and organization</li>
            <li>Responsive design and document viewer integration</li>
        </ul>

        <h3>Technologies Used</h3>
        <p><strong>Backend:</strong> PHP, MySQL<br>
        <strong>Frontend:</strong> HTML5, CSS3, JavaScript<br>
        <strong>Features:</strong> File upload, Search engine, Tag filters, Document management</p>

        <h3>Project Impact</h3>
        <p>This system simplifies the process of storing and discovering research materials, making collaboration and learning more efficient.</p>
    `
            }
        };

        projectInfoButtons.forEach(button => {
            button.addEventListener('click', function() {
                const projectId = this.getAttribute('data-project');
                const project = projectDetails[projectId];

                if (project) {
                    modalTitle.textContent = project.title;
                    modalBody.innerHTML = project.content;
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            });
        });

        // Close modal
        modalClose.addEventListener('click', function() {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        });

        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Animate project cards on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        projectCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });
    });
</script>

<?php include("includes/footer.php"); ?>