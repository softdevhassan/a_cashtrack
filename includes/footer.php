<?php if ($showLayout) { ?>
    <div class="text-center border-t mt-8 pt-4 px-4">
        <p class="text-gray-500 text-sm">© <?= date('Y') . ' ' . $_ENV['APP_FULL_NAME']; ?>. All rights reserved.</p>
    </div>
    </div>
    </main>
    </div>
<?php } ?>
</div>
<div class="absolute bottom-5 right-5">
    <?php show_alert(); ?>
</div>
<script src="<?= $links[$_ENV['APP_MODE']]['jquery'] ?>"></script>
<script src="<?= $links[$_ENV['APP_MODE']]['aos_js'] ?>"></script>
<script src="<?= $links[$_ENV['APP_MODE']]['alpine'] ?>" defer></script>
<script src="<?= $links[$_ENV['APP_MODE']]['select2_js'] ?>"></script>
<script>
    const menu = <?= json_encode($menu ?? []) ?>;
    const openRoutes = <?= json_encode($openRoutes ?? []) ?>;
    const currentRole = "<?= $_APP['user']['role'] ?? 'guest' ?>";
    const adminRoutes = []; // Define if needed or populate from PHP
    const instituteRoutes = []; // Define if needed
</script>
<script src="<?= $links[$_ENV['APP_MODE']]['custom_js'] ?>"></script>

<?php if (function_exists('page_sc')) {
    page_sc();
} ?>
</body>

</html>