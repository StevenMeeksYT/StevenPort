/* ============================================================
   FUTURISTIC JAVASCRIPT - Advanced Interactions & Effects
   Features: Particle Systems, Neural Networks, AI Elements
   Author: Steven Weathers - Enhanced AI Assistant
   ============================================================ */

class FuturisticEffects {
    constructor() {
        this.particles = [];
        this.neuralNodes = [];
        this.matrixColumns = [];
        this.isInitialized = false;
        this.mousePosition = { x: 0, y: 0 };
        this.cursorTrail = [];
        
        this.init();
    }

    init() {
        if (this.isInitialized) return;
        
        this.createParticleSystem();
        this.createNeuralNetwork();
        this.createMatrixRain();
        this.setupCursorTracking();
        this.setupScrollEffects();
        this.setupAudioVisualizer();
        this.setupHolographicElements();
        
        this.isInitialized = true;
        console.log('🚀 Futuristic Effects Initialized');
    }

    // Particle System
    createParticleSystem() {
        const particleContainer = document.createElement('div');
        particleContainer.className = 'particle-bg';
        document.body.appendChild(particleContainer);

        for (let i = 0; i < 50; i++) {
            this.createParticle(particleContainer);
        }

        // Continuous particle generation
        setInterval(() => {
            if (this.particles.length < 50) {
                this.createParticle(particleContainer);
            }
        }, 2000);
    }

    createParticle(container) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        // Random properties
        const size = Math.random() * 3 + 1;
        const delay = Math.random() * 5;
        const duration = Math.random() * 10 + 20;
        
        particle.style.width = size + 'px';
        particle.style.height = size + 'px';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = delay + 's';
        particle.style.animationDuration = duration + 's';
        
        container.appendChild(particle);
        
