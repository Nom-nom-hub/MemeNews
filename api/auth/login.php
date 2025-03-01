<?php
session_start();
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    $_SESSION['error'] = "Invalid request. Please try again.";
    header("Location: " . SITE_URL . "?page=login");
    exit;
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? (bool)$_POST['remember'] : false;
    
    // Validate input
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Please enter both username/email and password.";
        header("Location: " . SITE_URL . "?page=login");
        exit;
    }
    
    // Check if username is an email
    $is_email = filter_var($username, FILTER_VALIDATE_EMAIL);
    
    // Prepare query based on whether username is email or not
    if ($is_email) {
        $sql = "SELECT * FROM users WHERE email = ?";
    } else {
        $sql = "SELECT * FROM users WHERE username = ?";
    }
    
    // Execute query
    $user = $db->fetchOne($sql, [$username]);
    
    // If user not found or password doesn't match
    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = "Invalid username/email or password.";
        header("Location: " . SITE_URL . "?page=login");
        exit;
    }
    
    // User authenticated, set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['last_activity'] = time();
    
    // If remember me is checked, set cookie
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $expires = time() + (30 * 24 * 60 * 60); // 30 days
        
        // Store token in database
        $sql = "DELETE FROM user_tokens WHERE user_id = ?";
        $db->query($sql, [$user['id']]);
        
        $sql = "INSERT INTO user_tokens (user_id, token, expires) VALUES (?, ?, ?)";
        $db->query($sql, [$user['id'], password_hash($token, PASSWORD_DEFAULT), date('Y-m-d H:i:s', $expires)]);
        
        // Set cookie
        setcookie('remember_token', $token, $expires, '/', '', false, true);
        setcookie('remember_user', $user['id'], $expires, '/', '', false, true);
    }
    
    // Log activity
    logActivity($user['id'], 'login', 'User logged in');
    
    // Redirect to home page
    header("Location: " . SITE_URL);
    exit;
} else {
    // If not POST request, redirect to login page
    header("Location: " . SITE_URL . "?page=login");
    exit;
}
?> 