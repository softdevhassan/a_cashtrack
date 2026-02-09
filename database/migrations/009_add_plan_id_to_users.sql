-- Migration: Add plan_id to Users Table
-- Description: Adds plan_id column to users table if missing, to fix signup error
-- Created: 2026-02-09

ALTER TABLE `users` ADD COLUMN `plan_id` INT(11) DEFAULT NULL AFTER `role_id`;
ALTER TABLE `users` ADD CONSTRAINT `fk_users_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE SET NULL;
