<?php
// Alert first (requires session)
set_alert('warning', "You've been logged out!");

// Destroy session *after* saving alert
$session_alert = $_SESSION['alert']; // backup alert
$_SESSION = [];
session_unset();
session_destroy();

// Remove cookies
if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
setcookie('loggedin', '', time() - 3600, '/');
// Start fresh session for alert
session_start();
$_SESSION['alert'] = $session_alert;
redirect('/auth/login');
exit;
