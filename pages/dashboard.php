<?php
$user_id = $_APP['user']['id'];

if (!function_exists('fetch')) {
    require_once __DIR__ . '/../includes/functions.php';
    if (!isset($_APP['user'])) {
        if (!isLoggedIn()) {
            redirect('/auth/login');
            exit;
        }
        $_APP['user'] = $_SESSION['user'];
        $user_id = $_APP['user']['id'];
    }
}
$isSuperUser = isSuperUser();

// Admin Dashboard - System-Wide Analytics
if ($isSuperUser) {
    // System-wide stats for admin
    $stats = [];

    // 1. Total users
    $all_users = fetch('users', [], 'all');
    $stats['total_users'] = count($all_users);
    $stats['active_users'] = count(array_filter($all_users, fn($u) => $u['status'] == 'active'));

    // 2. Total wallets (all users)
    $all_wallets = fetch('wallets', [], 'all');
    $stats['total_wallets'] = count($all_wallets);
    $total_balance = 0;
    foreach ($all_wallets as $wallet) {
        $total_balance += (float)$wallet['current_balance'];
    }
    $stats['total_balance'] = $total_balance;

    // 3. Total transactions (all users)
    $all_transactions = fetch('transactions', [], 'all');
    $stats['total_transactions'] = count($all_transactions);
    $stats['total_income'] = array_sum(array_column(array_filter($all_transactions, fn($t) => $t['type'] == 'income'), 'amount'));
    $stats['total_expense'] = array_sum(array_column(array_filter($all_transactions, fn($t) => $t['type'] == 'expense'), 'amount'));
    $stats['total_transfers'] = count(array_filter($all_transactions, fn($t) => $t['type'] == 'transfer'));

    // 4. Active subscriptions
    $subscriptions = fetch('subscriptions', ['status' => 'active'], 'all');
    $stats['active_subscriptions'] = count($subscriptions);

    // 5. Total revenue (from all active subscriptions)
    $total_revenue = 0;
    foreach ($subscriptions as $sub) {
        $plan = fetch('plans', ['id' => $sub['plan_id']], 'one');
        if ($plan) {
            $total_revenue += (float)$plan['price'];
        }
    }
    $stats['total_revenue'] = $total_revenue;

    // 6. Daily transaction stats for chart (last 7 days)
    $chart_dates = [];
    $chart_income = [];
    $chart_expense = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $chart_dates[] = date('M d', strtotime($date));

        $day_txns = array_filter($all_transactions, fn($t) => date('Y-m-d', strtotime($t['created_at'])) == $date);

        $chart_income[] = array_sum(array_column(array_filter($day_txns, fn($t) => $t['type'] == 'income'), 'amount'));
        $chart_expense[] = array_sum(array_column(array_filter($day_txns, fn($t) => $t['type'] == 'expense'), 'amount'));
    }
} else {
    // Regular user dashboard - Personal stats only
    $stats = [];

    // 1. Wallet stats
    $wallets = fetch('wallets', ['user_id' => $user_id], 'all');
    $stats['total_wallets'] = count($wallets);
    $stats['total_balance'] = 0;
    foreach ($wallets as $wallet) {
        $stats['total_balance'] += (float)$wallet['current_balance'];
    }

    // 2. Transaction stats
    $transactions = fetch('transactions', ['user_id' => $user_id], 'all');
    $stats['total_transactions'] = count($transactions);
    $stats['income_transactions'] = count(array_filter($transactions, fn($t) => $t['type'] == 'income'));
    $stats['expense_transactions'] = count(array_filter($transactions, fn($t) => $t['type'] == 'expense'));

    // 3. Subscription & Plan
    $subscription = fetch('subscriptions', ['user_id' => $user_id, 'status' => 'active'], 'one', [], 'ORDER BY id DESC');
    $plan = null;
    if ($subscription) {
        $plan = fetch('plans', ['id' => $subscription['plan_id']], 'one');
        $stats['transactions_monthly'] = (int)($subscription['transaction_usage'] ?? 0);

        if ($plan) {
            $stats['plan_name'] = $plan['title'];
            $stats['plan_limit'] = $plan['transaction_limit'];
            $stats['plan_used'] = $stats['transactions_monthly'];
            $stats['plan_remaining'] = max(0, $plan['transaction_limit'] - $stats['transactions_monthly']);
            $stats['plan_percentage'] = $plan['transaction_limit'] > 0 ? round(($stats['transactions_monthly'] / $plan['transaction_limit']) * 100) : 0;
        }
    } else {
        $stats['transactions_monthly'] = 0;
    }

    // 4. Recent transactions (last 5)
    $recent_transactions = query("
        SELECT t.*, 
               c.name as category_name, 
               w1.name as from_wallet_name,
               w2.name as to_wallet_name
        FROM transactions t
        LEFT JOIN categories c ON t.category_id = c.id
        LEFT JOIN wallets w1 ON t.wallet_id = w1.id
        LEFT JOIN wallets w2 ON t.to_wallet_id = w2.id
        WHERE t.user_id = ? 
        ORDER BY t.created_at DESC
        LIMIT 5
    ", [$user_id]);

    // 5. Chart data (last 7 days)
    $chart_dates = [];
    $chart_income = [];
    $chart_expense = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $chart_dates[] = date('M d', strtotime($date));

        $day_txns = array_filter($transactions, fn($t) => date('Y-m-d', strtotime($t['created_at'])) == $date);
        $chart_income[] = array_sum(array_column(array_filter($day_txns, fn($t) => $t['type'] == 'income'), 'amount'));
        $chart_expense[] = array_sum(array_column(array_filter($day_txns, fn($t) => $t['type'] == 'expense'), 'amount'));
    }
}

