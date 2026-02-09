<?php
global $path;
$current_slug = $contentPath === "pages/404.php" ? '404' : end($path);
$openRoutes = array_map(function ($r) {
    $parts = explode('/', $r);
    return end($parts);
}, $_APP['OPEN_ROUTES'] ?? []);
$plainRoute = $current_slug;
$showLayout = $current_slug !== "404" && !in_array($plainRoute, $openRoutes) && $path[0] === 'pages';
// Role-based filtering
if (isset($_APP['user']) && !empty($_APP['user'])) {
    // Ensure role is fetched freshly
    if (!isset($_APP['user']['role']) || empty($_APP['user']['role'])) {
        $roleData = fetch(
            'app_settings',
            ['type' => 'roles', 'id' => $_APP['user']['role_id']],
            'one',
            ['name']
        );
        $_APP['user']['role'] = $roleData['name'] ?? 'User';
    }

    $filteredMenu = filterMenuByRole($menu, $_APP['user']['role']);
} else {
    // If no user session, show public menu (or full menu if that's the default behavior for guests)
    // Assuming guests shouldn't see protected routes, but existing logic showed $menu.
    // If 'loggedin' cookie was the only check, removing it means we rely on session.
    // If not logged in, we probably shouldn't show the dashboard menu at all, but let's stick to existing fallback.
    $filteredMenu = $menu;
}

