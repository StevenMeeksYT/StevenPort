-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for stevenport
DROP DATABASE IF EXISTS `stevenport`;
CREATE DATABASE IF NOT EXISTS `stevenport` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `stevenport`;

-- Dumping structure for table stevenport.anime_gallery
DROP TABLE IF EXISTS `anime_gallery`;
CREATE TABLE IF NOT EXISTS `anime_gallery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `artist` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table stevenport.basin_metadata
DROP TABLE IF EXISTS `basin_metadata`;
CREATE TABLE IF NOT EXISTS `basin_metadata` (
  `id` int NOT NULL AUTO_INCREMENT,
  `basin_code` varchar(10) NOT NULL,
  `basin_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table stevenport.projects
DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT 'Portfolio',
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table stevenport.research_papers
DROP TABLE IF EXISTS `research_papers`;
CREATE TABLE IF NOT EXISTS `research_papers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `authors` varchar(255) DEFAULT NULL,
  `year` int DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `abstract` text,
  `file_name` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table stevenport.tcdatabase
DROP TABLE IF EXISTS `tcdatabase`;
CREATE TABLE IF NOT EXISTS `tcdatabase` (
  `id` int NOT NULL AUTO_INCREMENT,
  `storm_id` varchar(50) DEFAULT NULL,
  `storm_img` varchar(255) DEFAULT NULL,
  `track_img` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `basin` varchar(20) DEFAULT NULL,
  `msw` varchar(20) DEFAULT NULL,
  `mslp` varchar(20) DEFAULT NULL,
  `formed` date DEFAULT NULL,
  `dissipated` date DEFAULT NULL,
  `ace_value` decimal(10,4) DEFAULT NULL,
  `damage` varchar(100) DEFAULT NULL,
  `fatalities` varchar(100) DEFAULT NULL,
  `desc` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tc_name` (`name`),
  KEY `idx_tc_basin` (`basin`),
  KEY `idx_tc_formed` (`formed`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table stevenport.tornado_db
DROP TABLE IF EXISTS `tornado_db`;
CREATE TABLE IF NOT EXISTS `tornado_db` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tornado_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `state` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `intensity_scale` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wind_speed` int DEFAULT NULL,
  `path_length` decimal(8,2) DEFAULT NULL,
  `path_width` decimal(8,2) DEFAULT NULL,
  `fatalities` int DEFAULT '0',
  `injuries` int DEFAULT '0',
  `damage` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_tornado_date` (`date`),
  KEY `idx_tornado_scale` (`intensity_scale`),
  KEY `idx_tornado_state` (`state`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table stevenport.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin','superadmin') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `bio` text,
  `last_login` timestamp NULL DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT '0',
  `email_verification_token` varchar(255) DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_expires` timestamp NULL DEFAULT NULL,
  `theme` enum('light','dark','auto') DEFAULT 'light',
  `language` varchar(10) DEFAULT 'en',
  `timezone` varchar(50) DEFAULT 'UTC',
  `notifications` tinyint(1) DEFAULT '1',
  `email_notifications` tinyint(1) DEFAULT '1',
  `account_status` enum('active','suspended','banned') DEFAULT 'active',
  `failed_login_attempts` int DEFAULT '0',
  `locked_until` timestamp NULL DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT '0',
  `two_factor_secret` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_email` (`email`),
  KEY `idx_users_username` (`username`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_status` (`account_status`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table stevenport.user_activity_logs
DROP TABLE IF EXISTS `user_activity_logs`;
CREATE TABLE IF NOT EXISTS `user_activity_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_activity_user_id` (`user_id`),
  KEY `idx_activity_action` (`action`),
  KEY `idx_activity_created` (`created_at`),
  CONSTRAINT `user_activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table stevenport.user_sessions
DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_token` (`session_token`),
  KEY `idx_sessions_user_id` (`user_id`),
  KEY `idx_sessions_token` (`session_token`),
  KEY `idx_sessions_expires` (`expires_at`),
  CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
