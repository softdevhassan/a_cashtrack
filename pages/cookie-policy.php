<div x-data="{ mobileOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">
    <?php include __DIR__ . '/../includes/public-header.php'; ?>

    <div class="bg-bp text-tp font-sans selection:bg-primary selection:text-white">

        <!-- Hero -->
        <section class="pt-32 pb-16 px-4 bg-gradient-to-br from-primary/5 via-secondary/5 to-bp relative overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute inset-0">
                <div class="absolute top-20 left-10 w-96 h-96 bg-primary/20 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-20 right-10 w-[500px] h-[500px] bg-secondary/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMyIgY3k9IjMiIHI9IjEuNSIgZmlsbD0icmdiYSg1NCwzNiwxMTQsMC4wOCkiLz48L3N2Zz4=')] opacity-40"></div>
            </div>

            <div class="container max-w-4xl mx-auto text-center relative z-10" data-aos="fade-up">
                <h1 class="text-4xl md:text-5xl font-black mb-6 text-tp">Cookie Policy</h1>
                <p class="text-lg text-ts">Last updated: <?= date('F j, Y') ?></p>
            </div>
        </section>

        <!-- Content -->
        <div class="container mx-auto px-4 py-16 max-w-4xl">
            <div class="prose prose-lg prose-slate max-w-none prose-headings:text-tp prose-p:text-ts prose-strong:text-tp prose-a:text-primary prose-a:no-underline hover:prose-a:underline prose-li:text-ts marker:text-primary">

                <p class="lead text-xl text-tp mb-8">
                    This Cookie Policy explains how <span class="font-bold text-primary"><?= e($_ENV['APP_NAME']) ?></span> uses cookies and similar technologies to recognize you when you visit our website at <?= e($_ENV['APP_WEBSITE']) ?>. It explains what these technologies are and why we use them, as well as your rights to control our use of them.
                </p>

                <div class="space-y-12">
                    <section>
                        <h2 class="flex items-center gap-3 !mt-0 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">1</span>
                            What are cookies?
                        </h2>
                        <p>
                            Cookies are small data files that are placed on your computer or mobile device when you visit a website. Cookies are widely used by website owners in order to make their websites work, or to work more efficiently, as well as to provide reporting information.
                        </p>
                    </section>

                    <section>
                        <h2 class="flex items-center gap-3 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">2</span>
                            Why do we use cookies?
                        </h2>
                        <p>
                            We use first-party and third-party cookies for several reasons. Some cookies are required for technical reasons in order for our Website to operate, and we refer to these as "essential" or "strictly necessary" cookies. Other cookies also enable us to track and target the interests of our users to enhance the experience on our Online Properties. Third parties serve cookies through our Website for advertising, analytics and other purposes.
                        </p>
                    </section>

                    <section>
                        <h2 class="flex items-center gap-3 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">3</span>
                            Types of Cookies We Use
                        </h2>

                        <div class="grid gap-6 not-prose">
                            <div class="p-6 bg-bs rounded-xl border border-gray-200">
                                <h3 class="font-bold text-lg text-tp mb-2 flex items-center gap-2">
                                    <i class="ri-shield-keyhole-line text-secondary"></i> Essential Cookies
                                </h3>
                                <p class="text-sm text-ts">These cookies are strictly necessary to provide you with services available through our Website and to use some of its features, such as access to secure areas.</p>
                            </div>
                            <div class="p-6 bg-bs rounded-xl border border-gray-200">
                                <h3 class="font-bold text-lg text-tp mb-2 flex items-center gap-2">
                                    <i class="ri-speed-line text-purple-600"></i> Performance and Functionality Cookies
                                </h3>
                                <p class="text-sm text-ts">These cookies are used to enhance the performance and functionality of our Website but are non-essential to their use.</p>
                            </div>
                            <div class="p-6 bg-bs rounded-xl border border-gray-200">
                                <h3 class="font-bold text-lg text-tp mb-2 flex items-center gap-2">
                                    <i class="ri-bar-chart-groupped-line text-pink-600"></i> Analytics and Customization Cookies
                                </h3>
                                <p class="text-sm text-ts">These cookies collect information that is used either in aggregate form to help us understand how our Website is being used or how effective our marketing campaigns are, or to help us customize our Website for you.</p>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h2 class="flex items-center gap-3 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">4</span>
                            How can I control cookies?
                        </h2>
                        <p>
                            You have the right to decide whether to accept or reject cookies. You can exercise your cookie rights by setting your cookie preferences in the Cookie Consent Manager. In addition, most web browsers allow you to control cookies through their settings preferences.
                        </p>
                    </section>

                    <section>
                        <h2 class="flex items-center gap-3 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">5</span>
                            Updates to this Policy
                        </h2>
                        <p>
                            We may update this Cookie Policy from time to time in order to reflect, for example, changes to the cookies we use or for other operational, legal or regulatory reasons. Please therefore re-visit this Cookie Policy regularly to stay informed about our use of cookies and related technologies.
                        </p>
                    </section>

                    <section class="text-center mt-16 pt-12 border-t border-gray-200">
                        <h2 class="text-2xl font-bold mb-4">Contact Us</h2>
                        <p class="mb-6 text-ts">If you have any questions about our use of cookies or other technologies, please email us at:</p>
                        <a href="mailto:support@<?= $_ENV['APP_WEBSITE_NAME'] ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-bs text-primary font-bold rounded-xl border-2 border-primary/10 hover:border-primary hover:bg-primary/5 transition-all no-underline">
                            <i class="ri-mail-send-line"></i> support@<?= $_ENV['APP_WEBSITE_NAME'] ?>
                        </a>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/public-footer.php'; ?>
</div>