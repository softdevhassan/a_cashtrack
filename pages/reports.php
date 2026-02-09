<?php
// Ensure user is logged in
if (!isset($_APP['user'])) {
    redirect('/auth/login');
}

$user_id = $_APP['user']['id'];
$isSuperUser = isSuperUser();

// Fetch Stats
$stats = [];

// 1. Total Users (Admin Only)
if ($isSuperUser) {
    $users = fetch('users', [], 'all');
    $stats['total_users'] = count($users);
    $stats['active_users'] = count(array_filter($users, fn($u) => !(bool)$u['locked']));
} else {
    $stats['total_users'] = 0;
    $stats['active_users'] = 0;
}

// 2. Total Emails Sent (All time)
$total_sent_sql = "SELECT COUNT(*) as count FROM email_queue WHERE status = 'sent'";
if (!$isSuperUser) $total_sent_sql .= " AND user_id = $user_id";
$total_sent_query = mysqli_fetch_assoc(query($total_sent_sql));
$stats['total_sent'] = $total_sent_query['count'] ?? 0;

// 3. Emails Sent Today
$today = date('Y-m-d');
$sent_today_sql = "SELECT COUNT(*) as count FROM email_queue WHERE status = 'sent' AND DATE(sent_at) = '$today'";
if (!$isSuperUser) $sent_today_sql .= " AND user_id = $user_id";
$sent_today_query = mysqli_fetch_assoc(query($sent_today_sql));
$stats['sent_today'] = $sent_today_query['count'] ?? 0;

// 4. Pending Emails
$pending_sql = "SELECT COUNT(*) as count FROM email_queue WHERE status = 'pending'";
if (!$isSuperUser) $pending_sql .= " AND user_id = $user_id";
$pending_query = mysqli_fetch_assoc(query($pending_sql));
$stats['pending_emails'] = $pending_query['count'] ?? 0;

// 5. Failed Emails
$failed_sql = "SELECT COUNT(*) as count FROM email_queue WHERE status = 'failed'";
if (!$isSuperUser) $failed_sql .= " AND user_id = $user_id";
$failed_query = mysqli_fetch_assoc(query($failed_sql));
$stats['failed_emails'] = $failed_query['count'] ?? 0;

// 6. Active Campaigns
$active_campaigns_sql = "SELECT COUNT(*) as count FROM campaigns WHERE status IN ('running', 'processing')";
if (!$isSuperUser) $active_campaigns_sql .= " AND user_id = $user_id";
$active_campaigns_query = mysqli_fetch_assoc(query($active_campaigns_sql));
$stats['active_campaigns'] = $active_campaigns_query['count'] ?? 0;



// 8. Recent Logs (last 5)
$logs_sql = "SELECT cl.*, c.title as campaign_title FROM campaign_logs cl LEFT JOIN campaigns c ON cl.campaign_id = c.id";
if (!$isSuperUser) $logs_sql .= " WHERE c.user_id = $user_id";
$logs_sql .= " ORDER BY cl.created_at DESC LIMIT 5";
$logs = query($logs_sql);