        // Remove particle after animation
        setTimeout(() => {
            if (particle.parentNode) {
                particle.parentNode.removeChild(particle);
            }
        }, (duration + delay) * 1000);
    }

    // Neural Network Visualization
    createNeuralNetwork() {
        const networkContainer = document.createElement('div');
        networkContainer.className = 'neural-network';
        document.body.appendChild(networkContainer);

        // Create nodes
        for (let i = 0; i < 15; i++) {
            const node = document.createElement('div');
            node.className = 'neural-node';
            node.style.left = Math.random() * 100 + '%';
            node.style.top = Math.random() * 100 + '%';
            node.style.animationDelay = Math.random() * 3 + 's';
            networkContainer.appendChild(node);
        }

        // Create connections
        this.createNeuralConnections(networkContainer);
    }

    createNeuralConnections(container) {
        const nodes = container.querySelectorAll('.neural-node');
        
        nodes.forEach((node, index) => {
            if (index < nodes.length - 1) {
                const connection = document.createElement('div');
                connection.className = 'neural-connection';
                
                const nodeRect = node.getBoundingClientRect();
                const nextNodeRect = nodes[index + 1].getBoundingClientRect();
                
                const angle = Math.atan2(
                    nextNodeRect.top - nodeRect.top,
                    nextNodeRect.left - nodeRect.left
                );
                
                const distance = Math.sqrt(
                    Math.pow(nextNodeRect.left - nodeRect.left, 2) +
                    Math.pow(nextNodeRect.top - nodeRect.top, 2)
                );
                
                connection.style.width = distance + 'px';
                connection.style.left = nodeRect.left + 'px';
                connection.style.top = nodeRect.top + 'px';
                connection.style.transform = `rotate(${angle}rad)`;
                connection.style.animationDelay = Math.random() * 2 + 's';
                
                container.appendChild(connection);
            }
        });
    }

    // Matrix Rain Effect
    createMatrixRain() {
        const matrixContainer = document.createElement('div');
        matrixContainer.className = 'matrix-rain';
        document.body.appendChild(matrixContainer);

        const chars = '01アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン';
        
        for (let i = 0; i < 20; i++) {
            const column = document.createElement('div');
            column.className = 'matrix-column';
            column.style.left = (i * 5) + '%';
            column.style.animationDelay = Math.random() * 10 + 's';
            column.style.animationDuration = (Math.random() * 10 + 10) + 's';
            
            // Generate random characters
            let text = '';
            for (let j = 0; j < 50; j++) {
                text += chars[Math.floor(Math.random() * chars.length)] + '<br>';
            }
            column.innerHTML = text;
            
            matrixContainer.appendChild(column);
        }
    }

    // Cursor Tracking
    setupCursorTracking() {
        document.addEventListener('mousemove', (e) => {
            this.mousePosition.x = e.clientX;
            this.mousePosition.y = e.clientY;
            
            this.createCursorTrail(e.clientX, e.clientY);
            this.updateHolographicElements(e.clientX, e.clientY);
        });
    }

    createCursorTrail(x, y) {
        const trail = document.createElement('div');
        trail.style.position = 'fixed';
        trail.style.left = x + 'px';
        trail.style.top = y + 'px';
        trail.style.width = '4px';
        trail.style.height = '4px';
        trail.style.background = 'var(--neon-cyan)';
        trail.style.borderRadius = '50%';
        trail.style.pointerEvents = 'none';
        trail.style.zIndex = '9999';
        trail.style.boxShadow = '0 0 10px var(--neon-cyan)';
        trail.style.animation = 'fade-out 0.5s ease-out forwards';
        
        document.body.appendChild(trail);
        
        setTimeout(() => {
            if (trail.parentNode) {
                trail.parentNode.removeChild(trail);
            }
        }, 500);
    }

    // Scroll Effects
    setupScrollEffects() {
        window.addEventListener('scroll', () => {
            this.parallaxEffect();
            this.revealElements();
        });
    }

    parallaxEffect() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.parallax');
        
        parallaxElements.forEach((element, index) => {
            const speed = 0.5 + (index * 0.1);
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    }

    revealElements() {
        const reveals = document.querySelectorAll('.reveal-on-scroll');
        
        reveals.forEach(element => {
            const windowHeight = window.innerHeight;
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < windowHeight - elementVisible) {
                element.classList.add('active');
            }
        });
    }

    // Audio Visualizer (Web Audio API)
    setupAudioVisualizer() {
        if (!window.AudioContext && !window.webkitAudioContext) return;
        
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const analyser = audioContext.createAnalyser();
        const dataArray = new Uint8Array(analyser.frequencyBinCount);
        
        // Create visualizer bars
        const visualizer = document.createElement('div');
        visualizer.className = 'audio-visualizer';
        visualizer.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            align-items: end;
            gap: 2px;
            height: 100px;
            z-index: 1000;
        `;
        
        for (let i = 0; i < 32; i++) {
            const bar = document.createElement('div');
            bar.style.cssText = `
                width: 4px;
                background: linear-gradient(to top, var(--neon-blue), var(--neon-purple));
                border-radius: 2px;
                transition: height 0.1s ease;
            `;
            visualizer.appendChild(bar);
        }
        
        document.body.appendChild(visualizer);
        
        // Simulate audio data for demo
        setInterval(() => {
            const bars = visualizer.querySelectorAll('div');
            bars.forEach((bar, index) => {
                const height = Math.random() * 80 + 10;
                bar.style.height = height + 'px';
                bar.style.opacity = 0.3 + (Math.random() * 0.7);
            });
        }, 100);
    }

    // Holographic Elements
    setupHolographicElements() {
        const holographicElements = document.querySelectorAll('.holographic');
        
        holographicElements.forEach(element => {
            element.addEventListener('mouseenter', () => {
                element.style.animation = 'hologram-flicker 0.5s infinite';
            });
            
            element.addEventListener('mouseleave', () => {
                element.style.animation = 'hologram-flicker 2s infinite';
            });
        });
    }

    updateHolographicElements(x, y) {
        const holographicElements = document.querySelectorAll('.holo-projection');
        
        holographicElements.forEach(element => {
            const rect = element.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            
            const distance = Math.sqrt(
                Math.pow(x - centerX, 2) + Math.pow(y - centerY, 2)
            );
            
            const maxDistance = 200;
            const intensity = Math.max(0, 1 - distance / maxDistance);
            
            element.style.filter = `brightness(${1 + intensity * 0.5}) saturate(${1 + intensity})`;
        });
    }

    // AI Terminal Interface
    createAITerminal(content, container) {
        const terminal = document.createElement('div');
        terminal.className = 'ai-terminal';
        
        const lines = content.split('\n');
        lines.forEach((line, index) => {
            const lineElement = document.createElement('div');
            lineElement.className = 'terminal-line';
            lineElement.innerHTML = line + '<span class="terminal-cursor"></span>';
            terminal.appendChild(lineElement);
        });
        
        container.appendChild(terminal);
        return terminal;
    }

    // Quantum Loader
    createQuantumLoader(container) {
        const loader = document.createElement('div');
        loader.className = 'quantum-loader';
        
        for (let i = 0; i < 4; i++) {
            const ring = document.createElement('div');
            ring.className = 'quantum-ring';
            loader.appendChild(ring);
        }
        
        container.appendChild(loader);
        return loader;
    }

    // Neural Network Visualization
    createNeuralViz(container) {
        const viz = document.createElement('div');
        viz.className = 'neural-viz';
        
        // Create layers
        for (let i = 0; i < 4; i++) {
            const layer = document.createElement('div');
            layer.className = 'neural-layer';
            viz.appendChild(layer);
        }
        
        // Create synapses
        for (let i = 0; i < 4; i++) {
            const synapse = document.createElement('div');
            synapse.className = 'neural-synapse';
            viz.appendChild(synapse);
        }
        
        container.appendChild(viz);
        return viz;
    }

    // Glassmorphism Card
    createGlassCard(content, container) {
        const card = document.createElement('div');
        card.className = 'glass-card';
        card.innerHTML = content;
        container.appendChild(card);
        return card;
    }

    // Cyberpunk Button
    createCyberButton(text, container, onClick) {
        const button = document.createElement('button');
        button.className = 'cyber-btn';
        button.textContent = text;
        button.addEventListener('click', onClick);
        container.appendChild(button);
        return button;
    }

    // 3D Card Flip
    flipCard(card) {
        card.classList.toggle('flip');
    }

    // Morphing Background
    createMorphingBackground(container) {
        const background = document.createElement('div');
        background.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, 
                var(--neon-blue) 0%, 
                var(--neon-purple) 25%, 
                var(--neon-pink) 50%, 
                var(--neon-cyan) 75%, 
                var(--neon-blue) 100%);
            background-size: 400% 400%;
            animation: morph-gradient 8s ease infinite;
            opacity: 0.1;
            z-index: -1;
        `;
        container.appendChild(background);
        return background;
    }

    // Data Stream Effect
    createDataStream(container) {
        const stream = document.createElement('div');
        stream.className = 'data-stream';
        stream.style.cssText = `
            position: relative;
            height: 2px;
            background: linear-gradient(90deg, 
                transparent, 
                var(--neon-cyan), 
                transparent);
            overflow: hidden;
        `;
        container.appendChild(stream);
        return stream;
    }

    // Futuristic Hover Effect
    addFuturisticHover(element) {
        element.classList.add('futuristic-hover');
    }

    // Theme Switcher
    switchToCyberpunk() {
        document.documentElement.setAttribute('data-theme', 'cyberpunk');
        localStorage.setItem('theme', 'cyberpunk');
        
        // Add cyberpunk-specific effects
        this.addCyberpunkEffects();
    }

    addCyberpunkEffects() {
        // Add glitch effect to text
        const texts = document.querySelectorAll('h1, h2, h3');
        texts.forEach(text => {
            text.classList.add('neon-glow');
        });

        // Add neon borders to cards
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.classList.add('neon-border');
        });
    }

    // Destroy effects (for cleanup)
    destroy() {
        const effects = document.querySelectorAll('.particle-bg, .neural-network, .matrix-rain');
        effects.forEach(effect => {
            if (effect.parentNode) {
                effect.parentNode.removeChild(effect);
            }
        });
        
        this.isInitialized = false;
        console.log('🧹 Futuristic Effects Cleaned Up');
    }
}

// Initialize futuristic effects when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.futuristicEffects = new FuturisticEffects();
    // Cyberpunk toggle removed to keep themes fixed (light/dark only)
});

// CSS for fade-out animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fade-out {
        0% {
            opacity: 1;
            transform: scale(1);
        }
        100% {
            opacity: 0;
            transform: scale(0.5);
        }
    }
    
    .reveal-on-scroll {
        opacity: 0;
        transform: translateY(50px);
        transition: all 0.8s ease;
    }
    
    .reveal-on-scroll.active {
        opacity: 1;
        transform: translateY(0);
    }
    
    .parallax {
        transition: transform 0.1s ease-out;
    }
`;
document.head.appendChild(style);