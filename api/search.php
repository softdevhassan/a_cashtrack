<?php

$query = isset($_GET['q']) ? e($_GET['q']) : '';
if (strlen($query) < 2) {
    echo je([]);
    exit;
}

$user_id = $_APP['user']['id'];
$isSuperUser = isSuperUser();
$results = [];

// 1. Search Pages (CashTrack relevant pages)
$pages = [
    ['title' => 'Dashboard', 'url' => '/pages/dashboard', 'icon' => 'ri-dashboard-line', 'keywords' => 'home start stats summary'],
    ['title' => 'Wallets', 'url' => '/pages/wallets', 'icon' => 'ri-wallet-3-line', 'keywords' => 'accounts banks cards money'],
    ['title' => 'Transactions', 'url' => '/pages/transactions', 'icon' => 'ri-exchange-dollar-line', 'keywords' => 'income expense transfer history log'],
    ['title' => 'Categories', 'url' => '/pages/categories', 'icon' => 'ri-folder-reduce-line', 'keywords' => 'tags types grouping'],
    ['title' => 'Reports', 'url' => '/pages/reports', 'icon' => 'ri-bar-chart-line', 'keywords' => 'analytics graphs trends'],
    ['title' => 'My Plan', 'url' => '/pages/my-plan', 'icon' => 'ri-vip-crown-line', 'keywords' => 'billing subscription upgrade pro'],
    ['title' => 'Profile', 'url' => '/pages/account', 'icon' => 'ri-user-settings-line', 'keywords' => 'account settings me'],
];

foreach ($pages as $page) {
    if (stripos($page['title'], $query) !== false || stripos($page['keywords'], $query) !== false) {
        $results[] = [
            'type' => 'Page',
            'title' => $page['title'],
            'subtitle' => 'Go to page',
            'url' => url($page['url']),
            'icon' => $page['icon']
        ];
    }
}

$param_q = "%$query%";

// 2. Search Wallets
$wallets = query("SELECT id, name, currency FROM wallets WHERE user_id = ? AND name LIKE ? LIMIT 5", [$user_id, $param_q]);

if ($wallets && $wallets instanceof mysqli_result) {
    while ($row = $wallets->fetch_assoc()) {
        $results[] = [
            'type' => 'Wallet',
            'title' => $row['name'],
            'subtitle' => 'Wallet (' . $row['currency'] . ')',
            // Since we don't have a dedicated single wallet view, maybe redirect to wallets page or open edit modal?
            // For now, redirect to wallets page.
            'url' => url('/pages/wallets'),
            'icon' => 'ri-wallet-line'
        ];
    }
}


// 3. Search Transactions (by Note or Amount)
// Casting amount to string for LIKE search
$txns = query("SELECT id, type, amount, note, transaction_date FROM transactions WHERE user_id = ? AND (note LIKE ? OR CAST(amount AS CHAR) LIKE ?) ORDER BY transaction_date DESC LIMIT 5", [$user_id, $param_q, $param_q]);

if ($txns && $txns instanceof mysqli_result) {
    while ($row = $txns->fetch_assoc()) {
        $action = ucfirst($row['type']);
        $results[] = [
            'type' => 'Transaction',
            'title' => $action . ': ' . number_format($row['amount']),
            'subtitle' => $row['note'] ? $row['note'] : $row['transaction_date'],
            'url' => url('/pages/transactions?id=' . $row['id']), // Assuming transactions page can highlight ID
            'icon' => ($row['type'] === 'income' ? 'ri-arrow-up-circle-line' : ($row['type'] === 'expense' ? 'ri-arrow-down-circle-line' : 'ri-exchange-line'))
        ];
    }
}

// 4. Search Categories
$cats = query("SELECT id, name, type FROM categories WHERE user_id = ? AND name LIKE ? LIMIT 5", [$user_id, $param_q]);

if ($cats && $cats instanceof mysqli_result) {
    while ($row = $cats->fetch_assoc()) {
        $results[] = [
            'type' => 'Category',
            'title' => $row['name'],
            'subtitle' => ucfirst($row['type']),
            'url' => url('/pages/categories'),
            'icon' => 'ri-price-tag-3-line'
        ];
    }
}

// 5. Search Users (Admin Only)
if ($isSuperUser) {
    $users = query("SELECT id, name, email FROM users WHERE (name LIKE ? OR email LIKE ?) LIMIT 5", [$param_q, $param_q]);
    if ($users && $users instanceof mysqli_result) {
        while ($row = $users->fetch_assoc()) {
            $results[] = [
                'type' => 'User',
                'title' => $row['name'],
                'subtitle' => $row['email'],
                'url' => url('/pages/users?id=' . $row['id']),
                'icon' => 'ri-user-settings-line'
            ];
        }
    }

    // Add Admin-specific pages
    $adminPages = [
        ['title' => 'Admin: Users', 'url' => '/pages/users', 'icon' => 'ri-admin-line', 'keywords' => 'manage users'],
        ['title' => 'Admin: Plans', 'url' => '/pages/plans-management', 'icon' => 'ri-vip-diamond-line', 'keywords' => 'manage plans pricing'],
        ['title' => 'Admin: Monitoring', 'url' => '/pages/admin-monitoring', 'icon' => 'ri-spy-line', 'keywords' => 'monitor institute'],
    ];

    foreach ($adminPages as $page) {
        if (stripos($page['title'], $query) !== false || stripos($page['keywords'], $query) !== false) {
            $results[] = [
                'type' => 'Page',
                'title' => $page['title'],
                'subtitle' => 'Admin Page',
                'url' => url($page['url']),
                'icon' => $page['icon']
            ];
        }
    }
}

echo je($results);
exit;
