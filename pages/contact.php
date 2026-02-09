<div x-data="{ mobileOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">
    <?php include __DIR__ . '/../includes/public-header.php'; ?>

    <div class="bg-bp text-tp font-sans selection:bg-primary selection:text-white">

        <!-- Hero -->
        <section class="pt-32 pb-16 px-4 bg-gradient-to-br from-primary/5 via-secondary/5 to-bp relative overflow-hidden">
            <!-- Decorative Background (Consistent) -->
            <div class="absolute inset-0">
                <div class="absolute top-20 left-10 w-96 h-96 bg-secondary/20 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-20 right-10 w-[500px] h-[500px] bg-primary/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMyIgY3k9IjMiIHI9IjEuNSIgZmlsbD0icmdiYSg1NCwzNiwxMTQsMC4wOCkiLz48L3N2Zz4=')] opacity-40"></div>
            </div>

            <div class="container max-w-4xl mx-auto text-center relative z-10" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-secondary/10 border border-secondary/20 text-secondary text-sm font-bold mb-6">
                    <i class="ri-mail-send-line"></i>
                    Get In Touch
                </div>
                <h1 class="text-5xl md:text-6xl font-black mb-6 text-tp">
                    We'd Love to <span class="text-transparent bg-clip-text bg-gradient-to-r from-secondary to-primary">Hear From You</span>
                </h1>
                <p class="text-xl text-ts">
                    Have questions about CashTrack? Need help getting started? Want to suggest a feature? We're here to help.
                </p>
            </div>
        </section>

        <!-- Contact Options Grid -->
        <section class="py-20 px-4 bg-bp">
            <div class="container max-w-6xl mx-auto">
                <div class="grid md:grid-cols-3 gap-8 mb-16" data-aos="fade-up">
                    <!-- Email Support -->
                    <div class="bg-bs rounded-3xl p-8 text-center border border-gray-200 hover:border-blue-500/50 transition-all hover:shadow-lg">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 text-white shadow-lg">
                            <i class="ri-mail-line text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-tp">Email Support</h3>
                        <p class="text-ts mb-4">
                            Get help from our support team
                        </p>
                        <a href="mailto:support@cashtrack.com" class="text-blue-600 font-bold hover:underline">
                            support@cashtrack.com
                        </a>
                    </div>

                    <!-- General Inquiries -->
                    <div class="bg-bs rounded-3xl p-8 text-center border border-gray-200 hover:border-purple-500/50 transition-all hover:shadow-lg">
                        <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-6 text-white shadow-lg">
                            <i class="ri-question-answer-line text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-tp">General Inquiries</h3>
                        <p class="text-ts mb-4">
                            Business partnerships & media
                        </p>
                        <a href="mailto:info@cashtrack.com" class="text-purple-600 font-bold hover:underline">
                            info@cashtrack.com
                        </a>
                    </div>

                    <!-- Social Media -->
                    <div class="bg-bs rounded-3xl p-8 text-center border border-gray-200 hover:border-primary/50 transition-all hover:shadow-lg">
                        <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-6 text-white shadow-lg">
                            <i class="ri-share-line text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 text-tp">Social Media</h3>
                        <p class="text-ts mb-4">
                            Follow us for updates & tips
                        </p>
                        <div class="flex justify-center gap-3">
                            <a href="#" class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white hover:bg-primary/90 transition-colors">
                                <i class="ri-twitter-x-line"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white hover:bg-primary/90 transition-colors">
                                <i class="ri-facebook-fill"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form & Info Side by Side -->
                <div class="grid md:grid-cols-2 gap-12" data-aos="fade-up" data-aos-delay="100">
                    <!-- Form -->
                    <div class="bg-bs rounded-3xl p-8 border border-gray-200 shadow-lg">
                        <h2 class="text-3xl font-black mb-6 text-tp">Send Us a Message</h2>

                        <form action="<?= url('/api/contact.php') ?>" method="POST" class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-tp mb-2">Your Name *</label>
                                <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white text-tp focus:ring-2 focus:ring-secondary/50 outline-none focus:outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-tp mb-2">Email Address *</label>
                                <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white text-tp focus:ring-2 focus:ring-secondary/50 outline-none focus:outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-tp mb-2">Subject *</label>
                                <input type="text" name="subject" required class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white text-tp focus:ring-2 focus:ring-secondary/50 outline-none focus:outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-tp mb-2">Message *</label>
                                <textarea name="message" rows="5" required class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white text-tp focus:ring-2 focus:ring-secondary/50 outline-none focus:outline-none transition-all resize-none"></textarea>
                            </div>

                            <button type="submit" class="w-full px-8 py-4 bg-gradient-to-r from-secondary to-primary text-white font-bold rounded-xl hover:shadow-xl hover:shadow-secondary/50 transition-all">
                                Send Message <i class="ri-send-plane-fill ml-2"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Info Card -->
                    <div>
                        <div class="bg-gradient-to-br from-bs to-bp rounded-3xl p-8 border border-gray-200 mb-6">
                            <h3 class="text-2xl font-bold mb-6 text-tp">Quick Info</h3>

                            <div class="space-y-6">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 bg-secondary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <i class="ri-time-line text-2xl text-secondary"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-tp mb-1">Response Time</div>
                                        <div class="text-ts text-sm">We typically respond within 24 hours during business days</div>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <i class="ri-map-pin-line text-2xl text-purple-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-tp mb-1">Location</div>
                                        <div class="text-ts text-sm">Remote-first team serving globally</div>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <i class="ri-customer-service-2-line text-2xl text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-tp mb-1">Support Hours</div>
                                        <div class="text-ts text-sm">Monday - Friday, 9 AM - 6 PM (GMT+5)</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Link Box -->
                        <div class="bg-gradient-to-r from-secondary to-primary rounded-3xl p-8 text-white">
                            <h3 class="text-2xl font-bold mb-3">Need Help Right Away?</h3>
                            <p class="text-white/80 mb-6">
                                Check out our FAQ for instant answers to common questions about plans, features, and billing.
                            </p>
                            <a href="<?= url('/pages/features') ?>" class="inline-block px-6 py-3 bg-white text-secondary font-bold rounded-xl hover:shadow-xl transition-all">
                                View FAQ <i class="ri-arrow-right-line ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <?php include __DIR__ . '/../includes/public-footer.php'; ?>
</div>