<?php

$seoFile = __DIR__ . '/../includes/seo.json';
$pages = [];
if (file_exists($seoFile)) {
    $seoData = jd(file_get_contents($seoFile), true);
    // Filter out 'default', '404', 'sitemap', and 'password-reset'
    foreach ($seoData as $key => $meta) {
        if (in_array($key, ['default', '404', 'sitemap', 'password-reset'])) continue;

        $url = ($key === 'index') ? '/' : (
            (strpos($key, 'auth/') === 0 || strpos($key, 'pages/') === 0) ? '/' . $key : '/pages/' . $key
        );

        // Correct specific known paths if needed based on key names in seo.json
        if ($key === 'login' || $key === 'signup' || $key === 'password-reset') {
            $url = '/auth/' . $key;
        } elseif ($key === 'plans' || $key === 'proceed-to-checkout') {
            $url = '/' . $key;
        } elseif ($key === 'index') {
            $url = '/';
        } else {
            // defaults to /pages/key if not handled above, but let's check strict mapping
            // seo.json keys: privacy-policy, terms-of-service, etc. -> /pages/privacy-policy
            if (strpos($url, '/pages/') === false && strpos($url, '/auth/') === false && $url !== '/') {
                $url = '/pages/' . $key;
            }
        }

        $pages[] = [
            'title' => $meta['title'],
            'desc' => $meta['description'],
            'url' => $url
        ];
    }
}
?>
<div x-data="{ mobileOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">
    <?php include __DIR__ . '/../includes/public-header.php'; ?>

    <div class="max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-12 flex-grow">

        <!-- Header -->
        <header class="mb-12 text-center md:text-left flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-gray-200 pb-8">
            <div>
                <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 md:text-5xl flex items-center gap-3">
                    <i class="ri-map-2-line text-accent"></i> Sitemap
                </h1>
                <p class="mt-4 text-lg text-slate-600">Explore all the pages available on <?= e($_ENV['APP_NAME']) ?>.</p>
            </div>
            <a href="<?= url('/') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition-colors">
                <i class="ri-home-4-line mr-2"></i> Back to Home
            </a>
        </header>

        <!-- Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($pages as $page): ?>
                <a href="<?= url($page['url']) ?>" class="group block p-6 bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-lg hover:border-accent/30 transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <h2 class="text-xl font-bold text-gray-800 group-hover:text-accent mb-2 transition-colors">
                            <?php
                            // Clean title for display (remove | AppName)
                            $displayTitle = explode(' | ', $page['title'])[0];
                            echo e($displayTitle);
                            ?>
                        </h2>
                        <i class="ri-arrow-right-up-line text-gray-300 group-hover:text-accent text-xl transition-colors"></i>
                    </div>
                    <p class="text-gray-500 text-sm line-clamp-2 mt-2">
                        <?= e($page['desc']) ?>
                    </p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../includes/public-footer.php'; ?>
</div>