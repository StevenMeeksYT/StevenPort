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

<!-- About Hero Section -->
<section class="about-hero">
    <div class="container">
        <div class="about-hero-content">
            <div class="profile-section">
                <div class="profile-image">
                    <div class="profile-placeholder">
                        <span class="profile-icon">👨‍💻</span>
                    </div>
                </div>
                <div class="profile-info">
                    <h1 class="profile-name">Ng Yi Thern</h1>
                    <h2 class="profile-title">Full-Stack Developer & Creative Problem Solver</h2>
                    <p class="profile-location">📍 Based in Malaysia</p>
                    <div class="profile-social">
                        <a href="#" class="social-link">📱 GitHub</a>
                        <a href="#" class="social-link">💼 LinkedIn</a>
                        <a href="#" class="social-link">📧 Email</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Content Section -->
<section class="about-content">
    <div class="container">
        <div class="about-grid">
            <div class="about-text">
                <h2>About Me</h2>
                <p>
                    I'm a passionate full-stack developer with a love for creating innovative web solutions 
                    and user-friendly applications. With expertise in PHP, HTML, CSS, JavaScript, and SQL, 
                    I specialize in building robust web systems that solve real-world problems.
                </p>
                <p>
                    My journey in web development began with a curiosity about how websites work, and it 
                    has evolved into a career focused on creating efficient, scalable, and beautiful 
                    digital experiences. I believe in clean code, user-centered design, and continuous learning.
                </p>
                <p>
                    When I'm not coding, you'll find me exploring new technologies, contributing to open-source 
                    projects, or working on creative side projects that combine my technical skills with my 
                    artistic interests.
                </p>
            </div>
            
            <div class="about-stats">
                <div class="stat-item">
                    <div class="stat-number">3+</div>
                    <div class="stat-label">Years Experience</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Projects Completed</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">5+</div>
                    <div class="stat-label">Technologies</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Client Satisfaction</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Skills Section -->
<section class="skills-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Technical Skills</h2>
            <p class="section-subtitle">Technologies and tools I work with</p>
        </div>
        
        <div class="skills-grid">
            <div class="skill-category">
                <h3>Frontend Development</h3>
                <div class="skills-list">
                    <div class="skill-item">
                        <span class="skill-name">HTML5</span>
                        <div class="skill-bar">
                            <div class="skill-progress" data-width="95%"></div>
                        </div>
                        <span class="skill-percentage">95%</span>
                    </div>
                    <div class="skill-item">
                        <span class="skill-name">CSS3</span>
                        <div class="skill-bar">
                            <div class="skill-progress" data-width="90%"></div>
                        </div>
                        <span class="skill-percentage">90%</span>
                    </div>
                    <div class="skill-item">
                        <span class="skill-name">JavaScript</span>
                        <div class="skill-bar">
                            <div class="skill-progress" data-width="85%"></div>
                        </div>
                        <span class="skill-percentage">85%</span>
                    </div>
                    <div class="skill-item">
                        <span class="skill-name">Responsive Design</span>
                        <div class="skill-bar">
                            <div class="skill-progress" data-width="92%"></div>
                        </div>
                        <span class="skill-percentage">92%</span>
                    </div>
                </div>
            </div>
            
            <div class="skill-category">
                <h3>Backend Development</h3>
                <div class="skills-list">
                    <div class="skill-item">
                        <span class="skill-name">PHP</span>
                        <div class="skill-bar">
                            <div class="skill-progress" data-width="88%"></div>
                        </div>
                        <span class="skill-percentage">88%</span>
                    </div>
                    <div class="skill-item">
                        <span class="skill-name">MySQL</span>
                        <div class="skill-bar">
                            <div class="skill-progress" data-width="85%"></div>
                        </div>
                        <span class="skill-percentage">85%</span>
                    </div>
                    <div class="skill-item">
                        <span class="skill-name">Database Design</span>
                        <div class="skill-bar">
                            <div class="skill-progress" data-width="82%"></div>
                        </div>
                        <span class="skill-percentage">82%</span>
                    </div>
                    <div class="skill-item">
                        <span class="skill-name">API Development</span>
                        <div class="skill-bar">
                            <div class="skill-progress" data-width="80%"></div>
                        </div>
                        <span class="skill-percentage">80%</span>
                    </div>
                </div>
            </div>
            
            <div class="skill-category">
                <h3>Tools & Technologies</h3>
                <div class="tools-grid">
                    <div class="tool-item">
                        <div class="tool-icon">🛠️</div>
                        <span class="tool-name">Git</span>
                    </div>
                    <div class="tool-item">
                        <div class="tool-icon">📊</div>
                        <span class="tool-name">Charts.js</span>
                    </div>
                    <div class="tool-item">
                        <div class="tool-icon">🎨</div>
                        <span class="tool-name">Adobe Creative</span>
                    </div>
                    <div class="tool-item">
                        <div class="tool-icon">⚡</div>
                        <span class="tool-name">Performance Optimization</span>
                    </div>
                    <div class="tool-item">
                        <div class="tool-icon">🔒</div>
                        <span class="tool-name">Security</span>
                    </div>
                    <div class="tool-item">
                        <div class="tool-icon">📱</div>
                        <span class="tool-name">Mobile Development</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Experience Section -->
