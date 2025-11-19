-- Add projects table to existing stevenport database
USE stevenport;

-- Create projects table for CMS
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) DEFAULT 'Portfolio',
    description TEXT,
    image VARCHAR(255),
    link VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert some sample projects
INSERT IGNORE INTO projects (title, category, description, image, link) VALUES
('Tropical Cyclone Database', 'Web Development', 'A comprehensive database system for tracking and managing tropical cyclone data with advanced search and filtering capabilities.', NULL, ''),
('Anime Art Gallery', 'Design', 'An AI-generated anime art gallery featuring various styles and characters with interactive viewing experience.', NULL, ''),
('Portfolio Website', 'Web Development', 'A modern, responsive portfolio website built with PHP, HTML, CSS, and JavaScript showcasing various projects and skills.', NULL, ''),
('Weather Forecast System', 'Web Development', 'Real-time weather forecasting system with interactive maps and detailed meteorological data visualization.', NULL, '');
