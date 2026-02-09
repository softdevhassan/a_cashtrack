<?php
// Fetch current user info
$user_id = $_APP['user']['id'];
$user = fetch('users', ['id' => $user_id], 'one');

// Handle update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {

    // ------------------ Image Upload ------------------
    $image_path = $user['image'] ?? '/images/default-avatar.webp';
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = __DIR__ . '/../assets/images/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $image_name;
        $new_path = '/images/' . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // delete old image
            if (!empty($user['image']) && $user['image'] !== '/images/default-avatar.webp') {
                $old_path = __DIR__ . '/../assets' . $user['image'];
                if (file_exists($old_path)) unlink($old_path);
            }
            $image_path = $new_path;
        } else {
            set_alert('error', 'Image upload failed.');
        }
    }

    // ------------------ Prepare Data ------------------
    $data = [
        'name' => e($_POST['name'] ?? ''),
        'email' => e($_POST['email'] ?? ''),
        'phone' => e($_POST['phone'] ?? ''),
        'username' => e($_POST['username'] ?? ''),
        'session_id' => session_id(),
        'last_updated_at' => date('Y-m-d H:i:s'),
        'last_updated_by' => $user_id
    ];

    // Password update (if entered)
    if (!empty($_POST['password'])) {
        $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    $data['image'] = $image_path;

    // ------------------ Update User ------------------
    $ok = update('users', $data, ['id' => $user_id]);

    // ------------------ Update Institute if Linked ------------------
    if (!empty($user['institute_id'])) {
        $inst_data = [
            'contact_person' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'last_updated_at' => date('Y-m-d H:i:s'),
            'last_updated_by' => $user_id,
        ];

        // Optional FindAndReplace() logic for institute text fields (if partial update needed)
        if (function_exists('FindAndReplace')) {
            $institute = fetch('institutes', ['id' => $user['institute_id']], 'one');
            if ($institute) {
                foreach ($inst_data as $key => $value) {
                    $inst_data[$key] = FindAndReplace($institute[$key], $value);
                }
            }
        }

        update('institutes', $inst_data, ['id' => $user['institute_id']]);
    }

    // ------------------ Update Session ------------------
    if ($ok) {
        // Refresh user data
        $updated_user = fetch('users', ['id' => $user_id], 'one');
        $_SESSION['user'] = $updated_user;
        $_APP['user'] = $updated_user;

        set_alert('success', 'Profile updated successfully.');
        redirect('/pages/account');
        exit;
    } else {
        set_alert('error', 'Failed to update profile.');
    }
}

/**
 * Optional utility — Replace old content with new while preserving old text pattern (can be refined).
 * For example, can handle partial field merges if used later.
 */
if (!function_exists('FindAndReplace')) {
    function FindAndReplace($old, $new)
    {
        if (trim($new) === '' || $new === $old) return $old;
        return $new;
    }
}
?>

<div class="max-w-3xl mx-auto border border-ts/20 bg-bp rounded-2xl p-6 mt-6" data-aos="fade-up">
    <h2 class="text-2xl font-semibold text-tp mb-6 flex items-center gap-2">
        <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center">
            <i class="ri-user-3-line text-accent text-xl"></i>
        </div>
        My Profile
    </h2>

    <form method="POST" enctype="multipart/form-data" class="space-y-5">
        <input type="hidden" name="action" value="update">

        <!-- Profile Image -->
        <div class="flex items-center gap-4">
            <img src="<?= $_APP['ASSETS_ROOT'] . e($user['image'] ?? '/images/default-avatar.webp') ?>"
                alt="Avatar" class="w-20 h-20 rounded-full border border-ts/20 object-cover">
            <div>
                <label class="block text-sm text-ts mb-1">Profile Image</label>
                <input type="file" name="image" accept="image/*"
                    class="border border-ts/40 w-full rounded-xl px-3 py-2 text-tp focus:outline-none focus:ring-2 focus:ring-accent/30">
            </div>
        </div>

        <!-- Name + Email -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-ts mb-1">Name</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                    class="w-full border border-ts/40 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent/30">
            </div>
            <div>
                <label class="block text-sm text-ts mb-1">Email</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                    class="w-full border border-ts/40 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent/30">
            </div>
        </div>



        <!-- Phone + Username -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-ts mb-1">Phone</label>
                <input type="text" name="phone" required value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                    class="w-full border border-ts/40 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent/30">
            </div>
            <div>
                <label class="block text-sm text-ts mb-1">Username <?= $user['role_id'] == 2 ? '(Can\'t Change)' : '' ?></label>
                <input type="text" name="username" <?= $user['role_id'] == 2 ? 'readonly' : 'required' ?> value="<?= htmlspecialchars($user['username'] ?? '') ?>"
                    class="w-full border border-ts/40 bg-bs rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent/30">
            </div>
        </div>

        <!-- Password -->
        <div>
            <label class="block text-sm text-ts mb-1">Password (Leave blank to keep current)</label>
            <input type="password" name="password"
                placeholder="********"
                class="w-full border border-ts/40 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent/30">

            <!-- Save -->
            <div class="pt-4">
                <button type="submit" class="px-5 py-2 bg-accent text-white rounded-xl border border-accent hover:opacity-90 transition">
                    Update Profile
                </button>
            </div>
    </form>
</div>