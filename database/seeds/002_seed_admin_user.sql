-- Seed: Admin User
-- Description: Create default admin account
-- Created: 2026-02-09
-- Credentials: admin / admin123

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `phone`, `image`, `role_id`, `plan_id`, `locked`, `session_id`, `details`, `email_verified_at`, `last_login_at`, `last_updated_by`, `created_at`) VALUES
(1, 'Administrator', 'admin', 'admin@cashtrack.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '/images/default-avatar.webp', 1, NULL, 0, NULL, '{"company":"CashTrack"}', NOW(), NOW(), 0, NOW());

-- Password is: admin123
-- Note: Plan_id is NULL for super admin (no limits)
