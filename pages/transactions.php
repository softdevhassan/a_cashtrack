<?php
if (!defined('ALLOWED')) die('Direct access not permitted');

$page_title = "Transactions";
$user_id = $_APP['user']['id'];

// Fetch user's wallets for dropdown
$wallets = fetch('wallets', ['user_id' => $user_id], 'all', [], 'ORDER BY name ASC');

// Fetch categories
$categories = fetch('categories', [], 'all', [], 'WHERE (user_id = ' . $user_id . ' OR user_id IS NULL) ORDER BY type, name ASC');

// Fetch recent transactions
$transactions = fetch('transactions', ['user_id' => $user_id], 'all', [], 'ORDER BY transaction_date DESC, created_at DESC LIMIT 50');

// Get Currency Symbol
$currencyConfig = $_ENV['CURRENCY'] ?? 'PKR, post';
$currencySymbol = trim(explode(',', $currencyConfig)[0]);
?>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 md:p-6">
    <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="ri-exchange-line text-indigo-600"></i> Transactions
            </h1>
            <p class="text-gray-600 dark:text-gray-400">Track your income, expenses, and transfers</p>
        </div>

        <?php show_alert(); ?>

        <!-- Quick Add Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <button onclick="openTransactionModal('income')" class="bg-green-600 hover:bg-green-700 text-white p-6 rounded-xl font-medium transition-all duration-200 text-left shadow-lg">
                <i class="ri-arrow-down-circle-line text-3xl mb-2"></i>
                <div class="text-lg">Add Income</div>
                <div class="text-sm text-green-100">Salary, business, etc.</div>
            </button>

            <button onclick="openTransactionModal('expense')" class="bg-red-600 hover:bg-red-700 text-white p-6 rounded-xl font-medium transition-all duration-200 text-left shadow-lg">
                <i class="ri-arrow-up-circle-line text-3xl mb-2"></i>
                <div class="text-lg">Add Expense</div>
                <div class="text-sm text-red-100">Bills, shopping, etc.</div>
            </button>

            <button onclick="openTransactionModal('transfer')" class="bg-blue-600 hover:bg-blue-700 text-white p-6 rounded-xl font-medium transition-all duration-200 text-left shadow-lg">
                <i class="ri-exchange-funds-line text-3xl mb-2"></i>
                <div class="text-lg">Add Transfer</div>
                <div class="text-sm text-blue-100">Between wallets</div>
            </button>
        </div>

        <!-- Transactions List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Recent Transactions</h2>
            </div>

            <?php if (empty($transactions)): ?>
                <div class="p-12 text-center">
                    <i class="ri-file-list-line text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No Transactions Yet</h3>
                    <p class="text-gray-600 dark:text-gray-400">Start tracking your finances by adding your first transaction</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($transactions as $transaction): ?>
                        <?php
                        $type_colors = [
                            'income' => 'text-green-600 bg-green-100',
                            'expense' => 'text-red-600 bg-red-100',
                            'transfer' => 'text-blue-600 bg-blue-100'
                        ];
                        $type_icons = [
                            'income' => 'ri-arrow-down-circle-line',
                            'expense' => 'ri-arrow-up-circle-line',
                            'transfer' => 'ri-exchange-funds-line'
                        ];
                        ?>
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="p-3 rounded-full <?php echo $type_colors[$transaction['type']]; ?>">
                                        <i class="<?php echo $type_icons[$transaction['type']]; ?> text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white capitalize">
                                            <?php echo e($transaction['type']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo date('M d, Y', strtotime($transaction['transaction_date'])); ?>
                                        </div>
                                        <?php if ($transaction['note']): ?>
                                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                <?php echo e($transaction['note']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold <?php echo $transaction['type'] === 'income' ? 'text-green-600' : 'text-red-600'; ?>">
                                        <?php echo $transaction['type'] === 'income' ? '+' : '-'; ?> <?php echo formatCurrency($transaction['amount']); ?>
                                    </div>
                                    <button onclick="deleteTransaction(<?= $transaction['id'] ?>)" class="ml-3 text-gray-400 hover:text-red-600 transition-colors" title="Delete Transaction">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- Transaction Modal -->
<div id="transactionModal" class="hidden fixed inset-0 bg-black/50 z-50 p-4 transition-opacity duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl transform transition-all scale-95" id="modalContent">
        <form id="transactionForm" onsubmit="saveTransaction(event)">
            <input type="hidden" name="type" id="type">
            <input type="hidden" name="transaction_id" id="transaction_id">

            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white" id="modalTitle">Add Transaction</h2>
                <button type="button" onclick="closeTransactionModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>

            <div class="p-6 space-y-6">
                <!-- Amount & Date Row -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold"><?= $currencySymbol ?></span>
                            <input type="number" name="amount" id="amount" required step="0.01" min="1"
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition-all font-bold text-gray-900 dark:text-white"
                                placeholder="0.00">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date *</label>
                        <input type="date" name="transaction_date" id="transaction_date" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition-all text-gray-900 dark:text-white">
                    </div>
                </div>

                <!-- Wallet Selection -->
                <div id="singleWalletField">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" id="walletLabel">Wallet *</label>
                    <div class="relative">
                        <i class="ri-wallet-3-line absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <select name="wallet_id" id="wallet_id" class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition-all text-gray-900 dark:text-white appearance-none">
                            <option value="">Select Wallet</option>
                            <?php foreach ($wallets as $w): ?>
                                <option value="<?= $w['id'] ?>" data-balance="<?= $w['current_balance'] ?>" data-currency="<?= $w['currency'] ?>">
                                    <?= e($w['name']) ?> (<?= $w['currency'] ?> <?= number_format($w['current_balance']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Transfer Fields (From/To) -->
                <div id="transferWallets" class="hidden grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Wallet *</label>
                        <div class="relative">
                            <i class="ri-arrow-up-circle-line absolute left-4 top-1/2 -translate-y-1/2 text-red-500"></i>
                            <select name="from_wallet_id" id="from_wallet_id" class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition-all text-gray-900 dark:text-white appearance-none">
                                <option value="">Select Source</option>
                                <?php foreach ($wallets as $w): ?>
                                    <option value="<?= $w['id'] ?>" data-balance="<?= $w['current_balance'] ?>">
                                        <?= e($w['name']) ?> (<?= number_format($w['current_balance']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Wallet *</label>
                        <div class="relative">
                            <i class="ri-arrow-down-circle-line absolute left-4 top-1/2 -translate-y-1/2 text-green-500"></i>
                            <select name="to_wallet_id" id="to_wallet_id" class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition-all text-gray-900 dark:text-white appearance-none">
                                <option value="">Select Destination</option>
                                <?php foreach ($wallets as $w): ?>
                                    <option value="<?= $w['id'] ?>">
                                        <?= e($w['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Category Field -->
                <div id="categoryField">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category *</label>
                    <div class="relative">
                        <i class="ri-price-tag-3-line absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <select name="category_id" id="category_id" class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition-all text-gray-900 dark:text-white appearance-none">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>" data-type="<?= $c['type'] ?>">
                                    <?= e($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Note Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Note (Optional)</label>
                    <textarea name="note" id="note" rows="2"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition-all text-gray-900 dark:text-white"
                        placeholder="Add a remark..."></textarea>
                </div>
            </div>

            <div class="p-6 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3 bg-gray-50/50 dark:bg-gray-800/50 rounded-b-2xl">
                <button type="button" onclick="closeTransactionModal()"
                    class="px-6 py-2.5 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 font-medium transition-colors">
                    Cancel
                </button>
                <button type="submit" id="submitBtn"
                    class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-medium shadow-lg shadow-indigo-500/30 transition-all transform hover:scale-[1.02]">
                    Save Transaction
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Set default date to today
    document.getElementById('transaction_date').valueAsDate = new Date();

    function openTransactionModal(type) {
        const modal = document.getElementById('transactionModal');
        const form = document.getElementById('transactionForm');
        const title = document.getElementById('modalTitle');
        const typeInput = document.getElementById('type');
        const submitBtn = document.getElementById('submitBtn');

        // Reset form
        form.reset();
        document.getElementById('transaction_date').valueAsDate = new Date();
        document.getElementById('transaction_id').value = '';

        // Set type
        typeInput.value = type;

        // Configure UI based on type
        const singleWallet = document.getElementById('singleWalletField');
        const transferWallets = document.getElementById('transferWallets');
        const categoryField = document.getElementById('categoryField');
        const walletLabel = document.getElementById('walletLabel');

        if (type === 'income') {
            title.textContent = 'Add Income';
            title.className = 'text-xl font-bold text-green-600 dark:text-green-400';
            submitBtn.className = 'px-6 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 font-medium shadow-lg shadow-green-500/30 transition-all transform hover:scale-[1.02]';

            singleWallet.classList.remove('hidden');
            transferWallets.classList.add('hidden');
            transferWallets.classList.remove('grid');
            categoryField.classList.remove('hidden');
            walletLabel.textContent = 'Deposit To Wallet *';

            filterCategories('income');
        } else if (type === 'expense') {
            title.textContent = 'Add Expense';
            title.className = 'text-xl font-bold text-red-600 dark:text-red-400';
            submitBtn.className = 'px-6 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 font-medium shadow-lg shadow-red-500/30 transition-all transform hover:scale-[1.02]';

            singleWallet.classList.remove('hidden');
            transferWallets.classList.add('hidden');
            transferWallets.classList.remove('grid');
            categoryField.classList.remove('hidden');
            walletLabel.textContent = 'Pay From Wallet *';

            filterCategories('expense');
        } else if (type === 'transfer') {
            title.textContent = 'Transfer Money';
            title.className = 'text-xl font-bold text-blue-600 dark:text-blue-400';
            submitBtn.className = 'px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium shadow-lg shadow-blue-500/30 transition-all transform hover:scale-[1.02]';

            singleWallet.classList.add('hidden');
            transferWallets.classList.remove('hidden');
            transferWallets.classList.add('grid');
            categoryField.classList.add('hidden');
        }

        // Show modal with animation
        modal.classList.remove('hidden');
        modal.classList.add('flex', 'items-center', 'justify-center');
        setTimeout(() => {
            const content = document.getElementById('modalContent');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeTransactionModal() {
        const modal = document.getElementById('transactionModal');
        const content = document.getElementById('modalContent');

        content.classList.remove('scale-100');
        content.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex', 'items-center', 'justify-center');
        }, 200);
    }

    function filterCategories(type) {
        const select = document.getElementById('category_id');
        const options = select.options;

        for (let i = 0; i < options.length; i++) {
            const option = options[i];
            if (option.value === "") continue;

            if (option.dataset.type === type) {
                option.style.display = 'block';
                option.disabled = false;
            } else {
                option.style.display = 'none';
                option.disabled = true;
            }
        }
        select.value = ""; // Reset selection
    }

    async function saveTransaction(e) {
        e.preventDefault();

        const form = document.getElementById('transactionForm');
        const formData = new FormData(form);
        const btn = document.getElementById('submitBtn');
        const originalText = btn.textContent;

        // Basic validation
        const amount = parseFloat(formData.get('amount'));
        const type = formData.get('type');

        if (amount <= 0) {
            alert('Amount must be greater than 0');
            return;
        }

        if (type === 'transfer') {
            const fromId = formData.get('from_wallet_id');
            const toId = formData.get('to_wallet_id');
            if (fromId === toId) {
                alert('Cannot transfer to the same wallet');
                return;
            }
        }

        // Loading state
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i> Saving...';

        try {
            const response = await fetch('<?= url('/api/transactions.php') ?>', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Show success toast (using existing alert system if available, or simple reload)
                // Since show_alert() is PHP based, we'll reload nicely
                window.location.reload();
            } else {
                alert(result.error || 'Failed to save transaction');
                btn.disabled = false;
                btn.textContent = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while saving');
            btn.disabled = false;
            btn.textContent = originalText;
        }
    }

    // Add delete functionality to the list
    async function deleteTransaction(id) {
        if (!confirm('Are you sure you want to delete this transaction? This will reverse the balance changes.')) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('transaction_id', id);

            const response = await fetch('<?= url('/api/transactions.php') ?>', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                window.location.reload();
            } else {
                alert(result.error || 'Failed to delete transaction');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while deleting');
        }
    }
</script>