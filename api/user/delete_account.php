<?php
// Start session
session_start();

// Include configuration and functions
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = "You must be logged in to delete your account.";
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
    
    // Validate password
    if (!isset($_POST['password']) || empty($_POST['password'])) {
        $_SESSION['error'] = "Please enter your password to confirm account deletion.";
        header("Location: " . SITE_URL . "/profile#privacy-settings");
        exit;
    }
    
    $password = $_POST['password'];
    
    // Get user's password hash
    $sql = "SELECT password FROM users WHERE id = ?";
    $user = $db->fetchOne($sql, [$user_id]);
    
    if (!$user) {
        $_SESSION['error'] = "User not found.";
        header("Location: " . SITE_URL . "/profile");
        exit;
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        $_SESSION['error'] = "Incorrect password. Account deletion cancelled.";
        header("Location: " . SITE_URL . "/profile#privacy-settings");
        exit;
    }
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        // Log activity before deleting user
        logActivity($user_id, 'account_deletion', 'Account deleted by user');
        
        // Delete user data - using cascading foreign keys where possible
        
        // Delete user preferences
        $sql = "DELETE FROM user_preferences WHERE user_id = ?";
        $db->query($sql, [$user_id]);
        
        // Delete user favorites
        $sql = "DELETE FROM user_favorites WHERE user_id = ?";
        $db->query($sql, [$user_id]);
        
        // Delete user auth tokens
        $sql = "DELETE FROM user_auth_tokens WHERE user_id = ?";
        $db->query($sql, [$user_id]);
        
        // Delete user activity logs
        $sql = "DELETE FROM user_activity_logs WHERE user_id = ?";
        $db->query($sql, [$user_id]);
        
        // Handle user memes
        $sql = "UPDATE user_memes SET user_id = NULL WHERE user_id = ?";
        $db->query($sql, [$user_id]);
        
        // Handle uploaded templates
        $sql = "UPDATE meme_templates SET uploaded_by = NULL WHERE uploaded_by = ?";
        $db->query($sql, [$user_id]);
        
        // Finally, delete the user
        $sql = "DELETE FROM users WHERE id = ?";
        $db->query($sql, [$user_id]);
        
        // Commit transaction
        $db->commit();
        
        // Clear "remember me" cookies if they exist
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
            setcookie('remember_user', '', time() - 3600, '/', '', false, true);
        }
        
        // Destroy session
        session_destroy();
        
        // Start a new session to show a message
        session_start();
        $_SESSION['success'] = "Your account has been successfully deleted. We're sorry to see you go!";
        
        header("Location: " . SITE_URL);
        exit;
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollBack();
        
        $_SESSION['error'] = "An error occurred while deleting your account. Please try again.";
        header("Location: " . SITE_URL . "?page=profile#privacy-settings");
        exit;
    }
} else {
    // Not a POST request
    header("Location: " . SITE_URL . "?page=profile");
    exit;
} 