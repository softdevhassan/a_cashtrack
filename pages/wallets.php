<?php
if (!defined('ALLOWED')) die('Direct access not permitted');

$page_title = "Wallets";
$user_id = $_APP['user']['id'];

// Fetch user's wallets
$wallets = fetch('wallets', ['user_id' => $user_id], 'all', [], 'ORDER BY created_at DESC');

// Calculate total balance
$total_balance = 0;
foreach ($wallets as $wallet) {
    $total_balance += $wallet['current_balance'];
}

// Get Currency Symbol
$currencyConfig = $_ENV['CURRENCY'] ?? 'PKR, post';
$currencySymbol = trim(explode(',', $currencyConfig)[0]);
?>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 md:p-6">
    <div class="max-w-7xl mx-auto">

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    <i class="ri-wallet-line text-indigo-600"></i> My Wallets
                </h1>
                <p class="text-gray-600 dark:text-gray-400">Manage your accounts and track balances</p>
            </div>
            <button onclick="openAddWalletModal()" class="mt-4 md:mt-0 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 flex items-center gap-2">
                <i class="ri-add-line"></i> Add Wallet
            </button>
        </div>

        <!-- Total Balance Card -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 mb-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm mb-1">Total Balance</p>
                    <h2 class="text-4xl font-bold"><?php echo formatCurrency($total_balance); ?></h2>
                </div>
                <div class="bg-white/20 p-4 rounded-full">
                    <i class="ri-money-dollar-circle-line text-4xl"></i>
                </div>
            </div>
        </div>

        <?php show_alert(); ?>

        <!-- Wallets Grid -->
        <?php if (empty($wallets)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center shadow-sm">
                <i class="ri-wallet-line text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No Wallets Yet</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Start by adding your first wallet to track your finances</p>
                <button onclick="openAddWalletModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200">
                    <i class="ri-add-line mr-2"></i> Add Your First Wallet
                </button>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($wallets as $wallet): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm hover:shadow-lg transition-all duration-200 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="p-3 rounded-full" style="background-color: <?php echo e($wallet['color']); ?>20;">
                                    <i class="<?php echo e($wallet['icon']); ?> text-2xl" style="color: <?php echo e($wallet['color']); ?>;"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white"><?php echo e($wallet['name']); ?></h3>
                                    <p class="text-sm text-gray-500 capitalize"><?php echo str_replace('_', ' ', e($wallet['type'])); ?></p>
                                </div>
                            </div>
                            <div class="relative">
                                <button onclick="toggleDropdown(<?php echo $wallet['id']; ?>)" class="text-gray-400 hover:text-gray-600 p-2">
                                    <i class="ri-more-2-fill text-xl"></i>
                                </button>
                                <div id="dropdown-<?php echo $wallet['id']; ?>" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-xl border border-gray-200 dark:border-gray-600 z-10">
                                    <a href="#" onclick="editWallet(<?php echo $wallet['id']; ?>)" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-t-lg">
                                        <i class="ri-edit-line mr-2"></i> Edit
                                    </a>
                                    <a href="#" onclick="deleteWallet(<?php echo $wallet['id']; ?>)" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-b-lg">
                                        <i class="ri-delete-bin-line mr-2"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Current Balance</span>
                                <span class="text-xl font-bold text-gray-900 dark:text-white">
                                    <?php echo e($wallet['currency']); ?> <?php echo number_format($wallet['current_balance'], 2); ?>
                                </span>
                            </div>
                            <?php if ($wallet['notes']): ?>
                                <p class="text-sm text-gray-500 mt-2"><?php echo e($wallet['notes']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- Add/Edit Wallet Modal -->
<div id="walletModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-900 dark:text-white">Add New Wallet</h2>
            <button onclick="closeWalletModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>

        <form id="walletForm" onsubmit="saveWallet(event)">
            <input type="hidden" id="walletId" value="">

            <div class="space-y-4">
                <!-- Wallet Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Wallet Name *</label>
                    <input type="text" id="walletName" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-700 dark:text-white"
                        placeholder="e.g., Cash Wallet">
                </div>

                <!-- Wallet Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type *</label>
                    <select id="walletType" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-700 dark:text-white">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Account</option>
                        <option value="mobile_wallet">Mobile Wallet</option>
                        <option value="investment">Investment</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Currency *</label>
                    <input type="text" id="walletCurrency" value="<?= $currencySymbol ?>" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Initial Balance -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Initial Balance *</label>
                    <input type="number" id="walletBalance" step="0.01" value="0" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Icon & Color -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Icon</label>
                        <select id="walletIcon"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-700 dark:text-white">
                            <option value="ri-wallet-line">💰 Wallet</option>
                            <option value="ri-bank-line">🏦 Bank</option>
                            <option value="ri-phone-line">📱 Mobile</option>
                            <option value="ri-line-chart-line">📈 Investment</option>
                            <option value="ri-bank-card-line">💳 Card</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Color</label>
                        <input type="color" id="walletColor" value="#6366f1"
                            class="w-full h-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none">
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                    <textarea id="walletNotes" rows="2"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-700 dark:text-white"
                        placeholder="Optional notes"></textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeWalletModal()"
                    class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Cancel
                </button>
                <button type="submit" id="saveBtn"
                    class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Save Wallet
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Toast notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    // Open add wallet modal
    function openAddWalletModal() {
        document.getElementById('modalTitle').textContent = 'Add New Wallet';
        document.getElementById('walletForm').reset();
        document.getElementById('walletId').value = '';
        document.getElementById('walletModal').classList.remove('hidden');
    }

    // Close modal
    function closeWalletModal() {
        document.getElementById('walletModal').classList.add('hidden');
    }

    // Save wallet (Create or Update)
    async function saveWallet(event) {
        event.preventDefault();

        const walletId = document.getElementById('walletId').value;
        const action = walletId ? 'update' : 'create';
        const saveBtn = document.getElementById('saveBtn');

        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';

        const formData = new FormData();
        formData.append('action', action);
        if (walletId) formData.append('id', walletId);
        formData.append('name', document.getElementById('walletName').value);
        formData.append('type', document.getElementById('walletType').value);
        formData.append('currency', document.getElementById('walletCurrency').value);
        formData.append('initial_balance', document.getElementById('walletBalance').value);
        formData.append('icon', document.getElementById('walletIcon').value);
        formData.append('color', document.getElementById('walletColor').value);
        formData.append('notes', document.getElementById('walletNotes').value);

        try {
            const response = await fetch('<?= url("/api/wallets") ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showToast(result.message || 'Wallet saved successfully!', 'success');
                closeWalletModal();
                location.reload(); // Reload to show updated wallet list
            } else {
                showToast(result.message || 'Failed to save wallet', 'error');
            }
        } catch (error) {
            showToast('Network error. Please try again.', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Wallet';
        }
    }

    // Edit wallet
    async function editWallet(id) {
        try {
            const response = await fetch(`<?= url("/api/wallets") ?>?action=get&id=${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();

            if (result.success && result.data) {
                const wallet = result.data;
                document.getElementById('modalTitle').textContent = 'Edit Wallet';
                document.getElementById('walletId').value = wallet.id;
                document.getElementById('walletName').value = wallet.name;
                document.getElementById('walletType').value = wallet.type;
                document.getElementById('walletCurrency').value = wallet.currency;
                document.getElementById('walletBalance').value = wallet.initial_balance || wallet.current_balance;
                document.getElementById('walletIcon').value = wallet.icon;
                document.getElementById('walletColor').value = wallet.color;
                document.getElementById('walletNotes').value = wallet.notes || '';
                document.getElementById('walletModal').classList.remove('hidden');
            } else {
                showToast('Failed to load wallet details', 'error');
            }
        } catch (error) {
            showToast('Network error. Please try again.', 'error');
        }
    }

    // Delete wallet
    async function deleteWallet(id) {
        if (!confirm('Are you sure you want to delete this wallet? All associated transactions will also be deleted.')) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);

        try {
            const response = await fetch('<?= url("/api/wallets") ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showToast(result.message || 'Wallet deleted successfully!', 'success');
                location.reload();
            } else {
                showToast(result.message || 'Failed to delete wallet', 'error');
            }
        } catch (error) {
            showToast('Network error. Please try again.', 'error');
        }
    }

    // Toggle dropdown
    function toggleDropdown(id) {
        const dropdown = document.getElementById(`dropdown-${id}`);
        dropdown.classList.toggle('hidden');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('button')) {
            document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                dropdown.classList.add('hidden');
            });
        }
    });

    // Close modal on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeWalletModal();
        }
    });
</script>