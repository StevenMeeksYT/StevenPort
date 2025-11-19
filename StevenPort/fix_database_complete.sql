-- Comprehensive Fix for StevenPort database
-- This handles all missing columns including updated_at

USE stevenport;

-- First, check if updated_at column exists and add it if missing
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'stevenport' 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'updated_at') = 0,
    'ALTER TABLE users ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    'SELECT "updated_at column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add all other missing columns (these will be ignored if they already exist)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS first_name VARCHAR(50) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS last_name VARCHAR(50) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS bio TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL DEFAULT NULL,
ADD COLUMN IF NOT EXISTS email_verified BOOLEAN DEFAULT FALSE,
ADD COLUMN IF NOT EXISTS email_verification_token VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS password_reset_token VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS password_reset_expires TIMESTAMP NULL DEFAULT NULL,
ADD COLUMN IF NOT EXISTS theme ENUM('light', 'dark', 'auto') DEFAULT 'light',
ADD COLUMN IF NOT EXISTS language VARCHAR(10) DEFAULT 'en',
ADD COLUMN IF NOT EXISTS timezone VARCHAR(50) DEFAULT 'UTC',
ADD COLUMN IF NOT EXISTS notifications BOOLEAN DEFAULT TRUE,
ADD COLUMN IF NOT EXISTS email_notifications BOOLEAN DEFAULT TRUE,
ADD COLUMN IF NOT EXISTS account_status ENUM('active', 'suspended', 'banned') DEFAULT 'active',
ADD COLUMN IF NOT EXISTS failed_login_attempts INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS locked_until TIMESTAMP NULL DEFAULT NULL,
ADD COLUMN IF NOT EXISTS two_factor_enabled BOOLEAN DEFAULT FALSE,
ADD COLUMN IF NOT EXISTS two_factor_secret VARCHAR(255) DEFAULT NULL;

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