<section class="experience-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Experience & Education</h2>
            <p class="section-subtitle">My professional journey and achievements</p>
        </div>
        
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <div class="timeline-date">2023 - Present</div>
                    <h3>Full-Stack Developer</h3>
                    <h4>Freelance & Personal Projects</h4>
                    <p>
                        Developing web applications and systems for various clients, focusing on 
                        tropical cyclone tracking, database management, and creative tools.
                    </p>
                    <ul>
                        <li>Built comprehensive database management systems</li>
                        <li>Developed real-time weather tracking applications</li>
                        <li>Created interactive galleries and media tools</li>
                        <li>Implemented responsive design principles</li>
                    </ul>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <div class="timeline-date">2022 - 2023</div>
                    <h3>Web Development Learning</h3>
                    <h4>Self-Directed Study</h4>
                    <p>
                        Intensive self-study in web development technologies, building personal 
                        projects and contributing to open-source communities.
                    </p>
                    <ul>
                        <li>Mastered PHP and MySQL for backend development</li>
                        <li>Learned modern JavaScript and CSS techniques</li>
                        <li>Studied database design and optimization</li>
                        <li>Explored API integration and development</li>
                    </ul>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <div class="timeline-date">2021 - 2022</div>
                    <h3>Computer Science Studies</h3>
                    <h4>Higher Education</h4>
                    <p>
                        Foundation in computer science principles, programming fundamentals, 
                        and software development methodologies.
                    </p>
                    <ul>
                        <li>Programming fundamentals and algorithms</li>
                        <li>Database management systems</li>
                        <li>Software engineering principles</li>
                        <li>Computer networks and security</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Certifications Section -->
<section class="certifications-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Certifications & Achievements</h2>
            <p class="section-subtitle">Professional certifications and notable achievements</p>
        </div>
        
        <div class="certifications-grid">
            <div class="cert-card">
                <div class="cert-icon">🏆</div>
                <h3>CompTIA A+ Certification</h3>
                <p>Computer hardware and software support technician certification</p>
                <div class="cert-date">2023</div>
            </div>
            
            <div class="cert-card">
                <div class="cert-icon">🔒</div>
                <h3>CompTIA Security+</h3>
                <p>Information security certification covering cybersecurity fundamentals</p>
                <div class="cert-date">2023</div>
            </div>
            
            <div class="cert-card">
                <div class="cert-icon">🌐</div>
                <h3>CCNA (Part 1 & 2)</h3>
                <p>Cisco Certified Network Associate - networking fundamentals</p>
                <div class="cert-date">2022</div>
            </div>
            
            <div class="cert-card">
                <div class="cert-icon">📚</div>
                <h3>Google Analytics Academy</h3>
                <p>Web analytics and digital marketing measurement certification</p>
                <div class="cert-date">2023</div>
            </div>
            
            <div class="cert-card">
                <div class="cert-icon">🎓</div>
                <h3>eLATiH Learning</h3>
                <p>Various online courses in web development and digital skills</p>
                <div class="cert-date">2022-2023</div>
            </div>
            
            <div class="cert-card">
                <div class="cert-icon">📐</div>
                <h3>AutoCAD Layouts</h3>
                <p>Computer-aided design and technical drawing certification</p>
                <div class="cert-date">2022</div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Let's Work Together</h2>
            <p>
                I'm always interested in new opportunities and exciting projects. 
                Whether you need a web application, database system, or creative solution, 
                I'm ready to help bring your ideas to life.
            </p>
            <div class="cta-actions">
                <a href="contact.php" class="btn btn-primary">Get In Touch</a>
                <a href="index.php" class="btn btn-outline">View My Work</a>
            </div>
        </div>
    </div>
