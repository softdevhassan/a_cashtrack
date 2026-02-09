<?php
if (!defined('ALLOWED')) die('Direct access not permitted');

$page_title = "Categories";
$user_id = $_APP['user']['id'];

// Fetch all categories (system + user custom)
$income_categories = fetch('categories', [], 'all', [], 'WHERE (user_id = ' . $user_id . ' OR user_id IS NULL) AND type = "income" ORDER BY is_system DESC, name ASC');
$expense_categories = fetch('categories', [], 'all', [], 'WHERE (user_id = ' . $user_id . ' OR user_id IS NULL) AND type = "expense" ORDER BY is_system DESC, name ASC');
?>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 md:p-6">
    <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    <i class="ri-price-tag-line text-indigo-600"></i> Categories
                </h1>
                <p class="text-gray-600 dark:text-gray-400">Organize your income and expenses</p>
            </div>
            <button onclick="openAddCategoryModal()" class="mt-4 md:mt-0 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200">
                <i class="ri-add-line mr-2"></i> Add Category
            </button>
        </div>

        <?php show_alert(); ?>

        <!-- Income Categories -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="ri-arrow-down-circle-line text-green-600"></i> Income Categories
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($income_categories as $category): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm hover:shadow-md transition-all border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-2">
                            <div class="p-2 rounded-full" style="background-color: <?php echo e($category['color']); ?>20;">
                                <i class="<?php echo e($category['icon']); ?> text-xl" style="color: <?php echo e($category['color']); ?>;"></i>
                            </div>
                            <?php if (!$category['is_system']): ?>
                                <button onclick="deleteCategory(<?php echo $category['id']; ?>)" class="text-gray-400 hover:text-red-600">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            <?php else: ?>
                                <span class="text-xs text-gray-400">System</span>
                            <?php endif; ?>
                        </div>
                        <div class="font-medium text-gray-900 dark:text-white text-sm">
                            <?php echo e($category['name']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Expense Categories -->
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="ri-arrow-up-circle-line text-red-600"></i> Expense Categories
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php foreach ($expense_categories as $category): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm hover:shadow-md transition-all border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-2">
                            <div class="p-2 rounded-full" style="background-color: <?php echo e($category['color']); ?>20;">
                                <i class="<?php echo e($category['icon']); ?> text-xl" style="color: <?php echo e($category['color']); ?>;"></i>
                            </div>
                            <?php if (!$category['is_system']): ?>
                                <button onclick="deleteCategory(<?php echo $category['id']; ?>)" class="text-gray-400 hover:text-red-600">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            <?php else: ?>
                                <span class="text-xs text-gray-400">System</span>
                            <?php endif; ?>
                        </div>
                        <div class="font-medium text-gray-900 dark:text-white text-sm">
                            <?php echo e($category['name']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<!-- Add Category Modal (Placeholder) -->
<div id="addCategoryModal" class="hidden fixed inset-0 bg-black/50 z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Add Category</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-4">Category management coming soon!</p>
        <button onclick="closeAddCategoryModal()" class="w-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white px-4 py-2 rounded-lg">Close</button>
    </div>
</div>

<script>
    function openAddCategoryModal() {
        const modal = document.getElementById('addCategoryModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex', 'items-center', 'justify-center');
    }

    function closeAddCategoryModal() {
        const modal = document.getElementById('addCategoryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex', 'items-center', 'justify-center');
    }

    function deleteCategory(id) {
        if (confirm('Are you sure you want to delete this category?')) {
            alert('Delete category feature coming soon!');
        }
    }
</script>
