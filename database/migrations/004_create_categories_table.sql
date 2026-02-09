-- Migration: Create Categories Table
-- Description: Income/Expense categories for transaction classification
-- Created: 2026-02-09

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'NULL for system categories',
  `name` varchar(255) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `icon` varchar(100) DEFAULT 'ri-folder-line',
  `color` varchar(7) DEFAULT '#6366f1',
  `is_system` tinyint(1) DEFAULT 0 COMMENT '1=Cannot be edited/deleted',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_type` (`type`),
  KEY `idx_system` (`is_system`),
  CONSTRAINT `fk_categories_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
