<?php
$user_id = $_APP['user']['id'];
$method = $_SERVER['REQUEST_METHOD'];

// CREATE TRANSACTION
if ($method === 'POST' && !isset($_POST['_method'])) {
    $type = trim($_POST['type'] ?? '');

    // Handle transfer separately (double-entry)
    if ($type === 'transfer') {
        $from_wallet_id = intval($_POST['from_wallet_id'] ?? 0);
        $to_wallet_id = intval($_POST['to_wallet_id'] ?? 0);
        $amount = floatval($_POST['amount'] ?? 0);
        $note = trim($_POST['note'] ?? '');

        if ($amount <= 0) {
            echo je(['success' => false, 'error' => 'Amount must be greater than 0']);
            exit;
        }

        if ($from_wallet_id === $to_wallet_id) {
            echo je(['success' => false, 'error' => 'Cannot transfer to same wallet']);
            exit;
        }

        $result = create_transfer($user_id, $from_wallet_id, $to_wallet_id, $amount, $note);
        echo je($result);
        exit;
    }

    // Regular transaction (income/expense)
    $check = can_create_transaction($user_id);

    if (!$check['allowed']) {
        echo je(['success' => false, 'error' => $check['message']]);
        exit;
    }

    // Validation
    if (!in_array($type, ['income', 'expense'])) {
        echo je(['success' => false, 'error' => 'Invalid transaction type']);
        exit;
    }

    $amount = floatval($_POST['amount'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0) ?: null;
    $wallet_id = intval($_POST['wallet_id'] ?? 0);
    $note = trim($_POST['note'] ?? '');
    $transaction_date = trim($_POST['transaction_date'] ?? date('Y-m-d'));

    if ($amount <= 0) {
        echo je(['success' => false, 'error' => 'Amount must be greater than 0']);
        exit;
    }

    if ($wallet_id <= 0) {
        echo je(['success' => false, 'error' => 'Please select a wallet']);
        exit;
    }

    // Verify wallet ownership
    $wallet = fetch('wallets', ['id' => $wallet_id, 'user_id' => $user_id], 'one');
    if (!$wallet) {
        echo je(['success' => false, 'error' => 'Invalid wallet']);
        exit;
    }

    // For expense, check sufficient balance
    if ($type === 'expense' && $wallet['current_balance'] < $amount) {
        echo je(['success' => false, 'error' => 'Insufficient balance in wallet']);
        exit;
    }

    $conn->begin_transaction();

    try {
        // Insert transaction
        $from_id = ($type === 'expense') ? $wallet_id : null;
        $to_id = ($type === 'income') ? $wallet_id : null;

        $stmt = $conn->prepare("
            INSERT INTO transactions (user_id, type, amount, category_id, wallet_id, to_wallet_id, note, transaction_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('isdiiiss', $user_id, $type, $amount, $category_id, $from_id, $to_id, $note, $transaction_date);
        $stmt->execute();
        $transaction_id = $conn->insert_id;

        // Update wallet balance
        if ($type === 'income') {
            $conn->query("UPDATE wallets SET current_balance = current_balance + $amount WHERE id = $wallet_id");
        } else {
            $conn->query("UPDATE wallets SET current_balance = current_balance - $amount WHERE id = $wallet_id");
        }

        // Increment transaction usage
        $conn->query("UPDATE subscriptions SET transaction_usage = transaction_usage + 1 WHERE user_id = $user_id AND status = 'active'");

        $conn->commit();

        echo je([
            'success' => true,
            'message' => 'Transaction created successfully',
            'transaction_id' => $transaction_id
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo je(['success' => false, 'error' => 'Transaction failed: ' . $e->getMessage()]);
    }
    exit;
}

// UPDATE TRANSACTION
if ($method === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
    $transaction_id = intval($_POST['transaction_id'] ?? 0);

    // Verify ownership
    $txn = fetch('transactions', ['id' => $transaction_id, 'user_id' => $user_id], 'one');
    if (!$txn) {
        echo je(['success' => false, 'error' => 'Transaction not found']);
        exit;
    }

    // For simplicity, only allow editing note and date
    $note = trim($_POST['note'] ?? $txn['note']);
    $transaction_date = trim($_POST['transaction_date'] ?? $txn['transaction_date']);

    $stmt = $conn->prepare("
        UPDATE transactions 
        SET note = ?, transaction_date = ?
        WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param('ssii', $note, $transaction_date, $transaction_id, $user_id);

    if ($stmt->execute()) {
        echo je(['success' => true, 'message' => 'Transaction updated successfully']);
    } else {
        echo je(['success' => false, 'error' => 'Failed to update transaction']);
    }
    exit;
}

// DELETE TRANSACTION
if ($method === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'DELETE') {
    $transaction_id = intval($_POST['transaction_id'] ?? 0);

    // Verify ownership
    $txn = fetch('transactions', ['id' => $transaction_id, 'user_id' => $user_id], 'one');
    if (!$txn) {
        echo je(['success' => false, 'error' => 'Transaction not found']);
        exit;
    }

    $conn->begin_transaction();

    try {
        // Reverse wallet balance changes
        if ($txn['type'] === 'income' && $txn['to_wallet_id']) {
            $conn->query("UPDATE wallets SET current_balance = current_balance - {$txn['amount']} WHERE id = {$txn['to_wallet_id']}");
        } elseif ($txn['type'] === 'expense' && $txn['wallet_id']) {
            $conn->query("UPDATE wallets SET current_balance = current_balance + {$txn['amount']} WHERE id = {$txn['wallet_id']}");
        } elseif ($txn['type'] === 'transfer') {
            // Reverse transfer
            if ($txn['wallet_id']) {
                $conn->query("UPDATE wallets SET current_balance = current_balance + {$txn['amount']} WHERE id = {$txn['wallet_id']}");
            }
            if ($txn['to_wallet_id']) {
                $conn->query("UPDATE wallets SET current_balance = current_balance - {$txn['amount']} WHERE id = {$txn['to_wallet_id']}");
            }
        }

        // Delete transaction
        $conn->query("DELETE FROM transactions WHERE id = $transaction_id AND user_id = $user_id");

        // Decrement usage (transfers count as 2)
        $decrement = ($txn['type'] === 'transfer') ? 2 : 1;
        $conn->query("UPDATE subscriptions SET transaction_usage = transaction_usage - $decrement WHERE user_id = $user_id AND status = 'active'");

        $conn->commit();

        echo je(['success' => true, 'message' => 'Transaction deleted successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        echo je(['success' => false, 'error' => 'Failed to delete transaction: ' . $e->getMessage()]);
    }
    exit;
}

// GET TRANSACTIONS
if ($method === 'GET') {
    $limit = intval($_GET['limit'] ?? 50);
    $offset = intval($_GET['offset'] ?? 0);
    $type = trim($_GET['type'] ?? ''); // Filter by type

    $where = "user_id = $user_id";
    if ($type && in_array($type, ['income', 'expense', 'transfer'])) {
        $where .= " AND type = '$type'";
    }

    $transactions = fetch('transactions', [], 'all', [], "WHERE $where ORDER BY transaction_date DESC, created_at DESC LIMIT $limit OFFSET $offset");

    // Enrich with wallet/category names
    foreach ($transactions as &$txn) {
        if ($txn['category_id']) {
            $cat = fetch('categories', ['id' => $txn['category_id']], 'one');
            $txn['category_name'] = $cat['name'] ?? 'Unknown';
        }

        if ($txn['wallet_id']) {
            $wallet = fetch('wallets', ['id' => $txn['wallet_id']], 'one');
            $txn['from_wallet_name'] = $wallet['name'] ?? 'Unknown';
        }

        if ($txn['to_wallet_id']) {
            $wallet = fetch('wallets', ['id' => $txn['to_wallet_id']], 'one');
            $txn['to_wallet_name'] = $wallet['name'] ?? 'Unknown';
        }
    }

    echo je([
        'success' => true,
        'transactions' => $transactions,
        'count' => count($transactions)
    ]);
    exit;
}

http_response_code(405);
echo je(['success' => false, 'error' => 'Method not allowed']);
