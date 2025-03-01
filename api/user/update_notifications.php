<?php
// Start session
session_start();

// Include configuration and functions
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = "You must be logged in to update notification settings.";
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
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
    $news_alerts = isset($_POST['news_alerts']) ? 1 : 0;
    $contest_notifications = isset($_POST['contest_notifications']) ? 1 : 0;
    
    // Update notification settings
    $sql = "UPDATE user_preferences SET 
            email_notifications = ?, 
            news_alerts = ?, 
            contest_notifications = ?, 
            updated_at = NOW() 
            WHERE user_id = ?";
    
    // Check if user preferences exist
    $checkSql = "SELECT user_id FROM user_preferences WHERE user_id = ?";
    $prefExists = $db->fetchOne($checkSql, [$user_id]);
    
    if ($prefExists) {
        // Update existing preferences
        $result = $db->update($sql, [
            $email_notifications,
            $news_alerts,
            $contest_notifications,
            $user_id
        ]);
    } else {
        // Insert new preferences
        $sql = "INSERT INTO user_preferences (
                user_id, 
                email_notifications, 
                news_alerts, 
                contest_notifications, 
                created_at, 
                updated_at
                ) VALUES (?, ?, ?, ?, NOW(), NOW())";
        
        $result = $db->insert($sql, [
            $user_id,
            $email_notifications,
            $news_alerts,
            $contest_notifications
        ]);
    }
    
    if ($result) {
        // Log activity
        logActivity($user_id, 'notification_update', 'Updated notification preferences');
        
        $_SESSION['success'] = "Notification preferences updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update notification preferences. Please try again.";
    }
    
    header("Location: " . SITE_URL . "?page=profile#notification-settings");
    exit;
} else {
    // Not a POST request
    header("Location: " . SITE_URL . "?page=profile");
    exit;
} 