<?php
// Ensure CSRF token exists
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 1. Show Forgot Password Form
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_GET['token'])) {
?>

  <div class="h-screen w-full bg-bp text-tp flex overflow-hidden font-sans selection:bg-primary selection:text-white">

    <!-- Left Side: Branding -->
    <div class="hidden lg:flex w-1/2 bg-bs relative flex-col justify-between p-12 border-r border-gray-200">
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-1/2 -right-24 w-64 h-64 bg-secondary/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>
      </div>

      <div class="relative z-10">
        <a href="<?= url('/') ?>" class="flex items-center gap-3 mb-12">
          <img src="<?= asset('images/logo-full-color.webp') ?>" alt="CashTrack" class="h-10 w-auto">
        </a>

        <div class="max-w-md">
          <h2 class="text-4xl font-black mb-6 text-tp">Forgot Password?</h2>
          <p class="text-xl text-ts mb-8">It happens to the best of us. Enter your credentials and we'll get you back on track.</p>
        </div>
      </div>

      <div class="relative z-10 text-ts text-sm">
        <p>&copy; <?= date('Y') ?> CashTrack. Secure Account Recovery.</p>
      </div>
    </div>

    <!-- Right Side: Form -->
    <div class="w-full lg:w-1/2 h-full overflow-y-auto bg-bp scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent">
      <div class="min-h-full flex flex-col justify-center max-w-md mx-auto p-8">

        <!-- Mobile Header -->
        <div class="lg:hidden mb-8 flex items-center justify-between">
          <a href="<?= url('/') ?>" class="flex items-center gap-2">
            <img src="<?= asset('images/logo-full-color.webp') ?>" alt="CashTrack" class="h-8 w-auto">
          </a>
        </div>

        <div class="mb-8">
          <h1 class="text-3xl font-black text-tp mb-2">Reset Password</h1>
          <p class="text-ts">Enter your email or username to receive a reset link.</p>
        </div>

        <?php show_alert(); ?>

        <form method="POST" class="space-y-6">
          <input type="hidden" name="csrf_token" value="<?= encryptData($_SESSION['csrf_token']) ?>">

          <!-- Email/Username -->
          <div>
            <label class="block text-sm font-medium text-ts mb-2">Email or Username</label>
            <div class="relative">
              <i class="ri-user-line absolute left-4 top-1/2 -translate-y-1/2 text-ts"></i>
              <input type="text" name="email_or_username" required placeholder="Enter email or username"
                class="w-full pl-12 pr-4 py-3 rounded-xl bg-bs border border-gray-200 text-tp placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none focus:outline-none transition-all">
            </div>
          </div>

          <!-- Submit -->
          <button type="submit" name="request_reset" class="w-full py-4 rounded-xl font-bold text-white bg-gradient-to-r from-primary to-secondary shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:scale-[1.01] transition-all">
            Send Reset Link <i class="ri-mail-send-line ml-2"></i>
          </button>

          <!-- Back to Login -->
          <div class="text-center">
            <a href="<?= url('/auth/login') ?>" class="text-primary hover:text-secondary font-bold flex items-center justify-center gap-2 transition-colors">
              <i class="ri-arrow-left-line"></i> Back to Login
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php
}

// 2. Handle Reset Request (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_reset'])) {
  $input = trim($_POST['email'] ?? $_POST['username'] ?? $_POST['email_or_username'] ?? '');

  if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
    $user = fetch('users', ['email' => $input], 'one');
  } else {
    $user = fetch('users', ['username' => $input], 'one');
  }

  if ($user) {
    // Generate secure token
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store in password_resets table
    query("DELETE FROM password_resets WHERE user_id = ?", [$user['id']], 'i');

    insert('password_resets', [
      'user_id' => $user['id'],
      'token' => $token,
      'expires_at' => $expires_at,
      'created_at' => date('Y-m-d H:i:s')
    ]);

    // Send Email
    $link = url("/auth/password-reset") . "?token=" . $token;

    $emailData = [
      'to' => $user['email'],
      'subject' => 'Reset Your Password - ' . ($_ENV['APP_NAME'] ?? 'CashTrack'),
      'placeholders' => [
        'app_name' => $_ENV['APP_NAME'] ?? 'CashTrack',
        'username' => $user['name'],
        'reset_link' => $link,
        'year' => date('Y'),
        'logo' => asset($_ENV['APP_PRIMARY_LOGO_TRANSPARENT'] ?? '')
      ]
    ];

    send_email('password-reset', $emailData);
  }

  // Success message (Always show to prevent enumeration)