</section>

<!-- Additional CSS for About Page -->
<style>
.about-hero {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    color: white;
    padding: var(--space-20) 0;
    position: relative;
    overflow: hidden;
}

.about-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    animation: float 15s ease-in-out infinite;
}

.about-hero-content {
    position: relative;
    z-index: 2;
}

.profile-section {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: var(--space-8);
    align-items: center;
}

.profile-image {
    position: relative;
}

.profile-placeholder {
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    border: 3px solid rgba(255, 255, 255, 0.3);
    position: relative;
    overflow: hidden;
}

.profile-placeholder::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    animation: shimmer 3s infinite;
}

.profile-icon {
    font-size: var(--font-size-5xl);
    z-index: 2;
}

.profile-name {
    font-size: var(--font-size-4xl);
    font-weight: 700;
    margin-bottom: var(--space-2);
}

.profile-title {
    font-size: var(--font-size-xl);
    font-weight: 500;
    margin-bottom: var(--space-4);
    opacity: 0.9;
}

.profile-location {
    font-size: var(--font-size-lg);
    margin-bottom: var(--space-6);
    opacity: 0.8;
}

.profile-social {
    display: flex;
    gap: var(--space-4);
}

.profile-social .social-link {
    color: white;
    text-decoration: none;
    padding: var(--space-2) var(--space-4);
    background: rgba(255, 255, 255, 0.1);
    border-radius: var(--radius-md);
    transition: all var(--transition-fast);
    backdrop-filter: blur(10px);
}

.profile-social .social-link:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    text-decoration: none;
}

.about-content {
    padding: var(--space-16) 0;
}

.about-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: var(--space-12);
    align-items: start;
}

.about-text h2 {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--space-6);
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.about-text p {
    font-size: var(--font-size-lg);
    line-height: 1.8;
    margin-bottom: var(--space-6);
    color: var(--text-secondary);
}

.about-stats {
    display: grid;
    gap: var(--space-4);
}

.stat-item {
    background: var(--bg-secondary);
    padding: var(--space-6);
    border-radius: var(--radius-xl);
    text-align: center;
    border: 1px solid var(--border-color);
    transition: all var(--transition-normal);
}

.stat-item:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-medium);
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

.skills-section {
    padding: var(--space-16) 0;
    background: var(--bg-secondary);
}

.skills-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--space-8);
}

.skill-category {
    background: var(--bg-primary);
    padding: var(--space-6);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-light);
}

.skill-category h3 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--space-6);
    color: var(--primary-color);
}

.skills-list {
    display: flex;
    flex-direction: column;
    gap: var(--space-4);
}

.skill-item {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: var(--space-3);
    align-items: center;
}

.skill-name {
    font-weight: 600;
    color: var(--text-primary);
}

.skill-bar {
    grid-column: 1 / -1;
    height: 8px;
    background: var(--bg-tertiary);
    border-radius: var(--radius-full);
    overflow: hidden;
    margin-top: var(--space-2);
}

.skill-progress {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    border-radius: var(--radius-full);
    width: 0%;
    transition: width 2s ease-in-out;
}

.skill-percentage {
    font-weight: 600;
    color: var(--primary-color);
}

.tools-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-4);
}

.tool-item {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    padding: var(--space-3);
    background: var(--bg-secondary);
    border-radius: var(--radius-md);
    transition: all var(--transition-fast);
}

.tool-item:hover {
    background: var(--bg-tertiary);
    transform: translateY(-2px);
}

.tool-icon {
    font-size: var(--font-size-xl);
}

.tool-name {
    font-weight: 500;
    color: var(--text-primary);
}

.experience-section {
    padding: var(--space-16) 0;
}

.timeline {
    position: relative;
    max-width: 800px;
    margin: 0 auto;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 30px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, var(--primary-color), var(--primary-light));
}

.timeline-item {
    position: relative;
    margin-bottom: var(--space-8);
    padding-left: 80px;
}

