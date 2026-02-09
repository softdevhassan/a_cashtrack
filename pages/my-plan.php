<?php
$user_id = $_APP['user']['id'];
$subscription = fetch('subscriptions', ['user_id' => $user_id, 'status' => 'active'], 'one', [], 'ORDER BY id DESC');
$plan = $subscription ? fetch('plans', ['id' => $subscription['plan_id']], 'one') : null;
$period_start = $subscription['starts_at'] ?? null;
$period_end = $subscription['ends_at'] ?? null;
$usage_count = 0;
if ($period_start && $period_end && $plan) {
    // CRITICAL API: Use persistent usage
    // Avoids reset on DB cleanup
    $usage_count = (int)($subscription['transaction_usage'] ?? 0);
}
$limit = $plan['transaction_limit'] ?? 0;
$percentage = $limit > 0 ? round(($usage_count / $limit) * 100) : 0;
$days_left = $period_end ? max(0, floor((strtotime($period_end) - strtotime(date('Y-m-d'))) / 86400)) : 0;
?>

<div class="border-b pb-4 mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0" data-aos="fade-up">
    <div>
        <h1 class="text-2xl font-bold text-tp">My Subscription</h1>
        <p class="text-tp/70 text-base">Manage your current plan and usage</p>
    </div>
    <div class="flex flex-wrap justify-end items-center gap-2">
        <a href="<?= url('/plans'); ?>" class="px-4 py-2 bg-accent text-white rounded-xl hover:bg-accent/90 flex items-center gap-2">
            <i class="ri-price-tag-3-line"></i> Upgrade Plan
        </a>
        <a href="<?= url('/pages/billing'); ?>" class="px-4 py-2 bg-bp text-accent rounded-xl hover:bg-bs border border-accent/30 flex items-center gap-2">
            <i class="ri-wallet-3-line"></i> Manage Billing
        </a>
    </div>
    <?= show_alert(); ?>
</div>

<?php if (!$plan): ?>
    <div class="bg-white border border-ts/30 rounded-2xl p-4 sm:p-6" data-aos="fade-up">
        <div class="flex items-center gap-3">
            <i class="ri-information-line text-2xl text-accent"></i>
            <div>
                <h3 class="text-lg font-semibold text-tp">No active subscription</h3>
                <p class="text-tp/70">Choose a plan to start tracking expenses.</p>
            </div>
        </div>
        <div class="mt-4">
            <a href="<?= url('/plans'); ?>" class="px-4 py-2 bg-accent text-white rounded-xl hover:bg-accent/90 inline-flex items-center gap-2">
                <i class="ri-price-tag-3-line"></i> View Plans
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-ts/30 rounded-2xl p-4 sm:p-6" data-aos="fade-up">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Current Plan</h2>
                <span class="px-3 py-1 rounded-full text-sm font-medium 
                    <?= $subscription['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                    <?= ucfirst($subscription['status']) ?>
                </span>
            </div>
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold text-tp"><?= e($plan['title']); ?></h3>
                    <p class="text-tp/70">Monthly Limit: <?= number_format($limit); ?> transactions</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-tp"><?= formatCurrency($plan['price']) ?></div>
                    <div class="text-xs text-tp/70">per <?= (int)($plan['duration_days'] ?? 30); ?> days</div>
                </div>
            </div>
            <div class="mt-6">
                <div class="mb-1 flex justify-between text-xs">
                    <span class="text-tp/70">Current Period Usage</span>
                    <span><?= number_format($usage_count); ?>/<?= number_format($limit); ?></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full <?= $percentage > 80 ? 'bg-red-500' : 'bg-green-500'; ?>" style="width: <?= min($percentage, 100); ?>%"></div>
                </div>
                <div class="mt-1 text-xs text-tp/70">
                    <?= max(0, $limit - $usage_count); ?> transactions remaining
                </div>
            </div>
        </div>
        <div class="bg-white border border-ts/30 rounded-2xl p-4 sm:p-6">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-tp/70">Starts</span>
                    <span class="font-medium text-tp"><?= date('d M Y', strtotime($period_start)); ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-tp/70">Ends</span>
                    <span class="font-medium text-tp"><?= date('d M Y', strtotime($period_end)); ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-tp/70">Days Left</span>
                    <span class="font-medium text-tp"><?= $days_left; ?></span>
                </div>
                <div class="pt-3 border-t border-ts/30">
                    <?php if (isset($plan['is_trial']) && $plan['is_trial']): ?>
                        <!-- Trial users can only upgrade, not renew trial -->
                        <a href="<?= url('/plans'); ?>" class="w-full inline-flex justify-center px-4 py-2 bg-accent text-white rounded-xl hover:bg-accent/90">
                            Upgrade Plan
                        </a>
                    <?php else: ?>
                        <!-- Paid users can renew current plan or change -->
                        <div class="flex flex-col gap-2">
                            <a href="<?= url('/payment/proceed-to-checkout?plan_id=' . $plan['id']); ?>" class="w-full inline-flex justify-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700">
                                Renew Current Plan
                            </a>
                            <a href="<?= url('/plans'); ?>" class="w-full inline-flex justify-center px-4 py-2 bg-white text-accent border border-accent rounded-xl hover:bg-gray-50">
                                Change Plan
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>