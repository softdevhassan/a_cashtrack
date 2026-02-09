-- Migration: Create Users Table
-- Description: User accounts with authentication and plan association
-- Created: 2026-02-09

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `image` varchar(255) DEFAULT '/images/default-avatar.webp',
  `role_id` int(11) DEFAULT 2 COMMENT '1=Admin, 2=User',
  `plan_id` int(11) DEFAULT NULL,
  `locked` tinyint(1) DEFAULT 0 COMMENT '0=Active, 1=Locked',
  `session_id` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL COMMENT 'JSON: company, verification_token, etc',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_updated_by` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`),
  UNIQUE KEY `unique_email` (`email`),
  KEY `idx_plan_id` (`plan_id`),
  KEY `idx_role` (`role_id`),
  KEY `idx_locked` (`locked`),
  CONSTRAINT `fk_users_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
