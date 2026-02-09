<nav id="public-header"
    :class="{ 'py-2': scrolled }"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
    <div class="container max-w-7xl mx-auto px-4 transition-all duration-300"
        :class="{ 'bg-bp/95 border-2 border-gray-200 rounded-2xl px-6 my-2': scrolled }">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="<?= url('/') ?>" class="flex items-center gap-3">
                <img src="<?= asset('images/logo-full-color.webp') ?>" alt="CashTrack" class="h-8 w-auto">
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-1">
                <a href="<?= url('/') ?>" class="px-4 py-2 text-tp hover:text-primary font-medium transition-colors rounded-lg hover:bg-bs">
                    Home
                </a>
                <a href="<?= url('/pages/features') ?>" class="px-4 py-2 text-tp hover:text-primary font-medium transition-colors rounded-lg hover:bg-bs">
                    Features
                </a>
                <a href="<?= url('/#pricing') ?>" class="px-4 py-2 text-tp hover:text-primary font-medium transition-colors rounded-lg hover:bg-bs">
                    Pricing
                </a>
                <a href="<?= url('/pages/about') ?>" class="px-4 py-2 text-tp hover:text-primary font-medium transition-colors rounded-lg hover:bg-bs">
                    About
                </a>
                <a href="<?= url('/pages/contact') ?>" class="px-4 py-2 text-tp hover:text-primary font-medium transition-colors rounded-lg hover:bg-bs">
                    Contact
                </a>
            </div>

            <!-- Auth Buttons -->
            <div class="hidden md:flex items-center gap-2">
                <?php if (empty($_APP['user'])): ?>
                    <!-- Not Logged In - Show Login & Signup -->
                    <a href="<?= url('/auth/login') ?>" class="px-5 py-2 text-tp font-semibold hover:text-primary transition-colors rounded-lg hover:bg-bs">
                        Login
                    </a>
                    <a href="<?= url('/auth/signup') ?>" class="px-5 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-semibold rounded-lg border-2 border-transparent hover:border-primary/20 transition-all">
                        Get Started
                    </a>
                <?php else: ?>
                    <!-- Logged In - Show Dashboard Button -->
                    <a href="<?= url('/pages/dashboard') ?>" class="px-6 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-semibold rounded-lg border-2 border-transparent hover:border-primary/20 transition-all flex items-center gap-2">
                        <i class="ri-dashboard-line"></i>
                        Dashboard
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileOpen = !mobileOpen" class="md:hidden text-tp p-2 rounded-lg hover:bg-bs">
                <i class="ri-menu-line text-2xl" x-show="!mobileOpen"></i>
                <i class="ri-close-line text-2xl" x-show="mobileOpen"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden pb-4 border-t border-gray-200 mt-2">
            <div class="flex flex-col gap-2 pt-4">
                <a href="<?= url('/') ?>" class="px-4 py-2 text-tp hover:bg-bs rounded-lg transition-colors">
                    Home
                </a>
                <a href="<?= url('/pages/features') ?>" class="px-4 py-2 text-tp hover:bg-bs rounded-lg transition-colors">
                    Features
                </a>
                <a href="<?= url('/#pricing') ?>" class="px-4 py-2 text-tp hover:bg-bs rounded-lg transition-colors">
                    Pricing
                </a>
                <a href="<?= url('/pages/about') ?>" class="px-4 py-2 text-tp hover:bg-bs rounded-lg transition-colors">
                    About
                </a>
                <a href="<?= url('/pages/contact') ?>" class="px-4 py-2 text-tp hover:bg-bs rounded-lg transition-colors">
                    Contact
                </a>
                <div class="border-t border-gray-200 my-2"></div>

                <?php if (empty($_APP['user'])): ?>
                    <a href="<?= url('/auth/login') ?>" class="px-4 py-2 text-tp hover:bg-bs rounded-lg transition-colors">
                        Login
                    </a>
                    <a href="<?= url('/auth/signup') ?>" class="px-4 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-semibold rounded-lg text-center">
                        Get Started
                    </a>
                <?php else: ?>
                    <a href="<?= url('/pages/dashboard') ?>" class="px-4 py-2.5 bg-gradient-to-r from-primary to-secondary text-white font-semibold rounded-lg text-center flex items-center justify-center gap-2">
                        <i class="ri-dashboard-line"></i>
                        Dashboard
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>