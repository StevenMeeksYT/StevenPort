-- Fix for StevenPort database - Add missing columns
-- Run this if you get "Unknown column" errors

USE stevenport;

-- Add missing columns to users table
ALTER TABLE users 
ADD COLUMN first_name VARCHAR(50) DEFAULT NULL,
ADD COLUMN last_name VARCHAR(50) DEFAULT NULL,
ADD COLUMN bio TEXT DEFAULT NULL,
ADD COLUMN last_login TIMESTAMP NULL DEFAULT NULL,
ADD COLUMN email_verified BOOLEAN DEFAULT FALSE,
ADD COLUMN email_verification_token VARCHAR(255) DEFAULT NULL,
ADD COLUMN password_reset_token VARCHAR(255) DEFAULT NULL,
ADD COLUMN password_reset_expires TIMESTAMP NULL DEFAULT NULL,
ADD COLUMN theme ENUM('light', 'dark', 'auto') DEFAULT 'light',
ADD COLUMN language VARCHAR(10) DEFAULT 'en',
ADD COLUMN timezone VARCHAR(50) DEFAULT 'UTC',
ADD COLUMN notifications BOOLEAN DEFAULT TRUE,
ADD COLUMN email_notifications BOOLEAN DEFAULT TRUE,
ADD COLUMN account_status ENUM('active', 'suspended', 'banned') DEFAULT 'active',
ADD COLUMN failed_login_attempts INT DEFAULT 0,
ADD COLUMN locked_until TIMESTAMP NULL DEFAULT NULL,
ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE,
ADD COLUMN two_factor_secret VARCHAR(255) DEFAULT NULL;

-- Create user_sessions table if it doesn't exist
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

-- Create user_activity_logs table if it doesn't exist
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