$initialOpenKey = null;
findOpenKeyRecursive($filteredMenu, $current_slug, $initialOpenKey);
$links = [
    'dev' => [
        // CSS (head)
        'tailwind'     => asset('js/tailwind.js'),
        'rubik_font'   => asset('css/rubik-font.css'),
        'aos_css'      => asset('css/aos.css'),
        'select2_css'  => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
        'remixicon'    => asset('vendor/remixicons/remixicon.css'),
        'custom_css'   => asset('css/custom.css'),

        // JS (footer)
        'jquery'       => asset('js/jquery.js'),
        'aos_js'       => asset('js/aos.js'),
        'alpine'       => asset('js/alpine.js'),
        'select2_js'   => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
        'custom_js'    => asset('js/custom.js'),
    ],
    'prod' => [
        // CSS (head)
        'tailwind'     => 'https://cdn.tailwindcss.com/3.4.17',
        'rubik_font'   => 'https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap',
        'aos_css'      => 'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css',
        'select2_css'  => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
        'remixicon'    => 'https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.min.css',
        'custom_css'   => asset('css/custom.css'), // minified in prod build

        // JS (footer)
        'jquery'       => 'https://cdn.jsdelivr.net/npm/jquery@3/dist/jquery.min.js',
        'aos_js'       => 'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js',
        'alpine'       => 'https://cdn.jsdelivr.net/npm/alpinejs@3.14/dist/cdn.min.js',
        'select2_js'   => 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
        'custom_js'    => asset('js/custom.js'), // minified in prod
    ],
];
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <?php
    // Get SEO Meta Data
    $seo = getSeoMeta($current_slug);
    ?>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />

    <!-- SEO Meta Tags -->
    <title><?= e($seo->title) ?></title>
    <meta name="description" content="<?= e($seo->description) ?>">
    <meta name="keywords" content="<?= e($seo->keywords) ?>">
    <meta name="robots" content="<?= e($seo->robots) ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e($seo->url) ?>">
    <meta property="og:title" content="<?= e($seo->title) ?>">
    <meta property="og:description" content="<?= e($seo->description) ?>">
    <meta property="og:image" content="<?= e($seo->image) ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= e($seo->url) ?>">
    <meta property="twitter:title" content="<?= e($seo->title) ?>">
    <meta property="twitter:description" content="<?= e($seo->description) ?>">
    <meta property="twitter:image" content="<?= e($seo->image) ?>">

    <link rel="icon" href="<?= asset($_ENV['APP_ICON']) ?>">
    <?php
    $plan_usage_text = '';
    if (isset($_APP['user']) && !empty($_APP['user'])) {
        $user_id = $_APP['user']['id'];
        $isSuperUser = isSuperUser();
        if ($isSuperUser) {
            $plan_usage_text = 'Super User';
        } else {
            $subscription = fetch('subscriptions', ['user_id' => $user_id, 'status' => 'active'], 'one', [], 'ORDER BY id DESC');
            if ($subscription) {
                $plan = fetch('plans', ['id' => $subscription['plan_id']], 'one');
                $limit = (int)($plan['transaction_limit'] ?? 0);
                $starts_at = $subscription['starts_at'];

                // Use persistent usage column
                $used = (int)($subscription['transaction_usage'] ?? 0);

                $perc = $limit > 0 ? min(100, round(($used / $limit) * 100)) : 0;
                $plan_usage_text = $limit > 0 ? (number_format($used) . '/' . number_format($limit) . ' · ' . $perc . '%') : 'No Plan';
            } else {
                $plan_usage_text = 'No Plan';
            }
        }
    }
    ?>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Tailwind -->
    <script src="<?= $links[$_ENV['APP_MODE']]['tailwind'] ?>"></script>


    <!-- Tailwind Config + Preloader -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    screens: {
                        'xs': '320px',
                        '3xl': '1920px',
                        '4xl': '2560px',
                    },
                    colors: {
                        tp: '#1e293b', // Text Primary (Slate 800)
                        ts: '#64748b', // Text Secondary (Slate 500)
                        bp: '#ffffff', // Background Primary (White)
                        bs: '#f8fafc', // Background Secondary (Slate 50)

                        // Brand Colors
                        primary: '#362472',
                        secondary: '#21A284',

                        // Mapping 'accent' to Primary for backward compatibility
                        accent: {
                            DEFAULT: '#362472', // Main Color
                            light: '#4d3699', // Lighter shade
                            dark: '#241852', // Darker shade

                            30: 'rgba(54, 36, 114, 0.1)',
                            light30: 'rgba(77, 54, 153, 0.1)',
                            dark30: 'rgba(36, 24, 82, 0.1)',
                        },
                    },

                    fontFamily: {
                        sans: ['Rubik', 'sans-serif']
                    }
                }
            }
        };

        // Apply Tailwind config colors to preloader
        const accentDefault = tailwind.config.theme.extend.colors.accent.DEFAULT;

        function preloader(action) {
            const loader = document.getElementById('preloader');
            if (!loader) return;
            if (action === 'hide') {
                loader.style.opacity = '0';
                loader.style.pointerEvents = 'none';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 500);
            } else {
                loader.style.display = 'flex';
                setTimeout(() => {
                    loader.style.opacity = '1';
                    loader.style.pointerEvents = 'auto';
                }, 10);
            }
        }
        window.addEventListener('load', () => setTimeout(() => preloader('hide'), 500));
    </script>

    <!-- Inline Styles -->
    <style>
        #preloader {
            position: fixed;
            inset: 0;
            background: #ffffff;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 24px;
            transition: opacity 0.4s ease;
        }

        .spinner-container {
            position: relative;
            width: 80px;
            height: 80px;
        }

        .spinner-ring {
            width: 100%;
            height: 100%;
            border: 3px solid rgba(54, 36, 114, 0.1);
            border-top-color: #362472;
            border-radius: 50%;
            animation: spin 1s cubic-bezier(0.55, 0.055, 0.675, 0.19) infinite;
        }

        .spinner-ring-2 {
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 3px solid transparent;
            border-top-color: #21A284;
            /* Secondary */
            border-radius: 50%;
            animation: spin 1.5s cubic-bezier(0.55, 0.055, 0.675, 0.19) reverse infinite;
        }

        .spinner-logo-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 45px;
            height: 45px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .spinner-logo-container img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .5;
            }
        }
    </style>



    <!-- External CSS (dev/local ↔ prod/CDN) -->
    <link rel="stylesheet" href="<?= $links[$_ENV['APP_MODE']]['rubik_font'] ?>">
    <link rel="stylesheet" href="<?= $links[$_ENV['APP_MODE']]['aos_css'] ?>">
    <link rel="stylesheet" href="<?= $links[$_ENV['APP_MODE']]['select2_css'] ?>">
    <link rel="stylesheet" href="<?= $links[$_ENV['APP_MODE']]['remixicon'] ?>">
    <link rel="stylesheet" href="<?= $links[$_ENV['APP_MODE']]['custom_css'] ?>">
