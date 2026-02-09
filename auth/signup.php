<?php
// Ensure CSRF token exists
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if plan_id is provided in URL
$plan_id = isset($_GET['planid']) ? intval($_GET['planid']) : null;

// Fetch plan details if plan_id is provided, otherwise default to Free/Trial
$selected_plan = null;
if ($plan_id) {
  $selected_plan = fetch('plans', ['id' => $plan_id, 'status' => 'active'], 'one');
}

// Default to free plan if none selected or invalid
if (!$selected_plan) {
  // Try to get trial plan first
  $selected_plan = fetch('plans', ['is_trial' => 1, 'status' => 'active'], 'one');
  if (!$selected_plan) {
    // Fallback to cheapest plan
    $all_plans = fetch('plans', ['status' => 'active'], 'all', [], 'ORDER BY price ASC LIMIT 1');
    $selected_plan = $all_plans[0] ?? null;
  }
  if ($selected_plan) {
    $plan_id = $selected_plan['id'];
  }
}

// Handle signup form submission
if (isset($_POST['SignupTrigger'])) {
  // CSRF validation
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    set_alert('error', 'Security token expired. Please try again.');
    redirect('/auth/signup' . ($plan_id ? '?planid=' . $plan_id : ''));
    exit;
  }

  // Collect form data
  $username = trim(e($_POST['username']));
  $email = trim(e($_POST['email']));
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $full_name = trim(e($_POST['full_name'] ?? ''));
  $agree_terms = isset($_POST['agree_terms']);
  $selected_plan_id = isset($_POST['plan_id']) ? intval($_POST['plan_id']) : null;

  // Validation
  $errors = [];

  // Username validation
  if (empty($username)) {
    $errors[] = 'Username is required';
  } elseif (strlen($username) < 3) {
    $errors[] = 'Username must be at least 3 characters';
  } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $errors[] = 'Username can only contain letters, numbers, and underscores';
  } elseif (fetch('users', ['username' => $username], 'one')) {
    $errors[] = 'Username already exists';
  }

  // Email validation
  if (empty($email)) {
    $errors[] = 'Email is required';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
  } elseif (fetch('users', ['email' => $email], 'one')) {
    $errors[] = 'Email already registered';
  }

  // Password validation
  if (empty($password)) {
    $errors[] = 'Password is required';
  } elseif (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters';
  } elseif ($password !== $confirm_password) {
    $errors[] = 'Passwords do not match';
  }

  // Terms agreement
  if (!$agree_terms) {
    $errors[] = 'You must agree to the terms and conditions';
  }

  // Plan validation
  if ($selected_plan_id) {
    $plan_check = fetch('plans', ['id' => $selected_plan_id, 'status' => 'active'], 'one');
    if (!$plan_check) {
      $errors[] = 'Selected plan is not available';
    }
  }

  // If no errors, create user
  if (empty($errors)) {
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Generate verification token
    $verification_token = bin2hex(random_bytes(32));

    // Prepare user data
    $user_data = [
      'name' => $full_name,
      'email' => $email,
      'username' => $username,
      'password' => $hashed_password,
      'phone' => null,
      'details' => je([
        'company' => '',
        'verification_token' => $verification_token
      ]),
      'role_id' => 2,
      'locked' => 0,
      'session_id' => session_id(),
      'last_updated_by' => 0,
      'email_verified_at' => null,
      'plan_id' => $selected_plan_id,
      'image' => '/images/default-avatar.webp'
    ];

    // Insert user
    $user_id = insert('users', $user_data);

    if ($user_id) {
      // Create subscription if plan selected
      if ($selected_plan_id) {
        $plan_details = fetch('plans', ['id' => $selected_plan_id], 'one');
        $is_free_or_trial = ($plan_details['price'] <= 0);
        $status = $is_free_or_trial ? 'active' : 'pending';
        $duration = $plan_details['duration_days'] ?? 30;

        $subscription_data = [
          'user_id' => $user_id,
          'plan_id' => $selected_plan_id,
          'status' => $status,
          'starts_at' => date('Y-m-d H:i:s'),
          'ends_at' => date('Y-m-d H:i:s', strtotime("+$duration days")),
          'created_at' => date('Y-m-d H:i:s')
        ];
        insert('subscriptions', $subscription_data);
      }

      // Auto-login user
      session_regenerate_id(true);
      $currentSessionId = session_id();

      // Fetch full user data to populate session correctly
      $logged_in_user = fetch('users', ['id' => $user_id], 'one');
      $_SESSION['user'] = recursiveJson($logged_in_user, 'jd');
      $_SESSION['user']['session_id'] = $currentSessionId;

      update('users', [
        'session_id' => $currentSessionId,
        'last_login_at' => date('Y-m-d H:i:s')
      ], ['id' => $user_id]);

      // Redirect based on plan
      if ($selected_plan_id && !$is_free_or_trial) {
        redirect('/payment/checkout?plan_id=' . $selected_plan_id);
        exit;
      }

      set_alert('success', 'Account created successfully! Welcome aboard.');
      redirect('/pages/dashboard');
      exit;
    } else {
      set_alert('error', 'Failed to create account. Please try again.');
    }
  } else {
    foreach ($errors as $error) {
      set_alert('error', $error);
    }
  }
}
?>