.timeline-marker {
    position: absolute;
    left: 22px;
    top: 8px;
    width: 16px;
    height: 16px;
    background: var(--primary-color);
    border-radius: 50%;
    border: 4px solid var(--bg-primary);
    box-shadow: 0 0 0 4px var(--primary-color);
}

.timeline-content {
    background: var(--bg-secondary);
    padding: var(--space-6);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-light);
    transition: all var(--transition-normal);
}

.timeline-content:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-medium);
}

.timeline-date {
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: var(--space-2);
}

.timeline-content h3 {
    font-size: var(--font-size-xl);
    margin-bottom: var(--space-2);
    color: var(--text-primary);
}

.timeline-content h4 {
    font-size: var(--font-size-lg);
    color: var(--text-secondary);
    margin-bottom: var(--space-4);
}

.timeline-content ul {
    margin-top: var(--space-4);
    padding-left: var(--space-6);
}

.timeline-content li {
    margin-bottom: var(--space-2);
    color: var(--text-secondary);
}

.certifications-section {
    padding: var(--space-16) 0;
    background: var(--bg-secondary);
}

.certifications-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--space-6);
}

.cert-card {
    background: var(--bg-primary);
    padding: var(--space-6);
    border-radius: var(--radius-xl);
    text-align: center;
    border: 1px solid var(--border-color);
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
}

.cert-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.1), transparent);
    transition: left 0.5s;
}

.cert-card:hover::before {
    left: 100%;
}

.cert-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-large);
    border-color: var(--primary-color);
}

.cert-icon {
    font-size: var(--font-size-4xl);
    margin-bottom: var(--space-4);
}

.cert-card h3 {
    font-size: var(--font-size-lg);
    margin-bottom: var(--space-3);
    color: var(--text-primary);
}

.cert-card p {
    color: var(--text-secondary);
    margin-bottom: var(--space-4);
    line-height: 1.6;
}

.cert-date {
    background: var(--primary-color);
    color: white;
    padding: var(--space-1) var(--space-3);
    border-radius: var(--radius-full);
    font-size: var(--font-size-sm);
    font-weight: 600;
    display: inline-block;
}

.cta-section {
    padding: var(--space-16) 0;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    color: white;
    text-align: center;
}

.cta-content h2 {
    font-size: var(--font-size-4xl);
    margin-bottom: var(--space-6);
}

.cta-content p {
    font-size: var(--font-size-lg);
    margin-bottom: var(--space-8);
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    opacity: 0.9;
}

.cta-actions {
    display: flex;
    gap: var(--space-4);
    justify-content: center;
    flex-wrap: wrap;
}

/* Responsive Design */
@media (max-width: 768px) {
    .profile-section {
        grid-template-columns: 1fr;
        text-align: center;
        gap: var(--space-6);
    }
    
    .profile-placeholder {
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }
    
    .profile-name {
        font-size: var(--font-size-3xl);
    }
    
    .about-grid {
        grid-template-columns: 1fr;
        gap: var(--space-8);
    }
    
    .about-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .skills-grid {
        grid-template-columns: 1fr;
    }
    
    .tools-grid {
        grid-template-columns: 1fr;
    }
    
    .timeline::before {
        left: 20px;
    }
    
    .timeline-item {
        padding-left: 60px;
    }
    
    .timeline-marker {
        left: 12px;
    }
    
    .certifications-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .cta-actions .btn {
        width: 100%;
        max-width: 300px;
    }
}

@media (max-width: 480px) {
    .about-stats {
        grid-template-columns: 1fr;
    }
    
    .profile-social {
        justify-content: center;
    }
}

/* Animation for skill bars */
@keyframes fillSkillBar {
    from {
        width: 0%;
    }
    to {
        width: var(--target-width);
    }
}

.skill-progress.animate {
    animation: fillSkillBar 2s ease-in-out forwards;
}
</style>

<!-- JavaScript for Skill Bar Animation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate skill bars when they come into view
    const skillBars = document.querySelectorAll('.skill-progress');
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const progressBar = entry.target;
                const width = progressBar.getAttribute('data-width');
                progressBar.style.setProperty('--target-width', width);
                progressBar.classList.add('animate');
                progressBar.style.width = width;
            }
        });
    }, {
        threshold: 0.5
    });
    
    skillBars.forEach(bar => {
        observer.observe(bar);
    });
});
</script>

<?php include("includes/footer.php"); ?>
