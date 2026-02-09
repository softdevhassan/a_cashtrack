<?php
// Ensure user is logged in
if (empty($_APP['user'])) {
    $plan_id = intval($_GET['plan_id'] ?? 0);
    $redirect_url = '/auth/signup' . ($plan_id ? '?planid=' . $plan_id : '');
    redirect($redirect_url);
    exit;
}

$user_id = $_APP['user']['id'];
$plan_id = intval($_GET['plan_id'] ?? 0);
if ($plan_id <= 0) {
    set_alert('error', 'Please select a valid plan.');
    redirect('/plans');
}
$plan = fetch('plans', ['id' => $plan_id, 'status' => 'active'], 'one');
if (!$plan) {
    set_alert('error', 'Selected plan is not available.');
    redirect('/plans');
}

// Check if plan is trial and user eligibility
$is_trial_plan = !empty($plan['is_trial']);
if ($is_trial_plan) {
    // Check if user has ever used a trial plan
    $used_trial = query("SELECT COUNT(*) as count FROM subscriptions s 
                        JOIN plans p ON s.plan_id = p.id 
                        WHERE s.user_id = $user_id AND p.is_trial = 1");
    $used_trial_count = mysqli_fetch_assoc($used_trial)['count'];

    if ($used_trial_count > 0) {
        set_alert('error', 'You have already used your free trial. Please select a paid plan.');
        redirect('/plans');
    }
}

// If plan is free or trial (price 0), activate immediately
if ($plan['price'] <= 0) {
    // Deactivate old active subscriptions
    update('subscriptions', ['status' => 'cancelled'], ['user_id' => $user_id, 'status' => 'active']);

    // Create new active subscription
    $duration = $plan['duration_days'] ?? 30;
    insert('subscriptions', [
        'user_id' => $user_id,
        'plan_id' => $plan['id'],
        'status' => 'active',
        'starts_at' => date('Y-m-d H:i:s'),
        'ends_at' => date('Y-m-d H:i:s', strtotime("+$duration days")),
        'created_at' => date('Y-m-d H:i:s')
    ]);

    set_alert('success', 'Plan activated successfully!');
    redirect('/pages/dashboard');
}

// If user already has active subscription for this plan, go to dashboard
$existing_sub = fetch('subscriptions', ['user_id' => $user_id, 'status' => 'active'], 'one');
if ($existing_sub && (int)$existing_sub['plan_id'] === $plan_id) {
    set_alert('info', 'You already have this plan active.');
    redirect('/pages/dashboard');
}
// Create or fetch pending invoice
$pending_invoice = fetch('billing_invoices', ['user_id' => $user_id, 'plan_id' => $plan_id, 'status' => 'pending'], 'one');
if (!$pending_invoice) {
    $invoice_id = insert('billing_invoices', [
        'user_id' => $user_id,
        'plan_id' => $plan_id,
        'amount' => $plan['price'],
        'status' => 'pending',
        'payment_method' => 'manual',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    $pending_invoice = fetch('billing_invoices', ['id' => $invoice_id], 'one');
}

// Handle Payment Verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'verify_payment') {
        $txId = trim($_POST['transaction_id'] ?? '');

        if (empty($txId)) {
            set_alert('error', 'Please enter the Transaction ID (TxID).');
        } else {
            // Check Environment
            $is_test_mode = isset($_ENV['APP_MODE']) && strtolower($_ENV['APP_MODE']) === 'dev';

            // 1. Check if TxID is already used in billing_invoices (Skip specific check in test mode if desired, but good to keep unique)
            $check_duplicate = query(
                "SELECT id FROM billing_invoices WHERE payment_method = ? OR payment_method = ?",
                ["binance_USDT_" . $txId, "binance_USDT_" . $txId]
            );

            // Better duplicate check
            $check_duplicate = query("SELECT id FROM billing_invoices WHERE payment_method LIKE ?", ["%binance_USDT_%" . $txId . "%"]);

            if ($check_duplicate && mysqli_num_rows($check_duplicate) > 0) {
                set_alert('error', 'This Transaction ID/Order ID has already been used.');
            } else {

                // --- TEST MODE BYPASS ---
                if ($is_test_mode) {
                    $valid_deposit = [
                        'txId' => $txId,
                        'amount' => $plan['price'],
                        'status' => 1,
                        'coin' => 'USDT'
                    ];
                    $amount = (float)$plan['price'];
                    $status = 1;
                } else {
                    // 2. Verify with Binance API (PROD MODE)
                    if (!empty($_ENV['BINANCE_API_KEY']) && !empty($_ENV['BINANCE_SECRET_KEY'])) {
                        try {
                            $client = new \Binance\Spot([
                                'key' => $_ENV['BINANCE_API_KEY'],
                                'secret' => $_ENV['BINANCE_SECRET_KEY']
                            ]);

                            // Fetch deposit history filtering by TxID
                            $deposits = $client->depositHistory(['txId' => $txId, 'recvWindow' => 60000]);

                            $valid_deposit = null;
                            $amount = 0;
                            $status = 0; // 1 for success

                            // 1. Check Blockchain Deposits
                            if (!empty($deposits)) {
                                foreach ($deposits as $deposit) {
                                    if ($deposit['txId'] === $txId) {
                                        if (($deposit['coin'] ?? '') !== 'USDT') {
                                            set_alert('error', 'Invalid Currency. Only USDT is accepted. Received: ' . ($deposit['coin'] ?? 'Unknown'));
                                            redirect('/payment/proceed-to-checkout?plan_id=' . $plan_id);
                                            exit;
                                        }

                                        $valid_deposit = $deposit;
                                        $amount = (float)$deposit['amount'];
                                        $status = (int)$deposit['status'];
                                        break;
                                    }
                                }
                            }

                            // 2. If not found, Check Binance Pay History (Internal Transfers)
                            if (!$valid_deposit) {
                                try {
                                    $pay_response = $client->payTradeHistory(['recvWindow' => 60000]);
                                    $pay_history = $pay_response['data'] ?? $pay_response;

                                    if (!empty($pay_history) && is_array($pay_history)) {
                                        foreach ($pay_history as $pay) {
                                            if (!is_array($pay)) continue;

                                            $p_txId = $pay['transactionId'] ?? '';
                                            $p_orderId = $pay['orderId'] ?? '';

                                            if ($p_txId === $txId || $p_orderId === $txId) {
                                                $p_currency = $pay['currency'] ?? $pay['coin'] ?? '';
                                                if ($p_currency !== 'USDT') {
                                                    set_alert('error', 'Invalid Currency. Only USDT is accepted. Received: ' . $p_currency);
                                                    redirect('/payment/proceed-to-checkout?plan_id=' . $plan_id);
                                                    exit;
                                                }

                                                if ((float)$pay['amount'] > 0) {
                                                    $valid_deposit = $pay;
                                                    $amount = (float)$pay['amount'];
                                                    $status = 1;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                } catch (\Throwable $e) {
                                    // Ignore Pay history errors
                                }
                            }
                        } catch (\Throwable $e) {
                            set_alert('error', 'Binance API Error: ' . $e->getMessage());
                        }
                    } else {
                        set_alert('error', 'Binance API configuration is missing.');
                    }
                } // End Test/Prod Split


                if ($valid_deposit) {
                    if ($status === 1) {
                        $price = (float)$plan['price'];
                        $is_amount_valid = false;

                        if (function_exists('bccomp')) {
                            $is_amount_valid = bccomp((string)$amount, (string)$price, 8) >= 0;
                        } else {
                            $is_amount_valid = $amount >= $price;
                        }

                        if ($is_amount_valid) {
                            $final_tx_id = $valid_deposit['txId'] ?? $valid_deposit['transactionId'] ?? $txId;

                            $check_canonical = query("SELECT id FROM billing_invoices WHERE payment_method = ?", ["binance_USDT_" . $final_tx_id]);
                            if ($check_canonical && mysqli_num_rows($check_canonical) > 0) {
                                set_alert('error', 'This Payment has already been claimed (Duplicate Transaction).');
                                redirect('/payment/proceed-to-checkout?plan_id=' . $plan_id);
                                exit;
                            }

                            update('billing_invoices', [
                                'status' => 'paid',
                                'payment_method' => 'binance_USDT_' . $final_tx_id,
                                'updated_at' => date('Y-m-d H:i:s')
                            ], ['id' => $pending_invoice['id']]);

                            $starts = date('Y-m-d');
                            $ends = date('Y-m-d', strtotime('+' . (int)$plan['duration_days'] . ' days'));

                            if ($existing_sub) {
                                update('subscriptions', [
                                    'plan_id' => $plan_id,
                                    'starts_at' => $starts,
                                    'ends_at' => $ends,
                                    'status' => 'active',
                                    'updated_at' => date('Y-m-d H:i:s')
                                ], ['id' => $existing_sub['id']]);
                            } else {
                                insert('subscriptions', [
                                    'user_id' => $user_id,
                                    'plan_id' => $plan_id,
                                    'starts_at' => $starts,
                                    'ends_at' => $ends,
                                    'status' => 'active',
                                    'created_at' => date('Y-m-d H:i:s')
                                ]);
                            }

                            set_alert('success', 'Payment verified! Subscription activated.');
                            redirect('/pages/dashboard');
                            exit;
                        } else {
                            set_alert('error', 'Deposit amount (' . $amount . ') is less than plan price (' . $plan['price'] . ').');
                        }
                    } else {
                        set_alert('warning', 'Transaction found but is still pending (Status: ' . $status . '). Please wait and try again.');
                    }
                } else {
                    set_alert('error', 'Transaction not found in our wallet history yet. Please check the TxID or wait a few minutes.');
                }
            }
        }
    } elseif ($action === 'cancel') {
        update('billing_invoices', ['status' => 'failed'], ['id' => $pending_invoice['id']]);
        set_alert('warning', 'Payment cancelled.');
        redirect('/plans');
        exit;
    }
}
?>

<div class="h-screen w-full bg-bp text-tp flex flex-col font-sans selection:bg-primary selection:text-white overflow-hidden"
    x-data="{ mobileOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">

    <!-- Includes Public Navbar with partial logic (relies on x-data above) -->
    <?php include __DIR__ . '/../includes/public-header.php'; ?>

    <div class="flex-1 overflow-y-auto">
        <div class="max-w-5xl mx-auto my-10 px-4 pt-20" data-aos="fade-up">
            <div class="border-b border-gray-200 pb-4 mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-black text-tp">Checkout</h1>
                    <p class="text-ts text-base">Complete your purchase with USDT</p>
                </div>
                <a href="<?= url('/plans'); ?>" class="text-sm border border-gray-200 px-3 py-1 rounded-md hover:bg-bs text-ts flex items-center gap-1 transition-colors">
                    <i class="ri-arrow-go-back-line"></i> Back to Plans
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Order Summary -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-bp border border-gray-200 rounded-2xl p-4 sm:p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-tp mb-4">Order Summary</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-ts">Plan</span>
                                <span class="font-medium text-tp"><?= e($plan['title']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ts">Duration</span>
                                <span class="font-medium text-tp"><?= (int)$plan['duration_days']; ?> Days</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ts">Email Limit</span>
                                <span class="font-medium text-tp"><?= number_format((int)$plan['email_limit']); ?></span>
                            </div>
                            <div class="border-t border-gray-100 pt-4 flex justify-between items-end">
                                <span class="text-tp font-bold">Total</span>
                                <span class="text-2xl font-black text-primary">$<?= number_format((float)$plan['price'], 2); ?></span>
                            </div>
                        </div>
                        <div class="mt-6 text-xs text-center text-ts">
                            Invoice #<?= (int)$pending_invoice['id']; ?>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="lg:col-span-2">
                    <?php if ($is_trial_plan): ?>
                        <div class="bg-bp border border-gray-200 rounded-2xl p-8 sm:p-12 text-center shadow-sm">
                            <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="ri-gift-line text-4xl text-primary"></i>
                            </div>
                            <h3 class="text-3xl font-black text-tp mb-3">Start Your Free Trial</h3>
                            <p class="text-ts mb-8 max-w-md mx-auto text-lg">
                                Get full access to <?= e($plan['title']); ?> for <?= $plan['duration_days']; ?> days.
                                No credit card required.
                            </p>

                            <form method="POST">
                                <input type="hidden" name="action" value="activate_trial">
                                <button type="submit" class="bg-gradient-to-r from-primary to-secondary text-white px-10 py-4 rounded-xl font-bold hover:shadow-lg hover:shadow-primary/30 transition-all transform hover:scale-105">
                                    Activate Free Trial
                                </button>
                            </form>

                            <div class="mt-6 text-xs text-ts">
                                By activating, you agree to our Terms of Service.
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="bg-bp border border-gray-200 rounded-2xl p-4 sm:p-6 shadow-sm">
                            <h3 class="text-lg font-bold text-tp mb-6 flex items-center gap-2">
                                <i class="ri-secure-payment-line text-secondary"></i> Payment Method: Binance Pay (USDT)
                            </h3>

                            <div class="flex flex-col md:flex-row gap-8 items-start">
                                <!-- QR Code -->
                                <div class="w-full md:w-1/3 flex flex-col items-center">
                                    <div class="bg-bs p-4 rounded-2xl border border-gray-200 mb-2">
                                        <?php if (!empty($_ENV['BINANCE_WALLET_ADDRESS'])): ?>
                                            <img src="https://quickchart.io/qr?text=<?= urlencode($_ENV['BINANCE_WALLET_ADDRESS']); ?>" alt="USDT QR Code" class="w-48 h-48 object-contain">
                                        <?php else: ?>
                                            <div class="w-48 h-48 flex items-center justify-center text-ts border-2 border-dashed border-gray-300 rounded-xl">
                                                No QR Image
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-sm text-ts text-center font-medium">Scan to Pay via Binance</p>
                                </div>

                                <!-- Instructions & Verification -->
                                <div class="w-full md:w-2/3 space-y-6">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs uppercase tracking-wide text-ts mb-1 font-bold">Send Amount</label>
                                            <div class="flex items-center space-x-2 text-2xl font-black text-tp">
                                                <span><?= number_format((float)$plan['price'], 2); ?></span>
                                                <span class="text-primary text-xl">USDT</span>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs uppercase tracking-wide text-ts mb-1 font-bold">Wallet Address (TRC20 only)</label>
                                            <div class="flex items-center space-x-2 bg-bs p-3 rounded-xl border border-gray-200 group hover:border-primary/50 transition-colors">
                                                <code class="text-sm break-all text-tp select-all font-mono flex-1"><?= e($_ENV['BINANCE_WALLET_ADDRESS'] ?? 'Not Configured'); ?></code>
                                                <button onclick="navigator.clipboard.writeText('<?= e($_ENV['BINANCE_WALLET_ADDRESS'] ?? ''); ?>'); showToast('Copied!')" class="text-primary hover:text-secondary p-2 rounded-lg hover:bg-primary/5 transition-colors">
                                                    <i class="ri-file-copy-line text-lg"></i>
                                                </button>
                                            </div>
                                            <p class="text-xs text-orange-600 mt-2 flex items-center gap-1 font-medium bg-orange-50 p-2 rounded-lg border border-orange-100">
                                                <i class="ri-alert-line"></i> Please ensure you send <strong>USDT (TRC20)</strong> only.
                                            </p>
                                        </div>
                                    </div>

                                    <hr class="border-gray-100">

                                    <form method="POST" class="space-y-4">
                                        <input type="hidden" name="action" value="verify_payment">

                                        <div>
                                            <label class="block text-sm font-medium text-tp mb-2">Transaction ID (TxID) or Order ID</label>
                                            <input type="text" name="transaction_id" required
                                                placeholder="Enter the Transaction Hash or Binance Order ID"
                                                class="w-full px-4 py-3 bg-bs border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none focus:outline-none transition-all text-tp placeholder-gray-400">
                                            <p class="text-xs text-ts mt-1.5">Paste the full transaction ID or Order ID after completing the transfer.</p>
                                        </div>

                                        <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary text-white py-4 rounded-xl font-bold hover:shadow-lg hover:shadow-primary/30 transition-all transform hover:scale-[1.01]">
                                            Verify Payment <i class="ri-arrow-right-line ml-1"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mt-6 text-center">
                        <form method="POST">
                            <input type="hidden" name="action" value="cancel">
                            <button type="submit" class="text-sm text-red-500 hover:text-red-700 hover:underline font-medium transition-colors">
                                Cancel Transaction & Return to Plans
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php function page_sc()
{ ?>
    <script>
        function showToast(msg) {
            // Simple toast implementation or use existing if any
            alert(msg);
        }
    </script>
<?php } ?>