?>
  <div class="min-h-screen flex items-center justify-center bg-bs px-4 font-sans selection:bg-primary selection:text-white">
    <div class="text-center bg-bp p-10 rounded-3xl shadow-xl border border-gray-200 max-w-md relative overflow-hidden" data-aos="zoom-in">

      <!-- Glow effect -->
      <div class="absolute -top-24 -left-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>

      <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center text-primary mx-auto mb-6 relative z-10">
        <i class="ri-mail-check-line text-4xl"></i>
      </div>
      <h2 class="text-3xl font-black mb-3 text-tp relative z-10">Check Your Email</h2>
      <p class="text-ts mb-8 leading-relaxed relative z-10">
        If an account exists with that username or email, we've sent you instructions to reset your password.
      </p>
      <a href="<?= url('/auth/login') ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary to-secondary text-white font-bold rounded-xl hover:shadow-xl transition-all relative z-10">
        <i class="ri-arrow-left-line"></i> Back to Login
      </a>
    </div>
  </div>
  <?php
  exit;
}

// 3. Show Reset Password Form (GET with token)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
  $token = $_GET['token'];

  // Validate token
  $reset = fetch('password_resets', ['token' => $token], 'one');

  if (!$reset || strtotime($reset['expires_at']) < time()) {
  ?>
    <div class="min-h-screen flex items-center justify-center bg-bs px-4 font-sans selection:bg-primary selection:text-white">
      <div class="text-center bg-bp p-10 rounded-3xl shadow-xl border border-gray-200 max-w-md" data-aos="zoom-in">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center text-red-600 mx-auto mb-6">
          <i class="ri-error-warning-line text-4xl"></i>
        </div>
        <h2 class="text-3xl font-black mb-3 text-tp">Link Expired</h2>
        <p class="text-ts mb-8">
          This password reset link is invalid or has expired.
        </p>
        <a href="<?= url('/auth/password-reset') ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-all">
          Request a New Link <i class="ri-arrow-right-line"></i>
        </a>
      </div>
    </div>
  <?php
    exit;
  }
  ?>

  <div class="h-screen w-full bg-bp text-tp flex overflow-hidden font-sans selection:bg-primary selection:text-white">

    <!-- Left Side: Branding -->
    <div class="hidden lg:flex w-1/2 bg-bs relative flex-col justify-between p-12 border-r border-gray-200">
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-1/2 -right-24 w-64 h-64 bg-secondary/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>
      </div>

      <div class="relative z-10">
        <a href="<?= url('/') ?>" class="flex items-center gap-3 mb-12">
          <img src="<?= asset('images/logo-full-color.webp') ?>" alt="CashTrack" class="h-10 w-auto">
        </a>

        <div class="max-w-md">
          <h2 class="text-4xl font-black mb-6 text-tp">Create New Password</h2>
          <p class="text-xl text-ts mb-8">Make it strong. Use a mix of letters, numbers, and symbols.</p>
        </div>
      </div>

      <div class="relative z-10 text-ts text-sm">
        <p>&copy; <?= date('Y') ?> CashTrack. Secure Account Recovery.</p>
      </div>
    </div>

    <!-- Right Side: Form -->
    <div class="w-full lg:w-1/2 h-full overflow-y-auto bg-bp scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent">
      <div class="min-h-full flex flex-col justify-center max-w-md mx-auto p-8">

        <!-- Mobile Header -->
        <div class="lg:hidden mb-8 flex items-center justify-between">
          <a href="<?= url('/') ?>" class="flex items-center gap-2">
            <img src="<?= asset('images/logo-full-color.webp') ?>" alt="CashTrack" class="h-8 w-auto">
          </a>
        </div>

        <div class="mb-8">
          <h1 class="text-3xl font-black text-tp mb-2">Reset Password</h1>
          <p class="text-ts">Set your new password below.</p>
        </div>

        <form method="POST" class="space-y-6">
          <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

          <!-- New Password -->
          <div>
            <label class="block text-sm font-medium text-ts mb-2">New Password</label>
            <div class="relative">
              <i class="ri-lock-line absolute left-4 top-1/2 -translate-y-1/2 text-ts"></i>
              <input type="password" name="new_password" required minlength="8" placeholder="Min 8 characters"
                class="w-full pl-12 pr-4 py-3 rounded-xl bg-bs border border-gray-200 text-tp placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none focus:outline-none transition-all">
            </div>
          </div>

          <!-- Confirm Password -->
          <div>
            <label class="block text-sm font-medium text-ts mb-2">Confirm Password</label>
            <div class="relative">
              <i class="ri-lock-star-line absolute left-4 top-1/2 -translate-y-1/2 text-ts"></i>
              <input type="password" name="confirm_password" required placeholder="Re-enter password"
                class="w-full pl-12 pr-4 py-3 rounded-xl bg-bs border border-gray-200 text-tp placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none focus:outline-none transition-all">
            </div>
          </div>

          <!-- Submit -->
          <button type="submit" name="do_reset" class="w-full py-4 rounded-xl font-bold text-white bg-gradient-to-r from-primary to-secondary shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:scale-[1.01] transition-all">
            Update Password <i class="ri-check-line ml-2"></i>
          </button>

        </form>
      </div>
    </div>
  </div>

