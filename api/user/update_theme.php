<?php
// Start session
session_start();

// Include configuration and functions
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = "You must be logged in to update theme settings.";
    header("Location: " . SITE_URL . "?page=login");
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    $_SESSION['error'] = "Invalid request. Please try again.";
    header("Location: " . SITE_URL . "?page=profile#theme-settings");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user ID
    $user_id = getCurrentUserId();
    
    // Debug - log POST data
    error_log("Theme update POST data: " . json_encode($_POST));
    
    // Sanitize and validate input
    $theme = isset($_POST['theme']) ? htmlspecialchars($_POST['theme'], ENT_QUOTES, 'UTF-8') : 'system';
    $font_size = filter_var($_POST['font_size'], FILTER_VALIDATE_INT);
    
    // Debug - log parsed values
    error_log("Parsed theme: {$theme}, font_size: {$font_size}");
    
    // Validate theme
    $allowed_themes = ['system', 'light', 'dark'];
    if (!in_array($theme, $allowed_themes)) {
        $_SESSION['error'] = "Invalid theme selection.";
        header("Location: " . SITE_URL . "?page=profile#theme-settings");
        exit;
    }
    
    // Validate font size
    if ($font_size < 70 || $font_size > 150) {
        $_SESSION['error'] = "Font size must be between 70% and 150%.";
        header("Location: " . SITE_URL . "?page=profile#theme-settings");
        exit;
    }
    
    // Update theme settings
    $sql = "UPDATE user_preferences SET 
            theme = ?, 
            font_size = ?, 
            updated_at = NOW() 
            WHERE user_id = ?";
    
    // Check if user preferences exist
    $checkSql = "SELECT user_id FROM user_preferences WHERE user_id = ?";
    $prefExists = $db->fetchOne($checkSql, [$user_id]);
    
    if ($prefExists) {
        // Update existing preferences
        $result = $db->update($sql, [
            $theme,
            $font_size,
            $user_id
        ]);
    } else {
        // Insert new preferences
        $sql = "INSERT INTO user_preferences (
                user_id, 
                theme, 
                font_size, 
                created_at, 
                updated_at
                ) VALUES (?, ?, ?, NOW(), NOW())";
        
        $result = $db->insert($sql, [
            $user_id,
            $theme,
            $font_size
        ]);
    }
    
    if ($result) {
        // Set new theme preference in session
        $_SESSION['user_theme'] = $theme;
        $_SESSION['user_font_size'] = $font_size;
        
        // Log activity
        logActivity($user_id, 'theme_update', "Updated theme to '{$theme}' and font size to {$font_size}%");
        
        $_SESSION['success'] = "Theme settings updated successfully.";
    } else {
        // Log the error for debugging
        error_log("Theme update failed for user ID: {$user_id}. Theme: {$theme}, Font size: {$font_size}");
        
        $_SESSION['error'] = "Failed to update theme settings. Please try again.";
    }
    
    header("Location: " . SITE_URL . "?page=profile#theme-settings");
    exit;
} else {
    // Not a POST request
    $_SESSION['error'] = "Invalid request method.";
    header("Location: " . SITE_URL . "?page=profile");
    exit;
} 