<div class="h-screen w-full bg-bp text-tp flex overflow-hidden font-sans selection:bg-primary selection:text-white">

  <!-- Left Side - Plan Details (Desktop only) -->
  <div class="hidden lg:flex w-1/3 bg-bs relative flex-col justify-between p-12 border-r border-gray-200">
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/10 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute top-1/2 -right-24 w-64 h-64 bg-secondary/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>
    </div>

    <div class="relative z-10">
      <a href="<?= url('/') ?>" class="flex items-center gap-3 mb-12">
        <img src="<?= asset('images/logo-full-color.webp') ?>" alt="CashTrack" class="h-10 w-auto">
      </a>

      <?php if ($selected_plan): ?>
        <div class="bg-white/50 backdrop-blur-xl rounded-2xl p-8 border border-white/50 shadow-xl">
          <div class="inline-flex px-3 py-1 rounded-full bg-primary/10 text-primary border border-primary/20 text-xs font-bold uppercase tracking-wider mb-6">
            Selected Plan
          </div>

          <h2 class="text-3xl font-bold mb-2 text-tp"><?= e($selected_plan['title']) ?></h2>
          <div class="flex items-baseline gap-2 mb-8">
            <?php if ($selected_plan['price'] <= 0): ?>
              <span class="text-5xl font-black text-tp">FREE</span>
            <?php else: ?>
              <span class="text-5xl font-black text-tp"><?= formatCurrency($selected_plan['price']) ?></span>
              <span class="text-ts text-lg">one-time</span>
            <?php endif; ?>
          </div>

          <div class="space-y-4">
            <div class="flex items-center gap-4 text-ts">
              <div class="w-10 h-10 rounded-lg bg-primary/5 flex items-center justify-center flex-shrink-0">
                <i class="ri-wallet-line text-primary"></i>
              </div>
              <span class="font-medium"><?= $selected_plan['wallet_limit'] ?> Wallets</span>
            </div>
            <div class="flex items-center gap-4 text-ts">
              <div class="w-10 h-10 rounded-lg bg-primary/5 flex items-center justify-center flex-shrink-0">
                <i class="ri-exchange-line text-primary"></i>
              </div>
              <span class="font-medium"><?= number_format($selected_plan['transaction_limit']) ?> Transactions</span>
            </div>
            <div class="flex items-center gap-4 text-ts">
              <div class="w-10 h-10 rounded-lg bg-primary/5 flex items-center justify-center flex-shrink-0">
                <i class="ri-check-double-line text-primary"></i>
              </div>
              <span class="font-medium">Premium Features</span>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="relative z-10 text-ts text-sm">
      <p>&copy; <?= date('Y') ?> CashTrack. Secure & Encrypted.</p>
    </div>
  </div>

  <!-- Right Side - Warning/Form -->
  <div class="w-full lg:w-2/3 h-full overflow-y-auto bg-bp scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent">
    <div class="min-h-full flex flex-col justify-center max-w-2xl mx-auto p-6 lg:p-10">

      <!-- Mobile Header -->
      <div class="lg:hidden mb-6 flex items-center justify-between">
        <a href="<?= url('/') ?>" class="flex items-center gap-2">
          <img src="<?= asset('images/logo-full-color.webp') ?>" alt="CashTrack" class="h-8 w-auto">
        </a>
        <a href="<?= url('/auth/login') ?>" class="text-sm font-medium text-primary hover:text-secondary">Login</a>
      </div>

      <div class="mb-6">
        <h1 class="text-3xl lg:text-3xl font-black mb-2 text-tp">Create Account</h1>
        <p class="text-ts text-sm">Join thousands of users tracking their expenses smarter.</p>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-3">
          <?php foreach ($errors as $error): ?>
            <div class="flex items-center gap-2 text-red-600 text-xs mb-1 last:mb-0">
              <i class="ri-error-warning-fill"></i>
              <?= $error ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" id="signupForm" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="plan_id" value="<?= $plan_id ?>">
        <input type="hidden" name="SignupTrigger" value="1">

        <!-- Full Name -->
        <div>
          <label class="block text-xs font-medium text-ts mb-1 ml-1">Full Name</label>
          <input type="text" name="full_name" required placeholder="John Doe"
            class="w-full px-3 py-2.5 rounded-lg bg-bs border border-gray-200 text-tp text-sm placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none focus:outline-none transition-all">
        </div>

        <!-- Username - Live Check -->
        <div>
          <label class="block text-xs font-medium text-ts mb-1 ml-1">Username</label>
          <div class="relative">
            <input type="text" name="username" id="username" required placeholder="johndoe" pattern="[a-zA-Z0-9_]+"
              class="w-full px-3 py-2.5 rounded-lg bg-bs border border-gray-200 text-tp text-sm placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none focus:outline-none transition-all">
            <div id="usernameIndicator" class="absolute right-3 top-1/2 -translate-y-1/2 text-sm font-medium hidden">
              <!-- JS will populate -->
            </div>
          </div>
          <p id="usernameMessage" class="mt-0.5 ml-1 text-xs h-3"></p>
        </div>

        <!-- Email -->
        <div>
          <label class="block text-xs font-medium text-ts mb-1 ml-1">Email Address</label>
          <input type="email" name="email" required placeholder="john@example.com"
            class="w-full px-3 py-2.5 rounded-lg bg-bs border border-gray-200 text-tp text-sm placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none focus:outline-none transition-all">
        </div>

        <!-- Password Logic -->
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-medium text-ts mb-1 ml-1">Password</label>
            <input type="password" name="password" id="password" required placeholder="Min 8 chars"
              class="w-full px-3 py-2.5 rounded-lg bg-bs border border-gray-200 text-tp text-sm placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none focus:outline-none transition-all">
          </div>
          <div>
            <label class="block text-xs font-medium text-ts mb-1 ml-1">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required placeholder="Re-enter password"
              class="w-full px-3 py-2.5 rounded-lg bg-bs border border-gray-200 text-tp text-sm placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none focus:outline-none transition-all">
          </div>
        </div>

        <!-- Live Password Feedback -->
        <div id="passwordFeedback" class="grid grid-cols-2 gap-x-2 gap-y-1 text-xs text-ts px-1 transition-all duration-300 max-h-0 overflow-hidden opacity-0">
          <div id="checkLen" class="flex items-center gap-1"><i class="ri-checkbox-circle-fill"></i> 8+ Characters</div>
          <div id="checkNum" class="flex items-center gap-1"><i class="ri-checkbox-circle-fill"></i> Number (0-9)</div>
          <div id="checkSym" class="flex items-center gap-1"><i class="ri-checkbox-circle-fill"></i> Symbol (!@#$%)</div>
          <div id="checkMatch" class="flex items-center gap-1"><i class="ri-checkbox-circle-fill"></i> Passwords Match</div>
        </div>

        <!-- Terms -->
        <div class="flex items-start gap-2 mt-1">
          <input type="checkbox" id="agree_terms" name="agree_terms" required
            class="mt-0.5 w-4 h-4 bg-bs border-gray-300 rounded text-primary focus:ring-primary cursor-pointer">
          <label for="agree_terms" class="text-xs text-ts cursor-pointer select-none">
            I agree to the <a href="<?= url('/pages/terms-of-service') ?>" class="text-primary hover:text-secondary hover:underline">Terms of Service</a> and <a href="<?= url('/pages/privacy-policy') ?>" class="text-primary hover:text-secondary hover:underline">Privacy Policy</a>
          </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" id="submitBtn" disabled
          class="w-full py-3 text-sm font-bold rounded-xl text-white bg-gradient-to-r from-primary to-secondary shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:scale-[1.01] transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none mt-2">
          Create Account
        </button>

        <p class="text-center text-ts text-xs mt-3">
          Already have an account? <a href="<?= url('/auth/login') ?>" class="text-primary font-bold hover:underline">Login here</a>
        </p>
      </form>
    </div>
  </div>
</div>

<?php function page_sc()
{ ?>
  <script>
    $(document).ready(function() {
      const $usernameInput = $('#username');
      const $usernameMsg = $('#usernameMessage');
      const $usernameInd = $('#usernameIndicator');
      let usernameTimeout;

      // Username Live Check
      $usernameInput.on('input', function() {
        clearTimeout(usernameTimeout);
        const val = $(this).val().trim();

        if (val.length < 3) {
          $usernameMsg.text('Min 3 chars').removeClass('text-primary text-red-600').addClass('text-ts');
          $usernameInd.addClass('hidden');
          return;
        }

        $usernameMsg.text('Checking...').removeClass('text-primary text-red-600').addClass('text-ts');

        usernameTimeout = setTimeout(() => {
          $.post('<?= url("/api/check-username.php") ?>', {
            username: val
          }, function(data) {
            // Handle response (assuming API returns JSON automatically or needs parsing)
            const res = (typeof data === 'string') ? JSON.parse(data) : data;

            $usernameInd.removeClass('hidden');
            if (res.status === 'available') {
              $usernameMsg.text('Username available!').removeClass('text-ts text-red-600').addClass('text-primary');
              $usernameInd.html('<i class="ri-checkbox-circle-fill text-primary text-xl"></i>');
              $usernameInput.removeClass('border-red-500 focus:border-red-500 focus:ring-red-500').addClass('border-primary focus:border-primary focus:ring-primary/20');
            } else {
              $usernameMsg.text('Username taken').removeClass('text-ts text-primary').addClass('text-red-600');
              $usernameInd.html('<i class="ri-close-circle-fill text-red-600 text-xl"></i>');
              $usernameInput.removeClass('border-primary focus:border-primary focus:ring-primary/20').addClass('border-red-500 focus:border-red-500 focus:ring-red-500');
            }
          });
        }, 500);
      });

      // Password Validation
      const $passInput = $('#password');
      const $confirmInput = $('#confirm_password');
      const $feedback = $('#passwordFeedback');
      const checks = {
        len: $('#checkLen'),
        num: $('#checkNum'),
        sym: $('#checkSym'),
        match: $('#checkMatch')
      };

      function validatePass() {
        const val = $passInput.val();
        const confirmVal = $confirmInput.val();

        if (val.length > 0) {
          $feedback.removeClass('max-h-0 opacity-0').addClass('max-h-20 opacity-100');
        }

        const hasLen = val.length >= 8;
        const hasNum = /\d/.test(val);
        const hasSym = /[!@#$%^&*(),.?":{}|<>]/.test(val);
        const matches = val === confirmVal && val.length > 0;

        updateCheck(checks.len, hasLen);
        updateCheck(checks.num, hasNum);
        updateCheck(checks.sym, hasSym);
        updateCheck(checks.match, matches);

        return hasLen && hasNum && hasSym && matches;
      }

      function updateCheck($el, valid) {
        if (valid) {
          $el.removeClass('text-ts').addClass('text-primary font-medium');
          $el.find('i').attr('class', 'ri-checkbox-circle-fill');
        } else {
          $el.addClass('text-ts').removeClass('text-primary font-medium');
          $el.find('i').attr('class', 'ri-checkbox-blank-circle-line');
        }
      }

      $passInput.on('input', validatePass);
      $confirmInput.on('input', validatePass);

      // Enable/Disable Submit
      $('#agree_terms').on('change', function() {
        $('#submitBtn').prop('disabled', !this.checked);
      });
    });
  </script>
<?php } ?>