<?php
// Start session
session_start();

// Include configuration and functions
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = "You must be logged in to update privacy settings.";
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
    
    // Process checkbox values (checkboxes are only submitted when checked)
    $public_profile = isset($_POST['public_profile']) ? 1 : 0;
    $show_username_on_memes = isset($_POST['show_username_on_memes']) ? 1 : 0;
    
    // Update privacy settings
    $sql = "UPDATE user_preferences SET 
            public_profile = ?, 
            show_username_on_memes = ?, 
            updated_at = NOW() 
            WHERE user_id = ?";
    
    // Check if user preferences exist
    $checkSql = "SELECT user_id FROM user_preferences WHERE user_id = ?";
    $prefExists = $db->fetchOne($checkSql, [$user_id]);
    
    if ($prefExists) {
        // Update existing preferences
        $result = $db->update($sql, [
            $public_profile,
            $show_username_on_memes,
            $user_id
        ]);
    } else {
        // Insert new preferences
        $sql = "INSERT INTO user_preferences (
                user_id, 
                public_profile, 
                show_username_on_memes, 
                created_at, 
                updated_at
                ) VALUES (?, ?, ?, NOW(), NOW())";
        
        $result = $db->insert($sql, [
            $user_id,
            $public_profile,
            $show_username_on_memes
        ]);
    }
    
    if ($result) {
        // Log activity
        logActivity($user_id, 'privacy_update', 'Updated privacy settings');
        
        $_SESSION['success'] = "Privacy settings updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update privacy settings. Please try again.";
    }
    
    header("Location: " . SITE_URL . "?page=profile#privacy-settings");
    exit;
} else {
    // Not a POST request
    header("Location: " . SITE_URL . "?page=profile");
    exit;
} 