<?php
if (!defined('ALLOWED')) die('Direct access not permitted');

$page_title = "Analytics";
$user_id = $_APP['user']['id'];
?>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 md:p-6">
    <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="ri-bar-chart-line text-indigo-600"></i> Analytics & Reports
            </h1>
            <p class="text-gray-600 dark:text-gray-400">Insights into your financial health</p>
        </div>

        <?php show_alert(); ?>

        <!-- Coming Soon -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center shadow-sm">
            <i class="ri-line-chart-line text-6xl text-indigo-600 mb-4"></i>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Analytics Dashboard Coming Soon</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                We're building powerful analytics features including:
            </p>
            <ul class="text-left max-w-md mx-auto space-y-2 mb-6">
                <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                    <i class="ri-check-line text-green-600"></i> Monthly income vs expense charts
                </li>
                <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                    <i class="ri-check-line text-green-600"></i> Category-wise breakdown (pie charts)
                </li>
                <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                    <i class="ri-check-line text-green-600"></i> Weekly & monthly trends
                </li>
                <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                    <i class="ri-check-line text-green-600"></i> Total balance across all wallets
                </li>
                <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                    <i class="ri-check-line text-green-600"></i> Export to PDF/CSV
                </li>
            </ul>
        </div>

    </div>
</div>
