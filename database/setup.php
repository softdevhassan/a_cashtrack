<?php

/**
 * CashTrack Database Setup Script
 * 
 * This script automatically runs all migrations and seeds the database
 * Run this file once during initial installation
 * 
 * Usage: php database/setup.php
 * Or visit: http://localhost/a_cashtrack/database/setup.php
 */

// Load environment and database connection
require_once __DIR__ . '/../includes/functions.php';

// Configuration
$migrations_dir = __DIR__ . '/migrations/';
$seeds_dir = __DIR__ . '/seeds/';

// HTML Output Start
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CashTrack - Database Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
            padding: 40px;
        }

        h1 {
            color: #667eea;
            margin-bottom: 10px;
        }

        h2 {
            color: #333;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .info {
            background: #e0e7ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #4c51bf;
        }

        .success {
            color: #10b981;
            margin: 8px 0;
            padding-left: 20px;
        }

        .error {
            color: #ef4444;
            margin: 8px 0;
            padding-left: 20px;
            font-weight: bold;
        }

        .warning {
            color: #f59e0b;
            margin: 8px 0;
            padding-left: 20px;
        }

        .sql-file {
            color: #6366f1;
            font-family: monospace;
            font-weight: 600;
        }

        .summary {
            background: #f0fdf4;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
            border-left: 4px solid #10b981;
        }

        .summary h3 {
            color: #059669;
            margin-bottom: 10px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            transition: all 0.3s;
        }

        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🚀 CashTrack Database Setup</h1>
        <p style="color: #666; margin-bottom: 20px;">Initializing your CashTrack installation...</p>

        <?php

        // Check database connection
        echo "<h2>📡 Step 1: Database Connection</h2>";
        if (!isset($conn) || $conn->connect_error) {
            echo "<p class='error'>❌ Database connection failed!</p>";
            echo "<p class='error'>Error: " . ($conn->connect_error ?? 'Connection object not found') . "</p>";
            echo "<p class='warning'>Please check your database credentials in .env file</p>";
            echo "</div></body></html>";
            exit;
        }
        echo "<p class='success'>✅ Database connected successfully!</p>";
        echo "<p class='info'>Database: <strong>" . $_ENV['DB_DATABASE'] . "</strong> on <strong>" . $_ENV['DB_HOST'] . "</strong></p>";

        // Run Migrations
        echo "<h2>🗃️ Step 2: Running Migrations</h2>";
        $migration_files = glob($migrations_dir . '*.sql');
        sort($migration_files); // Ensure correct order

        $migration_success = 0;
        $migration_errors = 0;

        // Disable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");

        // Disable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");

        foreach ($migration_files as $file) {
            $filename = basename($file);
            echo "<p>Running: <span class='sql-file'>$filename</span>...";

            $sql = file_get_contents($file);

            // Execute the SQL (can contain multiple statements)
            try {
                if ($conn->multi_query($sql)) {
                    do {
                        if ($result = $conn->store_result()) {
                            $result->free();
                        }
                    } while ($conn->more_results() && $conn->next_result());

                    echo " <span class='success'>✅ Done</span></p>";
                    $migration_success++;
                } else {
                    throw new Exception($conn->error);
                }
            } catch (Exception $e) {
                echo " <span class='warning'>⚠️ Skipped/Failed</span></p>";
                echo "<p class='warning'>Note: " . $e->getMessage() . "</p>";
                // Count as success if it's just a duplicate column or table already exists
                if (strpos($e->getMessage(), 'Duplicate column') !== false || strpos($e->getMessage(), 'already exists') !== false) {
                    $migration_success++;
                } else {
                    $migration_errors++;
                }
            }
        }

        // Re-enable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");

        echo "<p class='info'><strong>Migrations:</strong> $migration_success successful, $migration_errors failed</p>";

        // Run Seeds
        echo "<h2>🌱 Step 3: Seeding Database</h2>";
        $seed_files = glob($seeds_dir . '*.sql');
        sort($seed_files);

        $seed_success = 0;
        $seed_errors = 0;

        foreach ($seed_files as $file) {
            $filename = basename($file);
            echo "<p>Seeding: <span class='sql-file'>$filename</span>...";

            $sql = file_get_contents($file);

            if ($conn->multi_query($sql)) {
                do {
                    if ($result = $conn->store_result()) {
                        $result->free();
                    }
                } while ($conn->more_results() && $conn->next_result());

                echo " <span class='success'>✅ Done</span></p>";
                $seed_success++;
            } else {
                echo " <span class='error'>❌ Failed</span></p>";
                echo "<p class='error'>Error: " . $conn->error . "</p>";
                $seed_errors++;
            }
        }

        echo "<p class='info'><strong>Seeds:</strong> $seed_success successful, $seed_errors failed</p>";

        // Summary
        $total_errors = $migration_errors + $seed_errors;
        if ($total_errors == 0) {
            echo "<div class='summary'>";
            echo "<h3>✨ Setup Completed Successfully!</h3>";
            echo "<p><strong>Your CashTrack installation is ready to use.</strong></p>";
            echo "<p style='margin-top: 15px;'><strong>Default Admin Credentials:</strong></p>";
            echo "<p style='margin-left: 20px;'>Username: <code>admin</code><br>Password: <code>admin123</code></p>";
            echo "<p style='margin-top: 15px; color: #059669;'><strong>Created:</strong></p>";
            echo "<ul style='margin-left: 40px; margin-top: 10px;'>";
            echo "<li>8 Database Tables</li>";
            echo "<li>4 Subscription Plans</li>";
            echo "<li>16 Default Categories</li>";
            echo "<li>1 Admin Account</li>";
            echo "</ul>";
            echo "<a href='../auth/login' class='btn'>🎉 Go to Login Page</a>";
            echo "</div>";
        } else {
            echo "<div class='summary' style='background: #fef2f2; border-left-color: #ef4444;'>";
            echo "<h3 style='color: #dc2626;'>⚠️ Setup Completed with Errors</h3>";
            echo "<p>There were $total_errors error(s) during setup. Please review the errors above and run this script again.</p>";
            echo "<a href='' class='btn' onclick='location.reload(); return false;'>🔄 Retry Setup</a>";
            echo "</div>";
        }

        $conn->close();
        ?>
    </div>
</body>

</html>