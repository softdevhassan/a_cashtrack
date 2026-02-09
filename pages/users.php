<?php
// Handle form submissions for CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // Handle image upload
    $image_path = '/images/default-avatar.webp';
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = __DIR__ . '/../assets/images/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $image_name;
        $image_path = '/images/' . $image_name;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = '/images/default-avatar.webp';
            set_alert('error', 'Failed to upload image.');
        }
    }

    if ($action === 'add') {
        $data = [
            'name' => e($_POST['name'] ?? ''),
            'email' => e($_POST['email'] ?? ''),
            'phone' => e($_POST['phone'] ?? ''),
            'username' => e($_POST['username'] ?? ''),
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'image' => $image_path,
            'role_id' => intval($_POST['role_id'] ?? 1),
            'locked' => isset($_POST['locked']) ? 1 : 0,
            'last_updated_at' => date('Y-m-d H:i:s'),
            'last_updated_by' => $_APP['user']['id']
        ];
        $ok = insert('users', $data);
        if ($ok) {
            set_alert('success', 'User created successfully.');
            redirect('/pages/users');
            exit;
        } else {
            set_alert('error', 'Failed to create user.');
        }
    }

    if ($action === 'update' && !empty($_POST['id'])) {
        $id = intval($_POST['id']);

        $data = [
            'name' => e($_POST['name'] ?? ''),
            'email' => e($_POST['email'] ?? ''),
            'phone' => e($_POST['phone'] ?? ''),
            'username' => e($_POST['username'] ?? ''),
            'role_id' => intval($_POST['role_id'] ?? 1),
            'locked' => isset($_POST['locked']) ? 1 : 0,
            'session_id' => '',
            'last_updated_at' => date('Y-m-d H:i:s'),
            'last_updated_by' => $_APP['user']['id']
        ];
        if ($_APP['user']['id'] == $id) {
            $data['session_id'] = session_id();
        }
        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        if ($image_path !== '/images/default-avatar.webp') {
            if (!empty($_POST['current_image'])) {
                $old_path = __DIR__ . '/../assets' . $_POST['current_image'];
                if (file_exists($old_path) && strpos($_POST['current_image'], 'default-avatar') === false) {
                    unlink($old_path);
                }
            }
            $data['image'] = $image_path;
        }

        // If editing self, refresh session
        if ($_SESSION['user']['id'] == $id) {
            $_SESSION['user'] = array_merge($_SESSION['user'], $data);
        }

        $ok = update('users', $data, ['id' => $id]);
        if ($ok) {
            set_alert('success', 'User updated successfully.');
            redirect("/pages/users");
            exit;
        } else {
            set_alert('error', 'Failed to update user.');
        }
    }

    if ($action === 'delete' && !empty($_POST['id'])) {
        $id = intval($_POST['id']);
        $deleted = delete('users', ['id' => $id]);
        if ($deleted) {
            set_alert('success', 'User deleted successfully.');
            redirect('/pages/users');
            exit;
        } else {
            set_alert('error', 'Failed to delete user.');
        }
    }
}

// Handle status toggle
if (isset($_GET['action']) && $_GET['action'] === 'update_status' && !empty($_GET['id'])) {
    $status = $_GET['status'] === 'ACTIVE' ? 0 : 1; // Toggle locked
    update('users', ['locked' => $status], ['id' => $_GET['id']]);
    set_alert('success', 'User status updated successfully!');
    redirect('/pages/users');
}

// Fetch all users
$users = fetch('users', [], 'all');
?>

<!-- Header -->
<div class="border-b pb-4 mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0">
    <div>
        <h1 class="text-2xl font-bold text-tp">Users Management</h1>
        <p class="text-tp/70 text-base">Manage system users and permissions</p>
    </div>
    <div class="flex flex-wrap justify-end items-center gap-2">
        <button onclick="openModal('user-modal')" class="px-4 py-2 bg-accent text-white rounded-xl hover:bg-accent/90 flex items-center gap-2">
            <i class="ri-user-add-line"></i> Add New User
        </button>
    </div>
</div>

<!-- Search & Filters -->
<div class="mb-6">
    <div class="relative max-w-md">
        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-tp/50"></i>
        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search users by name, email, or username..."
            class="w-full pl-10 pr-4 py-2 border border-ts/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent/50 bg-white">
    </div>
</div>

