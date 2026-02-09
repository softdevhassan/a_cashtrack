<?php
$user_id = $_APP['user']['id'];
$method = $_SERVER['REQUEST_METHOD'];

// CREATE WALLET
if ($method === 'POST' && !isset($_POST['_method'])) {
    // Check if user can create wallet
    $check = can_create_wallet($user_id);

    if (!$check['allowed']) {
        echo je(['success' => false, 'error' => $check['message']]);
        exit;
    }

    // Validation
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? 'cash');
    $currencyConfig = $_ENV['CURRENCY'] ?? 'PKR, post';
    $defaultCurrency = trim(explode(',', $currencyConfig)[0]);
    $currency = trim($_POST['currency'] ?? $defaultCurrency);
    $initial_balance = floatval($_POST['initial_balance'] ?? 0);
    $color = trim($_POST['color'] ?? '#6366f1');
    $icon = trim($_POST['icon'] ?? 'ri-wallet-3-line');
    $notes = trim($_POST['notes'] ?? '');

    if (empty($name)) {
        echo je(['success' => false, 'error' => 'Wallet name is required']);
        exit;
    }

    // Valid types
    $valid_types = ['cash', 'bank_account', 'mobile_wallet', 'credit_card', 'investment', 'savings'];
    if (!in_array($type, $valid_types)) {
        $type = 'cash';
    }

    // Insert wallet
    $stmt = $conn->prepare("
        INSERT INTO wallets (user_id, name, type, currency, initial_balance, current_balance, color, icon, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        'isssddsss',
        $user_id,
        $name,
        $type,
        $currency,
        $initial_balance,
        $initial_balance,
        $color,
        $icon,
        $notes
    );

    if ($stmt->execute()) {
        $wallet_id = $conn->insert_id;

        // Increment wallet count in subscription
        query("UPDATE subscriptions SET wallet_usage = wallet_usage + 1 WHERE user_id = ? AND status = 'active'", [$user_id]);

        echo je([
            'success' => true,
            'message' => 'Wallet created successfully',
            'wallet_id' => $wallet_id
        ]);
    } else {
        echo je(['success' => false, 'error' => 'Failed to create wallet']);
    }
    exit;
}

// UPDATE WALLET
if ($method === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
    $wallet_id = intval($_POST['wallet_id'] ?? 0);

    // Verify ownership
    $wallet = fetch('wallets', ['id' => $wallet_id, 'user_id' => $user_id], 'one');
    if (!$wallet) {
        echo je(['success' => false, 'error' => 'Wallet not found']);
        exit;
    }

    $name = trim($_POST['name'] ?? $wallet['name']);
    $type = trim($_POST['type'] ?? $wallet['type']);
    $color = trim($_POST['color'] ?? $wallet['color']);
    $icon = trim($_POST['icon'] ?? $wallet['icon']);
    $notes = trim($_POST['notes'] ?? $wallet['notes']);

    $stmt = $conn->prepare("
        UPDATE wallets 
        SET name = ?, type = ?, color = ?, icon = ?, notes = ?
        WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param('sssssii', $name, $type, $color, $icon, $notes, $wallet_id, $user_id);

    if ($stmt->execute()) {
        echo je(['success' => true, 'message' => 'Wallet updated successfully']);
    } else {
        echo je(['success' => false, 'error' => 'Failed to update wallet']);
    }
    exit;
}

// DELETE WALLET
if ($method === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'DELETE') {
    $wallet_id = intval($_POST['wallet_id'] ?? 0);

    // Verify ownership
    $wallet = fetch('wallets', ['id' => $wallet_id, 'user_id' => $user_id], 'one');
    if (!$wallet) {
        echo je(['success' => false, 'error' => 'Wallet not found']);
        exit;
    }

    // Check if wallet has transactions
    $has_txns = fetch('transactions', [], 'one', [], "WHERE (wallet_id = $wallet_id OR to_wallet_id = $wallet_id) LIMIT 1");

    if ($has_txns) {
        echo je([
            'success' => false,
            'error' => 'Cannot delete wallet with existing transactions. Archive it instead.'
        ]);
        exit;
    }

    // Delete wallet
    if (query("DELETE FROM wallets WHERE id = ? AND user_id = ?", [$wallet_id, $user_id])) {
        // Decrement wallet count
        query("UPDATE subscriptions SET wallet_usage = wallet_usage - 1 WHERE user_id = ? AND status = 'active'", [$user_id]);

        echo je(['success' => true, 'message' => 'Wallet deleted successfully']);
    } else {
        echo je(['success' => false, 'error' => 'Failed to delete wallet']);
    }
    exit;
}

// GET WALLETS
if ($method === 'GET') {
    $wallets = fetch('wallets', ['user_id' => $user_id], 'all', [], 'ORDER BY created_at DESC');

    // Calculate total balance
    $total_balance = array_sum(array_column($wallets, 'current_balance'));

    echo je([
        'success' => true,
        'wallets' => $wallets,
        'total_balance' => $total_balance,
        'count' => count($wallets)
    ]);
    exit;
}

http_response_code(405);
echo je(['success' => false, 'error' => 'Method not allowed']);
