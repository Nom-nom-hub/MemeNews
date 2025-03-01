<?php
// Start session
session_start();

// Include configuration and functions
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = 'You must be logged in to delete collections.';
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
    
    // Validate collection ID
    if ($collection_id <= 0) {
        $_SESSION['error'] = 'Invalid collection ID.';
        header('Location: /collection');
        exit;
    }
    
    // Check if the collection exists and belongs to the user
    $sql = "SELECT name FROM user_collections WHERE id = ? AND user_id = ?";
    $collection = $db->fetchOne($sql, [$collection_id, $user_id]);
    
    if (!$collection) {
        $_SESSION['error'] = 'Collection not found or you do not have permission to delete it.';
        header('Location: /collection');
        exit;
    }
    
    // Start a transaction
    $db->beginTransaction();
    
    try {
        // First, delete all memes from this collection
        $sql = "DELETE FROM collection_memes WHERE collection_id = ?";
        $db->update($sql, [$collection_id]);
        
        // Then delete the collection itself
        $sql = "DELETE FROM user_collections WHERE id = ? AND user_id = ?";
        $result = $db->update($sql, [$collection_id, $user_id]);
        
        // If successful, commit the transaction
        if ($result) {
            $db->commit();
            
            // Log activity
            $collection_name = $collection['name'];
            logActivity($user_id, 'delete_collection', "Deleted collection '$collection_name' (ID: $collection_id)");
            
            $_SESSION['success'] = 'Collection deleted successfully.';
        } else {
            // Something went wrong
            $db->rollback();
            $_SESSION['error'] = 'Failed to delete collection. Please try again.';
        }
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        $db->rollback();
        $_SESSION['error'] = 'An error occurred while deleting the collection: ' . $e->getMessage();
    }
    
    header('Location: /collection');
    exit;
} else {
    // Not a POST request
    header('Location: /collection');
    exit;
} 