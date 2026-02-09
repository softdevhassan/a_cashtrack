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
                <h1 class="text-4xl md:text-5xl font-black mb-6 text-tp">Terms of Service</h1>
                <p class="text-lg text-ts">Last updated: <?= date('F j, Y') ?></p>
            </div>
        </section>

        <!-- Content -->
        <div class="container mx-auto px-4 py-16 max-w-4xl">
            <div class="prose prose-lg prose-slate max-w-none prose-headings:text-tp prose-p:text-ts prose-strong:text-tp prose-a:text-primary prose-a:no-underline hover:prose-a:underline prose-li:text-ts marker:text-primary">

                <div class="p-6 bg-bs border-l-4 border-secondary rounded-r-xl mb-12 shadow-sm">
                    <p class="m-0 text-tp font-medium">
                        These Terms of Service ("Terms") govern your use of the <?= e($_ENV['APP_NAME']) ?> website and services. By accessing or using our services, you agree to be bound by these Terms.
                    </p>
                </div>

                <div class="space-y-12">
                    <section>
                        <h2 class="flex items-center gap-3 !mt-0 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">1</span>
                            Acceptance of Terms
                        </h2>
                        <p>
                            By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement. In addition, when using these particular services, you shall be subject to any posted guidelines or rules applicable to such services.
                        </p>
                    </section>

                    <section>
                        <h2 class="flex items-center gap-3 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">2</span>
                            Description of Service
                        </h2>
                        <p>
                            <?= e($_ENV['APP_NAME']) ?> provides users with email marketing tools and services. You are responsible for obtaining access to the Service and that access may involve third party fees (such as Internet service provider or airtime charges).
                        </p>
                    </section>

                    <section>
                        <h2 class="flex items-center gap-3 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">3</span>
                            User Account
                        </h2>
                        <div class="flex gap-6 items-start not-prose bg-bs p-6 rounded-2xl border border-gray-200">
                            <i class="ri-user-settings-line text-3xl text-secondary mt-1"></i>
                            <p class="mb-0 text-ts leading-relaxed">
                                To access certain features of the Service, you may be required to create an account. You are responsible for maintaining the confidentiality of your account and password and for restricting access to your computer, and you agree to accept responsibility for all activities that occur under your account or password.
                            </p>
                        </div>
                    </section>

                    <section>
                        <h2 class="flex items-center gap-3 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">4</span>
                            Acceptable Use
                        </h2>
                        <p>You agree not to use the Service to:</p>
                        <ul class="space-y-3 pl-0 list-none">
                            <li class="flex items-start gap-3 text-red-600">
                                <i class="ri-close-circle-fill mt-1"></i>
                                <span class="text-ts">Send unsolicited bulk email (SPAM).</span>
                            </li>
                            <li class="flex items-start gap-3 text-red-600">
                                <i class="ri-close-circle-fill mt-1"></i>
                                <span class="text-ts">Upload, post, email, transmit or otherwise make available any content that is unlawful, harmful, threatening, abusive, harassing, torture, defamatory, vulgar, obscene, libelous, invasive of another's privacy, hateful, or racially, ethnically or otherwise objectionable.</span>
                            </li>
                            <li class="flex items-start gap-3 text-red-600">
                                <i class="ri-close-circle-fill mt-1"></i>
                                <span class="text-ts">Impersonate any person or entity.</span>
                            </li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="flex items-center gap-3 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">5</span>
                            Termination
                        </h2>
                        <p>
                            We may terminate or suspend access to our Service immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Terms.
                        </p>
                    </section>

                    <section>
                        <h2 class="flex items-center gap-3 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">6</span>
                            Changes to Terms
                        </h2>
                        <p>
                            We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material we will try to provide at least 30 days' notice prior to any new terms taking effect.
                        </p>
                    </section>

                    <section class="text-center mt-16 pt-12 border-t border-gray-200">
                        <h2 class="text-2xl font-bold mb-4">Contact Us</h2>
                        <p class="mb-6 text-ts">If you have questions or comments, please contact us at:</p>
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