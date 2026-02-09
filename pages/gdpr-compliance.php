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
                <h1 class="text-4xl md:text-5xl font-black mb-6 text-tp">GDPR Compliance</h1>
                <p class="text-lg text-ts">Last updated: <?= date('F j, Y') ?></p>
            </div>
        </section>

        <!-- Content -->
        <div class="container mx-auto px-4 py-16 max-w-4xl">
            <div class="prose prose-lg prose-slate max-w-none prose-headings:text-tp prose-p:text-ts prose-strong:text-tp prose-a:text-primary prose-a:no-underline hover:prose-a:underline prose-li:text-ts marker:text-primary">

                <div class="p-8 bg-green-50 border border-green-200 rounded-2xl mb-12 flex items-start gap-4">
                    <i class="ri-shield-check-fill text-green-600 text-3xl mt-1"></i>
                    <p class="m-0 text-ts leading-relaxed">
                        <span class="font-bold text-tp"><?= e($_ENV['APP_NAME']) ?></span> is committed to ensuring the security and protection of the personal information that we process, and to provide a compliant and consistent approach to data protection. We have always had a robust and effective data protection program in place which complies with existing law and abides by the data protection principles.
                    </p>
                </div>

                <div class="space-y-12">
                    <section>
                        <h2 class="flex items-center gap-3 !mt-0 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">1</span>
                            Our Commitment
                        </h2>
                        <p>
                            We recognize our obligations to update and expand this program to meet the demands of the GDPR. <?= e($_ENV['APP_NAME']) ?> is dedicated to safeguarding the personal information under our remit and in developing a data protection regime that is effective, fit for purpose and demonstrates an understanding of, and appreciation for the new Regulation.
                        </p>
                    </section>

                    <section>
                        <h2 class="flex items-center gap-3 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">2</span>
                            Data Processing
                        </h2>
                        <p>
                            We process personal data only when we have a legal basis to do so. The legal basis will depend on the reason or reasons <?= e($_ENV['APP_NAME']) ?> collected and needs to use your information. Under GDPR, the legal bases for processing personal data are:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 not-prose mt-6">
                            <div class="flex items-center gap-3 bg-bs p-4 rounded-xl border border-gray-200">
                                <i class="ri-checkbox-circle-fill text-secondary"></i> <span class="text-ts">Performance of a contract</span>
                            </div>
                            <div class="flex items-center gap-3 bg-bs p-4 rounded-xl border border-gray-200">
                                <i class="ri-checkbox-circle-fill text-secondary"></i> <span class="text-ts">Legitimate interest</span>
                            </div>
                            <div class="flex items-center gap-3 bg-bs p-4 rounded-xl border border-gray-200">
                                <i class="ri-checkbox-circle-fill text-secondary"></i> <span class="text-ts">Compliance with a legal obligation</span>
                            </div>
                            <div class="flex items-center gap-3 bg-bs p-4 rounded-xl border border-gray-200">
                                <i class="ri-checkbox-circle-fill text-secondary"></i> <span class="text-ts">Consent</span>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h2 class="flex items-center gap-3 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">3</span>
                            Data Subject Rights
                        </h2>
                        <p>
                            We provide easy-to-access information via our website of an individual’s right to access any personal information that <?= e($_ENV['APP_NAME']) ?> processes about them and to request information about:
                        </p>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2"><i class="ri-arrow-right-s-line text-primary mt-1"></i> What personal data we hold about them</li>
                            <li class="flex items-start gap-2"><i class="ri-arrow-right-s-line text-primary mt-1"></i> The purposes of the processing</li>
                            <li class="flex items-start gap-2"><i class="ri-arrow-right-s-line text-primary mt-1"></i> The categories of personal data concerned</li>
                            <li class="flex items-start gap-2"><i class="ri-arrow-right-s-line text-primary mt-1"></i> The recipients to whom the personal data has/will be disclosed</li>
                            <li class="flex items-start gap-2"><i class="ri-arrow-right-s-line text-primary mt-1"></i> How long we intend to store your personal data</li>
                            <li class="flex items-start gap-2"><i class="ri-arrow-right-s-line text-primary mt-1"></i> If we did not collect the data directly from them, information about the source</li>
                            <li class="flex items-start gap-2"><i class="ri-arrow-right-s-line text-primary mt-1"></i> The right to have incomplete or inaccurate data about them corrected or completed</li>
                            <li class="flex items-start gap-2"><i class="ri-arrow-right-s-line text-primary mt-1"></i> The right to request erasure of personal data or to restrict processing</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="flex items-center gap-3 !mb-6">
                            <span class="w-8 h-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center text-sm font-bold">4</span>
                            Data Security
                        </h2>
                        <p>
                            We take the privacy and security of individuals and their personal information very seriously and take every reasonable measure and precaution to protect and secure the personal data that we process. We have dedicated information security policies and procedures in place to protect personal information from unauthorized access, alteration, disclosure or destruction.
                        </p>
                    </section>

                    <section class="text-center mt-16 pt-12 border-t border-gray-200">
                        <h2 class="text-2xl font-bold mb-4">Contact Us</h2>
                        <p class="mb-6 text-ts">If you have any questions about our GDPR compliance or data protection practices, please contact our Data Protection Officer at:</p>
                        <a href="mailto:dpo@<?= $_ENV['APP_WEBSITE_NAME'] ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-bs text-primary font-bold rounded-xl border-2 border-primary/10 hover:border-primary hover:bg-primary/5 transition-all no-underline">
                            <i class="ri-mail-send-line"></i> dpo@<?= $_ENV['APP_WEBSITE_NAME'] ?>
                        </a>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/public-footer.php'; ?>
</div>