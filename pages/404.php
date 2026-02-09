<div x-data="{ mobileOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">
    <?php include __DIR__ . '/../includes/public-header.php'; ?>

    <div class="min-h-[80vh] flex flex-col items-center justify-center bg-white dark:bg-gray-900 text-center px-4 sm:px-6 relative overflow-hidden transition-colors duration-300">

        <!-- Background Elements -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/4 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <!-- Inner Container -->
        <div class="relative z-10 max-w-lg w-full" data-aos="zoom-in">

            <!-- Icon -->
            <div class="flex items-center justify-center mb-8">
                <div class="w-32 h-32 flex items-center justify-center rounded-3xl bg-indigo-50 dark:bg-white/5 border border-indigo-100 dark:border-white/10 text-indigo-600 dark:text-indigo-400 shadow-xl rotate-12 hover:rotate-0 transition-transform duration-500">
                    <i class="ri-error-warning-line text-6xl"></i>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-6xl sm:text-8xl font-black text-gray-900 dark:text-white mb-2 tracking-tighter">
                404
            </h1>
            <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-6">Page Not Found</h2>

            <!-- Description -->
            <p class="text-gray-500 dark:text-gray-400 text-lg mb-10 leading-relaxed">
                The page you’re looking for doesn’t exist or may have been moved.
            </p>

            <!-- Button -->
            <a href="<?= url('/') ?>"
                class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 dark:hover:bg-indigo-500 hover:scale-105 transition-all shadow-lg shadow-indigo-500/25">
                <i class="ri-home-4-line text-lg"></i>
                Return Home
            </a>

        </div>
    </div>

    <?php include __DIR__ . '/../includes/public-footer.php'; ?>
</div>