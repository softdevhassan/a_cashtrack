<?php
// Only allow admin users
if (!isSuperUser()) {
    redirect('/auth/login');
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);

    switch ($action) {
        case 'create':
            // Create new plan
            $errors = [];

            $required_fields = ['title', 'price', 'transaction_limit', 'duration_days'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                }
            }

            if (empty($errors)) {
                $plan_data = [
                    'title' => e($_POST['title']),
                    'price' => floatval($_POST['price']),
                    'transaction_limit' => intval($_POST['transaction_limit']),
                    'duration_days' => intval($_POST['duration_days']),
                    'is_trial' => isset($_POST['is_trial']) ? 1 : 0,
                    'status' => $_POST['status'] ?? 'active',
                    'description' => e($_POST['description'] ?? ''),
                    'features' => e($_POST['features'] ?? ''),
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $plan_id = insert('plans', $plan_data);
                if ($plan_id) {
                    set_alert('success', 'Plan created successfully!');
                    redirect('/pages/plans-management');
                } else {
                    set_alert('error', 'Failed to create plan.');
                }
            } else {
                foreach ($errors as $error) {
                    set_alert('error', $error);
                }
            }
            break;

        case 'update':
            // Update existing plan
            $plan_data = [
                'title' => e($_POST['title']),
                'price' => floatval($_POST['price']),
                'transaction_limit' => intval($_POST['transaction_limit']),
                'duration_days' => intval($_POST['duration_days']),
                'is_trial' => isset($_POST['is_trial']) ? 1 : 0,
                'status' => $_POST['status'] ?? 'active',
                'description' => e($_POST['description'] ?? ''),
                'features' => e($_POST['features'] ?? ''),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $update = update('plans', $plan_data, ['id' => $id]);
            if ($update) {
                set_alert('success', 'Plan updated successfully!');
            } else {
                set_alert('error', 'Failed to update plan.');
            }
            redirect('/pages/plans-management');
            break;

        case 'delete':
            // Delete plan (soft delete)
            $update = update('plans', [
                'status' => 'deleted',
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id]);

            if ($update) {
                set_alert('success', 'Plan deleted successfully!');
            } else {
                set_alert('error', 'Failed to delete plan.');
            }
            redirect('/pages/plans-management');
            break;

        case 'deactivate':
            // Deactivate plan (Pause)
            $update = update('plans', [
                'status' => 'inactive',
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id]);

            if ($update) {
                set_alert('success', 'Plan paused successfully!');
            } else {
                set_alert('error', 'Failed to pause plan.');
            }
            redirect('/pages/plans-management');
            break;

        case 'activate':
            // Activate plan
            $update = update('plans', [
                'status' => 'active',
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $id]);

            if ($update) {
                set_alert('success', 'Plan activated successfully!');
            } else {
                set_alert('error', 'Failed to activate plan.');
            }
            redirect('/pages/plans-management');
            break;
    }
}

// Get all plans (exclude deleted)
$plans = fetch('plans', [], 'all', [], 'ORDER BY created_at DESC');
$plans = array_filter($plans, fn($p) => $p['status'] !== 'deleted');

// Check for edit request
$edit_plan = null;
if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $edit_plan_id = intval($_GET['id']);
    // Check if plan exists in the already fetched plans or fetch it directly
    foreach ($plans as $p) {
        if ($p['id'] == $edit_plan_id) {
            $edit_plan = $p;
            break;
        }
    }
    // Fallback fetch if not found (e.g. if we filtered it out or pagination in future)
    if (!$edit_plan) {
        $edit_plan = fetch('plans', ['id' => $edit_plan_id], 'one');
    }
}

// Get stats
$stats = [
    'total_plans' => count($plans),
    'active_plans' => count(array_filter($plans, fn($p) => $p['status'] == 'active')),
    'total_subscriptions' => count(fetch('subscriptions', [], 'all')),
    'total_revenue' => 0  // Remove billing_invoices dependency
];
?>

<div class="border-b pb-4 mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0">
    <div>
        <h1 class="text-2xl font-bold text-tp">Plans Management</h1>
        <p class="text-tp/70 text-base">Manage subscription plans and pricing</p>
    </div>
    <div class="flex flex-wrap justify-end items-center gap-2">
        <button type="button" onclick="openModal('create-plan')"
            class="px-4 py-2 bg-accent text-white rounded-2xl hover:bg-accent/90 flex items-center gap-2">
            <i class="ri-add-line"></i> Create New Plan
        </button>
    </div>
</div>

