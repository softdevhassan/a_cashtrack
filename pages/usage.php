<?php
$user_id = $_APP['user']['id'];
$isSuperUser = isSuperUser();
$thirty_days_ago = date('Y-m-d', strtotime('-30 days'));

// Query modification for Super Users
$user_condition = $isSuperUser ? "1=1" : "user_id = $user_id";

$monthly_sent_res = mysqli_fetch_all(query("
    SELECT COUNT(*) AS count 
    FROM email_queue 
    WHERE $user_condition
    AND status = 'sent' 
    AND created_at >= ?
", [$thirty_days_ago]), MYSQLI_ASSOC);
$monthly_sent = $monthly_sent_res[0]['count'] ?? 0;
$pending_res = mysqli_fetch_all(query("
    SELECT COUNT(*) AS count 
    FROM email_queue 
    WHERE $user_condition
    AND status = 'pending'
"), MYSQLI_ASSOC);
$pending_count = $pending_res[0]['count'] ?? 0;
$failed_7_res = mysqli_fetch_all(query("
    SELECT COUNT(*) AS count 
    FROM email_queue 
    WHERE $user_condition
    AND status = 'failed' 
    AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
"), MYSQLI_ASSOC);
$failed_7 = $failed_7_res[0]['count'] ?? 0;
$daily_stats_rows = query("
    SELECT 
        DATE(created_at) AS date,
        COUNT(CASE WHEN status = 'sent' THEN 1 END) AS sent,
        COUNT(CASE WHEN status = 'failed' THEN 1 END) AS failed,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending
    FROM email_queue
    WHERE $user_condition
    AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date ASC
");
$daily_stats = [];
if ($daily_stats_rows && $daily_stats_rows instanceof mysqli_result) {
    while ($row = $daily_stats_rows->fetch_assoc()) {
        $daily_stats[] = $row;
    }
}
?>

<div class="border-b pb-4 mb-6">
    <h1 class="text-2xl font-bold text-tp">Usage</h1>
    <p class="text-tp/70 text-base">Track email sending statistics</p>
    <?= show_alert(); ?>
</div>

<div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" data-aos="fade-up">
    <div class="bg-white border border-ts/30 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-tp/70">Sent (30 days)</p>
                <h3 class="text-2xl font-bold text-tp"><?= number_format($monthly_sent); ?></h3>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="ri-mail-send-line text-xl text-green-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white border border-ts/30 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-tp/70">Pending</p>
                <h3 class="text-2xl font-bold text-tp"><?= number_format($pending_count); ?></h3>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                <i class="ri-time-line text-xl text-amber-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white border border-ts/30 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-tp/70">Failed (7 days)</p>
                <h3 class="text-2xl font-bold text-tp"><?= number_format($failed_7); ?></h3>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="ri-close-circle-line text-xl text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<div class="mb-6 p-4 bg-white border border-ts/30 rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="200">
    <div class="p-4 border-b border-ts/30">
        <h3 class="text-lg font-semibold text-tp">Daily Activity (7 days)</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-ts/30">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Date</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Sent</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Failed</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Pending</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-ts/30">
                <?php if (empty($daily_stats)): ?>
                    <tr>
                        <td colspan="4" class="px-4 sm:px-6 py-4 text-center text-sm text-tp/70">
                            <div class="py-8">
                                <i class="ri-bar-chart-box-line text-3xl text-tp/30 mb-2"></i>
                                <p>No activity recorded</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daily_stats as $d): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 sm:px-6 py-3 text-sm text-tp"><?= date('d M Y', strtotime($d['date'])); ?></td>
                            <td class="px-4 sm:px-6 py-3 text-sm font-medium text-green-700"><?= (int)($d['sent'] ?? 0); ?></td>
                            <td class="px-4 sm:px-6 py-3 text-sm font-medium text-red-700"><?= (int)($d['failed'] ?? 0); ?></td>
                            <td class="px-4 sm:px-6 py-3 text-sm font-medium text-amber-700"><?= (int)($d['pending'] ?? 0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
