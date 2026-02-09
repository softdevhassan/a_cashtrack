-- Migration: Create Plans Table
-- Description: Stores subscription plans with transaction/wallet limits
-- Created: 2026-02-09

DROP TABLE IF EXISTS `plans`;

CREATE TABLE `plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `transaction_limit` int(11) NOT NULL DEFAULT 50,
  `wallet_limit` int(11) NOT NULL DEFAULT 2,
  `duration_days` int(11) NOT NULL DEFAULT 365,
  `is_trial` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `features` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_is_trial` (`is_trial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
