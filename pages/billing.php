<?php
$user_id = $_APP['user']['id'];
$invoice_rows = mysqli_fetch_all(query("
    SELECT bi.*, p.title AS plan_title 
    FROM billing_invoices bi
    LEFT JOIN plans p ON bi.plan_id = p.id
    WHERE bi.user_id = ?
    ORDER BY bi.created_at DESC
", [$user_id]), MYSQLI_ASSOC);
$paid = array_filter($invoice_rows, fn($r) => ($r['status'] ?? '') === 'paid');
$pending = array_filter($invoice_rows, fn($r) => ($r['status'] ?? '') === 'pending');
$failed = array_filter($invoice_rows, fn($r) => ($r['status'] ?? '') === 'failed');
$stats = [
    'total_spent' => array_sum(array_map(fn($r) => (float)($r['amount'] ?? 0), $paid)),
    'paid_count' => count($paid),
    'pending_count' => count($pending),
    'failed_count' => count($failed),
];
?>

<div class="border-b pb-4 mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0">
    <div>
        <h1 class="text-2xl font-bold text-tp">Billing</h1>
        <p class="text-tp/70 text-base">View invoices and payment history</p>
    </div>
    <div class="flex flex-wrap justify-end items-center gap-2">
        <a href="<?= url('/plans'); ?>" class="px-4 py-2 bg-accent text-white rounded-xl hover:bg-accent/90 flex items-center gap-2">
            <i class="ri-price-tag-3-line"></i> View Plans
        </a>
    </div>
    <?= show_alert(); ?>
    </div>

<div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" data-aos="fade-up">
    <div class="bg-white border border-ts/30 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-tp/70">Total Spent</p>
                <h3 class="text-2xl font-bold text-tp">$<?= number_format($stats['total_spent'], 2); ?></h3>
            </div>
            <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center">
                <i class="ri-wallet-3-line text-xl text-accent"></i>
            </div>
        </div>
    </div>
    <div class="bg-white border border-ts/30 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-tp/70">Paid Invoices</p>
                <h3 class="text-2xl font-bold text-tp"><?= $stats['paid_count']; ?></h3>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="ri-checkbox-circle-line text-xl text-green-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white border border-ts/30 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-tp/70">Pending</p>
                <h3 class="text-2xl font-bold text-tp"><?= $stats['pending_count']; ?></h3>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                <i class="ri-time-line text-xl text-amber-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white border border-ts/30 rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-tp/70">Failed</p>
                <h3 class="text-2xl font-bold text-tp"><?= $stats['failed_count']; ?></h3>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="ri-close-circle-line text-xl text-red-600"></i>
            </div>
        </div>
    </div>
    </div>

<div class="bg-white border border-ts/30 rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="100">
    <div class="p-4 border-b border-ts/30 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-tp">Invoices</h3>
        
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-ts/30">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-ts/30">
                <?php if (empty($invoice_rows)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-tp/70">
                            <div class="py-8">
                                <i class="ri-file-list-3-line text-3xl text-tp/30 mb-2"></i>
                                <p>No invoices found</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($invoice_rows as $inv): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-tp">#<?= (int)$inv['id']; ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-tp"><?= e($inv['plan_title'] ?? 'Plan'); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-tp">$<?= number_format((float)$inv['amount'], 2); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $st = $inv['status'];
                                $badge = $st === 'paid' ? 'bg-green-100 text-green-700' : ($st === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
                                ?>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium <?= $badge; ?>"><?= e(strtoupper($st)); ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-tp"><?= e($inv['payment_method'] ?? ''); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-tp"><?= date('d M Y', strtotime($inv['created_at'])); ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="px-4 sm:px-6 py-4 border-t border-ts/30 text-sm text-tp/70">
        Showing <?= count($invoice_rows); ?> invoice<?= count($invoice_rows) === 1 ? '' : 's'; ?>
    </div>
    </div>
