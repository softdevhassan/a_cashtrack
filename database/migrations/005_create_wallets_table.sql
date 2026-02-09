-- Migration: Create Wallets Table
-- Description: User wallet/account management for tracking finances
-- Created: 2026-02-09

DROP TABLE IF EXISTS `wallets`;

CREATE TABLE `wallets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('cash','bank','mobile_wallet','investment','credit_card','other') DEFAULT 'cash',
  `currency` varchar(10) DEFAULT 'PKR',
  `current_balance` decimal(15,2) DEFAULT 0.00,
  `initial_balance` decimal(15,2) DEFAULT 0.00,
  `icon` varchar(100) DEFAULT 'ri-wallet-line',
  `color` varchar(7) DEFAULT '#6366f1',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_type` (`type`),
  CONSTRAINT `fk_wallets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
