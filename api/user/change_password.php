<?php
// Start session
session_start();

// Include configuration and functions
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = "You must be logged in to change your password.";
    header("Location: " . SITE_URL . "/login");
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    $_SESSION['error'] = "Invalid request. Please try again.";
    header("Location: " . SITE_URL . "/profile");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user ID
    $user_id = getCurrentUserId();
    
    // Sanitize input
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error'] = "All password fields are required.";
        header("Location: " . SITE_URL . "/profile");
        exit;
    }
    
    // Check if new password and confirm password match
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "New password and confirm password do not match.";
        header("Location: " . SITE_URL . "/profile");
        exit;
    }
    
    // Validate password strength
    if (strlen($new_password) < 8) {
        $_SESSION['error'] = "Password must be at least 8 characters long.";
        header("Location: " . SITE_URL . "/profile");
        exit;
    }
    
    if (!preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $_SESSION['error'] = "Password must contain at least one uppercase letter, one lowercase letter, and one number.";
        header("Location: " . SITE_URL . "/profile");
        exit;
    }
    
    // Get user's current password hash
    $sql = "SELECT password FROM users WHERE id = ?";
    $user = $db->fetchOne($sql, [$user_id]);
    
    if (!$user) {
        $_SESSION['error'] = "User not found.";
        header("Location: " . SITE_URL . "/profile");
        exit;
    }
    
    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        $_SESSION['error'] = "Current password is incorrect.";
        header("Location: " . SITE_URL . "/profile");
        exit;
    }
    
    // Hash new password
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update password
    $sql = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
    $result = $db->update($sql, [$password_hash, $user_id]);
    
    if ($result) {
        // Log activity
        logActivity($user_id, 'password_change', 'Changed account password');
        
        // Clear any "remember me" tokens
        $sql = "DELETE FROM user_auth_tokens WHERE user_id = ?";
        $db->update($sql, [$user_id]);
        
        // If "remember me" cookie exists, delete it
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
            setcookie('remember_user', '', time() - 3600, '/', '', false, true);
        }
        
        $_SESSION['success'] = "Password changed successfully. Please log in with your new password.";
        
        // Destroy session and redirect to login
        session_destroy();
        header("Location: " . SITE_URL . "?page=login");
        exit;
    } else {
        $_SESSION['error'] = "Failed to update password. Please try again.";
        header("Location: " . SITE_URL . "?page=profile");
        exit;
    }
} else {
    // Not a POST request
    header("Location: " . SITE_URL . "?page=profile");
    exit;
} 