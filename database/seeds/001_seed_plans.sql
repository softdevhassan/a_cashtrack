-- Seed: Plans Table
-- Description: Insert 4 default plans (Trial, Starter, Professional, Enterprise)
-- Created: 2026-02-09

INSERT INTO `plans` (`id`, `title`, `description`, `price`, `transaction_limit`, `wallet_limit`, `duration_days`, `is_trial`, `status`, `features`, `created_at`) VALUES
(1, 'Free Trial', 'Perfect for testing CashTrack. Limited features to get started.', 0.00, 50, 2, 365, 1, 'active', NULL, NOW()),
(2, 'Starter', 'Great for individuals managing personal finance.', 500.00, 500, 3, 365, 0, 'active', NULL, NOW()),
(3, 'Professional', 'For power users who need unlimited tracking.', 1500.00, 1000, 4, 365, 0, 'active', NULL, NOW()),
(4, 'Enterprise', 'For businesses and teams.', 3000.00, 2000, 5, 365, 0, 'active', NULL, NOW());
