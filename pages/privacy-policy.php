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
                <h1 class="text-4xl md:text-5xl font-black mb-6 text-tp">Privacy Policy</h1>
                <p class="text-lg text-ts">Last updated: <?= date('F j, Y') ?></p>
            </div>
        </section>

        <!-- Content -->
        <div class="container mx-auto px-4 py-16 max-w-4xl">
            <div class="prose prose-lg prose-slate max-w-none prose-headings:text-tp prose-p:text-ts prose-strong:text-tp prose-a:text-primary prose-a:no-underline hover:prose-a:underline prose-li:text-ts marker:text-primary">

                <div class="p-6 bg-bs border-l-4 border-primary rounded-r-xl mb-12 shadow-sm">
                    <p class="m-0 text-tp font-medium">
                        At <?= e($_ENV['APP_NAME']) ?>, accessible from <?= e($_ENV['APP_WEBSITE']) ?>, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by <?= e($_ENV['APP_NAME']) ?> and how we use it.
                    </p>
                </div>

                <div class="space-y-12">
                    <section>
                        <h2 class="flex items-center gap-3 !mt-0 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">1</span>
                            Information We Collect
                        </h2>
                        <p>
                            We collect several different types of information for various purposes to provide and improve our Service to you.
                        </p>
                        <h3 class="text-xl font-bold mt-6 mb-3 text-tp">Personal Data</h3>
                        <p>
                            While using our Service, we may ask you to provide us with certain personally identifiable information that can be used to contact or identify you ("Personal Data"). Personally identifiable information may include, but is not limited to:
                        </p>
                        <ul class="space-y-2">
                            <li><i class="ri-checkbox-circle-fill text-secondary mr-2"></i> Email address</li>
                            <li><i class="ri-checkbox-circle-fill text-secondary mr-2"></i> First name and last name</li>
                            <li><i class="ri-checkbox-circle-fill text-secondary mr-2"></i> Cookies and Usage Data</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="flex items-center gap-3 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">2</span>
                            How We Use Your Information
                        </h2>
                        <p>We use the collected data for various purposes:</p>
                        <div class="grid md:grid-cols-2 gap-4 not-prose mt-6">
                            <div class="p-4 bg-bs rounded-xl border border-gray-200">
                                <i class="ri-service-line text-primary text-xl mb-2 block"></i>
                                <span class="text-ts font-medium">To provide and maintain the Service</span>
                            </div>
                            <div class="p-4 bg-bs rounded-xl border border-gray-200">
                                <i class="ri-notification-3-line text-purple-600 text-xl mb-2 block"></i>
                                <span class="text-ts font-medium">To notify you about changes to our Service</span>
                            </div>
                            <div class="p-4 bg-bs rounded-xl border border-gray-200">
                                <i class="ri-customer-service-2-line text-blue-600 text-xl mb-2 block"></i>
                                <span class="text-ts font-medium">To provide customer care and support</span>
                            </div>
                            <div class="p-4 bg-bs rounded-xl border border-gray-200">
                                <i class="ri-bar-chart-box-line text-orange-600 text-xl mb-2 block"></i>
                                <span class="text-ts font-medium">To monitor the usage of the Service</span>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h2 class="flex items-center gap-3 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">3</span>
                            Sharing Your Personal Information
                        </h2>
                        <div class="bg-bs p-6 rounded-2xl border border-gray-200">
                            <p class="mb-4 text-ts">
                                We do not sell, trade, or otherwise transfer to outside parties your Personally Identifiable Information unless we provide users with advance notice. This does not include website hosting partners and other parties who assist us in operating our website, conducting our business, or serving our users, so long as those parties agree to keep this information confidential.
                            </p>
                            <p class="m-0 text-ts">
                                We may also release information when it's release is appropriate to comply with the law, enforce our site policies, or protect ours or others' rights, property or safety.
                            </p>
                        </div>
                    </section>

                    <section>
                        <h2 class="flex items-center gap-3 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">4</span>
                            Data Security
                        </h2>
                        <p>
                            The security of your data is important to us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your Personal Data, we cannot guarantee its absolute security.
                        </p>
                    </section>

                    <section class="text-center mt-16 pt-12 border-t border-gray-200">
                        <h2 class="text-2xl font-bold mb-4">Contact Us</h2>
                        <p class="mb-6 text-ts">If you have any questions about this Privacy Policy, please contact us:</p>
                        <a href="mailto:privacy@<?= $_ENV['APP_WEBSITE_NAME'] ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-bs text-primary font-bold rounded-xl border-2 border-primary/10 hover:border-primary hover:bg-primary/5 transition-all no-underline">
                            <i class="ri-mail-send-line"></i> privacy@<?= $_ENV['APP_WEBSITE_NAME'] ?>
                        </a>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/public-footer.php'; ?>
</div>