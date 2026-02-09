<?php
// setup.php

// 1. Load functions to get DB connection ($conn) and helpers like url()
require_once __DIR__ . '/includes/functions.php';

// Initialize variables for UI state
$status = 'pending'; // pending, success, error
$message = '';
$details = '';

// 2. Check if default_data.sql exists
$sqlFile = __DIR__ . '/' . ($_ENV['DB_DEFAULT_SQL'] ?? 'default_data.sql');

if (!file_exists($sqlFile)) {
    $status = 'error';
    $message = 'Configuration Error';
    $details = "SQL file not found at: " . htmlspecialchars($sqlFile);
} else {
    // 3. Read the SQL file
    $sql = file_get_contents($sqlFile);
    if ($sql === false) {
        $status = 'error';
        $message = 'Read Error';
        $details = "Could not read the SQL file contents.";
    } else {
        // 4. Execute queries
        try {
            if ($conn->multi_query($sql)) {
                do {
                    // Store first result set
                    if ($result = $conn->store_result()) {
                        $result->free();
                    }
                } while ($conn->more_results() && $conn->next_result());

                $status = 'success';
                $message = 'Database Setup Complete';
                $details = "All tables and default data have been successfully imported.";
            } else {
                $status = 'error';
                $message = 'Database Error';
                $details = $conn->error;
            }
        } catch (Exception $e) {
            $status = 'error';
            $message = 'Exception Occurred';
            $details = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - <?php echo $_ENV['APP_NAME'] ?? 'CashTrack'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#362472',
                        secondary: '#21A284',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full bg-white rounded-2xl ring-1 ring-gray-200 overflow-hidden md:max-w-lg">
        <div class="p-8 text-center">

            <?php if ($status === 'success'): ?>
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                    <i class="ri-check-line text-4xl text-green-600"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($message); ?></h2>
                <p class="text-gray-600 mb-8"><?php echo htmlspecialchars($details); ?></p>

                <div class="space-y-3">
                    <a href="<?php echo url('/'); ?>" class="block w-full py-3 px-4 bg-primary hover:bg-indigo-700 text-white font-medium rounded-lg transition duration-200">
                        Go to Dashboard
                    </a>
                </div>

            <?php elseif ($status === 'error'): ?>
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 mb-6">
                    <i class="ri-error-warning-line text-4xl text-red-600"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($message); ?></h2>
                <div class="bg-red-50 text-red-700 p-4 rounded-lg text-left text-sm mb-8 overflow-x-auto">
                    <code><?php echo htmlspecialchars($details); ?></code>
                </div>

                <div class="space-y-3">
                    <a href="<?php echo url('/setup'); ?>" class="block w-full py-3 px-4 bg-gray-800 hover:bg-gray-900 text-white font-medium rounded-lg transition duration-200">
                        <i class="ri-refresh-line mr-2"></i> Retry Setup
                    </a>
                    <a href="<?php echo url('/'); ?>" class="block w-full py-3 px-4 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium rounded-lg transition duration-200">
                        Back to Home
                    </a>
                </div>

            <?php else: ?>
                <!-- This state should ideally not be reached given logic above, but good fallback -->
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-blue-100 mb-6">
                    <i class="ri-loader-4-line text-4xl text-blue-600 animate-spin"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Processing...</h2>
                <p class="text-gray-600 mb-8">Please wait while we set up your database.</p>
            <?php endif; ?>

        </div>

        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200 text-center">
            <p class="text-xs text-secondary">
                &copy; <?php echo date('Y'); ?> <?php echo $_ENV['APP_NAME'] ?? 'CashTrack'; ?>. All rights reserved.
            </p>
        </div>
    </div>

</body>

</html>