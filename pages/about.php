<div x-data="{ mobileOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">
    <?php include __DIR__ . '/../includes/public-header.php'; ?>

    <div class="bg-bp text-tp font-sans selection:bg-primary selection:text-white">

        <!-- Hero with Mission Statement -->
        <section class="pt-32 pb-16 px-4 bg-gradient-to-br from-primary/5 via-secondary/5 to-bp relative overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute inset-0">
                <div class="absolute top-20 right-10 w-96 h-96 bg-primary/20 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-20 left-10 w-[500px] h-[500px] bg-secondary/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMyIgY3k9IjMiIHI9IjEuNSIgZmlsbD0icmdiYSg1NCwzNiwxMTQsMC4wOCkiLz48L3N2Zz4=')] opacity-40"></div>
            </div>

            <div class="container max-w-4xl mx-auto text-center relative z-10" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
                    <i class="ri-information-line"></i>
                    About CashTrack
                </div>
                <h1 class="text-5xl md:text-6xl font-black mb-6 text-tp">
                    We Believe Everyone Deserves <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">
                        Financial Clarity
                    </span>
                </h1>
                <p class="text-xl text-ts leading-relaxed max-w-3xl mx-auto">
                    CashTrack was built out of frustration with complicated expense trackers. We wanted something simple, powerful, and affordable - not another monthly subscription trap.
                </p>
            </div>
        </section>

        <!-- Story Section - Card Style -->
        <section class="py-20 px-4 bg-bp">
            <div class="container max-w-6xl mx-auto">
                <div class="grid md:grid-cols-2 gap-12 items-center" data-aos="fade-up">
                    <div>
                        <h2 class="text-4xl font-black mb-6 text-tp">Our Story</h2>
                        <p class="text-lg text-ts mb-4 leading-relaxed">
                            Like most people, we struggled to keep track of where our money was going. Bank apps only show one account. Spreadsheets get messy. Most expense trackers want you to pay monthly forever.
                        </p>
                        <p class="text-lg text-ts mb-4 leading-relaxed">
                            So we built CashTrack - a simple, powerful tool that lets you track cash, bank accounts, cards, and digital wallets all in one place. Pay once, use forever.
                        </p>
                        <p class="text-lg text-ts leading-relaxed">
                            Today, thousands of users trust CashTrack to manage their finances without recurring fees or complicated features they'll never use.
                        </p>
                    </div>
                    <div class="relative h-96 bg-gradient-to-br from-primary/10 to-secondary/10 rounded-3xl flex items-center justify-center border border-primary/20">
                        <i class="ri-lightbulb-flash-fill text-9xl text-primary/40"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- Values - Icon Grid -->
        <section class="py-20 px-4 bg-bs">
            <div class="container max-w-7xl mx-auto">
                <div class="text-center mb-16" data-aos="fade-up">
                    <h2 class="text-4xl font-black mb-4 text-tp">Our Values</h2>
                    <p class="text-ts max-w-2xl mx-auto">
                        The principles that guide everything we build
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="bg-bp rounded-2xl p-8 text-center border border-gray-200 hover:border-primary/50 transition-all hover:shadow-xl">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="ri-lightbulb-line text-3xl text-blue-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-tp">Simplicity First</h3>
                        <p class="text-ts">
                            No bloated features. Just the essentials you need to track your money effectively.
                        </p>
                    </div>

                    <div class="bg-bp rounded-2xl p-8 text-center border border-gray-200 hover:border-secondary/50 transition-all hover:shadow-xl">
                        <div class="w-16 h-16 bg-secondary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="ri-shield-check-line text-3xl text-secondary"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-tp">Privacy Matters</h3>
                        <p class="text-ts">
                            Your financial data is yours. We never sell it, share it, or use it for anything but serving you.
                        </p>
                    </div>

                    <div class="bg-bp rounded-2xl p-8 text-center border border-gray-200 hover:border-purple-500/50 transition-all hover:shadow-xl">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="ri-heart-line text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-tp">Fair Pricing</h3>
                        <p class="text-ts">
                            No monthly traps. Pay once for what you need, upgrade when you're ready. It's that simple.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section (Generic) -->
        <section class="py-20 px-4 bg-bp">
            <div class="container max-w-6xl mx-auto text-center">
                <h2 class="text-4xl font-black mb-6 text-tp" data-aos="fade-up">Built by People Who Get It</h2>
                <p class="text-xl text-ts max-w-3xl mx-auto mb-12" data-aos="fade-up" data-aos-delay="100">
                    We're a small team of developers and designers who believe financial tools should be accessible, affordable, and actually useful. No corporate bloat, just people helping people.
                </p>

                <div class="grid md:grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-6 bg-bs rounded-3xl border border-gray-200">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full mx-auto mb-4 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                            <i class="ri-code-s-slash-line"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-tp">Development Team</h3>
                        <p class="text-ts text-sm">Building features that actually matter</p>
                    </div>

                    <div class="p-6 bg-bs rounded-3xl border border-gray-200">
                        <div class="w-20 h-20 bg-gradient-to-br from-pink-500 to-red-500 rounded-full mx-auto mb-4 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                            <i class="ri-palette-line"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-tp">Design Team</h3>
                        <p class="text-ts text-sm">Making finance tracking beautiful</p>
                    </div>

                    <div class="p-6 bg-bs rounded-3xl border border-gray-200">
                        <div class="w-20 h-20 bg-gradient-to-br from-primary to-secondary/50 rounded-full mx-auto mb-4 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                            <i class="ri-customer-service-line"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-tp">Support Team</h3>
                        <p class="text-ts text-sm">Here when you need help</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Row -->
        <section class="py-20 px-4 bg-gradient-to-r from-primary via-purple-600 to-secondary relative overflow-hidden">
            <!-- Decorative bg -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMyIgY3k9IjMiIHI9IjEuNSIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjEpIi8+PC9zdmc+')] opacity-20"></div>

            <div class="container max-w-7xl mx-auto relative z-10">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white" data-aos="zoom-in">
                    <div>
                        <div class="text-5xl font-black mb-2">50+</div>
                        <div class="text-white/80">Free Transactions</div>
                    </div>
                    <div>
                        <div class="text-5xl font-black mb-2">16</div>
                        <div class="text-white/80">Built-in Categories</div>
                    </div>
                    <div>
                        <div class="text-5xl font-black mb-2">5</div>
                        <div class="text-white/80">Wallet Types</div>
                    </div>
                    <div>
                        <div class="text-5xl font-black mb-2">₹</div>
                        <div class="text-white/80">One-Time Payment</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-20 px-4 mb-16 bg-bs">
            <div class="container max-w-4xl mx-auto text-center" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-black mb-6 text-tp">
                    Join Thousands of Smart Savers
                </h2>
                <p class="text-xl text-ts mb-8">
                    Start tracking your finances the simple way. No credit card required.
                </p>
                <a href="<?= url('/auth/signup') ?>" class="inline-block px-10 py-5 bg-gradient-to-r from-primary to-secondary text-white font-bold rounded-2xl hover:shadow-2xl hover:shadow-primary/50 hover:scale-105 transition-all">
                    Get Started Free <i class="ri-arrow-right-line ml-2"></i>
                </a>
            </div>
        </section>

    </div>

    <?php include __DIR__ . '/../includes/public-footer.php'; ?>
</div>