<?php
}

// 4. Handle Password Update (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_reset'])) {
  $token = $_POST['token'];
  $password = $_POST['new_password'];
  $confirm = $_POST['confirm_password'];

  if ($password !== $confirm) {
    set_alert('error', 'Passwords do not match');
    redirect("/auth/password-reset?token=$token");
    exit;
  }

  if (strlen($password) < 8) {
    set_alert('error', 'Password must be at least 8 characters');
    redirect("/auth/password-reset?token=$token");
    exit;
  }

  // Validate token again
  $reset = fetch('password_resets', ['token' => $token], 'one');

  if (!$reset || strtotime($reset['expires_at']) < time()) {
    set_alert('error', 'Invalid or expired token');
    redirect('/auth/password-reset');
    exit;
  }

  // Update User Password
  $userId = $reset['user_id'];
  $hashed = password_hash($password, PASSWORD_DEFAULT);

  update('users', [
    'password' => $hashed,
    'last_updated_at' => date('Y-m-d H:i:s')
  ], ['id' => $userId]);

  // Delete used token
  query("DELETE FROM password_resets WHERE token = ?", [$token], 's');

?>
  <div class="min-h-screen flex items-center justify-center bg-bs px-4 font-sans selection:bg-primary selection:text-white">
    <div class="text-center bg-bp p-10 rounded-3xl shadow-xl border border-gray-200 max-w-md relative overflow-hidden" data-aos="zoom-in">
      <!-- Glow effect -->
      <div class="absolute -top-24 -left-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>

      <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center text-primary mx-auto mb-6 animate-bounce relative z-10">
        <i class="ri-checkbox-circle-line text-4xl"></i>
      </div>
      <h2 class="text-3xl font-black mb-3 text-tp relative z-10">Success!</h2>
      <p class="text-ts mb-6 relative z-10">
        Your password has been reset successfully. Redirecting to login...
      </p>
      <a href="<?= url('/auth/login') ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary to-secondary text-white font-bold rounded-xl hover:shadow-xl transition-all relative z-10">
        Go to Login <i class="ri-arrow-right-line"></i>
      </a>
    </div>
    <script>
      setTimeout(() => window.location.href = '<?= url('/auth/login') ?>', 3000)
    </script>
  </div>
<?php
}
?>