?>

<div class="border-b pb-4 mb-6">
    <h1 class="text-2xl font-bold text-tp">Dashboard</h1>
    <p class="text-tp/70 text-base">Welcome back, <?= e($_APP['user']['name']); ?>!</p>
</div>

<?php if ($isSuperUser): ?>
    <!-- ADMIN DASHBOARD -->

    <!-- System-Wide Stats Grid -->
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" data-aos="fade-up">
        <!-- Total Users -->
        <div class="bg-white border border-ts/30 rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-tp/70">Total Users</p>
                    <h3 class="text-2xl font-bold text-tp"><?= number_format($stats['total_users']); ?></h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="ri-user-line text-xl text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 text-xs text-tp/70">
                <?= number_format($stats['active_users']); ?> active
            </div>
        </div>

        <!-- Total Wallets -->
        <div class="bg-white border border-ts/30 rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-tp/70">Total Wallets</p>
                    <h3 class="text-2xl font-bold text-tp"><?= number_format($stats['total_wallets']); ?></h3>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="ri-wallet-3-line text-xl text-purple-600"></i>
                </div>
            </div>
            <div class="mt-4 text-xs text-tp/70">
                <?= formatCurrency($stats['total_balance']); ?>
            </div>
        </div>

        <!-- Total Transactions -->
        <div class="bg-white border border-ts/30 rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-tp/70">Transactions</p>
                    <h3 class="text-2xl font-bold text-tp"><?= number_format($stats['total_transactions']); ?></h3>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="ri-exchange-line text-xl text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 text-xs text-tp/70">
                <?= number_format($stats['total_transfers']); ?> transfers
            </div>
        </div>

        <!-- Revenue -->
        <div class="bg-white border border-ts/30 rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-tp/70">Total Revenue</p>
                    <h3 class="text-2xl font-bold text-tp"><?= formatCurrency($stats['total_revenue']); ?></h3>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="ri-money-dollar-circle-line text-xl text-orange-600"></i>
                </div>
            </div>
            <div class="mt-4 text-xs text-tp/70">
                <?= number_format($stats['active_subscriptions']); ?> subscriptions
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6" data-aos="fade-up" data-aos-delay="100">
        <!-- Left: Chart -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-ts/30 rounded-2xl">
                <div class="p-4 border-b border-ts/30">
                    <h3 class="text-lg font-semibold text-tp">System Income vs Expense (Last 7 Days)</h3>
                </div>
                <div class="p-6">
                    <div class="h-64">
                        <canvas id="transactionChart"></canvas>
                    </div>
                    <div class="mt-4 flex items-center justify-center gap-6 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-tp/70">Income: <?= formatCurrency($stats['total_income']); ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-tp/70">Expense: <?= formatCurrency($stats['total_expense']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Admin Quick Actions -->
        <div class="space-y-6">
            <div class="bg-white border border-ts/30 rounded-2xl">
                <div class="p-4 border-b border-ts/30">
                    <h3 class="text-lg font-semibold text-tp">Admin Actions</h3>
                </div>
                <div class="p-4 space-y-3">
                    <a href="<?= url('/pages/admin-monitoring'); ?>" class="flex items-center justify-between p-3 border border-ts/30 rounded-lg hover:bg-gray-50 group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200">
                                <i class="ri-line-chart-line text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-tp">Monitoring</h4>
                                <p class="text-xs text-tp/70">System overview</p>
                            </div>
                        </div>
                        <i class="ri-arrow-right-s-line text-tp/30 group-hover:text-tp"></i>
                    </a>

                    <a href="<?= url('/pages/users-management'); ?>" class="flex items-center justify-between p-3 border border-ts/30 rounded-lg hover:bg-gray-50 group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200">
                                <i class="ri-user-settings-line text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-tp">Users</h4>
                                <p class="text-xs text-tp/70">Manage users</p>
                            </div>
                        </div>
                        <i class="ri-arrow-right-s-line text-tp/30 group-hover:text-tp"></i>
                    </a>

                    <a href="<?= url('/pages/plans-management'); ?>" class="flex items-center justify-between p-3 border border-ts/30 rounded-lg hover:bg-gray-50 group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200">
                                <i class="ri-vip-crown-line text-purple-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-tp">Plans</h4>
                                <p class="text-xs text-tp/70">Manage plans</p>
                            </div>
                        </div>
                        <i class="ri-arrow-right-s-line text-tp/30 group-hover:text-tp"></i>
                    </a>

                    <a href="<?= url('/pages/account'); ?>" class="flex items-center justify-between p-3 border border-ts/30 rounded-lg hover:bg-gray-50 group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200">
                                <i class="ri-settings-3-line text-orange-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-tp">Settings</h4>
                                <p class="text-xs text-tp/70">Account settings</p>
                            </div>
                        </div>
                        <i class="ri-arrow-right-s-line text-tp/30 group-hover:text-tp"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- REGULAR USER DASHBOARD -->

    <!-- Quick Stats -->
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" data-aos="fade-up">
        <!-- Total Wallets -->
        <div class="bg-white border border-ts/30 rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-tp/70">Total Wallets</p>
                    <h3 class="text-2xl font-bold text-tp"><?= $stats['total_wallets']; ?></h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="ri-wallet-3-line text-xl text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 text-xs text-tp/70">
                Balance: <?= formatCurrency($stats['total_balance']); ?>
            </div>
        </div>

        <!-- Total Transactions -->
        <div class="bg-white border border-ts/30 rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-tp/70">Transactions</p>
                    <h3 class="text-2xl font-bold text-tp"><?= number_format($stats['total_transactions']); ?></h3>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="ri-exchange-line text-xl text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs">
                <span class="text-green-600">
                    <i class="ri-arrow-up-line"></i> <?= number_format($stats['income_transactions']); ?> income
                </span>
                <span class="text-red-600">
                    <i class="ri-arrow-down-line"></i> <?= number_format($stats['expense_transactions']); ?> expense
                </span>
            </div>
        </div>

        <!-- Plan Usage -->
        <div class="bg-white border border-ts/30 rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-tp/70">Plan Usage</p>
                    <h3 class="text-2xl font-bold text-tp">
                        <?= $plan ? e($plan['title']) : 'No Plan'; ?>
                    </h3>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="ri-price-tag-3-line text-xl text-orange-600"></i>
                </div>
            </div>
            <div class="mt-4">
                <?php if ($plan): ?>
                    <div class="mb-1 flex justify-between text-xs">
                        <span class="text-tp/70">Usage:</span>
                        <span><?= number_format($stats['plan_used']); ?>/<?= number_format($stats['plan_limit']); ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full <?= $stats['plan_percentage'] > 80 ? 'bg-red-500' : 'bg-green-500'; ?>"
                            style="width: <?= min($stats['plan_percentage'], 100); ?>%"></div>
                    </div>
                    <div class="mt-1 text-xs text-tp/70">
                        <?= $stats['plan_remaining']; ?> transactions remaining
                    </div>
                <?php else: ?>
                    <a href="<?= url('/pages/my-plan'); ?>" class="text-accent hover:text-accent/80 text-sm">
                        <i class="ri-external-link-line"></i> Choose a plan
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6" data-aos="fade-up" data-aos-delay="100">
        <!-- Left Column: Chart -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-ts/30 rounded-2xl">
                <div class="p-4 border-b border-ts/30">
                    <h3 class="text-lg font-semibold text-tp">Income vs Expense (Last 7 Days)</h3>
                </div>
                <div class="p-6">
                    <div class="h-64">
                        <canvas id="transactionChart"></canvas>
                    </div>
                    <div class="mt-4 flex items-center justify-center gap-6 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-tp/70">Income</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-tp/70">Expense</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Quick Actions & Recent -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white border border-ts/30 rounded-2xl">
                <div class="p-4 border-b border-ts/30">
                    <h3 class="text-lg font-semibold text-tp">Quick Actions</h3>
                </div>
                <div class="p-4 space-y-3">
                    <a href="<?= url('/pages/wallets'); ?>" class="flex items-center justify-between p-3 border border-ts/30 rounded-lg hover:bg-gray-50 group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200">
                                <i class="ri-wallet-3-line text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-tp">Manage Wallets</h4>
                                <p class="text-xs text-tp/70">Add or edit wallets</p>
                            </div>
                        </div>
                        <i class="ri-arrow-right-s-line text-tp/30 group-hover:text-tp"></i>
                    </a>

                    <a href="<?= url('/pages/transactions'); ?>" class="flex items-center justify-between p-3 border border-ts/30 rounded-lg hover:bg-gray-50 group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200">
                                <i class="ri-add-line text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-tp">Add Transaction</h4>
                                <p class="text-xs text-tp/70">Record income/expense</p>
                            </div>
                        </div>
                        <i class="ri-arrow-right-s-line text-tp/30 group-hover:text-tp"></i>
                    </a>

                    <a href="<?= url('/pages/my-plan'); ?>" class="flex items-center justify-between p-3 border border-ts/30 rounded-lg hover:bg-gray-50 group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200">
                                <i class="ri-vip-crown-line text-purple-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-tp">My Plan</h4>
                                <p class="text-xs text-tp/70">View & upgrade</p>
                            </div>
                        </div>
                        <i class="ri-arrow-right-s-line text-tp/30 group-hover:text-tp"></i>
                    </a>

                    <a href="<?= url('/pages/account'); ?>" class="flex items-center justify-between p-3 border border-ts/30 rounded-lg hover:bg-gray-50 group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200">
                                <i class="ri-settings-3-line text-orange-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-tp">Settings</h4>
                                <p class="text-xs text-tp/70">Account settings</p>
                            </div>
                        </div>
                        <i class="ri-arrow-right-s-line text-tp/30 group-hover:text-tp"></i>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white border border-ts/30 rounded-2xl">
                <div class="p-4 border-b border-ts/30">
                    <h3 class="text-lg font-semibold text-tp">Recent Transactions</h3>
                </div>
                <div class="p-4">
                    <?php if (empty($recent_transactions)): ?>
                        <div class="text-center py-4">
                            <i class="ri-inbox-line text-2xl text-tp/30 mb-2"></i>
                            <p class="text-sm text-tp/70">No transactions yet</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($recent_transactions as $txn): ?>
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full <?= $txn['type'] == 'income' ? 'bg-green-100' : ($txn['type'] == 'expense' ? 'bg-red-100' : 'bg-blue-100'); ?> flex items-center justify-center flex-shrink-0">
                                        <i class="<?= $txn['type'] == 'income' ? 'ri-arrow-down-line text-green-600' : ($txn['type'] == 'expense' ? 'ri-arrow-up-line text-red-600' : 'ri-arrow-right-line text-blue-600'); ?> text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-tp"><?= e($txn['category_name'] ?? ($txn['type'] == 'transfer' ? 'Transfer' : 'Uncategorized')); ?></p>
                                        <p class="text-xs text-tp/70 truncate">
                                            <?php if ($txn['type'] == 'transfer'): ?>
                                                <?= e($txn['from_wallet_name'] ?? 'Wallet'); ?> → <?= e($txn['to_wallet_name'] ?? 'Wallet'); ?>
                                            <?php else: ?>
                                                <?= e($txn['from_wallet_name'] ?? $txn['to_wallet_name'] ?? 'Wallet'); ?>
                                            <?php endif; ?>
                                        </p>
                                        <p class="text-xs text-tp/50">
                                            <?= date('M d, h:i A', strtotime($txn['created_at'])); ?>
                                        </p>
                                    </div>
                                    <div class="text-sm font-medium <?= $txn['type'] == 'income' ? 'text-green-600' : ($txn['type'] == 'expense' ? 'text-red-600' : 'text-blue-600'); ?>">
                                        <?= $txn['type'] == 'income' ? '+' : ($txn['type'] == 'expense' ? '-' : ''); ?><?= formatCurrency($txn['amount']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Transaction Chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('transactionChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= je($chart_dates); ?>,
                datasets: [{
                        label: 'Income',
                        data: <?= je($chart_income); ?>,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Expense',
                        data: <?= je($chart_expense); ?>,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [5, 5]
                        }
                    }
                }
            }
        });
    });
</script>

<?php function page_sc()
{ ?>
    <style>
        #transactionChart {
            width: 100%;
            height: 100%;
        }
    </style>
<?php } ?>