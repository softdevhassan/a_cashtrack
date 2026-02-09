<?php
// Ensure CSRF token exists
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['LoginTrigger'])) {
  if (!isset($_POST['csrf_token']) || decryptData($_POST['csrf_token']) !== decryptData($_SESSION['csrf_token'])) {
    set_alert('error', 'Invalid CSRF token');
    redirect('/auth/login');
    exit;
  }

  $username = e($_POST['username']);
  $password = $_POST['password'];
  $language = $_POST['language'] ?? 'en';
  $redirect = '/auth/login';

  // Default task
  $task = [
    'alert_type' => 'error',
    'alert_msg'  => 'System Error',
    'next'       => $redirect
  ];

  // Fetch user
  $checkDB = fetch('users', ['username' => $username], 'one');

  if (!$checkDB) {
    $task = [
      'alert_type' => 'error',
      'alert_msg'  => 'Username not found',
      'next'       => $redirect
    ];
  } elseif (!password_verify($password, $checkDB['password'])) {
    if (!isset($_SESSION['failed_logins'])) $_SESSION['failed_logins'] = 0;
    $_SESSION['failed_logins']++;
    if ($_SESSION['failed_logins'] > 5) {
      sleep(5);
    }
    $task = [
      'alert_type' => 'error',
      'alert_msg'  => 'Incorrect password',
      'next'       => $redirect
    ];
  } elseif (!empty($checkDB['locked']) && $checkDB['locked'] == 1) {
    $task = [
      'alert_type' => 'error',
      'alert_msg'  => 'Your account is locked',
      'next'       => $redirect
    ];
  } else {
    session_regenerate_id(true);
    $currentSessionId = session_id();

    // Save session info to DB
    update('users', ['session_id' => $currentSessionId], ['id' => $checkDB['id']]);

    // Fetch institute info
    $_SESSION['user'] = recursiveJson($checkDB, 'jd');
    $_SESSION['user']['session_id'] = $currentSessionId; // store session_id in session too

    // Cookie expiration logic
    $time = !empty($_POST['remember']) ? time() + (86400 * 7) : time() + 86400;

    // Set secure cookie for session tracking
    setcookie('loggedin', encryptData($currentSessionId), [
      'expires' => $time,
      'path' => '/',
      'secure' => isset($_SERVER['HTTPS']), // Only send over HTTPS
      'httponly' => true,                   // Prevent JavaScript access
      'samesite' => 'Lax'                // Mitigate CSRF via cookies
    ]);

    // Reset failed login attempts
    unset($_SESSION['failed_logins']);

    $task = [
      'alert_type' => 'success',
      'alert_msg'  => 'Welcome back, @' . htmlspecialchars($checkDB['username']),
      'next'       => '/pages/dashboard'
    ];
  }


  // ✅ Final unified action executor
  set_alert($task['alert_type'], $task['alert_msg']);
  redirect($task['next']);
  exit;
}
?>

