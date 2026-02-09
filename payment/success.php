<?php
$invoice_id = intval($_GET['invoice_id'] ?? 0);

if ($invoice_id <= 0) {
    set_alert('error', 'Invalid request.');
    redirect('/plans');
}

$invoice = fetch('billing_invoices', ['id' => $invoice_id], 'one');
if (!$invoice) {
    set_alert('error', 'Invoice not found.');
    redirect('/plans');
}

// Check if payment is already completed
if ($invoice['status'] === 'paid') {
?>
    <!-- NOTE: header.php is already included by index.php for 'payment' route -->
    <!-- We do not need to include public-header.php -->

    <div class="min-h-screen flex items-center justify-center bg-bs px-4 font-sans selection:bg-primary selection:text-white">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMyIgY3k9IjMiIHI9IjIiIGZpbGw9InJnYmEoNTQsMzYsMTE0LDAuMSkiLz48L3N2Zz4=')] opacity-50"></div>

        <div class="relative z-10 text-center bg-bp p-12 rounded-3xl shadow-xl border border-gray-200 max-w-lg" data-aos="zoom-in">
            <!-- Success Icon with Animation -->
            <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center text-primary mx-auto mb-6 animate-bounce-slow shadow-lg shadow-primary/20">
                <i class="ri-checkbox-circle-fill text-5xl"></i>
            </div>

            <h2 class="text-4xl font-black mb-3 text-tp">Payment Successful!</h2>
            <p class="text-ts mb-8 leading-relaxed text-lg">
                Your payment has been verified and your subscription is now active. Welcome to CashTrack!
            </p>

            <div class="flex flex-col gap-3">
                <a href="<?= url('/pages/dashboard') ?>" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-primary to-secondary text-white font-bold rounded-xl hover:shadow-xl hover:shadow-primary/30 transition-all hover:scale-[1.02] no-underline">
                    Go to Dashboard <i class="ri-arrow-right-line"></i>
                </a>
                <a href="<?= url('/pages/wallets') ?>" class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-white text-tp font-bold rounded-xl border-2 border-gray-200 hover:border-primary hover:text-primary transition-all no-underline">
                    <i class="ri-wallet-3-line"></i> Create Your First Wallet
                </a>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100">
                <p class="text-sm text-ts">
                    Invoice #<?= $invoice_id ?> | Paid on <?= date('M d, Y') ?>
                </p>
            </div>
        </div>
    </div>

    <style>
        @keyframes bounce-slow {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-bounce-slow {
            animation: bounce-slow 2s ease-in-out infinite;
        }
    </style>
<?php
    exit;
} else {
    // Payment not verified yet
    set_alert('warning', 'Payment not yet verified. Please complete the payment process.');
    redirect('/payment/proceed-to-checkout?plan_id=' . $invoice['plan_id']);
    exit;
}
?>