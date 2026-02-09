<?php

require __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('Asia/Karachi');

if (!function_exists('loadEnv')) {
    function loadEnv($path = null)
    {
        $path = $path ?? __DIR__ . '/../.env';
        if (!file_exists($path)) return false;
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) return false;

        foreach ($lines as $line) {
            if (strpos(ltrim($line), '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            if (!empty($name)) $_ENV[$name] = $value;
        }
        return true;
    }
}

// Ensure ENV is loaded if accessed directly
if (empty($_ENV['DB_SERVER'])) {
    loadEnv(__DIR__ . '/../.env');
}

$conn = mysqli_connect($_ENV['DB_SERVER'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);
if (!$conn) {
    echo "<strong><h5>!ERROR: </h5> While Connecting to DB;</strong>";
    exit;
}

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
$root_path = $_ENV['ROOT_PATH'] ?? '';
$_APP = [
    'APP_NAME' => $_ENV['APP_NAME'] ?? 'DGT',
    'PROTOCOL' => $protocol,
    'DOMAIN' => $domain,
    'ROOT_PATH' => $root_path,
    'ROOT_URL' => $protocol . $domain . $root_path,
    'PAGE_ROOT' => $protocol . $domain . $root_path . '/pages',
    'VIEW_ROOT' => $protocol . $domain . $root_path . '/views',
    'PRINT_ROOT' => $protocol . $domain . $root_path . '/prints',
    'ASSETS_ROOT' => $protocol . $domain . $root_path . '/assets',
    'OPEN_ROUTES' => explode(', ', $_ENV['OPEN_ROUTES']),
    'user' => $_SESSION['user'] ?? []
];
function generate_token()
{
    return bin2hex(random_bytes(16));
}
function isSuperUser()
{
    global $_APP, $_ENV;
    if (!isset($_APP['user']['id'])) return false;

    // Check role_id
    if (isset($_APP['user']['role_id']) && $_APP['user']['role_id'] == 1) return true;

    // Check env list
    if (isset($_APP['user']['username'])) {
        $superUsers = array_map('trim', explode(',', $_ENV['SUPER_USERS'] ?? ''));
        if (in_array($_APP['user']['username'], $superUsers)) return true;
    }
    return false;
}
$isSuperUser = isSuperUser();

function send_email($filename_or_email, $data_or_subject, $body = null)
{
    global $_ENV;

    // Determine if arguments are (filename, data) [Template Mode] or (email, subject, body) [Direct Mode]
    $is_template_mode = is_array($data_or_subject);

    $to = '';
    $subject = '';
    $messageBody = '';
    $placeholders = [];

    if ($is_template_mode) {
        // Template Mode: send_email('template_name', ['to' => '...', 'subject' => '...', 'placeholders' => ...])
        $filename = $filename_or_email;
        $data = $data_or_subject;

        $templatePath = __DIR__ . "/../templates/{$filename}.html";
        if (!file_exists($templatePath)) {
            return [
                'status' => false,
                'msg' => "Email template '{$filename}.html' not found!"
            ];
        }

        $template = file_get_contents($templatePath);
        $to = $data['to'] ?? null;
        $subject = $data['subject'] ?? 'No Subject';
        $placeholders = $data['placeholders'] ?? [];

        // Add default logos if not present
        if (!isset($placeholders['logo_white'])) $placeholders['logo_white'] = asset($_ENV['APP_PRIMARY_LOGO_WHITE'] ?? '');
        if (!isset($placeholders['logo_transparent'])) $placeholders['logo_transparent'] = asset($_ENV['APP_PRIMARY_LOGO_TRANSPARENT'] ?? '');

        // Replace placeholders
        foreach ($placeholders as $key => $value) {
            $template = str_replace("{{{$key}}}", $value, $template);
        }
        $messageBody = $template;
    } else {
        // Direct Mode: send_email('email@example.com', 'Subject', 'Body Content')
        $to = $filename_or_email;
        $subject = $data_or_subject;
        $messageBody = $body;
    }

    $response = [
        'status' => false,
        'msg' => 'Email not sent'
    ];

    if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $response['msg'] = 'Recipient email address is missing or invalid.';
        return $response;
    }

    $fromName = $_ENV['APP_NAME'] ?? 'CashTrack';
    $user_from_email = $_ENV['FROM_EMAIL'] ?? 'noreply@cashtrack.pk';


    // --- PHPMailer Setup ---
    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'] ?? '';
        $mail->Password = $_ENV['SMTP_PASSWORD'] ?? '';
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = (int)($_ENV['SMTP_PORT'] ?? 587);

        // From
        $mail->setFrom(
            $_ENV['SMTP_FROM_EMAIL'] ?? 'noreply@cashtrack.pk',
            $fromName ?? ($_ENV['SMTP_FROM_NAME'] ?? 'CashTrack')
        );

        // Recipients
        if (is_array($to)) {
            foreach ($to as $email) {
                $mail->addAddress($email);
            }
        } else {
            $mail->addAddress($to);
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $messageBody;
        $mail->AltBody = strip_tags($messageBody);

        $mail->send();

        $response = [
            'status' => true,
            'msg' => 'Email sent successfully via SMTP to ' . (is_array($to) ? implode(', ', $to) : $to)
        ];
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        $response = [
            'status' => false,
            'msg' => 'Failed to send email: ' . $mail->ErrorInfo
        ];
        error_log("Email sending failed to " . (is_array($to) ? implode(', ', $to) : $to) . ": " . $mail->ErrorInfo);
    }
    return $response;
}


function uploadFileAndReplace($field, $file_for, $uid, $old_file_id)
{
    global $uploadDir, $conn;

    if (empty($_FILES[$field]['name'])) return $old_file_id;

    // Delete old file safely
    if ($old_file_id) {
        // Temporarily set logo_id or signature_id to NULL before delete
        $conn->query("UPDATE institutes 
                      SET logo_id = NULL 
                      WHERE logo_id = " . intval($old_file_id));
        $conn->query("UPDATE institutes 
                      SET signature_id = NULL 
                      WHERE signature_id = " . intval($old_file_id));

        $old = fetch('files', ['id' => $old_file_id], 'one');
        if ($old && file_exists(__DIR__ . '/..' . $old['path'])) {
            unlink(__DIR__ . '/..' . $old['path']);
        }
        delete('files', ['id' => $old_file_id]);
    }

    $fileName = $_FILES[$field]['name'];
    $tmp = $_FILES[$field]['tmp_name'];
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    $newName = uniqid($file_for . '_') . '.' . $ext;
    $path = $uploadDir . $newName;

    if (move_uploaded_file($tmp, $path)) {
        $fileData = [
            'file_for' => $file_for,
            'name' => $fileName,
            'path' => '/uploads/' . $newName,
            'last_updated_by' => $uid,
            'last_updated_at' => date('Y-m-d H:i:s')
        ];
        return insert('files', $fileData);
    }

    return $old_file_id;
}

$path = strtok($_SERVER['REQUEST_URI'], '?');
$path_raw = str_replace($_APP['ROOT_PATH'], '', $path);
$path = explode('/', $path_raw);
array_shift($path);
$current_slug = end($path);
$required = explode(', ', $_ENV['CORE_TABLES']);
$missing  = [];
if (end($path) != 'setup') {
    foreach ($required as $table) {
        $res = $conn->query("SHOW TABLES LIKE '$table'");
        if (!$res || $res->num_rows === 0) {
            $missing[] = $table;
        }
    }
}
if (!empty($missing)) {
    redirect('/setup');
}
function genPassword($len = 8)
{
    $sets = [
        'A' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'a' => 'abcdefghijklmnopqrstuvwxyz',
        '0' => '0123456789',
        's' => '!@#$%^&*()-_=+[]{}<>?'
    ];
    $pwd = '';
    foreach ($sets as $set) $pwd .= $set[random_int(0, strlen($set) - 1)];
    $all = implode('', $sets);
    while (strlen($pwd) < $len) $pwd .= $all[random_int(0, strlen($all) - 1)];
    return str_shuffle($pwd);
}

function certCode($s, $m = 'en')
{
    return $s;
}

function docTitle()
{
    global $path, $_APP, $_ENV;
    $seperator = $_ENV['TITLE_SEPERATOR'] ?? ' | ';
    $current = ucwords(str_replace(['-', '_'], [' ', ' '], $path[1] ?? ''));
    return $current . $seperator . $_APP['APP_NAME'];
}

/*
 * SEO Meta Data Generator
 */
function getSeoMeta($slug)
{
    global $_APP, $_ENV;

    $appName = $_APP['APP_NAME'];
    $defaultTitle = docTitle(); // Fallback to existing logic
    $defaultDesc = "CashTrack is your personal finance companion. Track expenses, manage wallets, and gain insights into your spending habits effortlessly.";
    $defaultKeywords = "expense tracker, finance manager, budget planner, wallet management, personal finance";
    $defaultImage = asset($_ENV['APP_SOCIAL_IMAGE'] ?? 'images/social-share.webp');
    $url = url('/' . $slug);
    // Load SEO data from JSON
    $seoFile = __DIR__ . '/seo.json';
    $meta = [];
    if (file_exists($seoFile)) {
        $meta = json_decode(file_get_contents($seoFile), true);
        $meta['default']['image'] = asset($meta['default']['image']);
    }

    // Fallback defaults if json fails or missing
    if (empty($meta['default'])) {
        $meta['default'] = [
            'title' => $defaultTitle,
            'description' => $defaultDesc,
            'keywords' => $defaultKeywords,
            'image' => $defaultImage,
            'robots' => 'index, follow'
        ];
    }

    // Normalize slug (handle 'pages/xxx' vs 'xxx')
    $key = str_replace('pages/', '', $slug);
    if ($key === '' || $key === '/') $key = 'index';

    // Merge specific page data with defaults
    $data = isset($meta[$key]) ? array_merge($meta['default'], $meta[$key], ['image' => asset($meta[$key]['image'])]) : $meta['default'];

    // Ensure title has app name if not explicitly set in specific data (though we did above)
    // If we rely on docTitle() as default, it already includes app name.

    return (object)[
        'title' => $data['title'],
        'description' => $data['description'],
        'keywords' => $data['keywords'],
        'image' => $data['image'],
        'url' => $url,
        'robots' => $data['robots']
    ];
}

function url($to = '')
{
    global $_APP;
    return $_APP['ROOT_URL'] . $to;
}
function redirect($to)
{
    header('Location: ' . url($to));
    exit;
}

function e($string)
{
    $string = trim($string);
    return $string;
}
function db_escape($value)
{
    global $conn;
    $value = trim($value);
    return mysqli_real_escape_string($conn, $value);
}
function asset($file)
{
    global $_APP;
    return $_APP['ASSETS_ROOT'] . '/' . ltrim($file, '/');
}
function isLoggedIn()
{
    return isset($_COOKIE['loggedin']) && $_COOKIE['loggedin'] == true;
}
function encryptData($data)
{
    $key = $_ENV['ENCRYPTION_KEY']; // plain text key
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encrypted);
}

function decryptData($data)
{
    $key = $_ENV['ENCRYPTION_KEY'];
    $raw = base64_decode($data);
    $iv = substr($raw, 0, 16);
    $cipher = substr($raw, 16);
    return openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
}
function je($data)
{
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
function jd($json, $assoc = true)
{
    return json_decode($json, $assoc);
}
function query($sql, $params = [], $types = '')
{
    global $conn;
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("DB Error: " . $conn->error);
    if ($params) {
        if ($types === '') {
            foreach ($params as $p) {
                $types .= is_int($p) ? 'i' : (is_float($p) ? 'd' : 's');
            }
        }
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result !== false ? $result : $stmt->affected_rows;
}



// Function to generate challan number
function generateChallanNo(): string
{
    return 'C' . date('ymdHi');
}
function insert($table, $data)
{
    global $conn;
    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), '?'));

    $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;

    $types = str_repeat('s', count($data));
    $stmt->bind_param($types, ...array_values($data));

    if ($stmt->execute()) {
        $insert_id = $conn->insert_id; // <-- get last insert ID
        $stmt->close();
        return $insert_id; // <-- return actual ID
    } else {
        $stmt->close();
        return false;
    }
}



function update($table, $data, $where)
{
    global $conn;

    $setPart = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));

    if (empty($where)) {
        // Update every row: no WHERE clause
        $sql = "UPDATE {$table} SET {$setPart}";
        $params = array_values($data);
    } else {
        $wherePart = implode(' AND ', array_map(fn($k) => "$k = ?", array_keys($where)));
        $sql = "UPDATE {$table} SET {$setPart} WHERE {$wherePart}";
        $params = array_merge(array_values($data), array_values($where));
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;

    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);

    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function fetch($table, $where = [], $type = 'all', $columns = [], $append = '')
{
    global $_APP, $conn;

    // Columns
    $select = !empty($columns) ? implode(', ', $columns) : '*';
    // Base query
    $sql = "SELECT {$select} FROM {$table}";
    $params = [];
    $types = '';

    // Where clause
    if (!empty($where)) {
        $conditions = [];
        foreach ($where as $k => $v) {
            $conditions[] = "$k = ?";
            $params[] = $v;
            $types .= 's';
        }
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    // Append extra SQL (e.g. ORDER BY, LIMIT, etc.)
    if ($append) {
        $sql .= ' ' . ltrim($append);
    }
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($type === 'one') {
        return $result->fetch_assoc();
    } else {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}


function delete($table, $where)
{
    $whereSql = implode(' AND ', array_map(fn($k) => "$k=?", array_keys($where)));
    $sql = "DELETE FROM {$table} WHERE {$whereSql}";
    $ids = [];
    if (isset($where['id'])) {
        $ids[] = $where['id'];
    } else {
        $rows = fetch($table, $where, 'all');
        foreach ($rows as $r) if (isset($r['id'])) $ids[] = $r['id'];
    }
    query($sql, array_values($where));
    return $ids;
}

function now($format = 'mysql')
{
    $formats = [
        'mysql' => 'Y-m-d H:i:s',
        'date' => 'Y-m-d',
        'time' => 'H:i:s',
        'iso' => 'c',
        'readable' => 'D, d M Y H:i',
        'short' => 'd M Y',
        'ymd' => 'Ymd',
        'unix' => 'U'
    ];
    $fmt = $formats[$format] ?? $format;
    return date($fmt);
}
function dd($var)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}
function token32($input = null)
{
    if ($input === null) $input = bin2hex(random_bytes(16));
    return substr(hash('sha256', (string)$input . (string)($_ENV['ENCRYPTION_KEY'] ?? '')), 0, 32);
}

$menu = json_decode(file_get_contents(__DIR__ . "/navbar.json"), true);

function filterMenuByRole($menu, $role)
{
    $role = strtoupper($role);
    $envKey = "ROLE_{$role}_ROUTES";
    if (!isset($_ENV[$envKey])) {
        return [];
    }
    $allowed = trim($_ENV[$envKey]);
    // allow all routes
    if ($allowed === 'all') {
        return $menu;
    }

    // make allowed list clean
    $allowedRoutes = array_filter(array_map('trim', explode(', ', $allowed)));
    // Recursive filtering
    return filterMenuRecursive($menu, $allowedRoutes);
}

function filterMenuRecursive($menuItems, $allowedRoutes)
{
    $filtered = [];

    foreach ($menuItems as $item) {
        $keep = false;
        $slug = trim($item['url'] ?? '');
        if ($slug && in_array($slug, $allowedRoutes)) {
            $keep = true;
        }

        if (!empty($item['children'])) {
            $filteredChildren = filterMenuRecursive($item['children'], $allowedRoutes);
            if (!empty($filteredChildren)) {
                $item['children'] = $filteredChildren;
                $keep = true;
            }
        }

        if ($keep) {
            $filtered[] = $item;
        }
    }

    return $filtered;
}
// 🟩 Set an alert
function set_alert($type, $msg, $temp = 1)
{
    $_SESSION['alert'] = [
        'type' => $type,   // success | error | warning | info
        'msg'  => $msg,
        'temp' => $temp     // 1 = remove after show, 0 = persistent
    ];
}
function findOpenKeyRecursive($menuItems, $currentSlug, &$foundKey = null, $parentKey = null)
{
    foreach ($menuItems as $key => $item) {
        // Check if this menu item has a URL and matches the current slug
        if (!empty($item['url'])) {
            $slug = trim($item['url'], '/');
            $parts = explode('/', $slug);
            $last = end($parts);
            if ($last === $currentSlug) {
                $foundKey = $parentKey ?? $key;
                return true;
            }
        }

        // If it has children, search deeper recursively
        if (!empty($item['children'])) {
            if (findOpenKeyRecursive($item['children'], $currentSlug, $foundKey, $key)) {
                return true;
            }
        }
    }
    return false;
}
// 🟨 Show and optionally clear alert
function show_alert()
{
    if (!isset($_SESSION['alert'])) {
        return;
    }

    $alert = $_SESSION['alert'];
    $type  = $alert['type'] ?? 'info';
    $msg   = $alert['msg'] ?? '';
    $temp  = $alert['temp'] ?? 1;

    // Theme-based style mapping
    $styles = [
        'success' => ['icon' => 'ri-check-line', 'text' => 'green-800', 'ring' => 'green-800/50', 'bg' => 'bg-green-100'],
        'error'   => ['icon' => 'ri-close-circle-line', 'text' => 'red-800', 'ring' => 'red-800/50', 'bg' => 'bg-red-100'],
        'warning' => ['icon' => 'ri-alert-line', 'text' => 'amber-800', 'ring' => 'amber-800/50', 'bg' => 'bg-amber-100'],
        'info'    => ['icon' => 'ri-information-line', 'text' => 'bp', 'ring' => 'accent/50', 'bg' => 'bg-accent/80'],
    ];

    $icon  = $styles[$type]['icon'];
    $text  = $styles[$type]['text'];
    $bg    = $styles[$type]['bg'];
    $ring = $styles[$type]['ring'];

    echo '
    <div id="alert-box" class="ring-2 ring-' . $ring . ' ' . $bg . ' text-' . $text . ' rounded-lg p-3 mb-3 flex items-center justify-between transition-all duration-300">
        <div class="flex items-center gap-2 font-medium text-sm">
            <i class="' . $icon . ' text-lg text-' . $text . '"></i>
            <span>' . htmlspecialchars($msg) . '</span>
        </div>
        <button onclick="$(this).closest(\'#alert-box\').fadeOut(300, function(){ $(this).remove(); });" 
                class="text-xl text-' . $text . ' hover:opacity-70 transition-all duration-200">
            <i class="ri-close-line"></i>
        </button>
    </div>
    ';

    if ($temp == 1 || $temp === true) {
        unset($_SESSION['alert']);
    }
}



function recursiveJson($data, $mode = 'jd')
{
    if ($data === null || $data === '') return $data;

    if ($mode === 'jd') {
        // First, decode any JSON strings
        if (is_string($data) && isJson($data)) {
            $data = json_decode($data, true);
        }

        if (is_array($data) || is_object($data)) {
            $isObject = is_object($data);
            $newData = $isObject ? new stdClass() : [];

            foreach ($data as $key => $value) {
                // Process value recursively
                $processedValue = recursiveJson($value, 'jd');

                if ($isObject) {
                    $newData->$key = $processedValue;
                } else {
                    $newData[$key] = $processedValue;
                }
            }
            return $newData;
        }

        return $data;
    }

    // Encode mode
    elseif ($mode === 'je') {
        if (is_array($data)) {
            foreach ($data as $key => $value) $data[$key] = recursiveJson($value, 'je');
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        } elseif (is_object($data)) {
            foreach ($data as $key => $value) $data->$key = recursiveJson($value, 'je');
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            return $data;
        }
    }

    return $data;
}



// ✅ Helper function to detect valid JSON string
function isJson($string)
{
    if (!is_string($string)) return false;
    json_decode($string);
    return (json_last_error() === JSON_ERROR_NONE);
}

function formatCurrency($amount)
{
    global $_ENV;
    $currencyConfig = $_ENV['CURRENCY'] ?? 'PKR, post';
    $parts = array_map('trim', explode(',', $currencyConfig));
    $symbol = $parts[0] ?? 'PKR';
    $position = $parts[1] ?? 'post';

    // Format amount (integer formatting, no decimals for now unless specified)
    $formattedAmount = number_format((float)$amount, 0);

    if (strtolower($position) === 'pre') {
        return $symbol . ' ' . $formattedAmount;
    } else {
        return $formattedAmount . ' ' . $symbol;
    }
}
function H($title, $tagline = null, $type = 'm')
{
    // Determine heading styles
    switch ($type) {
        case 's': // section heading
            $titleClass = "text-2xl font-semibold text-slate-700 flex items-center gap-2";
            $taglineClass = "text-slate-500 text-sm mt-1";
            $iconSize = "text-2xl";
            break;

        case 'xs': // small heading
            $titleClass = "text-lg font-semibold text-slate-700 flex items-center gap-2";
            $taglineClass = "text-slate-500 text-xs mt-1";
            $iconSize = "text-lg";
            break;

        default: // main heading
            $titleClass = "text-3xl font-semibold text-slate-800 flex items-center gap-2";
            $taglineClass = "text-slate-500 text-sm mt-1";
            $iconSize = "text-3xl";
            break;
    }

    $icon = '';
    if (str_contains($title, 'dashboard')) $icon = 'ri-dashboard-line';
    elseif (str_contains($title, 'sale')) $icon = 'ri-shopping-bag-3-line';
    elseif (str_contains($title, 'purchase')) $icon = 'ri-shopping-cart-line';
    elseif (str_contains($title, 'ledger')) $icon = 'ri-bank-line';
    elseif (str_contains($title, 'user')) $icon = 'ri-user-3-line';
    else $icon = 'ri-file-text-line';

    // Render HTML
    echo '<div class="mb-8">';
    echo "<h1 class=\"$titleClass\">";
    echo "<i class=\"$icon text-primary $iconSize\"></i>";
    echo $title;
    echo "</h1>";

    if ($tagline) {
        echo "<p class=\"$taglineClass\">$tagline</p>";
    }

    echo '</div>';
}
///////////////////////////////////////
/********** ACCOUNTS FUNCTONS *******/
/////////////////////////////////////
function generateAccountCode($parent_id = null)
{
    if (empty($parent_id)) {
        $rows = query("SELECT account_code FROM accounts WHERE parent_id IS NULL OR parent_id = 0 ORDER BY id DESC LIMIT 1");
        $last = $rows && $rows->num_rows ? $rows->fetch_assoc()['account_code'] : null;
        if ($last === null) return '1';
        $num = floatval($last) + 1;
        return (string)$num;
    } else {
        $parent = fetch('accounts', ['id' => $parent_id], 'one');
        $parent_code = $parent['account_code'] ?? '0';
        $res = query("SELECT account_code FROM accounts WHERE parent_id = ? ORDER BY id DESC", [$parent_id], 'i');
        $count = $res ? $res->num_rows : 0;
        $nextIndex = $count + 1;
        return $parent_code . '.' . $nextIndex;
    }
}
function H2($title, $print_page = null, $add_url = null)
{
    global $_APP;
    echo '
        <div class="border-b pb-4 mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-tp">' . $title . '</h1>
        <div class="flex justify-evenly items-center gap-2">
            <a href="javascript:window.history.back();" class="text-sm border border-ts/30 px-3 py-1 rounded-md hover:bg-bs">
                <i class="ri-arrow-go-back-line"></i> Back
            </a>' . ($print_page ? '
            <a href="' . $_APP['ROOT_URL'] . '/prints/' . $print_page . '" id="printURL" class=" text-sm border border-ts/30 px-3 py-1 rounded-md hover:bg-bs flex items-center gap-1">
                <i class="ri-printer-line"></i> Print
            </a>' : '') . ($add_url ? '
                <a href="' . $_APP['ROOT_URL'] . '/pages/' . $add_url . '" class="px-3 py-1 border border-accent text-accent rounded hover:bg-accent hover:text-bp">
            <i class="ri-add-large-line"></i> Add New
        </a>' : '') . '
        </div>
    </div>
    ';
}
function buildTree(array $items, $parentId = null)
{
    $branch = [];
    foreach ($items as $item) {
        // parent_id in DB may be NULL or 0 or ''
        $p = $item['parent_id'] === null || $item['parent_id'] === '' ? null : intval($item['parent_id']);
        if ($p === $parentId) {
            $children = buildTree($items, intval($item['id']));
            if ($children) $item['children'] = $children;
            $branch[] = $item;
        }
    }
    return $branch;
}

// -----------------------------
// LIMIT FUNCTIONS
// -----------------------------

/**
 * Check if user can create a new wallet
 * Returns: ['allowed' => bool, 'message' => string]
 */
function can_create_wallet($user_id)
{
    // Get user's active subscription
    $subscription = fetch('subscriptions', ['user_id' => $user_id, 'status' => 'active'], 'one');

    if (!$subscription) {
        return ['allowed' => false, 'message' => 'No active subscription found. Please subscribe to a plan.'];
    }

    // Get plan details
    $plan = fetch('plans', ['id' => $subscription['plan_id']], 'one');

    if (!$plan) {
        return ['allowed' => false, 'message' => 'Plan not found.'];
    }

    // Count current wallets
    $wallet_count = count(fetch('wallets', ['user_id' => $user_id], 'all'));

    // Check limit
    if ($wallet_count >= $plan['wallet_limit']) {
        return [
            'allowed' => false,
            'message' => "Wallet limit reached! Your {$plan['title']} allows {$plan['wallet_limit']} wallets. Upgrade to add more."
        ];
    }

    return ['allowed' => true, 'message' => ''];
}

/**
 * Check if user can create a new transaction  
 * Returns: ['allowed' => bool, 'message' => string, 'usage' => int, 'limit' => int]
 */
function can_create_transaction($user_id)
{
    // Get user's active subscription
    $subscription = fetch('subscriptions', ['user_id' => $user_id, 'status' => 'active'], 'one');

    if (!$subscription) {
        return ['allowed' => false, 'message' => 'No active subscription found. Please subscribe to a plan.'];
    }

    // Get plan details
    $plan = fetch('plans', ['id' => $subscription['plan_id']], 'one');

    if (!$plan) {
        return ['allowed' => false, 'message' => 'Plan not found.'];
    }

    // Get current transaction count
    $txn_count = (int)($subscription['transaction_usage'] ?? 0);
    $limit = (int)$plan['transaction_limit'];

    // Check limit
    if ($txn_count >= $limit) {
        return [
            'allowed' => false,
            'message' => "Transaction limit reached! Your {$plan['title']} allows {$limit} transactions. Upgrade your plan to continue.",
            'usage' => $txn_count,
            'limit' => $limit
        ];
    }

    return [
        'allowed' => true,
        'message' => '',
        'usage' => $txn_count,
        'limit' => $limit
    ];
}

/**
 * Create transfer (DOUBLE-ENTRY BOOKKEEPING)
 * Creates 2 transactions: 1 debit from source, 1 credit to destination
 * 
 * @param int $user_id
 * @param int $from_wallet_id
 * @param int $to_wallet_id  
 * @param float $amount
 * @param string $note
 * @return array ['success' => bool, 'message' => string]
 */
function create_transfer($user_id, $from_wallet_id, $to_wallet_id, $amount, $note = '')
{
    global $conn;

    // Check transaction limit (transfers count as 2 transactions)
    $check = can_create_transaction($user_id);
    if (!$check['allowed']) {
        return ['success' => false, 'message' => $check['message']];
    }

    // Check if enough space for 2 transactions
    if (($check['usage'] + 2) > $check['limit']) {
        return [
            'success' => false,
            'message' => "Not enough space for transfer! Transfers require 2 transaction slots. You have " . ($check['limit'] - $check['usage']) . " remaining."
        ];
    }

    // Verify wallets belong to user
    $from_wallet = fetch('wallets', ['id' => $from_wallet_id, 'user_id' => $user_id], 'one');
    $to_wallet = fetch('wallets', ['id' => $to_wallet_id, 'user_id' => $user_id], 'one');

    if (!$from_wallet || !$to_wallet) {
        return ['success' => false, 'message' => 'Invalid wallet(s)'];
    }

    // Check sufficient balance
    if ($from_wallet['current_balance'] < $amount) {
        return ['success' => false, 'message' => 'Insufficient balance in source wallet'];
    }

    $conn->begin_transaction();

    try {
        $date = date('Y-m-d');

        // Entry 1: Debit from source wallet (expense type for accounting)
        $stmt = $conn->prepare("
            INSERT INTO transactions (user_id, type, amount, wallet_id, to_wallet_id, note, transaction_date)
            VALUES (?, 'transfer', ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('idiiis', $user_id, $amount, $from_wallet_id, $to_wallet_id, $note, $date);
        $stmt->execute();

        // Update source wallet balance
        $conn->query("UPDATE wallets SET current_balance = current_balance - $amount WHERE id = $from_wallet_id");

        // Update destination wallet balance
        $conn->query("UPDATE wallets SET current_balance = current_balance + $amount WHERE id = $to_wallet_id");

        // Increment subscription usage by 2 (double-entry)
        $conn->query("UPDATE subscriptions SET transaction_usage = transaction_usage + 2 WHERE user_id = $user_id AND status = 'active'");

        $conn->commit();

        return ['success' => true, 'message' => 'Transfer completed successfully'];
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => 'Transfer failed: ' . $e->getMessage()];
    }
}

// -----------------------------
// EMAIL HELPER FUNCTIONS
// -----------------------------

/**
 * send_password_reset_email() - Convenience function for password reset emails
 */
function send_password_reset_email($user_email, $reset_link)
{
    $subject = 'Password Reset Request - CashTrack';
    $body = "
        <h2>Password Reset Request</h2>
        <p>Hi,</p>
        <p>You requested to reset your password. Click the link below to reset it:</p>
        <p><a href=\"{$reset_link}\" style=\"padding: 10px 20px; background-color: #6366f1; color: white; text-decoration: none; border-radius: 5px;\">Reset Password</a></p>
        <p>If you didn't request this, please ignore this email.</p>
        <p>This link expires in 1 hour.</p>
        <br>
        <p>Best regards,<br>CashTrack Team</p>
    ";

    return send_email(['to' => $user_email, 'subject' => $subject], $subject, $body);
}

/**
 * send_welcome_email() - Welcome email for new users
 */
function send_welcome_email($user_email, $user_name)
{
    $subject = 'Welcome to CashTrack!';
    $body = "
        <h2>Welcome to CashTrack, {$user_name}!</h2>
        <p>Thank you for joining CashTrack. We're excited to help you manage your finances.</p>
        <h3>Getting Started:</h3>
        <ul>
            <li>Add your first wallet</li>
            <li>Record transactions</li>
            <li>Track your spending with categories</li>
            <li>View analytics and insights</li>
        </ul>
        <p><a href=\"" . ($_ENV['APP_WEBSITE'] ?? 'https://cashtrack.pk') . "\" style=\"padding: 10px 20px; background-color: #6366f1; color: white; text-decoration: none; border-radius: 5px;\">Get Started</a></p>
        <br>
        <p>Best regards,<br>CashTrack Team</p>
    ";

    return send_email(['to' => $user_email, 'subject' => $subject], $subject, $body);
}

/**
 * send_subscription_expiry_notification() - Notify users of expiring subscription
 */
function send_subscription_expiry_notification($user_email, $days_remaining, $plan_name)
{
    $subject = "Your {$plan_name} Plan Expires in {$days_remaining} Days";
    $body = "
        <h2>Subscription Expiring Soon</h2>
        <p>Your <strong>{$plan_name}</strong> subscription will expire in {$days_remaining} days.</p>
        <p>To continue enjoying unlimited features, renew your subscription now.</p>
        <p><a href=\"" . url('/pages/my-plan') . "\" style=\"padding: 10px 20px; background-color: #6366f1; color: white; text-decoration: none; border-radius: 5px;\">Renew Now</a></p>
        <br>
        <p>Best regards,<br>CashTrack Team</p>
    ";

    return send_email(['to' => $user_email, 'subject' => $subject], $subject, $body);
}
