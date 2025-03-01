<?php
// Start session
session_start();

// Include configuration and functions
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = 'You must be logged in to create collections.';
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
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
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
    
    // Check for duplicate collection names for this user
    $sql = "SELECT id FROM user_collections WHERE user_id = ? AND name = ?";
    $existing = $db->fetchOne($sql, [$user_id, $name]);
    
    if ($existing) {
        $_SESSION['error'] = 'You already have a collection with this name. Please choose a different name.';
        header('Location: /collection');
        exit;
    }
    
    // Insert new collection
    $sql = "INSERT INTO user_collections (user_id, name, description, created_at, updated_at) 
            VALUES (?, ?, ?, NOW(), NOW())";
    $result = $db->execute($sql, [$user_id, $name, $description]);
    
    if ($result) {
        // Get the new collection ID
        $collection_id = $db->lastInsertId();
        
        // Log activity
        logActivity($user_id, 'create_collection', "Created collection '$name' (ID: $collection_id)");
        
        $_SESSION['success'] = 'Collection created successfully.';
    } else {
        $_SESSION['error'] = 'Failed to create collection. Please try again.';
    }
    
    header('Location: /collection');
    exit;
} else {
    // Not a POST request
    header('Location: /collection');
    exit;
}