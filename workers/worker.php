<?php

/**
 * worker.php - CashTrack Worker (OPTIONAL)
 * 
 * CashTrack is 100% LIMIT-BASED system:
 * - NO time-based restrictions
 * - NO monthly resets
 * - NO expiry notifications
 * 
 * All limits enforced at creation time:
 * - Can't create wallet if limit reached
 * - Can't create transaction if limit reached
 * - User sees usage in header/topbar
 * 
 * This worker is OPTIONAL - only for database maintenance
 */

if (php_sapi_name() !== 'cli') {
    die("Run from CLI only\n");
}

define('WORKER_MODE', true);
date_default_timezone_set('Asia/Karachi');

// Load environment
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(ltrim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

require_once __DIR__ . '/../includes/functions.php';

echo "CashTrack Worker - " . date('Y-m-d H:i:s') . "\n";
echo "System is LIMIT-BASED. No time checks needed.\n";

// Optional: Clean up old data (e.g., deleted users' data)
// Add your custom cleanup tasks here if needed

echo "Done.\n";
