<?php
class DbCleanupWorker
{
    private $db;

    public function __construct()
    {
        $this->db = new mysqli($_ENV['DB_SERVER'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function run()
    {
        // echo "[" . date('Y-m-d H:i:s') . "] Starting queue cleanup...\n";

        // Archive completed campaigns older than 30 days
        $this->archiveOldCampaigns();

        // Clean up old email queue entries
        $this->cleanupOldQueueEntries();

        // Cleanup billing invoices
        $this->cleanupBillingInvoices();

        // Delete permanently old archived campaigns
        $this->deleteArchivedCampaigns();

        // Clean unused tables and settings
        $this->cleanUnusedTables();

        // Optimize tables - DISABLED for high-concurrency 
        // Running OPTIMIZE TABLE locks the table and destroys performance with parallel workers.
        // This should be run manually or on a separate weekly cron.
        // $this->optimizeTables();

        // echo "[" . date('Y-m-d H:i:s') . "] Cleanup completed.\n";
    }

    private function archiveOldCampaigns()
    {
        $thirty_days_ago = date('Y-m-d H:i:s', strtotime('-30 days'));

        $result = $this->db->query("
            UPDATE campaigns 
            SET status = 'archived'
            WHERE status IN ('completed', 'failed')
            AND updated_at < '$thirty_days_ago'
        ");

        if ($this->db->affected_rows > 0) {
            echo "Archived " . $this->db->affected_rows . " old campaigns\n";
        }
    }

    private function cleanupOldQueueEntries()
    {
        // Reduced to 24 hours (1 day) as per new requirement to keep DB size low
        // This is critical for high-volume sending (10M/day causes huge table growth)
        $twenty_four_hours_ago = date('Y-m-d H:i:s', strtotime('-24 hours'));

        // Delete old sent/failed emails
        $this->db->query("
            DELETE FROM email_queue 
            WHERE status IN ('sent', 'failed')
            AND (sent_at < '$twenty_four_hours_ago' OR created_at < '$twenty_four_hours_ago')
        ");

        if ($this->db->affected_rows > 0) {
            echo "Deleted " . $this->db->affected_rows . " old queue entries (older than 24h)\n";
        }
    }

    private function cleanupBillingInvoices()
    {
        $six_months_ago = date('Y-m-d H:i:s', strtotime('-6 months'));

        $this->db->query("DELETE FROM billing_invoices WHERE created_at < '$six_months_ago'");

        if ($this->db->affected_rows > 0) {
            echo "Deleted " . $this->db->affected_rows . " old billing invoices (older than 6 months)\n";
        }
    }

    private function deleteArchivedCampaigns()
    {
        $six_months_ago = date('Y-m-d H:i:s', strtotime('-6 months'));
        $thirty_days_ago = date('Y-m-d H:i:s', strtotime('-30 days'));

        // Delete campaigns that are already archived and older than 6 months
        $this->db->query("DELETE FROM campaigns WHERE status = 'archived' AND updated_at < '$six_months_ago'");

        if ($this->db->affected_rows > 0) {
            echo "Deleted " . $this->db->affected_rows . " permanently archived campaigns (older than 6 months)\n";
        }

        // Also cleanup campaign logs older than 30 days
        $this->db->query("DELETE FROM campaign_logs WHERE created_at < '$thirty_days_ago'");
        if ($this->db->affected_rows > 0) {
            echo "Deleted " . $this->db->affected_rows . " old campaign logs (older than 30 days)\n";
        }
    }

    private function cleanUnusedTables()
    {
        // Drop unused tables
        $this->db->query("DROP TABLE IF EXISTS unsubscribes");
        $this->db->query("DROP TABLE IF EXISTS email_connections");

        // Clean app_settings
        // Keep 'roles' (multiple), 'max_attempts' (email_settings)
        // Original file insert: ('roles', 'Admin', NULL), ('roles', 'User', NULL), ('email_settings', 'rotation_batch_size', '50'), ('email_settings', 'max_attempts', '3')
        // We delete anything that is NOT roles or max_attempts
        $this->db->query("DELETE FROM app_settings WHERE name NOT IN ('max_attempts', 'Admin', 'User')");

        $deleted = $this->db->affected_rows;
        if ($deleted > 0) {
            echo "Deleted $deleted unused app_settings keys.\n";

            // Reset Auto Increment
            $res = $this->db->query("SELECT MAX(id) as max_id FROM app_settings");
            $max_id = 0;
            if ($res && $row = $res->fetch_assoc()) {
                $max_id = (int)$row['max_id'];
            }
            $next_id = $max_id + 1;
            $this->db->query("ALTER TABLE app_settings AUTO_INCREMENT = $next_id");
            echo "Reset app_settings AUTO_INCREMENT to $next_id\n";
        }
    }

    private function optimizeTables()
    {
        $tables = ['email_queue', 'campaigns', 'campaign_logs', 'app_settings'];

        foreach ($tables as $table) {
            $this->db->query("OPTIMIZE TABLE $table");
        }
    }

    public function __destruct()
    {
        $this->db->close();
    }
}

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure ENV is loaded if not already (redundant if loaded by index.php, but safe)
if (!isset($_ENV['DB_SERVER'])) {
    $envPath = __DIR__ . '/../.env';
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(ltrim($line), '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
}

// Set header to HTML to override JSON header from index.php if needed
if (!headers_sent()) {
    header('Content-Type: text/html; charset=utf-8');
}

/*
if (!defined('WORKER_MODE')) {
    if (php_sapi_name() !== 'cli') {
        echo "<pre>"; // Format output for browser
    }
    $worker = new QueueCleanupWorker();
    $worker->run();
    if (php_sapi_name() !== 'cli') {
        echo "</pre>";
    }
}
*/
