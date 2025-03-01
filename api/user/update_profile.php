<?php
// Start session
session_start();

// Include configuration and functions
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = "You must be logged in to update your profile.";
    header("Location: " . SITE_URL . "?page=login");
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    $_SESSION['error'] = "Invalid request. Please try again.";
    header("Location: " . SITE_URL . "?page=profile");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user ID
    $user_id = getCurrentUserId();
    
    // Sanitize input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $bio = isset($_POST['bio']) ? htmlspecialchars($_POST['bio'], ENT_QUOTES, 'UTF-8') : null;
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address.";
        header("Location: " . SITE_URL . "?page=profile");
        exit;
    }
    
    // Check if email already exists for another user
    $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $result = $db->fetchOne($sql, [$email, $user_id]);
    
    if ($result) {
        $_SESSION['error'] = "Email address is already in use by another account.";
        header("Location: " . SITE_URL . "?page=profile");
        exit;
    }
    
    // Update user profile
    $sql = "UPDATE users SET email = ?, bio = ?, updated_at = NOW() WHERE id = ?";
    $result = $db->update($sql, [$email, $bio, $user_id]);
    
    if ($result) {
        // Update session with new email
        $_SESSION['user_email'] = $email;
        
        // Log activity
        logActivity($user_id, 'profile_update', 'Updated profile information');
        
        $_SESSION['success'] = "Profile information updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update profile. Please try again.";
    }
    
    header("Location: " . SITE_URL . "?page=profile");
    exit;
} else {
    // Not a POST request
    header("Location: " . SITE_URL . "?page=profile");
    exit;
} 