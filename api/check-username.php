<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo je(['status' => 'error', 'message' => 'Invalid method']);
    exit;
}

$username = trim($_POST['username'] ?? '');

if (empty($username)) {
    echo je(['status' => 'error', 'message' => 'Username is required']);
    exit;
}

if (strlen($username) < 3) {
    echo je(['status' => 'error', 'message' => 'Min 3 characters']);
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    echo je(['status' => 'error', 'message' => 'Invalid characters']);
    exit;
}

$exists = fetch('users', ['username' => $username], 'one');

if ($exists) {
    echo je(['status' => 'taken']);
} else {
    echo je(['status' => 'available']);
}
