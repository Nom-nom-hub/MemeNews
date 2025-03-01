<?php
/**
 * API Endpoint: Get Template
 * 
 * Retrieves template information based on ID
 */

// Start the session
session_start();

// Include configuration files
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if CSRF token is valid
if (!isset($_GET['csrf_token']) || !validateCSRFToken($_GET['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

// Check if template ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing template ID']);
    exit;
}

// Sanitize input
$templateId = sanitize($_GET['id']);

try {
    $template = null;
    
    // If it's a numeric ID, try to fetch from database
    if (is_numeric($templateId)) {
        // Try to fetch the template from the database
        $sql = "SELECT * FROM meme_templates WHERE id = ?";
        $template = $db->fetchOne($sql, [(int)$templateId]);
    }
    
    // If not found in DB or ID is not numeric (might be MD5 hash)
    if (!$template) {
        // Check for template files directly
        $templatesDir = __DIR__ . '/../img/templates';
        $files = glob($templatesDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        
        foreach ($files as $file) {
            $basename = basename($file);
            if (md5($basename) === $templateId) {
                $name = ucwords(str_replace(['-', '_', '.jpg', '.jpeg', '.png', '.gif'], [' ', ' ', '', '', '', ''], $basename));
                list($width, $height) = getimagesize($file);
                
                $template = [
                    'name' => $name,
                    'file_path' => 'img/templates/' . $basename,
                    'width' => $width,
                    'height' => $height
                ];
                break;
            }
        }
    }
    
    if ($template) {
        // Return template details
        echo json_encode([
            'success' => true,
            'template_url' => SITE_URL . '/' . $template['file_path'],
            'template_name' => $template['name'],
            'width' => $template['width'],
            'height' => $template['height']
        ]);
    } else {
        // Handle case when template not found
        // Return a placeholder image
        echo json_encode([
            'success' => true,
            'template_url' => SITE_URL . '/img/placeholder.svg',
            'template_name' => 'Placeholder Template',
            'width' => 600,
            'height' => 400
        ]);
    }
} catch (Exception $e) {
    // Error occurred, return placeholder data
    echo json_encode([
        'success' => true,
        'template_url' => SITE_URL . '/img/placeholder.svg',
        'template_name' => 'Placeholder Template',
        'width' => 600,
        'height' => 400
    ]);
}
?> 