<?php
session_start();
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Log activity if user is logged in
if (isset($_SESSION['user_id'])) {
    logActivity($_SESSION['user_id'], 'logout', 'User logged out');
    
    // Clear remember me token if exists
    if (isset($_COOKIE['remember_user']) && isset($_COOKIE['remember_token'])) {
        $user_id = $_COOKIE['remember_user'];
        
        // Delete token from database
        $sql = "DELETE FROM user_tokens WHERE user_id = ?";
        $db->query($sql, [$user_id]);
        
        // Expire cookies
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        setcookie('remember_user', '', time() - 3600, '/', '', false, true);
    }
}

// Clear all session variables
$_SESSION = [];

// If session cookie exists, expire it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to home page
header("Location: " . SITE_URL);
exit;
?> 