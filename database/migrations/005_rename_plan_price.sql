-- Migration: Rename price_pkr to price in plans table
-- Created: 2026-02-09

ALTER TABLE `plans` CHANGE `price_pkr` `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00;
