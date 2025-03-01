<?php
// Start session
session_start();

// Include configuration and functions
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to add templates to favorites.'
    ]);
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request. Please try again.'
    ]);
    exit;
}

// Check if template ID is provided
if (!isset($_POST['template_id']) || empty($_POST['template_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Template ID is required.'
    ]);
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user ID
    $user_id = getCurrentUserId();
    
    // Get template ID
    $template_id = intval($_POST['template_id']);
    
    // Check if template exists
    $sql = "SELECT id FROM meme_templates WHERE id = ?";
    $template = $db->fetchOne($sql, [$template_id]);
    
    if (!$template) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Template not found.'
        ]);
        exit;
    }
    
    // Check if already in favorites
    $sql = "SELECT id FROM user_favorites WHERE user_id = ? AND template_id = ?";
    $favorite = $db->fetchOne($sql, [$user_id, $template_id]);
    
    if ($favorite) {
        echo json_encode([
            'success' => true,
            'message' => 'Template is already in your favorites.',
            'already_favorited' => true
        ]);
        exit;
    }
    
    // Add to favorites
    $result = addTemplateToFavorites($user_id, $template_id);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Template added to favorites successfully.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add template to favorites. Please try again.'
        ]);
    }
} else {
    // Not a POST request
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method Not Allowed'
    ]);
} 