// 8b. Worker Logs (from worker.log file) - Admin Only
$worker_logs = [];
if ($isSuperUser) {
    $worker_log_path = __DIR__ . '/../workers/worker.log';
    if (file_exists($worker_log_path)) {
        // Read file into array
        $file_lines = file($worker_log_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($file_lines !== false) {
            // Get last 20 lines
            $last_lines = array_slice($file_lines, -20);
            // Reverse to show newest first (descending)
            $worker_logs = array_reverse($last_lines);
        }
    }
}

// 9. Plan Distribution - Admin Only
$plan_distribution = null;
if ($isSuperUser) {
    $plan_distribution = query("
        SELECT p.title, COUNT(s.id) as count 
        FROM plans p 
        LEFT JOIN subscriptions s ON p.id = s.plan_id AND s.status = 'active'
        GROUP BY p.id
    ");
}

// Export Report Data (CSV)
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    // Filename
    $filename = "system_report_" . date('Y-m-d') . ".csv";

    // Headers
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // Stats Section
    fputcsv($output, ['--- SYSTEM STATISTICS ---']);
    fputcsv($output, ['Metric', 'Value']);
    foreach ($stats as $key => $val) {
        fputcsv($output, [ucwords(str_replace('_', ' ', $key)), $val]);
    }
    fputcsv($output, []); // Empty line

    // Logs Section
    fputcsv($output, ['--- RECENT LOGS ---']);
    fputcsv($output, ['Time', 'Campaign', 'Message']);

    // Fetch more logs for export (e.g., last 100)
    $export_logs_sql = "SELECT cl.*, c.title as campaign_title FROM campaign_logs cl LEFT JOIN campaigns c ON cl.campaign_id = c.id";
    if (!$isSuperUser) $export_logs_sql .= " WHERE c.user_id = $user_id";
    $export_logs_sql .= " ORDER BY cl.created_at DESC LIMIT 100";
    $export_logs = query($export_logs_sql);

    foreach ($export_logs as $log) {
        fputcsv($output, [
            $log['created_at'],
            $log['campaign_title'] ?? 'Unknown',
            $log['message']
        ]);
    }

    fclose($output);
    exit;
}
?>

<div class="space-y-6">
    <div class="flex items-center justify-between no-print">
        <h1 class="text-2xl font-bold text-tp">System Reports & Performance</h1>
        <div class="flex space-x-2">
            <div class="relative inline-block text-left" id="export-dropdown">
                <button onclick="toggleExportMenu()" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center gap-2">
                    <i class="ri-download-line"></i> Export Data
                </button>
                <div id="export-menu" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1" role="menu">
                        <button onclick="exportReport('csv')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <i class="ri-file-text-line text-green-600"></i> Export as CSV
                        </button>
                        <button onclick="exportReport('xlsx')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <i class="ri-file-excel-line text-green-600"></i> Export as Excel
                        </button>
                    </div>
                </div>
            </div>
            <button onclick="window.print()" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center gap-2">
                <i class="ri-printer-line"></i> Print Report
            </button>
            <button onclick="location.reload()" class="px-4 py-2 bg-accent text-white rounded-lg text-sm font-medium hover:bg-accent-dark flex items-center gap-2">
                <i class="ri-refresh-line"></i> Refresh Data
            </button>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Sent -->
        <div class="bg-white p-4 sm:p-6 rounded-2xl border border-gray-200" data-aos="fade-up" data-aos-delay="0">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-green-50 rounded-xl">
                    <i class="ri-mail-send-line text-xl text-green-600"></i>
                </div>
                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-green-50 text-green-600">All Time</span>
            </div>
            <h3 class="text-2xl font-bold text-tp mb-1"><?php echo number_format($stats['total_sent']); ?></h3>
            <p class="text-sm text-ts">Total Emails Sent</p>
        </div>

        <!-- Sent Today -->
        <div class="bg-white p-4 sm:p-6 rounded-2xl border border-gray-200" data-aos="fade-up" data-aos-delay="100">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-50 rounded-xl">
                    <i class="ri-calendar-check-line text-xl text-blue-600"></i>
                </div>
                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-600">Today</span>
            </div>
            <h3 class="text-2xl font-bold text-tp mb-1"><?php echo number_format($stats['sent_today']); ?></h3>
            <p class="text-sm text-ts">Emails Sent Today</p>
        </div>

        <!-- Pending Queue -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200" data-aos="fade-up" data-aos-delay="200">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-yellow-50 rounded-xl">
                    <i class="ri-hourglass-line text-xl text-yellow-600"></i>
                </div>
                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-yellow-50 text-yellow-600">Queue</span>
            </div>
            <h3 class="text-2xl font-bold text-tp mb-1"><?php echo number_format($stats['pending_emails']); ?></h3>
            <p class="text-sm text-ts">Pending Emails</p>
        </div>
    </div>

    <!-- Full Width Charts & Logs -->
    <div class="space-y-6">
        <!-- Main Chart Area -->
        <div class="w-full bg-white p-4 sm:p-6 rounded-2xl border border-gray-200" data-aos="fade-up" data-aos-delay="400">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-tp">Email Traffic (Last 7 Days)</h3>
            </div>
            <div class="h-80 w-full relative">
                <!-- Chart.js Canvas Placeholder -->
                <canvas id="trafficChart"></canvas>
            </div>
        </div>

        <!-- Plan Distribution (Admin Only) -->
        <?php if ($isSuperUser && $plan_distribution): ?>
            <div class="w-full bg-white p-6 rounded-2xl border border-gray-200" data-aos="fade-up" data-aos-delay="500">
                <h3 class="text-lg font-bold text-tp mb-6">User Plan Distribution</h3>
                <div class="space-y-4">
                    <?php while ($row = $plan_distribution->fetch_assoc()): ?>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-tp"><?php echo htmlspecialchars($row['title']); ?></span>
                                <span class="text-ts"><?php echo $row['count']; ?> users</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-accent h-2 rounded-full" style="width: <?php echo ($stats['active_users'] > 0) ? ($row['count'] / $stats['active_users'] * 100) : 0; ?>%"></div>
                            </div>
                        </div>
                    <?php endwhile; ?>

                    <div class="pt-4 border-t border-gray-100 mt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-ts">Total Active Users</span>
                            <span class="text-lg font-bold text-tp"><?php echo number_format($stats['active_users']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Logs Section -->
<div class="space-y-6 mt-8">
    <!-- Campaign Logs (Visible to All) -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200" data-aos="fade-up" data-aos-delay="600">
        <h3 class="text-lg font-bold text-tp mb-4">Recent Campaign Activity</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-ts uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Time</th>
                        <th class="px-4 py-3">Campaign</th>
                        <th class="px-4 py-3">Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($logs && $logs->num_rows > 0): ?>
                        <?php while ($log = $logs->fetch_assoc()): ?>
                            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                                <td class="px-4 py-3 text-ts"><?php echo date('M d H:i', strtotime($log['created_at'])); ?></td>
                                <td class="px-4 py-3 font-medium text-tp"><?php echo htmlspecialchars($log['campaign_title'] ?? 'Unknown'); ?></td>
                                <td class="px-4 py-3 text-tp"><?php echo htmlspecialchars($log['message']); ?></td>

                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-ts">No recent activity logs found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Worker Logs (Admin Only) -->
    <?php if ($isSuperUser && !empty($worker_logs)): ?>
        <div class="bg-gray-900 p-6 rounded-2xl border border-gray-800" data-aos="fade-up" data-aos-delay="700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white">
                    <i class="ri-terminal-box-line mr-2"></i> Worker Logs
                </h3>
                <span class="text-xs text-gray-400 bg-gray-800 px-2 py-1 rounded">Admin Only</span>
            </div>
            <div class="font-mono text-xs text-green-400 bg-black/50 p-4 rounded-xl h-64 overflow-y-auto custom-scrollbar">
                <?php foreach ($worker_logs as $line): ?>
                    <div class="mb-1 whitespace-pre-wrap hover:bg-white/5 p-0.5 rounded"><?php echo htmlspecialchars($line); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<style>
    @media print {
        @page {
            size: auto;
            margin: 10mm;
        }

        body {
            background: white !important;
            color: black !important;
        }

        /* Hide Sidebar, Navbar, and other non-print elements */
        /* Assuming common class names based on standard layouts */
        aside,
        nav,
        header,
        .sidebar,
        .left-sidebar,
        .top-bar,
        .navbar,
        .no-print {
            display: none !important;
        }

        /* Expand Main Content */
        main,
        .main-content,
        #app {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: none !important;
        }

        /* Ensure grid layouts work reasonably well in print */
        .grid {
            display: block !important;
        }

        .grid>div {
            margin-bottom: 20px;
            page-break-inside: avoid;
            border: 1px solid #ddd !important;
        }

        /* Hide scrollbars in print */
        .overflow-x-auto,
        .overflow-y-auto {
            overflow: visible !important;
            height: auto !important;
            max-height: none !important;
        }
    }
</style>

<script>
    // Stats data from PHP
    <?php
    $js_stats = [
        'Total Emails Sent' => $stats['total_sent'],
        'Emails Sent Today' => $stats['sent_today'],
        'Pending Emails' => $stats['pending_emails'],
        'Failed Emails' => $stats['failed_emails'],
        'Active Campaigns' => $stats['active_campaigns']
    ];

    if ($isSuperUser) {
        $js_stats['Total Users'] = $stats['total_users'];
        $js_stats['Active Users'] = $stats['active_users'];
    }
    ?>
    const systemStats = <?= je($js_stats); ?>;


    function toggleExportMenu() {
        const menu = document.getElementById('export-menu');
        menu.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('export-dropdown');
        const menu = document.getElementById('export-menu');
        if (!dropdown.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });

    function exportReport(type) {
        // 1. Prepare Stats Data
        const statsData = [
            ['Metric', 'Value']
        ];
        for (const [key, value] of Object.entries(systemStats)) {
            statsData.push([key, value]);
        }

        // 2. Prepare Logs Data
        const logsData = [
            ['Timestamp', 'Campaign', 'Message']
        ];
        // Select all rows from the logs table
        const rows = document.querySelectorAll('table tbody tr');
        rows.forEach(row => {
            const cols = row.querySelectorAll('td');
            if (cols.length >= 2) { // Ensure it's a valid data row
                // Clean up text content (remove extra spaces/newlines)
                const timestamp = cols[0].innerText.trim();
                const contentCell = cols[1];
                const campaignTitle = contentCell.querySelector('.text-tp')?.innerText.trim() || 'Unknown';
                // Get the message text node (direct child text of td, usually after the div)
                // Or just get full text and remove the title
                let fullText = contentCell.innerText.trim();
                let message = fullText.replace(campaignTitle, '').trim();

                logsData.push([timestamp, campaignTitle, message]);
            }
        });

        // 3. Create Workbook
        const wb = XLSX.utils.book_new();

        // Add Stats Sheet
        const wsStats = XLSX.utils.aoa_to_sheet(statsData);
        XLSX.utils.book_append_sheet(wb, wsStats, "System Stats");

        // Add Logs Sheet
        const wsLogs = XLSX.utils.aoa_to_sheet(logsData);
        XLSX.utils.book_append_sheet(wb, wsLogs, "System Logs");

        // 4. Export
        const date = new Date().toISOString().split('T')[0];
        const fileName = `System_Report_${date}.${type}`;
        XLSX.writeFile(wb, fileName);

        // Hide menu
        document.getElementById('export-menu').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('trafficChart').getContext('2d');

        // Mock data - in production, fetch real data via PHP/AJAX
        const labels = Array.from({
            length: 7
        }, (_, i) => {
            const d = new Date();
            d.setDate(d.getDate() - (6 - i));
            return d.toLocaleDateString('en-US', {
                weekday: 'short'
            });
        });

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Emails Sent',
                    data: [12, 19, 3, 5, 2, 3, <?php echo $stats['sent_today']; ?>], // Last value is real
                    borderColor: '#5140A2',
                    backgroundColor: 'rgba(81, 64, 162, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            borderDash: [2, 2]
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>