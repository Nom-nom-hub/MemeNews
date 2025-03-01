<?php
// Start session
session_start();

// Include configuration and functions
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = "You must be logged in to export your data.";
    header("Location: " . SITE_URL . "/login");
    exit;
}

// Get user ID
$user_id = getCurrentUserId();

// Get user data
$userData = [];

// Get user account info
$sql = "SELECT id, username, email, bio, created_at, updated_at FROM users WHERE id = ?";
$user = $db->fetchOne($sql, [$user_id]);

if (!$user) {
    $_SESSION['error'] = "User not found.";
    header("Location: " . SITE_URL . "/profile");
    exit;
}

$userData['account'] = $user;

// Get user preferences
$sql = "SELECT * FROM user_preferences WHERE user_id = ?";
$preferences = $db->fetchOne($sql, [$user_id]);

$userData['preferences'] = $preferences ?: [];

// Get user activity logs
$sql = "SELECT action, details, ip_address, created_at FROM user_activity_logs WHERE user_id = ? ORDER BY created_at DESC";
$activityLogs = $db->fetchAll($sql, [$user_id]);

$userData['activity_logs'] = $activityLogs ?: [];

// Get user memes
$sql = "SELECT id, template_id, top_text, bottom_text, custom_text, created_at FROM user_memes WHERE user_id = ? ORDER BY created_at DESC";
$memes = $db->fetchAll($sql, [$user_id]);

$userData['memes'] = $memes ?: [];

// Get user favorites
$sql = "SELECT 
            uf.template_id, 
            mt.name, 
            uf.created_at 
        FROM 
            user_favorites uf
        LEFT JOIN 
            meme_templates mt ON uf.template_id = mt.id
        WHERE 
            uf.user_id = ? 
        ORDER BY 
            uf.created_at DESC";
$favorites = $db->fetchAll($sql, [$user_id]);

$userData['favorites'] = $favorites ?: [];

// Get user uploaded templates
$sql = "SELECT id, name, file_path, category, tags, created_at FROM meme_templates WHERE uploaded_by = ? ORDER BY created_at DESC";
$templates = $db->fetchAll($sql, [$user_id]);

$userData['uploaded_templates'] = $templates ?: [];

// Log activity
logActivity($user_id, 'data_export', 'Exported user data');

// Set appropriate headers
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="memenews_user_data_' . date('Y-m-d') . '.json"');

// Output JSON data
echo json_encode($userData, JSON_PRETTY_PRINT);
exit; 