-- Seed: Default Categories
-- Description: 16 system categories (6 income, 10 expense)
-- Created: 2026-02-09

INSERT INTO `categories` (`id`, `user_id`, `name`, `type`, `icon`, `color`, `is_system`, `created_at`) VALUES
-- INCOME CATEGORIES (6)
(1, NULL, 'Salary', 'income', 'ri-briefcase-line', '#10b981', 1, NOW()),
(2, NULL, 'Business', 'income', 'ri-store-line', '#3b82f6', 1, NOW()),
(3, NULL, 'Investment', 'income', 'ri-line-chart-line', '#8b5cf6', 1, NOW()),
(4, NULL, 'Freelance', 'income', 'ri-computer-line', '#06b6d4', 1, NOW()),
(5, NULL, 'Rental Income', 'income', 'ri-home-4-line', '#f59e0b', 1, NOW()),
(6, NULL, 'Other Income', 'income', 'ri-money-dollar-circle-line', '#6366f1', 1, NOW()),

-- EXPENSE CATEGORIES (10)
(7, NULL, 'Food & Dining', 'expense', 'ri-restaurant-line', '#ef4444', 1, NOW()),
(8, NULL, 'Transportation', 'expense', 'ri-car-line', '#f97316', 1, NOW()),
(9, NULL, 'Shopping', 'expense', 'ri-shopping-cart-line', '#ec4899', 1, NOW()),
(10, NULL, 'Entertainment', 'expense', 'ri-movie-line', '#a855f7', 1, NOW()),
(11, NULL, 'Bills & Utilities', 'expense', 'ri-file-list-3-line', '#64748b', 1, NOW()),
(12, NULL, 'Healthcare', 'expense', 'ri-heart-pulse-line', '#dc2626', 1, NOW()),
(13, NULL, 'Education', 'expense', 'ri-book-open-line', '#2563eb', 1, NOW()),
(14, NULL, 'Personal Care', 'expense', 'ri-scissors-line', '#db2777', 1, NOW()),
(15, NULL, 'Home & Garden', 'expense', 'ri-plant-line', '#059669', 1, NOW()),
(16, NULL, 'Other Expenses', 'expense', 'ri-more-line', '#71717a', 1, NOW());
