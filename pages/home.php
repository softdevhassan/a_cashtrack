<div x-data="{ mobileOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">
    <?php include __DIR__ . '/../includes/public-header.php'; ?>

    <?php
    // Fetch active plans for pricing section
    $plans = fetch('plans', ['status' => 'active'], 'all', [], 'ORDER BY price ASC');

    // Get currency symbol
    $currencyConfig = $_ENV['CURRENCY'] ?? 'PKR, post';
    $currencySymbol = trim(explode(',', $currencyConfig)[0]);
    ?>

    <!-- Main Content Wrapper -->
    <div class="bg-gradient-to-br from-bs via-bp to-bs text-tp font-sans selection:bg-primary selection:text-white overflow-x-hidden">

        <!-- Hero Section - Asymmetric Split -->
        <section class="py-32 px-4 bg-gradient-to-br from-primary/5 via-secondary/5 to-bp overflow-hidden relative">
            <!-- Decorative Background with Blur Shapes -->
            <div class="absolute inset-0">
                <!-- Animated Blur Shapes -->
                <div class="absolute top-20 left-10 w-96 h-96 bg-primary/20 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-20 right-10 w-[500px] h-[500px] bg-secondary/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>
                <div class="absolute top-1/2 left-1/3 w-64 h-64 bg-purple-500/10 rounded-full blur-2xl animate-pulse" style="animation-delay: 3s;"></div>
                <!-- Grid Pattern -->
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMyIgY3k9IjMiIHI9IjEuNSIgZmlsbD0icmdiYSg1NCwzNiwxMTQsMC4wOCkiLz48L3N2Zz4=')] opacity-40"></div>
            </div>

            <div class="container max-w-7xl mx-auto relative z-10">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Left: Text Content -->
                    <div data-aos="fade-right">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 dark:bg-primary/50/10 border border-primary/20 dark:border-primary/50/20 text-primary dark:text-primary text-sm font-bold mb-6">
                            <i class="ri-sparkling-line"></i>
                            Simple. Smart. Transaction-Based.
                        </div>

                        <h1 class="text-5xl md:text-6xl lg:text-7xl font-black mb-6 text-gray-900  leading-tight">
                            Track Every <br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-secondary to-secondary dark:from-primary dark:via-secondary dark:to-secondary">
                                Penny
                            </span>
                        </h1>

                        <p class="text-xl md:text-2xl text-gray-600  mb-8 leading-relaxed">
                            The smartest way to manage your wallets, track expenses, and stay on top of your finances - <strong>one-time purchase</strong>.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 mb-8">
                            <a href="<?= url('/auth/signup') ?>" class="px-8 py-4 bg-gradient-to-r from-primary to-secondary text-white font-bold rounded-xl hover:shadow-2xl hover:shadow-primary/50/50 hover:scale-105 transition-all text-center">
                                Start Free - 50 Transactions <i class="ri-arrow-right-line ml-2"></i>
                            </a>
                            <a href="<?= url('/pages/features') ?>" class="px-8 py-4 bg-white  text-gray-900  font-bold rounded-xl border-2 border-gray-200  hover:border-primary/50 dark:hover:border-primary/50 transition-all text-center">
                                Explore Features
                            </a>
                        </div>

                        <!-- Trust Indicators -->
                        <div class="flex items-center gap-6 text-sm text-gray-600 ">
                            <div class="flex items-center gap-2">
                                <i class="ri-check-line text-primary text-lg"></i>
                                <span>No credit card required</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="ri-check-line text-primary text-lg"></i>
                                <span>No monthly fees</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Dashboard Mockup -->
                    <div class="relative max-w-lg mx-auto lg:mx-0" data-aos="fade-left" data-aos-delay="200">
                        <!-- Main Dashboard Card -->
                        <div class="bg-white rounded-2xl shadow-2xl p-5 border border-gray-200">
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">Your Wallets</h3>
                                    <p class="text-xs text-gray-500">Total Balance</p>
                                </div>
                                <div class="w-9 h-9 bg-primary/10 rounded-xl flex items-center justify-center">
                                    <i class="ri-wallet-3-fill text-primary text-lg"></i>
                                </div>
                            </div>

                            <!-- Total Balance -->
                            <div class="mb-4">
                                <div class="text-3xl font-black text-gray-900 mb-1.5"><?= $currencySymbol ?> 125,450</div>
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="flex items-center gap-1 text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                                        <i class="ri-arrow-up-line text-xs"></i>
                                        12.5%
                                    </span>
                                    <span class="text-gray-500">this month</span>
                                </div>
                            </div>

                            <!-- Wallet Cards -->
                            <div class="space-y-2.5">
                                <!-- Cash Wallet -->
                                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-secondary/5 rounded-xl border border-blue-100">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center text-white">
                                            <i class="ri-money-dollar-circle-line text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-sm">Cash</div>
                                            <div class="text-xs text-gray-500">On hand</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-gray-900 text-sm"><?= $currencySymbol ?> 15,000</div>
                                        <div class="text-xs text-gray-500">2 transactions</div>
                                    </div>
                                </div>

                                <!-- Bank Account -->
                                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-100">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 bg-purple-600 rounded-lg flex items-center justify-center text-white">
                                            <i class="ri-bank-line text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-sm">Bank Account</div>
                                            <div class="text-xs text-gray-500">Meezan Bank</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-gray-900 text-sm"><?= $currencySymbol ?> 108,450</div>
                                        <div class="text-xs text-gray-500">12 transactions</div>
                                    </div>
                                </div>

                                <!-- Credit Card -->
                                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border border-orange-100">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 bg-orange-600 rounded-lg flex items-center justify-center text-white">
                                            <i class="ri-bank-card-line text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-sm">Credit Card</div>
                                            <div class="text-xs text-gray-500">Visa ****1234</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-gray-900 text-sm"><?= $currencySymbol ?> 2,000</div>
                                        <div class="text-xs text-gray-500">5 transactions</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="text-center p-2.5 bg-green-50 rounded-xl border border-green-100">
                                    <div class="text-xs text-green-700 font-semibold mb-0.5">Income</div>
                                    <div class="text-base font-bold text-green-600"><?= $currencySymbol ?> 85K</div>
                                </div>
                                <div class="text-center p-2.5 bg-red-50 rounded-xl border border-red-100">
                                    <div class="text-xs text-red-700 font-semibold mb-0.5">Expense</div>
                                    <div class="text-base font-bold text-red-600"><?= $currencySymbol ?> 42K</div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Stats Card -->
                        <div class="absolute -top-4 -right-4 bg-white rounded-xl shadow-xl p-3 border border-gray-200 w-40" data-aos="zoom-in" data-aos-delay="400">
                            <div class="flex items-center gap-2 mb-1.5">
                                <div class="w-7 h-7 bg-secondary/10 rounded-lg flex items-center justify-center">
                                    <i class="ri-trophy-fill text-secondary text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500">This Month</div>
                                    <div class="text-sm font-bold text-gray-900">28 Days</div>
                                </div>
                            </div>
                            <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-primary/50 to-secondary/50 w-4/5"></div>
                            </div>
                        </div>

                        <!-- Floating Category Card -->
                        <div class="absolute -bottom-4 -left-4 bg-white rounded-xl shadow-xl p-3 border border-gray-200 w-48" data-aos="zoom-in" data-aos-delay="600">
                            <div class="text-xs text-gray-500 mb-1.5">Top Category</div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="ri-shopping-bag-fill text-purple-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-sm">Shopping</div>
                                        <div class="text-xs text-gray-500">32% of expenses</div>
                                    </div>
                                </div>
                                <div class="font-bold text-red-600 text-sm">-18K</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Grid - Bento Box Style -->
        <section id="features" class="py-24 px-4 bg-bp border-t border-gray-200">
            <div class="container max-w-7xl mx-auto">
                <div class="text-center mb-16" data-aos="fade-up">
                    <span class="px-4 py-2 rounded-full bg-gradient-to-r from-primary/10 to-secondary/10 text-primary text-sm font-bold">
                        Features
                    </span>
                    <h2 class="text-4xl md:text-5xl font-black mt-4 mb-4 text-tp">
                        Everything You Need.<br>Nothing You Don't.
                    </h2>
                    <p class="text-ts max-w-2xl mx-auto">
                        Powerful features designed to make expense tracking effortless
                    </p>
                </div>

                <!-- Bento Grid - Fixed Layout -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4" data-aos="fade-up" data-aos-delay="100">
                    <!-- Row 1: Large Feature 1 (2 cols) + Feature 2 (1 col) -->
                    <div class="md:col-span-2 bg-gradient-to-br from-primary to-secondary rounded-3xl p-8 text-white relative overflow-hidden group hover:scale-[1.02] transition-transform border-2 border-primary/20">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                        <div class="relative z-10">
                            <i class="ri-wallet-3-line text-5xl mb-4 opacity-90"></i>
                            <h3 class="text-3xl font-bold mb-3">Multi-Wallet Management</h3>
                            <p class="text-white/90 text-lg mb-4">
                                Track cash, bank accounts, credit cards, and digital wallets all in one place. Never lose sight of your money.
                            </p>
                            <div class="flex gap-2 flex-wrap">
                                <span class="px-3 py-1 bg-white/20 rounded-full text-sm border border-white/30">Cash</span>
                                <span class="px-3 py-1 bg-white/20 rounded-full text-sm border border-white/30">Bank</span>
                                <span class="px-3 py-1 bg-white/20 rounded-full text-sm border border-white/30">Cards</span>
                                <span class="px-3 py-1 bg-white/20 rounded-full text-sm border border-white/30">Crypto</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-bp border-2 border-gray-200 rounded-3xl p-8 hover:border-primary/50 transition-all">
                        <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center mb-4">
                            <i class="ri-pie-chart-line text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-tp">Visual Analytics</h3>
                        <p class="text-ts">
                            Beautiful charts and insights. See where your money goes at a glance.
                        </p>
                    </div>

                    <!-- Row 2: Feature 3 (1 col) + Large Feature 2 (2 cols) -->
                    <div class="bg-bp border-2 border-gray-200 rounded-3xl p-8 hover:border-secondary/50 transition-all">
                        <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mb-4">
                            <i class="ri-exchange-funds-line text-3xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-tp">Smart Transfers</h3>
                        <p class="text-ts">
                            Move money between wallets with double-entry accounting automatically.
                        </p>
                    </div>

                    <div class="md:col-span-2 bg-gradient-to-br from-purple-600 to-pink-600 rounded-3xl p-8 text-white relative overflow-hidden hover:scale-[1.02] transition-transform border-2 border-purple-500/20">
                        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                        <div class="relative z-10">
                            <i class="ri-organization-chart text-5xl mb-4 opacity-90"></i>
                            <h3 class="text-3xl font-bold mb-3">16 Smart Categories</h3>
                            <p class="text-white/90 text-lg mb-4">
                                Pre-built categories for income and expenses. From salary to shopping, everything organized.
                            </p>
                            <div class="flex gap-2 flex-wrap">
                                <span class="px-3 py-1 bg-white/20 rounded-full text-sm border border-white/30">Salary</span>
                                <span class="px-3 py-1 bg-white/20 rounded-full text-sm border border-white/30">Shopping</span>
                                <span class="px-3 py-1 bg-white/20 rounded-full text-sm border border-white/30">Food</span>
                                <span class="px-3 py-1 bg-white/20 rounded-full text-sm border border-white/30">Transport</span>
                                <span class="px-3 py-1 bg-white/20 rounded-full text-sm border border-white/30">+12 more</span>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Feature 4 (1 col) + Feature 5 (1 col) + Feature 6 (1 col) -->
                    <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-3xl p-8 text-white relative overflow-hidden transition-all border-2 border-orange-400/20">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="relative z-10">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mb-4 border border-white/30">
                                <i class="ri-shield-check-line text-3xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">Secure & Private</h3>
                            <p class="text-white/90">
                                Your data stays yours. Bank-level encryption keeps everything safe.
                            </p>
                        </div>
                    </div>

                    <div class="bg-bp border-2 border-gray-200 rounded-3xl p-8 hover:border-secondary/50 transition-all">
                        <div class="w-14 h-14 bg-secondary/10 rounded-2xl flex items-center justify-center mb-4">
                            <i class="ri-smartphone-line text-3xl text-secondary"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-tp">Mobile Ready</h3>
                        <p class="text-ts">
                            Track on the go. Fully responsive design works on any device.
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-secondary to-blue-600 rounded-3xl p-8 text-white relative overflow-hidden transition-all border-2 border-secondary/20">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
                        <div class="relative z-10">
                            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mb-4 border border-white/30">
                                <i class="ri-infinity-line text-3xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">One-Time Payment</h3>
                            <p class="text-white/90">
                                No monthly fees. Pay once and track up to your transaction limit. Upgrade anytime if needed.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works - Timeline Style -->
        <section class="py-24 px-4 bg-gradient-to-br from-primary/5 via-secondary/5 to-bp relative overflow-hidden" data-aos="fade-up">
            <!-- Decorative Background -->
            <div class="absolute inset-0">
                <!-- Animated Blur Shapes -->
                <div class="absolute top-10 right-10 w-80 h-80 bg-primary/20 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-10 left-10 w-96 h-96 bg-secondary/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-purple-500/10 rounded-full blur-2xl animate-pulse" style="animation-delay: 3s;"></div>

                <!-- Grid Pattern -->
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMyIgY3k9IjMiIHI9IjEuNSIgZmlsbD0icmdiYSg1NCwzNiwxMTQsMC4wOCkiLz48L3N2Zz4=')] opacity-40"></div>
            </div>
            <div class="container max-w-6xl mx-auto relative z-10">
                <div class="text-center mb-16">
                    <span class="px-4 py-2 rounded-full bg-gradient-to-r from-secondary/10 to-secondary/10 text-secondary text-sm font-bold border border-secondary/20">
                        How It Works
                    </span>
                    <h2 class="text-4xl md:text-5xl font-black mt-4 text-tp">
                        Start Tracking in 3 Steps
                    </h2>
                </div>

                <div class="relative">
                    <!-- Vertical Line -->
                    <div class="absolute left-1/2 transform -translate-x-1/2 w-1 h-full bg-gradient-to-b from-primary via-secondary to-blue-500 hidden md:block rounded-full"></div>

                    <!-- Steps -->
                    <div class="space-y-16">
                        <!-- Step 1 -->
                        <div class="flex flex-col md:flex-row items-center gap-8">
                            <div class="flex-1 text-right md:pr-12">
                                <div class="bg-bp rounded-2xl p-6 border-2 border-gray-200 inline-block hover:border-primary/50 transition-all shadow-lg">
                                    <div class="text-sm text-primary font-bold mb-2">STEP 1</div>
                                    <h3 class="text-2xl font-bold mb-2 text-tp">Create Your Wallets</h3>
                                    <p class="text-ts">
                                        Add your cash, bank accounts, and cards. Set initial balances and you're ready.
                                    </p>
                                </div>
                            </div>
                            <div class="w-16 h-16 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-xl z-10 border-2 border-white">
                                1
                            </div>
                            <div class="flex-1 md:pl-12"></div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex flex-col md:flex-row items-center gap-8">
                            <div class="flex-1 md:pr-12"></div>
                            <div class="w-16 h-16 bg-gradient-to-br from-secondary to-primary rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-xl z-10 border-2 border-white">
                                2
                            </div>
                            <div class="flex-1 text-left md:pl-12">
                                <div class="bg-bp rounded-2xl p-6 border-2 border-gray-200 inline-block hover:border-secondary/50 transition-all shadow-lg">
                                    <div class="text-sm text-secondary font-bold mb-2">STEP 2</div>
                                    <h3 class="text-2xl font-bold mb-2 text-tp">Record Transactions</h3>
                                    <p class="text-ts">
                                        Add income, expenses, or transfers. Categorize and add notes for better tracking.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex flex-col md:flex-row items-center gap-8">
                            <div class="flex-1 text-right md:pr-12">
                                <div class="bg-bp rounded-2xl p-6 border-2 border-gray-200 inline-block hover:border-blue-500/50 transition-all shadow-lg">
                                    <div class="text-sm text-blue-600 font-bold mb-2">STEP 3</div>
                                    <h3 class="text-2xl font-bold mb-2 text-tp">Watch Your Progress</h3>
                                    <p class="text-ts">
                                        View charts, analyze spending patterns, and make smarter financial decisions.
                                    </p>
                                </div>
                            </div>
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-xl z-10 border-2 border-white">
                                3
                            </div>
                            <div class="flex-1 md:pl-12"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing - Modern Cards -->
        <section id="pricing" class="py-24 px-4 bg-white ">
            <div class="container max-w-7xl mx-auto">
                <div class="text-center mb-16" data-aos="fade-up">
                    <span class="px-4 py-2 rounded-full bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-500/10 dark:to-pink-500/10 text-purple-700 dark:text-purple-400 text-sm font-bold">
                        Pricing
                    </span>
                    <h2 class="text-4xl md:text-5xl font-black mt-4 mb-4 text-gray-900 ">
                        Simple, One-Time Pricing
                    </h2>
                    <p class="text-gray-600  max-w-2xl mx-auto">
                        Pay once, use forever. No monthly fees, no subscriptions. Just pick your limit.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" data-aos="fade-up" data-aos-delay="100">
                    <?php foreach ($plans as $index => $plan): ?>
                        <?php
                        $colors = [
                            'emerald' => ['from-primary/50', 'to-secondary/50', 'bg-primary/5', 'text-primary', 'border-primary/20'],
                            'blue' => ['from-blue-500', 'to-secondary/50', 'bg-blue-50', 'text-blue-700', 'border-blue-200'],
                            'purple' => ['from-purple-500', 'to-pink-500', 'bg-purple-50', 'text-purple-700', 'border-purple-200'],
                            'orange' => ['from-orange-500', 'to-red-500', 'bg-orange-50', 'text-orange-700', 'border-orange-200']
                        ];
                        $colorKeys = array_keys($colors);
                        $color = $colors[$colorKeys[$index % 4]];
                        $isPopular = $plan['title'] == 'Professional';
                        ?>
                        <div class="relative bg-white  rounded-3xl border <?= $isPopular ? 'border-2 border-primary/50 dark:border-primary/50' : 'border-gray-200 ' ?> p-8 hover:shadow-2xl transition-all hover:scale-105 <?= $isPopular ? 'ring-4 ring-primary/50/20' : '' ?>">
                            <?php if ($isPopular): ?>
                                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 px-4 py-1 bg-gradient-to-r from-primary to-secondary text-white text-sm font-bold rounded-full shadow-lg">
                                    POPULAR
                                </div>
                            <?php endif; ?>

                            <div class="mb-4">
                                <h3 class="text-2xl font-bold text-gray-900 "><?= e($plan['title']) ?></h3>
                                <p class="text-gray-600  text-sm mt-1"><?= e($plan['description']) ?></p>
                            </div>

                            <div class="mb-6">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-5xl font-black text-gray-900">
                                        <?= $plan['price'] == 0 ? 'FREE' : formatCurrency($plan['price']) ?>
                                    </span>
                                </div>
                                <div class="text-sm text-ts mt-1">
                                    <?= number_format($plan['transaction_limit']) ?> Transactions
                                </div>
                            </div>

                            <a href="<?= url('/auth/signup') ?>" class="group relative overflow-hidden block w-full py-3.5 text-center font-bold rounded-xl transition-all mb-6 border-2 <?= $isPopular ? 'bg-gradient-to-r from-primary to-secondary text-white border-transparent hover:border-primary/30' : 'bg-bp text-tp border-gray-300 hover:border-primary hover:bg-bs' ?>">
                                <?php if ($isPopular): ?>
                                    <div class="absolute inset-0 bg-white/20 transform translate-y-full group-hover:translate-y-0 transition-transform"></div>
                                <?php endif; ?>
                                <span class="relative flex items-center justify-center gap-2">
                                    <?php if ($isPopular): ?>
                                        <i class="ri-sparkling-line"></i>
                                    <?php endif; ?>
                                    Get Started
                                    <i class="ri-arrow-right-line group-hover:translate-x-1 transition-transform"></i>
                                </span>
                            </a>

                            <ul class="space-y-3 text-sm">
                                <?php
                                $features = explode("\r\n", $plan['features']);
                                foreach ($features as $feature):
                                    if (empty(trim($feature))) continue;
                                ?>
                                    <li class="flex items-start gap-2 text-gray-700 ">
                                        <i class="ri-checkbox-circle-fill text-primary/50 mt-0.5"></i>
                                        <span><?= e($feature) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>

            <!-- Accounting Software Subsection -->
            <div class="mt-16 bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-200 rounded-3xl p-8 md:p-12 text-center" data-aos="fade-up">
                <div class="mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-600 rounded-2xl mb-4">
                        <i class="ri-building-line text-3xl text-white"></i>
                    </div>
                    <h3 class="text-3xl md:text-4xl font-black text-tp mb-3">Want to Manage Accounting for Business?</h3>
                    <p class="text-lg text-ts max-w-2xl mx-auto">Subscribe to our professional Accounting Software for comprehensive business financial management.</p>
                </div>

                <?php
                $supportWhatsApp = $_ENV['SUPPORT_WHATSAPP'] ?? '923001234567';
                $supportEmail = $_ENV['SUPPORT_EMAIL'] ?? 'support@cashtrack.com';
                ?>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6 mt-8">
                    <a href="https://wa.me/<?= $supportWhatsApp ?>" target="_blank" class="flex items-center gap-3 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition-all">
                        <i class="ri-whatsapp-line text-2xl"></i>
                        <span>WhatsApp</span>
                    </a>
                    <a href="mailto:<?= $supportEmail ?>" class="flex items-center gap-3 px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all">
                        <i class="ri-mail-line text-2xl"></i>
                        <span>Email</span>
                    </a>
                </div>

            </div>
    </div>
    </section>

    <!-- FAQ Section - Modern Card Design -->
    <section class="py-24 px-4 bg-white ">
        <div class="container max-w-6xl mx-auto">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="px-4 py-2 rounded-full bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-500/10 dark:to-pink-500/10 text-purple-700 dark:text-purple-400 text-sm font-bold">
                    FAQ
                </span>
                <h2 class="text-4xl md:text-5xl font-black mt-4 mb-4 text-gray-900 ">
                    Frequently Asked Questions
                </h2>
                <p class="text-gray-600  max-w-2xl mx-auto">
                    Everything you need to know about CashTrack. Can't find the answer you're looking for? Feel free to contact us.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" data-aos="fade-up" data-aos-delay="100">
                <!-- FAQ 1 -->
                <div class="group bg-gradient-to-br from-primary/5 to-secondary/5 dark:from-primary/50/5 dark:to-secondary/50/5 rounded-2xl p-6 border-2 border-primary/20/50 dark:border-primary/50/20 hover:border-primary dark:hover:border-primary/50 hover:shadow-xl hover:shadow-primary/50/10 transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary/50 to-secondary/50 rounded-xl flex items-center justify-center text-white flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="ri-infinity-line text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900  mb-2">
                                How does the payment work?
                            </h3>
                            <p class="text-gray-700 leading-relaxed">
                                Pay once for your chosen plan - no monthly subscriptions. Each plan includes a set number of transactions. When you reach the limit, simply upgrade to continue tracking.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="group bg-gradient-to-br from-blue-50 to-secondary/5 dark:from-blue-500/5 dark:to-secondary/50/5 rounded-2xl p-6 border-2 border-blue-200/50 dark:border-blue-500/20 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-xl hover:shadow-blue-500/10 transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-secondary/50 rounded-xl flex items-center justify-center text-white flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="ri-arrow-up-line text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900  mb-2">
                                What happens when I hit my limit?
                            </h3>
                            <p class="text-gray-700  leading-relaxed">
                                You can still view all your past data, but you won't be able to add new transactions. Simply upgrade to a higher plan to continue tracking.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="group bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-500/5 dark:to-pink-500/5 rounded-2xl p-6 border-2 border-purple-200/50 dark:border-purple-500/20 hover:border-purple-400 dark:hover:border-purple-500 hover:shadow-xl hover:shadow-purple-500/10 transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center text-white flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="ri-gift-line text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900  mb-2">
                                Can I try it for free?
                            </h3>
                            <p class="text-gray-700  leading-relaxed">
                                Absolutely! Start with our Free Trial plan that includes 50 transactions and 2 wallets. No credit card required.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="group bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-500/5 dark:to-red-500/5 rounded-2xl p-6 border-2 border-orange-200/50 dark:border-orange-500/20 hover:border-orange-400 dark:hover:border-orange-500 hover:shadow-xl hover:shadow-orange-500/10 transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl flex items-center justify-center text-white flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="ri-shield-check-line text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900  mb-2">
                                Is my data secure?
                            </h3>
                            <p class="text-gray-700  leading-relaxed">
                                Yes! We use bank-level encryption to protect your financial data. Your information stays private and secure on our servers.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="group bg-gradient-to-br from-secondary/5 to-secondary/5 dark:from-secondary/50/5 dark:to-secondary/50/5 rounded-2xl p-6 border-2 border-secondary/20/50 dark:border-secondary/50/20 hover:border-secondary dark:hover:border-secondary/50 hover:shadow-xl hover:shadow-secondary/50/10 transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-secondary/50 to-secondary/50 rounded-xl flex items-center justify-center text-white flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="ri-wallet-3-line text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900  mb-2">
                                Can I track multiple wallets?
                            </h3>
                            <p class="text-gray-700  leading-relaxed">
                                Yes! Depending on your plan, you can track 2-5 wallets including cash, bank accounts, credit cards, and digital wallets.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 6 -->
                <div class="group bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-500/5 dark:to-purple-500/5 rounded-2xl p-6 border-2 border-indigo-200/50 dark:border-indigo-500/20 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-xl hover:shadow-indigo-500/10 transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center text-white flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="ri-refund-line text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900  mb-2">
                                Do you offer refunds?
                            </h3>
                            <p class="text-gray-700  leading-relaxed">
                                We offer a 30-day money-back guarantee. If you're not satisfied, contact our support team for a full refund.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section - Redesigned Attractive Version -->
    <section class="py-24 px-4 bg-gradient-to-br from-primary/5 via-secondary/5 to-bp relative overflow-hidden border-y-2 border-gray-200">
        <!-- Decorative Background Elements -->
        <div class="absolute inset-0">
            <!-- Blur Shapes -->
            <div class="absolute top-10 left-10 w-72 h-72 bg-primary/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-secondary/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-purple-500/10 rounded-full blur-2xl"></div>

            <!-- Grid Pattern -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMyIgY3k9IjMiIHI9IjEuNSIgZmlsbD0icmdiYSg1NCwzNiwxMTQsMC4xKSIvPjwvc3ZnPg==')] opacity-40"></div>
        </div>

        <!-- Main CTA Content -->
        <div class="container max-w-6xl mx-auto relative z-10">
            <div class="bg-bp/80 backdrop-blur-sm border-2 border-gray-200 rounded-3xl p-12 md:p-16">

                <!-- Top Icon Badge -->
                <div class="flex justify-center mb-8" data-aos="fade-down">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-primary to-secondary rounded-2xl blur-lg opacity-50"></div>
                        <div class="relative bg-gradient-to-r from-primary to-secondary p-4 rounded-2xl border-2 border-white">
                            <i class="ri-rocket-2-line text-4xl text-white"></i>
                        </div>
                    </div>
                </div>

                <!-- Heading -->
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-4xl md:text-6xl font-black text-tp mb-4">
                        Ready to Take <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-purple-600 to-secondary">Control?</span>
                    </h2>
                    <p class="text-xl md:text-2xl text-ts max-w-3xl mx-auto leading-relaxed">
                        Start tracking your expenses in under 60 seconds. No credit card required, no monthly fees. Just smart financial management.
                    </p>
                </div>

                <!-- Features Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center gap-4 p-4 bg-bs border-2 border-gray-200 rounded-xl hover:border-primary/50 transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary/10 to-secondary/10 rounded-xl flex items-center justify-center flex-shrink-0 border border-primary/20">
                            <i class="ri-check-double-line text-2xl text-primary"></i>
                        </div>
                        <div class="text-left">
                            <div class="font-bold text-tp">50 Free Transactions</div>
                            <div class="text-sm text-ts">No trial limits</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-bs border-2 border-gray-200 rounded-xl hover:border-secondary/50 transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-secondary/10 to-primary/10 rounded-xl flex items-center justify-center flex-shrink-0 border border-secondary/20">
                            <i class="ri-time-line text-2xl text-secondary"></i>
                        </div>
                        <div class="text-left">
                            <div class="font-bold text-tp">One-Time Payment</div>
                            <div class="text-sm text-ts">Pay once, upgrade when needed</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-bs border-2 border-gray-200 rounded-xl hover:border-purple-500/50 transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500/10 to-pink-500/10 rounded-xl flex items-center justify-center flex-shrink-0 border border-purple-500/20">
                            <i class="ri-shield-check-line text-2xl text-purple-600"></i>
                        </div>
                        <div class="text-left">
                            <div class="font-bold text-tp">Secure & Private</div>
                            <div class="text-sm text-ts">Your data, your control</div>
                        </div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8" data-aos="fade-up" data-aos-delay="200">
                    <a href="<?= url('/auth/signup') ?>" class="group relative px-10 py-4 bg-gradient-to-r from-primary to-secondary text-white font-bold rounded-xl border-2 border-transparent hover:border-primary/30 transition-all overflow-hidden">
                        <div class="absolute inset-0 bg-white/20 transform translate-y-full group-hover:translate-y-0 transition-transform"></div>
                        <span class="relative flex items-center gap-2">
                            <i class="ri-sparkling-line"></i>
                            Start Free Trial
                            <i class="ri-arrow-right-line group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </a>
                    <a href="<?= url('/pages/features') ?>" class="px-10 py-4 bg-bp text-tp font-bold rounded-xl border-2 border-gray-300 hover:border-primary hover:bg-bs transition-all flex items-center gap-2">
                        <i class="ri-information-line"></i>
                        Learn More
                    </a>
                </div>

                <!-- Trust Indicators -->
                <div class="flex flex-wrap items-center justify-center gap-8 text-sm" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-center gap-2 text-ts">
                        <div class="w-2 h-2 bg-primary rounded-full animate-pulse"></div>
                        <span>10,000+ Active Users</span>
                    </div>
                    <div class="flex items-center gap-2 text-ts">
                        <div class="w-2 h-2 bg-secondary rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
                        <span>1M+ Transactions Tracked</span>
                    </div>
                    <div class="flex items-center gap-2 text-ts">
                        <div class="w-2 h-2 bg-purple-600 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
                        <span>4.9★ Average Rating</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<?php
// Include footer
include __DIR__ . '/../includes/public-footer.php';
?>
</div>