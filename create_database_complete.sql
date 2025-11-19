-- Complete Database Setup for StevenPort Account Settings System
-- This script creates the database and all tables from scratch

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS stevenport CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE stevenport;

-- Create the main users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin', 'superadmin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Profile information
    first_name VARCHAR(50) DEFAULT NULL,
    last_name VARCHAR(50) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    
    -- Security and login tracking
    last_login TIMESTAMP NULL DEFAULT NULL,
    email_verified BOOLEAN DEFAULT FALSE,
    email_verification_token VARCHAR(255) DEFAULT NULL,
    password_reset_token VARCHAR(255) DEFAULT NULL,
    password_reset_expires TIMESTAMP NULL DEFAULT NULL,
    
    -- User preferences
    theme ENUM('light', 'dark', 'auto') DEFAULT 'light',
    language VARCHAR(10) DEFAULT 'en',
    timezone VARCHAR(50) DEFAULT 'UTC',
    notifications BOOLEAN DEFAULT TRUE,
    email_notifications BOOLEAN DEFAULT TRUE,
    
    -- Account status and security
    account_status ENUM('active', 'suspended', 'banned') DEFAULT 'active',
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL DEFAULT NULL,
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    two_factor_secret VARCHAR(255) DEFAULT NULL
);

-- Create tropical cyclone database table (if it doesn't exist)
CREATE TABLE IF NOT EXISTS tcdatabase (
    id INT AUTO_INCREMENT PRIMARY KEY,
    storm_id VARCHAR(50),
    storm_img VARCHAR(255),
    track_img VARCHAR(255),
    name VARCHAR(100),
    basin VARCHAR(20),
    msw VARCHAR(20),
    mslp VARCHAR(20),
    formed DATE,
    dissipated DATE,
    ace_value DECIMAL(10,4),
    damage VARCHAR(100),
    fatalities VARCHAR(100),
    `desc` TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

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

-- Create user sessions table
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create user activity logs table
CREATE TABLE IF NOT EXISTS user_activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin user
INSERT IGNORE INTO users (username, email, password, role, first_name, last_name) 
VALUES ('admin', 'admin@stevenport.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Admin', 'User');

-- Insert some sample tropical cyclone data
INSERT IGNORE INTO tcdatabase (storm_id, name, basin, msw, mslp, formed, dissipated, ace_value, damage, fatalities, `desc`) VALUES
('2023-01', 'Hurricane Ian', 'NATL', '155', '937', '2022-09-23', '2022-09-30', '12.5', '$112 billion', '150', 'Category 4 hurricane that made landfall in Florida'),
('2023-02', 'Typhoon Noru', 'WPAC', '140', '940', '2022-09-21', '2022-09-28', '8.2', '$1.2 billion', '12', 'Category 4 typhoon that affected the Philippines'),
('2023-03', 'Cyclone Freddy', 'SWIO', '130', '950', '2023-02-05', '2023-03-14', '15.2', '$481 million', '1,434', 'Long-lived tropical cyclone that crossed the Indian Ocean');

-- Insert some sample projects
INSERT IGNORE INTO projects (title, category, description, image, link) VALUES
('Tropical Cyclone Database', 'Web Development', 'A comprehensive database system for tracking and managing tropical cyclone data with advanced search and filtering capabilities.', NULL, ''),
('Anime Art Gallery', 'Design', 'An AI-generated anime art gallery featuring various styles and characters with interactive viewing experience.', NULL, ''),
('Portfolio Website', 'Web Development', 'A modern, responsive portfolio website built with PHP, HTML, CSS, and JavaScript showcasing various projects and skills.', NULL, ''),
('Weather Forecast System', 'Web Development', 'Real-time weather forecasting system with interactive maps and detailed meteorological data visualization.', NULL, '');

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(account_status);
CREATE INDEX idx_sessions_user_id ON user_sessions(user_id);
CREATE INDEX idx_sessions_token ON user_sessions(session_token);
CREATE INDEX idx_sessions_expires ON user_sessions(expires_at);
CREATE INDEX idx_activity_user_id ON user_activity_logs(user_id);
CREATE INDEX idx_activity_action ON user_activity_logs(action);
CREATE INDEX idx_activity_created ON user_activity_logs(created_at);
CREATE INDEX idx_tc_name ON tcdatabase(name);
CREATE INDEX idx_tc_basin ON tcdatabase(basin);
CREATE INDEX idx_tc_formed ON tcdatabase(formed);
