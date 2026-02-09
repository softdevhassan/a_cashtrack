<div x-data="{ mobileOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">
    <?php include __DIR__ . '/../includes/public-header.php'; ?>

    // Fetch active plans if not already available
    if (!isset($plans)) {
    $plans = fetch('plans', ['status' => 'active'], 'all', [], 'ORDER BY price ASC');
    }
    ?>

    <div class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white font-sans min-h-screen transition-colors duration-300">
        <!-- Hero Section -->
        <div class="relative py-20 bg-indigo-50 dark:bg-white/5 overflow-hidden">
            <div class="absolute inset-0 bg-indigo-500/5 dark:bg-indigo-500/10"></div>
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl opacity-50 animate-pulse"></div>
            <div class="container mx-auto px-4 relative z-10 text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 text-gray-900 dark:text-white">Pricing Plans</h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Simple, transparent pricing. No hidden fees. Choose the plan that best fits your business needs.
                </p>
            </div>
        </div>

        <!-- Pricing Cards (Reusing Home Logic) -->
        <section class="py-20 relative">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto items-center">
                    <?php if (!empty($plans)): ?>
                        <?php foreach ($plans as $index => $plan): ?>
                            <?php
                            $isPopular = ($index == 1);
                            $isTrial = !empty($plan['is_trial']);

                            $darkModeClasses = $isPopular
                                ? 'dark:bg-gradient-to-b dark:from-gray-800 dark:to-gray-900 dark:border-indigo-500/50 dark:shadow-[0_0_40px_rgba(79,70,229,0.15)]'
                                : 'dark:bg-white/5 dark:border-white/10 dark:hover:border-white/20';

                            $lightModeClasses = $isPopular
                                ? 'bg-white border-indigo-200 shadow-xl'
                                : 'bg-white border-gray-200 hover:border-indigo-300 shadow-sm';

                            $scaleClass = $isPopular ? 'scale-105 z-10' : 'hover:-translate-y-1';

                            // Check if user has already used a trial
                            $has_trial_history = false;
                            if ($isTrial && !empty($_APP['user'])) {
                                $user_id = $_APP['user']['id'];
                                $trial_check = query("SELECT 1 FROM subscriptions s JOIN plans p ON s.plan_id = p.id WHERE s.user_id = $user_id AND p.is_trial = 1 LIMIT 1");
                                if ($trial_check->num_rows > 0) {
                                    $has_trial_history = true;
                                }
                            }
                            ?>

                            <div class="rounded-3xl p-8 relative border transition-all duration-300 <?= "$lightModeClasses $darkModeClasses $scaleClass" ?>">
                                <?php if ($isPopular): ?>
                                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-indigo-600 text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wide shadow-lg">
                                        Most Popular
                                    </div>
                                <?php endif; ?>

                                <?php if ($isTrial): ?>
                                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-blue-500 text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wide shadow-lg">
                                        Free Trial
                                    </div>
                                <?php endif; ?>

                                <div class="mb-6">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2"><?= e($plan['title']) ?></h3>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm h-10 line-clamp-2"><?= e($plan['description']) ?></p>
                                </div>

                                <div class="mb-8">
                                    <?php if ($plan['price'] <= 0): ?>
                                        <span class="text-5xl font-extrabold text-gray-900 dark:text-white">FREE</span>
                                    <?php else: ?>
                                        <div class="flex items-baseline">
                                            <span class="text-4xl font-extrabold text-gray-900 dark:text-white"><?= formatCurrency($plan['price']) ?></span>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">One-time payment</p>
                                    <?php endif; ?>
                                </div>

                                <ul class="space-y-4 mb-8">
                                    <!-- Transaction Limit (Most Important) -->
                                    <li class="flex items-center gap-3 bg-indigo-50 dark:bg-indigo-500/10 p-3 rounded-xl">
                                        <i class="ri-exchange-line text-indigo-600 dark:text-indigo-400 text-xl"></i>
                                        <span class="text-gray-900 dark:text-white font-bold"><?= number_format($plan['transaction_limit']) ?> Transactions</span>
                                    </li>
                                    <!-- Wallet Limit -->
                                    <li class="flex items-center gap-3">
                                        <i class="ri-wallet-3-line text-primary dark:text-primary text-lg"></i>
                                        <span class="text-gray-700 dark:text-gray-300"><?= $plan['wallet_limit'] ?> Wallets</span>
                                    </li>
                                    <!-- Category Management -->
                                    <li class="flex items-center gap-3">
                                        <i class="ri-folders-line text-blue-600 dark:text-blue-400 text-lg"></i>
                                        <span class="text-gray-700 dark:text-gray-300">Category Management</span>
                                    </li>
                                    <!-- PDF/Excel Export (only for paid plans) -->
                                    <?php if ($plan['price'] > 0): ?>
                                        <li class="flex items-center gap-3">
                                            <i class="ri-file-excel-line text-green-600 dark:text-green-400 text-lg"></i>
                                            <span class="text-gray-700 dark:text-gray-300">PDF & Excel Export</span>
                                        </li>
                                    <?php endif; ?>
                                    <!-- Analytics -->
                                    <li class="flex items-center gap-3">
                                        <i class="ri-bar-chart-line text-purple-600 dark:text-purple-400 text-lg"></i>
                                        <span class="text-gray-700 dark:text-gray-300"><?= ($plan['price'] >= 1500) ? 'Advanced' : 'Basic' ?> Analytics</span>
                                    </li>
                                    <!-- Priority Support (only for Enterprise) -->
                                    <?php if ($plan['price'] >= 3000): ?>
                                        <li class="flex items-center gap-3">
                                            <i class="ri-customer-service-2-line text-orange-600 dark:text-orange-400 text-lg"></i>
                                            <span class="text-gray-700 dark:text-gray-300">Priority Support</span>
                                        </li>
                                    <?php endif; ?>
                                </ul>

                                <?php
                                $ctaLink = !empty($_APP['user']) ? url("/payment/proceed-to-checkout?plan_id={$plan['id']}") : url("/auth/signup?planid={$plan['id']}");
                                $ctaText = !empty($plan['is_trial']) ? 'Start Free Trial' : 'Choose Plan';

                                $btnClass = $isPopular
                                    ? 'bg-indigo-600 text-white hover:bg-indigo-700 dark:hover:bg-indigo-500 shadow-lg shadow-indigo-500/25'
                                    : 'bg-gray-100 text-gray-900 hover:bg-gray-200 dark:bg-white/10 dark:text-white dark:hover:bg-white/20';

                                if ($isTrial && $has_trial_history) {
                                    echo '<button disabled class="block w-full py-3.5 rounded-xl font-bold text-center bg-gray-200 dark:bg-gray-800 text-gray-400 cursor-not-allowed">Trial Already Used</button>';
                                } else {
                                    echo '<a href="' . $ctaLink . '" class="block w-full py-3.5 rounded-xl font-bold text-center transition-all ' . $btnClass . '">' . $ctaText . '</a>';
                                }
                                ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Enterprise CTA -->
                <div class="max-w-4xl mx-auto mt-20" data-aos="fade-up">
                    <div class="bg-gradient-to-r from-gray-900 to-indigo-900 dark:from-indigo-900/50 dark:to-purple-900/50 text-white p-8 md:p-12 rounded-3xl relative overflow-hidden shadow-2xl">
                        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4xKSIvPjwvc3ZnPg==')] opacity-30"></div>
                        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                            <div>
                                <h3 class="text-2xl font-bold mb-2">Need a high-volume custom plan?</h3>
                                <p class="text-indigo-200 text-lg">We offer tailored solutions for enterprise senders sending 1M+ emails.</p>
                            </div>
                            <a href="mailto:support@<?= $_ENV['APP_WEBSITE_NAME'] ?>" class="bg-white text-indigo-900 px-8 py-3 rounded-xl font-bold hover:bg-indigo-50 transition-colors shadow-lg">
                                Contact Sales
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include __DIR__ . '/../includes/public-footer.php'; ?>
</div>