<?php
// Only allow admin users
if (!isSuperUser()) {
    redirect('/auth/login');
}

// Get overall stats
$stats = [];

// 1. User stats
$all_users = fetch('users', [], 'all');
$stats['total_users'] = count($all_users);
$stats['active_users'] = count(array_filter($all_users, fn($u) => $u['status'] == 'active'));

// 2. Subscription stats
$subscriptions = fetch('subscriptions', [], 'all');
$stats['total_subscriptions'] = count($subscriptions);
$stats['active_subscriptions'] = count(array_filter($subscriptions, fn($s) => $s['status'] == 'active'));

// 3. Transaction stats (all users)
$all_transactions = fetch('transactions', [], 'all');
$stats['total_transactions'] = count($all_transactions);
$stats['total_income'] = array_sum(array_column(array_filter($all_transactions, fn($t) => $t['type'] == 'income'), 'amount'));
$stats['total_expense'] = array_sum(array_column(array_filter($all_transactions, fn($t) => $t['type'] == 'expense'), 'amount'));

// 4. Wallet stats (all users)
$all_wallets = fetch('wallets', [], 'all');
$stats['total_wallets'] = count($all_wallets);

// 5. Plans breakdown
$plans = fetch('plans', [], 'all');
$plan_usage = [];
foreach ($plans as $plan) {
    $subs = array_filter($subscriptions, fn($s) => $s['plan_id'] == $plan['id'] && $s['status'] == 'active');
    $plan_usage[] = [
        'plan_name' => $plan['title'],
        'subscribers' => count($subs),
        'revenue' => count($subs) * $plan['price']
    ];
}

// 6. Recent users (last 10)
$recent_users = array_slice(array_reverse($all_users), 0, 10);

?>

<div class="border-b pb-4 mb-6">
    <h1 class="text-2xl font-bold text-tp">Admin Monitoring</h1>
    <p class="text-tp/70 text-base">System overview and user statistics</p>
</div>

<!-- Stats Grid -->
<div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" data-aos="fade-up">
    <!-- Total Users -->
    <div class="bg-white border border-ts/30 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-tp/70">Total Users</p>
                <h3 class="text-2xl font-bold text-tp"><?= $stats['total_users']; ?></h3>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="ri-user-line text-xl text-blue-600"></i>
            </div>
        </div>
        <div class="mt-4 text-xs text-tp/70">
            <?= $stats['active_users']; ?> active
        </div>
    </div>

    <!-- Active Subscriptions -->
    <div class="bg-white border border-ts/30 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-tp/70">Active Subscriptions</p>
                <h3 class="text-2xl font-bold text-tp"><?= $stats['active_subscriptions']; ?></h3>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="ri-vip-crown-line text-xl text-green-600"></i>
            </div>
        </div>
        <div class="mt-4 text-xs text-tp/70">
            <?= $stats['total_subscriptions']; ?> total
        </div>
    </div>

    <!-- Total Transactions -->
    <div class="bg-white border border-ts/30 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-tp/70">Total Transactions</p>
                <h3 class="text-2xl font-bold text-tp"><?= number_format($stats['total_transactions']); ?></h3>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="ri-exchange-line text-xl text-purple-600"></i>
            </div>
        </div>
    </div>

    <!-- Total Wallets -->
    <div class="bg-white border border-ts/30 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-tp/70">Total Wallets</p>
                <h3 class="text-2xl font-bold text-tp"><?= number_format($stats['total_wallets']); ?></h3>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="ri-wallet-3-line text-xl text-orange-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Two Column Layout -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6" data-aos="fade-up" data-aos-delay="100">
    <!-- Left: Plan Usage -->
    <div class="lg:col-span-2 bg-white border border-ts/30 rounded-2xl">
        <div class="p-4 border-b border-ts/30">
            <h3 class="text-lg font-semibold text-tp">Plan Distribution</h3>
        </div>
        <div class="p-6">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-ts/30">
                        <th class="text-left text-sm font-medium text-tp/70 py-2">Plan</th>
                        <th class="text-center text-sm font-medium text-tp/70 py-2">Subscribers</th>
                        <th class="text-right text-sm font-medium text-tp/70 py-2">Monthly Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($plan_usage)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-sm text-tp/70 py-4">No data available</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($plan_usage as $usage): ?>
                            <tr class="border-b border-ts/10">
                                <td class="py-3 text-sm text-tp"><?= e($usage['plan_name']); ?></td>
                                <td class="py-3 text-sm text-center text-tp"><?= $usage['subscribers']; ?></td>
                                <td class="py-3 text-sm text-right font-medium text-tp"><?= formatCurrency($usage['revenue']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Recent Users -->
    <div class="bg-white border border-ts/30 rounded-2xl">
        <div class="p-4 border-b border-ts/30">
            <h3 class="text-lg font-semibold text-tp">Recent Users</h3>
        </div>
        <div class="p-4">
            <?php if (empty($recent_users)): ?>
                <div class="text-center py-4">
                    <i class="ri-user-line text-2xl text-tp/30 mb-2"></i>
                    <p class="text-sm text-tp/70">No users found</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($recent_users as $user): ?>
                        <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                            <img src="<?= asset($user['image'] ?? 'images/default-avatar.webp'); ?>"
                                class="w-10 h-10 rounded-full border border-ts/20">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-tp truncate"><?= e($user['name']); ?></p>
                                <p class="text-xs text-tp/70 truncate"><?= e($user['email']); ?></p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full <?= $user['status'] == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <?= ucfirst($user['status']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Note -->
<div class="bg-blue-50 border border-blue-100 rounded-2xl p-4" data-aos="fade-up">
    <div class="flex items-start gap-3">
        <i class="ri-information-line text-blue-600 text-xl"></i>
        <div>
            <h4 class="font-medium text-tp mb-1">Privacy Note</h4>
            <p class="text-sm text-tp/70">
                This dashboard shows aggregated statistics only. Individual transaction details and wallet balances are private and not accessible to administrators.
            </p>
        </div>
    </div>
</div>