</head>

<body class="font-sans antialiased bg-bp text-tp overflow-hidden">

    <!-- ✅ Preloader - Modern Professional -->
    <div id="preloader">
        <div class="spinner-container">
            <div class="spinner-ring"></div>
            <div class="spinner-ring-2"></div>
            <div class="spinner-logo-container">
                <img src="<?= asset('images/logo-icon-color.webp') ?>" alt="CashTrack">
            </div>
        </div>
        <div class="loading-text">Loading...</div>
    </div>

    <?php if (!empty($_SESSION['monitoring']) && !empty($_SESSION['monitored_institute'])): ?>
        <div class="bg-yellow-100 text-yellow-800 text-sm py-2 px-4 flex justify-between items-center">
            <span>
                <i class="ri-spy-line text-base"></i> You’re viewing as <strong><?= htmlspecialchars($_SESSION['monitored_institute']['name']); ?></strong>
            </span>
            <a href="<?= url('/api/monitor-institute') ?>?action=reset"
                class="text-red-600 font-semibold hover:underline flex items-center gap-1">
                <i class="ri-close-line"></i> Exit
            </a>
        </div>
    <?php endif; ?>


    <?php if (!$showLayout): ?>
        <div>
        <?php else: ?>
            <div x-data="{ 
        mobileOpen: false, 
        sidebarOpenKey0: <?= json_encode($initialOpenKey) ?>, 
        sidebarOpenKey1: null, 
        sidebarOpenKey2: null 
    }" class="min-h-screen bg-bs relative">

                <!-- TOPBAR (Fixed Top, Full Width) -->
                <header class="hidden-on-print fixed top-0 left-0 right-0 h-16 z-50 bg-bp border-b border-accent/10">
                    <div class="mx-4 md:mx-6">
                        <div class="h-16 flex items-center justify-between gap-4">

                            <!-- Left: Mobile Toggle & Desktop Logo -->
                            <div class="flex items-center gap-3">
                                <button @click="mobileOpen = !mobileOpen"
                                    class="md:hidden p-2 rounded-md text-ts hover:bg-bp">
                                    <i class="ri-menu-3-line text-2xl"></i>
                                </button>

                                <!-- Desktop Logo -->
                                <a href="<?= url('/pages/dashboard') ?>" class="hidden md:flex items-center gap-2 font-bold text-xl text-gray-900">
                                    <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-lg flex items-center justify-center text-white">
                                        <i class="ri-wallet-3-fill"></i>
                                    </div>
                                    <span class="tracking-tight"><?= $_ENV['APP_NAME'] ?? 'CashTrack' ?></span>
                                </a>
                            </div>

                            <!-- Center: Search & Mobile Logo -->
                            <div class="flex-1 flex justify-center items-center"
                                x-data="{ 
                                    query: '', 
                                    results: null, 
                                    isSearching: false, 
                                    showResults: false,
                                    selectedIndex: -1,
                                    performSearch() {
                                        if (this.query.length < 2) {
                                            this.results = null;
                                            this.showResults = false;
                                            this.selectedIndex = -1;
                                            return;
                                        }
                                        this.isSearching = true;
                                        this.showResults = true;
                                        this.selectedIndex = -1;
                                        
                                        // Simulated search (Replace with actual API call)
                                        fetch('<?= url('/api/search') ?>?q=' + encodeURIComponent(this.query))
                                            .then(res => res.json())
                                            .then(data => {
                                                this.results = data;
                                                this.isSearching = false;
                                            })
                                            .catch(() => {
                                                this.isSearching = false;
                                                this.results = []; // Error state
                                            });
                                    },
                                    init() {
                                        this.$watch('query', value => this.performSearch());
                                        
                                        // Ctrl+K shortcut
                                        document.addEventListener('keydown', (e) => {
                                            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                                                e.preventDefault();
                                                this.$refs.searchInput.focus();
                                            }
                                        });
                                    },
                                    moveSelection(direction) {
                                        if (!this.results || this.results.length === 0) return;
                                        
                                        this.selectedIndex += direction;
                                        
                                        // Cycle navigation
                                        if (this.selectedIndex < 0) {
                                            this.selectedIndex = this.results.length - 1;
                                        } else if (this.selectedIndex >= this.results.length) {
                                            this.selectedIndex = 0;
                                        }

                                        // Ensure selected item is visible
                                        this.$nextTick(() => {
                                            const selectedEl = document.getElementById('result-item-' + this.selectedIndex);
                                            if (selectedEl) {
                                                selectedEl.scrollIntoView({ block: 'nearest' });
                                            }
                                        });
                                    },
                                    selectResult() {
                                        if (this.selectedIndex >= 0 && this.results && this.results[this.selectedIndex]) {
                                            window.location.href = this.results[this.selectedIndex].url;
                                        }
                                    }
                                }">

                                <!-- Search (Desktop) -->
                                <div class="hidden md:block relative w-[400px]">
                                    <div class="relative">
                                        <input x-ref="searchInput"
                                            x-model.debounce.300ms="query"
                                            @focus="showResults = true"
                                            @click.outside="showResults = false"
                                            @keydown.arrow-down.prevent="moveSelection(1)"
                                            @keydown.arrow-up.prevent="moveSelection(-1)"
                                            @keydown.enter.prevent="selectResult()"
                                            type="search"
                                            placeholder="Search...  (Ctrl + K)"
                                            class="w-full px-4 py-2 pl-10 bg-bs border border-transparent focus:bg-white focus:border-accent/30 focus:ring-4 focus:ring-accent/10 rounded-xl transition-all outline-none" />
                                        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-ts"></i>

                                        <!-- Loading Spinner -->
                                        <div x-show="isSearching" class="absolute right-3 top-1/2 -translate-y-1/2">
                                            <i class="ri-loader-4-line animate-spin text-accent"></i>
                                        </div>
                                    </div>

                                    <!-- Quick Search Dropdown -->
                                    <div x-show="showResults && query.length > 0"
                                        x-transition.opacity.duration.200ms
                                        class="absolute top-full left-0 w-full mt-2 bg-white rounded-xl shadow-xl border border-ts/10 overflow-hidden z-50 animate-fade-in-up">

                                        <template x-if="results && results.length > 0">
                                            <div class="max-h-[70vh] overflow-y-auto py-2">
                                                <template x-for="(item, index) in results" :key="index">
                                                    <a :href="item.url"
                                                        :id="'result-item-' + index"
                                                        :class="{'bg-bs border-l-4 border-accent pl-3': selectedIndex === index, 'px-4': selectedIndex !== index}"
                                                        class="block py-3 hover:bg-bs border-b border-ts/5 last:border-0 group transition-all duration-150">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center gap-3">
                                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-gray-50 text-ts group-hover:bg-accent/10 group-hover:text-accent transition-colors">
                                                                    <i :class="item.icon || 'ri-file-line'" class="text-lg"></i>
                                                                </div>
                                                                <div>
                                                                    <h4 class="text-sm font-medium text-tp group-hover:text-accent" x-text="item.title"></h4>
                                                                    <p class="text-xs text-ts" x-text="item.subtitle"></p>
                                                                </div>
                                                            </div>
                                                            <i class="ri-arrow-right-s-line text-ts/50 group-hover:text-accent" x-show="selectedIndex === index"></i>
                                                        </div>
                                                    </a>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="results && results.length === 0">
                                            <div class="p-8 text-center text-ts">
                                                <i class="ri-search-2-line text-3xl mb-2 opacity-50"></i>
                                                <p class="text-sm">No results found for "<span x-text="query"></span>"</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Mobile Logo (Centered) -->
                                <a href="<?= url(); ?>" class="md:hidden flex items-center">
                                    <img src="<?= asset($_ENV['APP_PRIMARY_LOGO_TRANSPARENT']) ?>" class="h-10 w-auto" alt="logo">
                                </a>
                            </div>

                            <!-- Right -->
                            <div class="flex items-center gap-3">

                                <?php if ($plan_usage_text): ?>
                                    <div class="hidden md:flex items-center">
                                        <div class="px-2.5 py-1 rounded-md bg-accent/10 text-accent text-xs font-medium">
                                            <?= e($plan_usage_text); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Profile -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open"
                                        class="flex items-center w-[40px] h-[40px] p-1 border-2 border-accent/20 hover:border-accent/40 rounded-full hover:bg-bs overflow-hidden">
                                        <img src="<?= asset($_APP['user']['image'] ?? 'images/default-avatar.webp') ?>" class="w-full h-full object-cover rounded-full">
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-transition
                                        class="absolute border border-ts/20 rounded-xl hover:border-accent/40 right-0 mt-2 w-56 bg-bs p-3 z-40">
                                        <div class="flex items-center gap-3 pb-3 border-b border-bp">
                                            <img src="<?= asset($_APP['user']['image'] ?? 'images/default-avatar.webp') ?>" class="w-10 h-10 rounded-full">
                                            <div class="text-sm">
                                                <div class="font-medium text-tp"><?= isset($_APP['user']['insitute']['name']) ? $_APP['user']['insitute']['name'] : $_APP['user']['name'] ?></div>
                                                <div class="text-xs text-ts">@<?= e($_APP['user']['username'] ?? 'Guest') ?></div>
                                            </div>
                                        </div>
                                        <div class="mt-3 flex flex-col gap-2">
                                            <a href="<?= url('/pages/account') ?>" class="px-3 py-2 rounded-md hover:bg-bp border border-transparent hover:border-accent/30 flex items-center gap-2 text-tp">
                                                <i class="ri-user-line"></i>
                                                <span>Profile</span>
                                            </a>
                                            <a href="<?= url('/auth/logout') ?>" class="px-3 py-2 rounded-md hover:bg-bp border border-transparent hover:border-accent/30 flex items-center gap-2 text-red-600">
                                                <i class="ri-logout-box-r-line"></i>
                                                <span>Logout</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- SIDEBAR (Fixed Left, Below Topbar) -->
                <aside
                    :class="mobileOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
                    class="hidden-on-print fixed top-16 bottom-0 left-0 z-40 w-64 bg-accent text-white border-r border-accent/30 transform transition-transform duration-300 ease-in-out flex flex-col">

                    <!-- Mobile Header (Only Close Button) -->
                    <div class="flex items-center justify-between p-4 md:hidden border-b border-white/20">
                        <span class="font-bold text-lg">Menu</span>
                        <button @click="mobileOpen = false" class="text-white/80 hover:text-white">
                            <i class="ri-close-line text-2xl font-bold"></i>
                        </button>
                    </div>

                    <!-- Sidebar Menu -->
                    <nav class="flex-1 overflow-y-auto px-2 py-4">
                        <ul class="flex flex-col gap-1">
                            <?php
                            function renderMenu($menuItems, $level = 0, $current_slug = '', &$index = 0)
                            {
                                foreach ($menuItems as $i => $item) {
                                    $menuKey = $index++;
                                    $hasChildren = !empty($item['children']);
                                    $itemUrl = isset($item['url']) ? trim($item['url'], '/') : '';
                                    $itemParts = $itemUrl ? explode('/', $itemUrl) : [];
                                    $itemSlugEnd = $itemParts ? end($itemParts) : '';
                                    $isActive = $itemSlugEnd === $current_slug;

                                    $hasActiveChild = false;
                                    if ($hasChildren) {
                                        $hasActiveChild = hasActiveChild($item['children'], $current_slug);
                                    }

                                    echo '<li>';

                                    if ($hasChildren) {
                                        $openKeyVar = 'sidebarOpenKey' . $level;

                                        echo '
                                <button
                                    @click="' . $openKeyVar . ' = ' . $openKeyVar . ' === ' . $menuKey . ' ? null : ' . $menuKey . '"
                                    :class="' . $openKeyVar . ' === ' . $menuKey . ' || ' . ($hasActiveChild ? 'true' : 'false') . '
                                        ? \'bg-white/10 text-white\' 
                                        : \'text-white/70 hover:bg-white/10 hover:text-white\'"
                                    class="w-full flex items-center justify-between gap-3 px-3 py-2 rounded-md transition">
                                    <div class="flex items-center gap-3">
                                        <i class="' . e($item['icon'] ?? '') . ' text-xl"></i>
                                        <span class="text-nowrap">' . e($item['title']) . '</span>
                                    </div>
                                    <i :class="' . $openKeyVar . ' === ' . $menuKey . ' ? \'ri-arrow-up-s-line\' : \'ri-arrow-down-s-line\'"
                                        class="text-lg transition-transform"></i>
                                </button>';

                                        echo '
                                <ul x-show="' . $openKeyVar . ' === ' . $menuKey . ' || ' . ($hasActiveChild ? 'true' : 'false') . '"
    class="mt-2 ml-3 flex flex-col gap-1 transition-all duration-300 ease-in-out overflow-hidden"
    :style="(' . $openKeyVar . ' === ' . $menuKey . ' || ' . ($hasActiveChild ? 'true' : 'false') . ')
        ? \'max-height: 500px; opacity: 1;\' 
        : \'max-height: 0; opacity: 0;\'">';

                                        renderMenu($item['children'], $level + 1, $current_slug, $index);
                                        echo '</ul>';
                                    } else {
                                        echo '
                                <a href="' . url($item['url']) . '"
                                    class="' . ($isActive ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white') . ' 
                                    flex items-center gap-3 px-3 py-2 rounded-md transition">
                                    <i class="' . e($item['icon'] ?? '') . ' text-lg"></i>
                                    <span class="text-nowrap">' . e($item['title']) . '</span>
                                </a>';
                                    }

                                    echo '</li>';
                                }
                            }

                            function hasActiveChild($menuItems, $current_slug)
                            {
                                foreach ($menuItems as $item) {
                                    if (!empty($item['url'])) {
                                        $slug = trim($item['url'], '/');
                                        $parts = explode('/', $slug);
                                        $last = end($parts);
                                        if ($last === $current_slug) {
                                            return true;
                                        }
                                    }
                                    if (!empty($item['children']) && hasActiveChild($item['children'], $current_slug)) {
                                        return true;
                                    }
                                }
                                return false;
                            }

                            $i = 0;
                            renderMenu($filteredMenu, 0, $current_slug, $i);
                            ?>
                        </ul>
                    </nav>

                    <!-- Footer -->
                    <div class="border-t border-bp/10 px-4 py-4 text-xs text-bp/70 shrink-0">
                        © <?= date('Y') ?> <?= e($_ENV['APP_WEBSITE_NAME']) ?>
                    </div>
                </aside>

                <!-- MAIN CONTENT WRAPPER -->
                <div class="pt-16 md:ml-64 min-h-screen flex flex-col bg-bs transition-all duration-300">

                    <!-- Main Element -->
                    <main class="flex-1 overflow-y-auto p-6 bg-bs h-[calc(100vh-4rem)]">
                        <div class="max-w-full bg-bs">
                        <?php endif; ?>