<?php
// Start session
session_start();

// Include configuration and functions
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = 'You must be logged in to update collections.';
    header('Location: /login');
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    $_SESSION['error'] = 'Invalid request. Please try again.';
    header('Location: /collection');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user ID
    $user_id = getCurrentUserId();
    
    // Sanitize and validate input
    $collection_id = isset($_POST['collection_id']) ? intval($_POST['collection_id']) : 0;
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Validate collection ID
    if ($collection_id <= 0) {
        $_SESSION['error'] = 'Invalid collection ID.';
        header('Location: /collection');
        exit;
    }
    
    // Validate collection name
    if (empty($name)) {
        $_SESSION['error'] = 'Collection name is required.';
        header('Location: /collection');
        exit;
    }
    
    if (strlen($name) > 100) {
        $_SESSION['error'] = 'Collection name cannot exceed 100 characters.';
        header('Location: /collection');
        exit;
    }
    
    // Validate description (optional)
    if (strlen($description) > 500) {
        $_SESSION['error'] = 'Collection description cannot exceed 500 characters.';
        header('Location: /collection');
        exit;
    }
    
    // Check if the collection exists and belongs to the user
    $sql = "SELECT * FROM user_collections WHERE id = ? AND user_id = ?";
    $collection = $db->fetchOne($sql, [$collection_id, $user_id]);
    
    if (!$collection) {
        $_SESSION['error'] = 'Collection not found or you do not have permission to update it.';
        header('Location: /collection');
        exit;
    }
    
    // Check for duplicate collection names for this user (excluding current collection)
    $sql = "SELECT id FROM user_collections WHERE user_id = ? AND name = ? AND id != ?";
    $existing = $db->fetchOne($sql, [$user_id, $name, $collection_id]);
    
    if ($existing) {
        $_SESSION['error'] = 'You already have a collection with this name. Please choose a different name.';
        header('Location: /collection');
        exit;
    }
    
    // Update collection
    $sql = "UPDATE user_collections SET name = ?, description = ?, updated_at = NOW() WHERE id = ? AND user_id = ?";
    $result = $db->update($sql, [$name, $description, $collection_id, $user_id]);
    
    if ($result) {
        // Log activity
        $old_name = $collection['name'];
        if ($old_name !== $name) {
            logActivity($user_id, 'update_collection', "Updated collection name from '$old_name' to '$name' (ID: $collection_id)");
        } else {
            logActivity($user_id, 'update_collection', "Updated collection '$name' (ID: $collection_id)");
        }
        
        $_SESSION['success'] = 'Collection updated successfully.';
    } else {
        $_SESSION['error'] = 'Failed to update collection. Please try again.';
    }
    
    header('Location: /collection');
    exit;
} else {
    // Not a POST request
    header('Location: /collection');
    exit;
} 