<!-- Users Table -->
<div class="bg-white border border-ts/30 rounded-2xl overflow-hidden shadow-sm" data-aos="fade-up">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50/50">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-xs font-medium text-tp/70 uppercase tracking-wider">User</th>
                    <th class="px-4 sm:px-6 py-3 text-xs font-medium text-tp/70 uppercase tracking-wider">Contact</th>
                    <th class="px-4 sm:px-6 py-3 text-xs font-medium text-tp/70 uppercase tracking-wider">Role</th>
                    <th class="px-4 sm:px-6 py-3 text-xs font-medium text-tp/70 uppercase tracking-wider">Status</th>
                    <th class="px-4 sm:px-6 py-3 text-xs font-medium text-tp/70 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ts/10" id="usersTableBody">
                <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 sm:px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="<?= $_APP['ASSETS_ROOT'] . e($user['image']) ?>" alt="" class="w-10 h-10 rounded-full object-cover border border-ts/20">
                                <div>
                                    <div class="font-medium text-tp"><?= htmlspecialchars($user['name']) ?></div>
                                    <div class="text-xs text-tp/60">@<?= htmlspecialchars($user['username']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 sm:px-6 py-4">
                            <div class="text-sm text-tp"><?= htmlspecialchars($user['email']) ?></div>
                            <div class="text-xs text-tp/60"><?= htmlspecialchars($user['phone']) ?></div>
                        </td>
                        <td class="px-4 sm:px-6 py-4">
                            <?php
                            $roles = fetch('app_settings', ['type' => 'roles', 'id' => $user['role_id']], 'one');
                            $roleName = $roles['name'] ?? 'Unknown';
                            ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-600 border border-blue-100">
                                <?= $roleName ?>
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4">
                            <?php if ($user['locked']): ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-red-50 text-red-600 border border-red-100">Locked</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-50 text-green-600 border border-green-100">Active</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="?action=update_status&id=<?= $user['id'] ?>&status=<?= $user['locked'] ? 'ACTIVE' : 'LOCKED' ?>"
                                    class="p-2 text-tp/70 hover:text-tp hover:bg-gray-100 rounded-lg transition" title="Toggle Status">
                                    <i class="<?= $user['locked'] ? 'ri-lock-2-line' : 'ri-lock-unlock-line' ?>"></i>
                                </a>
                                <button onclick='editUser(<?= je($user) ?>)'
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <?php if ($_APP['user']['id'] != $user['id']): ?>
                                    <button onclick="confirmDelete(<?= $user['id'] ?>)"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="user-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal('user-modal')"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg" data-aos="zoom-in">
                <form method="POST" enctype="multipart/form-data" id="userForm">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="userId" value="">
                    <input type="hidden" name="current_image" id="currentImage" value="">

                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-xl font-bold leading-6 text-gray-900" id="modalTitle">Add New User</h3>
                            <button type="button" onclick="closeModal('user-modal')" class="text-gray-400 hover:text-gray-500">
                                <i class="ri-close-line text-2xl"></i>
                            </button>
                        </div>

                        <!-- Form Content -->
                        <div class="space-y-4">
                            <div class="flex justify-center mb-6">
                                <div class="relative group cursor-pointer w-24 h-24" onclick="document.getElementById('imageInput').click()">
                                    <img id="imagePreview" src="<?= $_APP['ASSETS_ROOT'] ?>/images/default-avatar.webp" class="w-full h-full rounded-full object-cover border-4 border-gray-100 group-hover:border-accent/20 transition-all">
                                    <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                                        <i class="ri-camera-line text-white text-2xl"></i>
                                    </div>
                                    <div class="absolute bottom-0 right-0 bg-accent text-white p-1 rounded-full shadow-lg">
                                        <i class="ri-pencil-line text-sm"></i>
                                    </div>
                                </div>
                                <input type="file" name="image" id="imageInput" accept="image/*" class="hidden" onchange="previewImage(this)">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                    <input type="text" name="name" id="userName" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                                    <input type="text" name="username" id="userUsername" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" id="userEmail"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                    <input type="text" name="phone" id="userPhone"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1" id="passwordLabel">Password *</label>
                                    <input type="password" name="password" id="userPassword"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                                    <select name="role_id" id="userRole" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent">
                                        <option value="">Select Role</option>
                                        <?php
                                        $roles = fetch('app_settings', ['type' => 'roles'], 'all');
                                        foreach ($roles as $role) {
                                            echo "<option value='{$role['id']}'>{$role['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <input type="checkbox" name="locked" id="userLocked" value="1" class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent">
                                <label for="userLocked" class="text-sm font-medium text-gray-700 cursor-pointer select-none">Lock Account (Prevent Login)</label>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-4 sm:px-6 flex flex-col sm:flex-row-reverse gap-3">
                        <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-accent px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-accent/90 sm:w-auto transition-all">
                            Save Changes
                        </button>
                        <button type="button" onclick="closeModal('user-modal')" class="inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:w-auto transition-all">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        if (id === 'user-modal' && document.getElementById('formAction').value === 'add') {
            document.getElementById('userForm').reset();
            document.getElementById('modalTitle').innerText = 'Add New User';
            document.getElementById('passwordLabel').innerText = 'Password *';
            document.getElementById('userPassword').required = true;
            document.getElementById('imagePreview').src = '<?= $_APP['ASSETS_ROOT'] ?>/images/default-avatar.webp';
        }
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        // Reset form action to add when closing, so next open is clean
        setTimeout(() => {
            document.getElementById('formAction').value = 'add';
            document.getElementById('userId').value = '';
        }, 200);
    }

    function editUser(user) {
        document.getElementById('formAction').value = 'update';
        document.getElementById('userId').value = user.id;
        document.getElementById('modalTitle').innerText = 'Edit User';

        document.getElementById('userName').value = user.name;
        document.getElementById('userUsername').value = user.username;
        document.getElementById('userEmail').value = user.email;
        document.getElementById('userPhone').value = user.phone;
        document.getElementById('userRole').value = user.role_id;
        document.getElementById('userLocked').checked = user.locked == 1;
        document.getElementById('currentImage').value = user.image;

        // Password optional on edit
        document.getElementById('passwordLabel').innerText = 'Password (leave blank to keep)';
        document.getElementById('userPassword').required = false;

        if (user.image) {
            document.getElementById('imagePreview').src = '<?= $_APP['ASSETS_ROOT'] ?>' + user.image;
        }

        openModal('user-modal');
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const rows = document.getElementById('usersTableBody').getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const text = rows[i].textContent || rows[i].innerText;
            if (text.toLowerCase().indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }

    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this user?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="${id}">`;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('user-modal');
        }
    });
</script>