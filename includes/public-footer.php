<!-- Footer - Professional Sleek Design -->
<footer class="relative bg-gradient-to-b from-bp to-bs text-tp border-t-2 border-gray-200">
    <div class="container max-w-7xl mx-auto px-4 py-12">

        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-12 mb-12">

            <!-- Brand Section -->
            <div class="lg:col-span-4">
                <div class="flex items-center gap-3 mb-4">
                    <img src="<?= asset('images/logo-full-color.webp') ?>" alt="CashTrack" class="h-9 w-auto">
                </div>
                <p class="text-ts text-sm leading-relaxed mb-6 max-w-sm">
                    Professional expense tracking for smart savers. Lifetime access with no recurring fees. Take control of your financial journey today.
                </p>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="text-center p-3 bg-bs rounded-xl border border-gray-200">
                        <div class="text-lg font-bold text-primary">10K+</div>
                        <div class="text-xs text-ts">Users</div>
                    </div>
                    <div class="text-center p-3 bg-bs rounded-xl border border-gray-200">
                        <div class="text-lg font-bold text-primary">50K+</div>
                        <div class="text-xs text-ts">Wallets</div>
                    </div>
                    <div class="text-center p-3 bg-bs rounded-xl border border-gray-200">
                        <div class="text-lg font-bold text-primary">1M+</div>
                        <div class="text-xs text-ts">Tracked</div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="lg:col-span-2">
                <h3 class="text-tp font-bold text-sm uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">
                    Product
                </h3>
                <ul class="space-y-2.5">
                    <li>
                        <a href="<?= url('/pages/features') ?>" class="text-sm text-ts hover:text-primary transition-colors inline-flex items-center gap-2 group">
                            <i class="ri-arrow-right-s-line text-xs group-hover:translate-x-1 transition-transform"></i>
                            Features
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/#pricing') ?>" class="text-sm text-ts hover:text-primary transition-colors inline-flex items-center gap-2 group">
                            <i class="ri-arrow-right-s-line text-xs group-hover:translate-x-1 transition-transform"></i>
                            Pricing
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/auth/signup') ?>" class="text-sm text-ts hover:text-primary transition-colors inline-flex items-center gap-2 group">
                            <i class="ri-arrow-right-s-line text-xs group-hover:translate-x-1 transition-transform"></i>
                            Get Started
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/') ?>" class="text-sm text-ts hover:text-primary transition-colors inline-flex items-center gap-2 group">
                            <i class="ri-arrow-right-s-line text-xs group-hover:translate-x-1 transition-transform"></i>
                            Home
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Company -->
            <div class="lg:col-span-2">
                <h3 class="text-tp font-bold text-sm uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">
                    Company
                </h3>
                <ul class="space-y-2.5">
                    <li>
                        <a href="<?= url('/pages/about') ?>" class="text-sm text-ts hover:text-primary transition-colors inline-flex items-center gap-2 group">
                            <i class="ri-arrow-right-s-line text-xs group-hover:translate-x-1 transition-transform"></i>
                            About Us
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/pages/contact') ?>" class="text-sm text-ts hover:text-primary transition-colors inline-flex items-center gap-2 group">
                            <i class="ri-arrow-right-s-line text-xs group-hover:translate-x-1 transition-transform"></i>
                            Contact
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-sm text-ts hover:text-primary transition-colors inline-flex items-center gap-2 group">
                            <i class="ri-arrow-right-s-line text-xs group-hover:translate-x-1 transition-transform"></i>
                            Careers
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-sm text-ts hover:text-primary transition-colors inline-flex items-center gap-2 group">
                            <i class="ri-arrow-right-s-line text-xs group-hover:translate-x-1 transition-transform"></i>
                            Blog
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Legal -->
            <div class="lg:col-span-2">
                <h3 class="text-tp font-bold text-sm uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">
                    Legal
                </h3>
                <ul class="space-y-2.5">
                    <li>
                        <a href="<?= url('/pages/privacy-policy') ?>" class="text-sm text-ts hover:text-primary transition-colors inline-flex items-center gap-2 group">
                            <i class="ri-arrow-right-s-line text-xs group-hover:translate-x-1 transition-transform"></i>
                            Privacy
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/pages/terms-of-service') ?>" class="text-sm text-ts hover:text-primary transition-colors inline-flex items-center gap-2 group">
                            <i class="ri-arrow-right-s-line text-xs group-hover:translate-x-1 transition-transform"></i>
                            Terms
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/pages/cookie-policy') ?>" class="text-sm text-ts hover:text-primary transition-colors inline-flex items-center gap-2 group">
                            <i class="ri-arrow-right-s-line text-xs group-hover:translate-x-1 transition-transform"></i>
                            Cookies
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('/pages/gdpr-compliance') ?>" class="text-sm text-ts hover:text-primary transition-colors inline-flex items-center gap-2 group">
                            <i class="ri-arrow-right-s-line text-xs group-hover:translate-x-1 transition-transform"></i>
                            GDPR
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="lg:col-span-2">
                <h3 class="text-tp font-bold text-sm uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">
                    Stay Updated
                </h3>
                <p class="text-xs text-ts mb-3">Get tips, updates, and exclusive offers.</p>
                <div class="space-y-2">
                    <input type="email" placeholder="Your email"
                        class="w-full px-3 py-2 text-sm bg-bp border-2 border-gray-200 rounded-lg text-tp placeholder-gray-400 focus:outline-none focus:border-primary transition-colors">
                    <button class="w-full px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white text-sm font-semibold rounded-lg border-2 border-transparent hover:border-primary/20 transition-all">
                        Subscribe
                    </button>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t-2 border-gray-200 pt-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">

                <!-- Copyright -->
                <div class="flex items-center gap-4 text-sm text-ts">
                    <span>&copy; <?= date('Y') ?> CashTrack</span>
                    <span class="hidden md:inline">•</span>
                    <span class="flex items-center gap-1.5">
                        <i class="ri-shield-check-line text-secondary"></i>
                        Secure & Private
                    </span>
                </div>

                <!-- Social Links -->
                <div class="flex items-center gap-2">
                    <a href="#" class="w-9 h-9 bg-bs border-2 border-gray-200 hover:border-primary hover:bg-primary rounded-lg flex items-center justify-center transition-all group">
                        <i class="ri-twitter-x-line text-ts group-hover:text-white text-sm"></i>
                    </a>
                    <a href="#" class="w-9 h-9 bg-bs border-2 border-gray-200 hover:border-primary hover:bg-primary rounded-lg flex items-center justify-center transition-all group">
                        <i class="ri-facebook-fill text-ts group-hover:text-white text-sm"></i>
                    </a>
                    <a href="#" class="w-9 h-9 bg-bs border-2 border-gray-200 hover:border-primary hover:bg-primary rounded-lg flex items-center justify-center transition-all group">
                        <i class="ri-linkedin-fill text-ts group-hover:text-white text-sm"></i>
                    </a>
                    <a href="#" class="w-9 h-9 bg-bs border-2 border-gray-200 hover:border-primary hover:bg-primary rounded-lg flex items-center justify-center transition-all group">
                        <i class="ri-instagram-line text-ts group-hover:text-white text-sm"></i>
                    </a>
                    <a href="#" class="w-9 h-9 bg-bs border-2 border-gray-200 hover:border-primary hover:bg-primary rounded-lg flex items-center justify-center transition-all group">
                        <i class="ri-github-fill text-ts group-hover:text-white text-sm"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>