<?php
/**
 * API Endpoint: Generate Caption
 * 
 * Generates a meme caption using OpenRouter AI based on a news headline
 */

// Start the session
session_start();

// Include configuration files
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

// Validate headline parameter
if (!isset($_POST['headline']) || empty($_POST['headline'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing headline parameter']);
    exit;
}

// Sanitize input
$headline = sanitize($_POST['headline']);

try {
    // Log the request
    logActivity('generate_caption', 'Headline: ' . $headline);
    
    // Call the OpenRouter API to generate a caption
    $caption = generateMemeCaption($headline);
    
    // Return the generated caption
    echo json_encode([
        'success' => true,
        'caption' => $caption,
        'headline' => $headline
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => DEBUG_MODE ? $e->getMessage() : 'An error occurred while generating the caption'
    ]);
}
?> 