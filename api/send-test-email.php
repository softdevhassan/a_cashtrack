<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo je(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

$input = jd(file_get_contents('php://input'), true);
$email = trim(e($input['email'] ?? $_POST['email'] ?? ''));
$subject = trim(e($input['subject'] ?? $_POST['subject'] ?? 'Test Email from CashTrack'));
$body = $input['body'] ?? $_POST['body'] ?? '<p>This is a test email to verify your sending configuration.</p>';

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo je(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

try {
    $result = send_email($email, $subject, $body);

    if ($result['status'] === true) {
        echo je(['success' => true, 'message' => $result['msg']]);
    } else {
        echo je(['success' => false, 'error' => $result['msg']]);
    }
} catch (Exception $e) {
    echo je(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
}
exit;
