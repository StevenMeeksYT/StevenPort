<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
$username = $_SESSION['username'] ?? $_COOKIE['username'] ?? null;
$isAdmin = ($_SESSION['role'] ?? '') === 'admin' || ($_COOKIE['role'] ?? '') === 'admin';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StevenPort</title>
    <script>
        // Set theme early to avoid flash-of-incorrect-theme (reads from localStorage or prefers-color-scheme)
        (function(){
            try{
                var t = localStorage.getItem('theme');
                if(!t){
                    if(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) t = 'dark';
                    else t = 'light';
                }
                document.documentElement.setAttribute('data-theme', t);
            }catch(e){}
        })();
    </script>
    <link rel="stylesheet" href="master.css">
    <link rel="stylesheet" href="bf2042.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <script src="futuristic.js" defer></script>
    <script src="master.js" defer></script>
    <style>
        /* Responsive table & image helpers applied site-wide */
        img { max-width: 100%; height: auto; }
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        .styled-table th, .styled-table td { padding: 0.5rem; border: 1px solid var(--border-color, #ddd); }

        /* Collapse tables into stacked blocks on small screens for improved readability */
        @media (max-width: 800px) {
            table.responsive, table.styled-table, table { display: block; }
            table thead { display: none; }
            table tbody { display: block; }
            table tr { display: block; margin-bottom: 0.75rem; border-bottom: 1px solid rgba(0,0,0,0.05); }
            table td { display: block; text-align: left; white-space: normal; }
            table td::before { content: attr(data-label); font-weight: 600; display: inline-block; width: 45%; }
        }
        /* Responsive buttons inside shared containers */
        .button-container { display:flex; gap:.5rem; flex-wrap:wrap; }
        .button-container .btn { min-width:120px; }
        .modal-actions { display:flex; gap:.5rem; flex-wrap:wrap; }
        @media (max-width: 800px) {
            .button-container .btn, .modal-actions .btn { width:100%; }
        }
        /* Ensure theme toggle is visible and interactive on all screen sizes */
        .theme-toggle { display: inline-flex !important; align-items: center; justify-content: center; min-width:44px; }
        .desktop-nav .theme-toggle { order: 999; margin-left: .5rem; pointer-events: auto !important; z-index: 9999 !important; }
        .mobile-nav .theme-toggle { display:flex; margin-bottom: .5rem; }
        /* Make sure the theme toggle shows clearly on narrow/wide screens */
        @media (min-width: 1024px) {
            .desktop-nav .theme-toggle { margin-left: var(--space-4); }
        }
        
    </style>
</head>

<body>
    <!-- Enhanced Header with Mobile Navigation -->
    <header class="topbar fade-in">
        <div class="container">
            <a href="index.php" class="brand">
                <span class="brand-icon">⚡</span>
                StevenPort
            </a>

            <!-- Desktop Navigation -->
            <nav class="desktop-nav">
                <a href="index.php" class="nav-link">Home</a>
                <a href="about.php" class="nav-link">About</a>
                <a href="projects.php" class="nav-link">Projects</a>
                <a href="contact.php" class="nav-link">Contact</a>
                <a href="StevenFJCombineFull.pdf" class="btn btn-secondary" target="_blank" rel="noopener" title="Review Portfolio">Review</a>
                <a href="StevenFJCombineFull.pdf" class="btn btn-primary" download title="Download Portfolio">Download</a>

                <?php if ($username): ?>
                    <a href="profile.php" class="nav-link user-link"><?php echo htmlspecialchars($username); ?></a>
                    <a href="logout.php" class="nav-link logout-link">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link login-link">Login</a>
                    <a href="register.php" class="nav-link register-link">Register</a>
                <?php endif; ?>

                <!-- Desktop Theme Toggle (visible on larger screens). Uses data-theme-toggle so master.js keeps it in sync with mobile toggle -->
                <button id="themeToggleDesktop" class="btn theme-toggle" type="button" data-theme-toggle aria-pressed="false" aria-label="Toggle theme" title="Toggle Theme" onclick="(function(){ if(window.MasterUI && typeof MasterUI.toggleTheme==='function'){ MasterUI.toggleTheme(); } else { var r=document.documentElement; var next = r.getAttribute('data-theme')==='dark'?'light':'dark'; r.setAttribute('data-theme', next); localStorage.setItem('theme', next); var icon = next==='dark'?'🌞':'🌙'; document.querySelectorAll('[data-theme-toggle]').forEach(function(b){ try{ b.textContent = icon; b.setAttribute('aria-pressed', next==='dark'?'true':'false'); }catch(e){} }); } })();">🌙</button>
            </nav>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <span class="hamburger"></span>
                <span class="hamburger"></span>
                <span class="hamburger"></span>
            </button>
            
            <!-- Theme Toggle (visible on all screen sizes) -->
            <!-- <button id="themeToggle" class="btn theme-toggle" type="button" data-theme-toggle aria-pressed="false" aria-label="Toggle theme" title="Toggle Theme">🌙</button> -->
        </div>

        <!-- Mobile Navigation -->
        <nav class="mobile-nav" id="mobileNav">
            <!-- 🌙 Mobile Theme Toggle Button -->
            <button id="themeToggle" class="btn theme-toggle" type="button" data-theme-toggle aria-pressed="false" aria-label="Toggle theme" title="Toggle Theme">🌙</button>
            <a href="index.php" class="mobile-nav-link">Home</a>
            <a href="about.php" class="mobile-nav-link">About</a>
            <a href="projects.php" class="mobile-nav-link">Projects</a>
            <a href="contact.php" class="mobile-nav-link">Contact</a>
            <a href="StevenFJCombineFull.pdf" class="mobile-nav-link" target="_blank" rel="noopener">Review Portfolio</a>
            <a href="StevenFJCombineFull.pdf" class="mobile-nav-link" download>Download Portfolio</a>

            <?php if ($username): ?>
                <a href="profile.php" class="mobile-nav-link"><?php echo htmlspecialchars($username); ?></a>
                <a href="logout.php" class="mobile-nav-link">Logout</a>
            <?php else: ?>
                <a href="login.php" class="mobile-nav-link">Login</a>
                <a href="register.php" class="mobile-nav-link">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Main Content Wrapper -->
    <main class="main-content">

        <!-- Theme toggle is managed centrally by master.js (no inline script here) -->

        <script>
            // Accessibility helper: add data-label attributes to table cells so the responsive CSS can show headers
            document.addEventListener('DOMContentLoaded', function () {
                function labelTables() {
                    const tables = Array.from(document.querySelectorAll('table.styled-table, table.responsive, table'));
                    tables.forEach(table => {
                        const thead = table.querySelector('thead');
                        if (!thead) return;
                        const headers = Array.from(thead.querySelectorAll('th')).map(th => th.innerText.trim());
                        const rows = table.querySelectorAll('tbody tr');
                        rows.forEach(row => {
                            const cells = Array.from(row.children).filter(n => n.tagName.toLowerCase() === 'td');
                            cells.forEach((td, idx) => {
                                if (!td.hasAttribute('data-label')) {
                                    td.setAttribute('data-label', headers[idx] || '');
                                }
                            });
                        });
                    });
                }

                // Run on initial load and after a short delay for pages that render tables dynamically
                labelTables();
                setTimeout(labelTables, 500);
            });
        </script>
