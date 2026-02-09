-- CashTrack Database Schema
-- Expense Management System
-- Created: 2026-02-09

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `a_cashtrack`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` int NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `value2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `value3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_updated_by` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `type`, `name`, `value1`, `value2`, `value3`, `last_updated_at`, `last_updated_by`) VALUES
(1, 'roles', 'admin', 'admin', '', NULL, '2026-02-09 05:00:00', 1),
(2, 'roles', 'user', 'user', '', NULL, '2026-02-09 05:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '/images/default-avatar.webp',
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `role_id` int NOT NULL DEFAULT '2',
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `session_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `special_string` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `last_updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_updated_by` int NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `username`, `password`, `image`, `details`, `role_id`, `locked`, `session_id`, `special_string`, `last_updated_at`, `last_updated_by`, `email_verified_at`, `created_at`) VALUES
(1, 'Admin', 'admin@cashtrack.pk', '+923001234567', 'admin', '$2y$10$092AFY31lUpIdbHXXmFJLe833cEMkM1DHJiUKEtgQKWPEVRaQAfQ2', '/images/default-avatar.webp', '{}', 1, 0, NULL, NULL, '2026-02-09 05:00:00', 1, NULL, '2026-02-09 05:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_usdt` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_trial` tinyint(1) DEFAULT '0',
  `description` text NOT NULL,
  `features` text NOT NULL,
  `transaction_limit` int NOT NULL DEFAULT '0',
  `wallet_limit` int NOT NULL DEFAULT '0',
  `duration_days` int NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `title`, `price`, `price_usdt`, `is_trial`, `description`, `features`, `transaction_limit`, `wallet_limit`, `duration_days`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Free Trial', 0.00, 0.00, 1, 'Perfect for testing CashTrack. Limited features to get started.', '50 Transactions\r\n2 Wallets\r\nLifetime Access\r\nBasic Features', 50, 2, 36500, 'active', '2026-02-09 05:00:00', '2026-02-09 05:00:00'),
(2, 'Starter', 500.00, 2.00, 0, 'Great for individuals managing personal finance.', '500 Transactions\r\n3 Wallets\r\nLifetime Access\r\nCategory Management\r\nBasic Analytics', 500, 3, 36500, 'active', '2026-02-09 05:00:00', '2026-02-09 05:00:00'),
(3, 'Professional', 1500.00, 5.00, 0, 'For power users who need unlimited tracking.', '1000 Transactions\r\n4 Wallets\r\nLifetime Access\r\nAdvanced Reports\r\nPDF Export', 1000, 4, 36500, 'active', '2026-02-09 05:00:00', '2026-02-09 05:00:00'),
(4, 'Enterprise', 3000.00, 10.00, 0, 'For businesses and teams.', '2000 Transactions\r\n5 Wallets\r\nLifetime Access\r\nMulti-user Access\r\nPriority Support', 2000, 5, 36500, 'active', '2026-02-09 05:00:00', '2026-02-09 05:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `plan_id` int NOT NULL,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `status` enum('active','expired','cancelled') DEFAULT 'active',
  `transaction_usage` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('bank','digital_wallet','crypto','cash','other') DEFAULT 'other',
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `icon` varchar(100) DEFAULT 'ri-wallet-line',
  `color` varchar(50) DEFAULT '#6366f1',
  `currency` varchar(10) DEFAULT 'PKR',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `icon` varchar(100) DEFAULT 'ri-price-tag-line',
  `color` varchar(50) DEFAULT '#6366f1',
  `is_system` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories` - System Default Categories
--

INSERT INTO `categories` (`id`, `user_id`, `name`, `type`, `icon`, `color`, `is_system`, `created_at`) VALUES
(1, NULL, 'Salary', 'income', 'ri-money-dollar-circle-line', '#10b981', 1, '2026-02-09 05:00:00'),
(2, NULL, 'Business Income', 'income', 'ri-briefcase-line', '#059669', 1, '2026-02-09 05:00:00'),
(3, NULL, 'Investment Returns', 'income', 'ri-stock-line', '#14b8a6', 1, '2026-02-09 05:00:00'),
(4, NULL, 'Freelance', 'income', 'ri-freelancer-line', '#06b6d4', 1, '2026-02-09 05:00:00'),
(5, NULL, 'Other Income', 'income', 'ri-exchange-line', '#0891b2', 1, '2026-02-09 05:00:00'),
(6, NULL, 'Food & Dining', 'expense', 'ri-restaurant-line', '#ef4444', 1, '2026-02-09 05:00:00'),
(7, NULL, 'Transportation', 'expense', 'ri-bus-line', '#f97316', 1, '2026-02-09 05:00:00'),
(8, NULL, 'Shopping', 'expense', 'ri-shopping-cart-line', '#f59e0b', 1, '2026-02-09 05:00:00'),
(9, NULL, 'Bills & Utilities', 'expense', 'ri-file-list-line', '#eab308', 1, '2026-02-09 05:00:00'),
(10, NULL, 'Healthcare', 'expense', 'ri-hospital-line', '#ec4899', 1, '2026-02-09 05:00:00'),
(11, NULL, 'Entertainment', 'expense', 'ri-gamepad-line', '#a855f7', 1, '2026-02-09 05:00:00'),
(12, NULL, 'Education', 'expense', 'ri-book-line', '#8b5cf6', 1, '2026-02-09 05:00:00'),
(13, NULL, 'Rent', 'expense', 'ri-home-line', '#dc2626', 1, '2026-02-09 05:00:00'),
(14, NULL, 'Insurance', 'expense', 'ri-shield-check-line', '#65a30d', 1, '2026-02-09 05:00:00'),
(15, NULL, 'Charity', 'expense', 'ri-hand-heart-line', '#84cc16', 1, '2026-02-09 05:00:00'),
(16, NULL, 'Other Expense', 'expense', 'ri-more-line', '#94a3b8', 1, '2026-02-09 05:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint NOT NULL,
  `user_id` int NOT NULL,
  `type` enum('income','expense','transfer') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `category_id` int DEFAULT NULL,
  `wallet_id` int DEFAULT NULL,
  `to_wallet_id` int DEFAULT NULL,
  `note` text,
  `transaction_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_logs`
--

CREATE TABLE `payment_logs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `plan_id` int NOT NULL,
  `amount_pkr` decimal(10,2) NOT NULL,
  `amount_usdt` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'binance',
  `transaction_id` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','failed','cancelled') DEFAULT 'pending',
  `payment_details` longtext,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_system_type_name` (`type`,`name`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `idx_status` (`status`,`ends_at`);

ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_user_type` (`user_id`,`type`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_system` (`is_system`);

ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `wallet_id` (`wallet_id`),
  ADD KEY `to_wallet_id` (`to_wallet_id`),
  ADD KEY `idx_user_type_date` (`user_id`,`type`,`transaction_date`),
  ADD KEY `idx_transaction_date` (`transaction_date`);

ALTER TABLE `payment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `idx_status` (`status`);

ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `app_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `plans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `subscriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `wallets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

ALTER TABLE `transactions`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

ALTER TABLE `payment_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `password_resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_ibfk_4` FOREIGN KEY (`to_wallet_id`) REFERENCES `wallets` (`id`) ON DELETE SET NULL;

ALTER TABLE `payment_logs`
  ADD CONSTRAINT `payment_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `payment_logs_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE RESTRICT;

ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
