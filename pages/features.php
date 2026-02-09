<div x-data="{ mobileOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">
    <?php include __DIR__ . '/../includes/public-header.php'; ?>

    <div class="bg-bp text-tp font-sans selection:bg-primary selection:text-white">

        <!-- Hero -->
        <section class="pt-32 pb-20 px-4 bg-gradient-to-br from-primary/5 via-secondary/5 to-bp relative overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute inset-0">
                <div class="absolute top-20 left-10 w-96 h-96 bg-primary/20 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-20 right-10 w-[500px] h-[500px] bg-secondary/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMyIgY3k9IjMiIHI9IjEuNSIgZmlsbD0icmdiYSg1NCwzNiwxMTQsMC4wOCkiLz48L3N2Zz4=')] opacity-40"></div>
            </div>

            <div class="container max-w-5xl mx-auto text-center relative z-10" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                    <i class="ri-magic-line"></i>
                    Powerful Features
                </div>
                <h1 class="text-5xl md:text-6xl font-black mb-6 text-tp">
                    Built for <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Effortless</span> Tracking
                </h1>
                <p class="text-xl text-ts max-w-3xl mx-auto">
                    Every feature designed to make expense tracking faster, smarter, and more intuitive than ever before.
                </p>
            </div>
        </section>

        <!-- Feature Deep-Dive Grid -->
        <section class="py-20 px-4 bg-bp">
            <div class="container max-w-7xl mx-auto">
                <!-- Feature 1: Wallets (Left Aligned) -->
                <div class="grid md:grid-cols-2 gap-12 items-center mb-24" data-aos="fade-right">
                    <div>
                        <div class="inline-block px-3 py-1 bg-primary/10 text-primary border border-primary/20 rounded-full text-sm font-bold mb-4">
                            Core Feature
                        </div>
                        <h2 class="text-4xl font-black mb-4 text-tp">Multiple Wallet Types</h2>
                        <p class="text-lg text-ts mb-6">
                            Track all your money in one place. Whether it's cash in your pocket, money in the bank, credit cards, or crypto wallets - CashTrack handles it all.
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <i class="ri-money-dollar-circle-line text-2xl text-primary mt-1"></i>
                                <div>
                                    <div class="font-bold text-tp">Cash Wallets</div>
                                    <div class="text-ts text-sm">Physical money tracking</div>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="ri-bank-line text-2xl text-blue-600 mt-1"></i>
                                <div>
                                    <div class="font-bold text-tp">Bank Accounts</div>
                                    <div class="text-ts text-sm">Track multiple accounts</div>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="ri-bank-card-line text-2xl text-purple-600 mt-1"></i>
                                <div>
                                    <div class="font-bold text-tp">Credit & Debit Cards</div>
                                    <div class="text-ts text-sm">Monitor card spending</div>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="ri-smartphone-line text-2xl text-orange-600 mt-1"></i>
                                <div>
                                    <div class="font-bold text-tp">Mobile Wallets</div>
                                    <div class="text-ts text-sm">JazzCash, Easypaisa, etc.</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="relative h-96 bg-gradient-to-br from-primary/10 to-secondary/10 rounded-3xl flex items-center justify-center border border-primary/20">
                        <i class="ri-wallet-3-fill text-9xl text-primary/40"></i>
                    </div>
                </div>

                <!-- Feature 2: Categories (Right Aligned) -->
                <div class="grid md:grid-cols-2 gap-12 items-center mb-24" data-aos="fade-left">
                    <div class="order-2 md:order-1 relative h-96 bg-gradient-to-br from-purple-50 to-pink-50 rounded-3xl flex items-center justify-center border border-purple-200">
                        <i class="ri-folder-chart-fill text-9xl text-purple-600/40"></i>
                    </div>
                    <div class="order-1 md:order-2">
                        <div class="inline-block px-3 py-1 bg-purple-100 text-purple-700 border border-purple-200 rounded-full text-sm font-bold mb-4">
                            Smart Organization
                        </div>
                        <h2 class="text-4xl font-black mb-4 text-tp">16 Pre-Built Categories</h2>
                        <p class="text-lg text-ts mb-6">
                            No setup needed. Start tracking immediately with our carefully curated categories for both income and expenses.
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 bg-green-50 rounded-xl border border-green-200">
                                <div class="text-xs text-green-700 font-bold mb-1">INCOME</div>
                                <div class="text-sm text-ts">Salary, Business</div>
                            </div>
                            <div class="p-3 bg-red-50 rounded-xl border border-red-200">
                                <div class="text-xs text-red-700 font-bold mb-1">EXPENSE</div>
                                <div class="text-sm text-ts">Food, Transport</div>
                            </div>
                            <div class="p-3 bg-blue-50 rounded-xl border border-blue-200">
                                <div class="text-xs text-blue-700 font-bold mb-1">INCOME</div>
                                <div class="text-sm text-ts">Freelance, Investment</div>
                            </div>
                            <div class="p-3 bg-orange-50 rounded-xl border border-orange-200">
                                <div class="text-xs text-orange-700 font-bold mb-1">EXPENSE</div>
                                <div class="text-sm text-ts">Bills, Shopping</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 3: Double Entry (Left Aligned) -->
                <div class="grid md:grid-cols-2 gap-12 items-center mb-24" data-aos="fade-right">
                    <div>
                        <div class="inline-block px-3 py-1 bg-blue-100 text-blue-700 border border-blue-200 rounded-full text-sm font-bold mb-4">
                            Accounting Standard
                        </div>
                        <h2 class="text-4xl font-black mb-4 text-tp">Double-Entry Transfers</h2>
                        <p class="text-lg text-ts mb-6">
                            Move money between wallets with proper accounting. Every transfer creates two entries - one debit and one credit - just like professional bookkeeping.
                        </p>
                        <div class="bg-bs rounded-2xl p-6 border border-gray-200">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="flex-1">
                                    <div class="text-sm text-ts">From</div>
                                    <div class="font-bold text-tp flex items-center gap-2">
                                        <i class="ri-wallet-line text-red-500"></i>
                                        Cash Wallet
                                    </div>
                                </div>
                                <i class="ri-arrow-right-line text-2xl text-blue-600"></i>
                                <div class="flex-1">
                                    <div class="text-sm text-ts">To</div>
                                    <div class="font-bold text-tp flex items-center gap-2">
                                        <i class="ri-bank-line text-primary"></i>
                                        Bank Account
                                    </div>
                                </div>
                            </div>
                            <?php
                            $currencyConfig = $_ENV['CURRENCY'] ?? 'PKR, post';
                            $currencySymbol = trim(explode(',', $currencyConfig)[0]);
                            ?>
                            <div class="text-center text-3xl font-black text-blue-600"><?= $currencySymbol ?> 5,000</div>
                            <div class="text-center text-xs text-ts mt-2">Creates 2 transaction entries automatically</div>
                        </div>
                    </div>
                    <div class="relative h-96 bg-gradient-to-br from-blue-50 to-secondary/10 rounded-3xl flex items-center justify-center border border-blue-200">
                        <i class="ri-exchange-funds-fill text-9xl text-blue-600/40"></i>
                    </div>
                </div>

                <!-- Feature 4: Analytics (Right Aligned) -->
                <div class="grid md:grid-cols-2 gap-12 items-center" data-aos="fade-left">
                    <div class="order-2 md:order-1 relative h-96 bg-gradient-to-br from-orange-50 to-red-50 rounded-3xl flex items-center justify-center border border-orange-200">
                        <i class="ri-pie-chart-fill text-9xl text-orange-600/40"></i>
                    </div>
                    <div class="order-1 md:order-2">
                        <div class="inline-block px-3 py-1 bg-orange-100 text-orange-700 border border-orange-200 rounded-full text-sm font-bold mb-4">
                            Insights
                        </div>
                        <h2 class="text-4xl font-black mb-4 text-tp">Visual Analytics</h2>
                        <p class="text-lg text-ts mb-6">
                            Beautiful charts and graphs that help you understand your spending patterns at a glance.
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3">
                                <i class="ri-line-chart-line text-xl text-orange-600"></i>
                                <span class="text-ts">Income vs Expense trends</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="ri-pie-chart-line text-xl text-orange-600"></i>
                                <span class="text-ts">Category breakdown charts</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="ri-bar-chart-line text-xl text-orange-600"></i>
                                <span class="text-ts">Monthly comparisons</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="ri-funds-line text-xl text-orange-600"></i>
                                <span class="text-ts">Balance history tracking</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Other Features Grid -->
        <section class="py-20 px-4 bg-bs">
            <div class="container max-w-7xl mx-auto">
                <div class="text-center mb-16" data-aos="fade-up">
                    <h2 class="text-4xl font-black mb-4 text-tp">And Much More...</h2>
                    <p class="text-ts">Everything you need for complete financial control</p>
                </div>

                <div class="grid md:grid-cols-3 gap-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="bg-bp rounded-2xl p-6 border-2 border-gray-200 hover:border-secondary/50 transition-all">
                        <div class="w-12 h-12 bg-secondary/10 rounded-xl flex items-center justify-center mb-4">
                            <i class="ri-smartphone-line text-2xl text-secondary"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-tp">Mobile Responsive</h3>
                        <p class="text-ts">Track on any device - phone, tablet, or desktop. Fully responsive design.</p>
                    </div>

                    <div class="bg-bp rounded-2xl p-6 border-2 border-gray-200 hover:border-violet-500/50 transition-all">
                        <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center mb-4">
                            <i class="ri-shield-check-line text-2xl text-violet-600"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-tp">Secure & Private</h3>
                        <p class="text-ts">Bank-level encryption. Your data is always safe and never shared.</p>
                    </div>

                    <div class="bg-bp rounded-2xl p-6 border-2 border-gray-200 hover:border-pink-500/50 transition-all">
                        <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center mb-4">
                            <i class="ri-file-text-line text-2xl text-pink-600"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-tp">Detailed Notes</h3>
                        <p class="text-ts">Add notes to every transaction. Remember why you spent that money.</p>
                    </div>

                    <div class="bg-bp rounded-2xl p-6 border-2 border-gray-200 hover:border-primary/50 transition-all">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4">
                            <i class="ri-calendar-check-line text-2xl text-primary"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-tp">Date Tracking</h3>
                        <p class="text-ts">Set custom dates for transactions. Perfect for backdating entries.</p>
                    </div>

                    <div class="bg-bp rounded-2xl p-6 border-2 border-gray-200 hover:border-blue-500/50 transition-all">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                            <i class="ri-currency-line text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-tp">Multi-Currency</h3>
                        <p class="text-ts">Track wallets in any currency you prefer.</p>
                    </div>

                    <div class="bg-bp rounded-2xl p-6 border-2 border-gray-200 hover:border-orange-500/50 transition-all">
                        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                            <i class="ri-vip-crown-line text-2xl text-orange-600"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-tp">Transaction-Based Plans</h3>
                        <p class="text-ts">Pay once for your transaction limit. No monthly subscriptions or hidden fees.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-20 px-4 mb-16 bg-gradient-to-br from-primary/5 via-secondary/5 to-bp border-t border-gray-200">
            <div class="container max-w-4xl mx-auto text-center" data-aos="zoom-in">
                <h2 class="text-4xl md:text-5xl font-black mb-6 text-tp">
                    Experience All Features Free
                </h2>
                <p class="text-xl text-ts mb-8">
                    Start with 50 free transactions and 2 wallets. No credit card required.
                </p>
                <a href="<?= url('/auth/signup') ?>" class="inline-block px-10 py-5 bg-gradient-to-r from-primary to-secondary text-white font-bold rounded-2xl hover:shadow-2xl hover:shadow-primary/50 hover:scale-105 transition-all">
                    Start Free Trial <i class="ri-arrow-right-line ml-2"></i>
                </a>
            </div>
        </section>

    </div>

    <?php include __DIR__ . '/../includes/public-footer.php'; ?>
</div>