<!-- Stats Overview -->
<div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white border border-ts/30 rounded-2xl p-4 sm:p-6" data-aos="fade-up">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-tp/70">Total Plans</p>
                <h3 class="text-2xl font-bold text-tp"><?= $stats['total_plans']; ?></h3>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="ri-price-tag-3-line text-xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white border border-ts/30 rounded-2xl p-4 sm:p-6" data-aos="fade-up" data-aos-delay="100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-tp/70">Active Plans</p>
                <h3 class="text-2xl font-bold text-tp"><?= $stats['active_plans']; ?></h3>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="ri-checkbox-circle-line text-xl text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white border border-ts/30 rounded-2xl p-4 sm:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-tp/70">Active Subscriptions</p>
                <h3 class="text-2xl font-bold text-tp"><?= $stats['total_subscriptions']; ?></h3>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="ri-user-star-line text-xl text-purple-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white border border-ts/30 rounded-2xl p-4 sm:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-tp/70">Monthly Revenue</p>
                <h3 class="text-2xl font-bold text-tp"><?= formatCurrency($stats['total_revenue']); ?></h3>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="ri-money-dollar-circle-line text-xl text-orange-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Plans List -->
<div class="bg-white border border-ts/30 rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="100">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Plan</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Price</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Transactions/Month</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Duration</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Status</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Created</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-tp/70 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-ts/30">
                <?php if (empty($plans)): ?>
                    <tr>
                        <td colspan="7" class="px-4 sm:px-6 py-4 text-center text-sm text-tp/70">
                            <div class="py-8">
                                <i class="ri-price-tag-3-line text-3xl text-tp/30 mb-2"></i>
                                <p>No plans found. Create your first plan!</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($plans as $plan): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 sm:px-6 py-4">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-tp"><?= e($plan['title']); ?></div>
                                        <div class="text-xs text-tp/70"><?= e($plan['description']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-tp"><?= formatCurrency($plan['price']); ?></div>
                                <div class="text-xs text-tp/70">per month</div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-tp"><?= number_format($plan['transaction_limit']); ?></div>
                                <div class="text-xs text-tp/70">transactions</div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-tp"><?= $plan['duration_days']; ?> days</div>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full 
                                <?= $plan['status'] == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?= ucfirst($plan['status']); ?>
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-tp/70">
                                <?= date('M d, Y', strtotime($plan['created_at'])); ?>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="editPlan(<?= $plan['id']; ?>)"
                                        class="text-blue-600 hover:text-blue-900">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <button type="button" onclick="openModal('delete-plan', <?= $plan['id']; ?>)"
                                        class="text-red-600 hover:text-red-900">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                    <?php if ($plan['status'] == 'active'): ?>
                                        <button type="button" onclick="togglePlanStatus(<?= $plan['id']; ?>, 'deactivate')"
                                            class="text-orange-600 hover:text-orange-900">
                                            <i class="ri-pause-circle-line"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" onclick="togglePlanStatus(<?= $plan['id']; ?>, 'activate')"
                                            class="text-green-600 hover:text-green-900">
                                            <i class="ri-play-circle-line"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create/Edit Plan Modal -->
<div id="plan-modal" class="hidden fixed inset-0 bg-black/50 z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" data-aos="zoom-in">
        <form method="POST" id="plan-form">
            <input type="hidden" name="action" id="form-action" value="create">
            <input type="hidden" name="id" id="plan-id" value="">

            <div class="p-4 sm:p-6 border-b border-ts/30">
                <h3 class="text-lg font-semibold text-tp" id="modal-title">Create New Plan</h3>
            </div>

            <div class="p-4 sm:p-6 space-y-6">
                <!-- Plan Title -->
                <div>
                    <label class="block text-sm font-medium text-tp mb-2">Plan Title *</label>
                    <input type="text" name="title" required
                        class="w-full px-3 py-2 border border-ts/30 rounded-2xl focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent"
                        placeholder="e.g., Starter Plan">
                    <p class="text-xs text-tp/70 mt-1">Name of the plan that users will see</p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-tp mb-2">Description</label>
                    <textarea name="description" rows="2"
                        class="w-full px-3 py-2 border border-ts/30 rounded-2xl focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent"
                        placeholder="Brief description of plan features"></textarea>
                </div>

                <!-- Price & Limits -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-tp mb-2">Price *</label>
                        <input type="number" name="price" required step="0.01" min="0"
                            class="w-full px-3 py-2 border border-ts/30 rounded-2xl focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent"
                            placeholder="500">
                        <p class="text-xs text-tp/70 mt-1">Monthly price</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-tp mb-2">Transaction Limit *</label>
                        <input type="number" name="transaction_limit" required min="1"
                            class="w-full px-3 py-2 border border-ts/30 rounded-2xl focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent"
                            placeholder="500">
                        <p class="text-xs text-tp/70 mt-1">Transactions per month</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-tp mb-2">Duration (Days) *</label>
                        <input type="number" name="duration_days" required min="1"
                            class="w-full px-3 py-2 border border-ts/30 rounded-2xl focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent"
                            placeholder="30">
                        <p class="text-xs text-tp/70 mt-1">Subscription period</p>
                    </div>
                </div>

                <!-- Features -->
                <div>
                    <label class="block text-sm font-medium text-tp mb-2">Features (one per line)</label>
                    <textarea name="features" rows="4"
                        class="w-full px-3 py-2 border border-ts/30 rounded-2xl focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent font-mono text-sm"
                        placeholder="✓ 10k Monthly Emails
✓ Campaign Scheduling
✓ Basic Analytics
✓ Email Support"></textarea>
                    <p class="text-xs text-tp/70 mt-1">List features separated by new lines</p>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-tp mb-2">Status</label>
                    <select name="status"
                        class="w-full px-3 py-2 border border-ts/30 rounded-2xl focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="p-4 sm:p-6 border-t border-ts/30 flex justify-end gap-3">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 bg-gray-100 border border-ts/30 text-tp rounded-2xl hover:bg-gray-200">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-accent text-white rounded-2xl hover:bg-accent/90">
                    <span id="submit-btn-text">Create Plan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black/50 z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-md" data-aos="zoom-in">
        <form method="POST">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="delete-plan-id" value="">

            <div class="p-4 sm:p-6">
                <div class="text-center mb-4">
                    <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <i class="ri-alert-line text-2xl text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-tp mb-2">Delete Plan</h3>
                    <p class="text-sm text-tp/70">Are you sure you want to delete this plan? This action cannot be undone.</p>
                </div>
            </div>

            <div class="p-4 sm:p-6 border-t border-ts/30 flex justify-end gap-3">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 bg-gray-100 border border-ts/30 text-tp rounded-2xl hover:bg-gray-200">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded-2xl hover:bg-red-700">
                    Delete Plan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Modal functions
    function openModal(type, planId = null) {
        closeModal(); // Close any open modal first

        if (type === 'create-plan') {
            document.getElementById('modal-title').textContent = 'Create New Plan';
            document.getElementById('form-action').value = 'create';
            document.getElementById('submit-btn-text').textContent = 'Create Plan';
            document.getElementById('plan-form').reset();
            document.getElementById('plan-id').value = '';
            document.getElementById('plan-modal').classList.remove('hidden');
            document.getElementById('plan-modal').classList.add('flex', 'items-center', 'justify-center');

            // Remove ID from URL if exists
            const url = new URL(window.location);
            if (url.searchParams.has('id')) {
                url.searchParams.delete('id');
                window.history.replaceState({}, document.title, url);
            }
        } else if (type === 'delete-plan' && planId) {
            document.getElementById('delete-plan-id').value = planId;
            const deleteModal = document.getElementById('delete-modal');
            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex', 'items-center', 'justify-center');
        }
    }

    function closeModal() {
        const planModal = document.getElementById('plan-modal');
        planModal.classList.add('hidden');
        planModal.classList.remove('flex', 'items-center', 'justify-center');

        const deleteModal = document.getElementById('delete-modal');
        deleteModal.classList.add('hidden');
        deleteModal.classList.remove('flex', 'items-center', 'justify-center');
    }

    // Edit plan function - reloads page with ID
    function editPlan(planId) {
        window.location.href = '?id=' + planId;
    }

    <?php if ($edit_plan): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const plan = <?= je($edit_plan); ?>;

            document.getElementById('modal-title').textContent = 'Edit Plan';
            document.getElementById('form-action').value = 'update';
            document.getElementById('submit-btn-text').textContent = 'Update Plan';
            document.getElementById('plan-id').value = plan.id;

            // Populate form fields
            const form = document.getElementById('plan-form');
            form.querySelector('input[name="title"]').value = plan.title;
            form.querySelector('textarea[name="description"]').value = plan.description || '';
            form.querySelector('input[name="price"]').value = plan.price;
            form.querySelector('input[name="transaction_limit"]').value = plan.transaction_limit;
            form.querySelector('input[name="duration_days"]').value = plan.duration_days;
            form.querySelector('textarea[name="features"]').value = plan.features || '';
            form.querySelector('select[name="status"]').value = plan.status;

            // Handle checkbox
            const trialCheckbox = form.querySelector('input[name="is_trial"]');
            if (trialCheckbox) {
                trialCheckbox.checked = plan.is_trial == 1;
            }

            const modal = document.getElementById('plan-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex', 'items-center', 'justify-center');
        });
    <?php endif; ?>

    // Toggle plan status
    function togglePlanStatus(planId, action) {
        if (!confirm(`Are you sure you want to ${action} this plan?`)) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
        <input type="hidden" name="action" value="${action}">
        <input type="hidden" name="id" value="${planId}">
    `;

        document.body.appendChild(form);
        form.submit();
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    // Close modal on background click
    document.getElementById('plan-modal')?.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    document.getElementById('delete-modal')?.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>

<?php function page_sc()
{ ?>
    <style>
        /* Additional styles for better UX */
        #plan-modal .modal-content {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive table */
        @media (max-width: 768px) {
            .table-responsive {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
<?php } ?>