<!-- Full Screen Modern Login -->
<div class="h-screen w-full bg-bp text-tp flex overflow-hidden font-sans selection:bg-primary selection:text-white">

  <!-- Left Side: Branding (Desktop) -->
  <div class="hidden lg:flex w-1/2 bg-bs relative flex-col justify-between p-12 border-r border-gray-200">
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/10 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute top-1/2 -right-24 w-64 h-64 bg-secondary/10 rounded-full blur-3xl animate-pulse"></div>
    </div>

    <?php
    $currencyConfig = $_ENV['CURRENCY'] ?? 'PKR, post';
    $currencySymbol = trim(explode(',', $currencyConfig)[0]);
    ?>
    <div class="relative z-10">
      <a href="<?= url('/') ?>" class="flex items-center gap-3 mb-12">
        <img src="<?= asset('images/logo-full-color.webp') ?>" alt="CashTrack" class="h-10 w-auto">
      </a>

      <div class="mt-8 space-y-8">
        <div class="space-y-4">
          <h1 class="text-5xl font-black text-tp leading-tight">
            Master Your <br />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Cash Flow</span>
          </h1>
          <p class="text-xl text-ts max-w-md leading-relaxed">
            Track expenses, manage multiple wallets, and gain insights into your spending habits—all in one place.
          </p>
        </div>

        <!-- Glass Card - Mini Dashboard Preview -->
        <div class="w-full max-w-sm bg-white/50 backdrop-blur-xl border border-white/60 p-6 rounded-2xl shadow-xl transform hover:scale-[1.02] transition-transform duration-500">
          <div class="flex items-center justify-between mb-6">
            <div>
              <p class="text-xs text-ts font-bold uppercase tracking-wider mb-1">Total Balance</p>
              <p class="text-3xl font-black text-tp"><?= $currencySymbol ?> 12,450.00</p>
            </div>
            <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary/20">
              <i class="ri-wallet-3-line text-xl"></i>
            </div>
          </div>

          <div class="space-y-4">
            <div class="bg-white/60 rounded-xl p-3 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                  <i class="ri-arrow-up-line text-lg"></i>
                </div>
                <div>
                  <span class="block text-xs font-bold text-tp">Income</span>
                  <span class="block text-[10px] text-ts">Last 30 days</span>
                </div>
              </div>
              <span class="font-bold text-green-600">+<?= $currencySymbol ?> 2,500.00</span>
            </div>

            <div class="bg-white/60 rounded-xl p-3 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-red-600">
                  <i class="ri-arrow-down-line text-lg"></i>
                </div>
                <div>
                  <span class="block text-xs font-bold text-tp">Expense</span>
                  <span class="block text-[10px] text-ts">Last 30 days</span>
                </div>
              </div>
              <span class="font-bold text-red-600">-<?= $currencySymbol ?> 850.00</span>
            </div>
          </div>
        </div>
      </div>

      <form id="resetForm" action="<?= url('/auth/password-reset'); ?>" method="POST" class="hidden">
        <input type="hidden" name="request_reset" value="1">
        <input type="hidden" name="username" id="forget_username">
      </form>
    </div>

    <div class="relative z-10 mt-12">
      <p class="text-sm text-ts">© <?= date('Y') ?> CashTrack. All rights reserved.</p>
    </div>
  </div>

  <!-- Right Side: Login Form -->
  <div class="w-full lg:w-1/2 bg-bp flex items-center justify-center p-8 relative overflow-y-auto">
    <!-- Mobile Background Elements -->
    <div class="absolute inset-0 overflow-hidden lg:hidden pointer-events-none">
      <div class="absolute -top-24 -right-24 w-72 h-72 bg-primary/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-0 left-0 w-64 h-64 bg-secondary/10 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-md w-full relative z-10">
      <div class="text-center mb-8 lg:text-left">
        <div class="lg:hidden inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-xl shadow-lg shadow-primary/20 mb-6">
          <i class="ri-wallet-3-fill text-2xl text-white"></i>
        </div>
        <h2 class="text-3xl font-black text-tp mb-2">Welcome Back</h2>
        <p class="text-ts">Enter your credentials to access your account.</p>
      </div>

      <?php show_alert(); ?>

      <form action="<?= url('/auth/login') ?>" method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <!-- Username -->
        <div>
          <label class="block text-sm font-medium text-ts mb-2">Username</label>
          <div class="relative">
            <span class="absolute left-4 top-3.5 text-ts pointer-events-none">
              <i class="ri-user-line text-lg"></i>
            </span>
            <input type="text" name="username" id="username" required
              class="w-full pl-12 pr-4 py-3 bg-bs border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary text-tp placeholder-gray-400 focus:outline-none transition-all duration-200"
              placeholder="Enter your username">
          </div>
        </div>

        <!-- Password -->
        <div>
          <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-medium text-ts">Password</label>
            <button type="button" id="ForgetTrigger" class="text-sm font-medium text-primary hover:text-secondary transition-colors">
              Forgot Password?
            </button>
          </div>
          <div class="relative">
            <span class="absolute left-4 top-3.5 text-ts pointer-events-none">
              <i class="ri-lock-password-line text-lg"></i>
            </span>
            <input type="password" name="password" required
              class="w-full pl-12 pr-4 py-3 bg-bs border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary text-tp placeholder-gray-400 focus:outline-none transition-all duration-200"
              placeholder="••••••••">
          </div>
        </div>

        <!-- Submit -->
        <button type="submit" name="LoginTrigger"
          class="w-full bg-gradient-to-r from-primary to-secondary hover:from-primary hover:to-secondary text-white font-bold py-3.5 rounded-xl shadow-lg shadow-primary/20 transform transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]">
          Sign In
        </button>

        <!-- Register Link -->
        <div class="text-center mt-6">
          <p class="text-ts">Don't have an account?
            <a href="<?= url('/auth/signup') ?>" class="text-primary hover:text-secondary font-bold transition-colors">Create one now</a>
          </p>
        </div>
      </form>
    </div>
  </div>
</div>

<?php function page_sc()
{ ?>

  <script>
    $(document).ready(function() {

      // Sync username to hidden reset form
      $('#username').on('keyup', function() {
        $('#forget_username').val($(this).val());
      });

      // Handle Forgot Password Click
      $('#ForgetTrigger').on('click', function(e) {
        const uname = $('#forget_username').val();
        if (uname.length === 0) {
          // Just alert in console or simple alert if no custom alert function
          alert('Username is required to reset password!');
          return false;
        } else {
          // Submit reset form to password-reset.php
          $('#resetForm').submit();
        }
      });
    });
  </script>
<?php } ?>