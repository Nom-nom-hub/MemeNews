<?php
/**
 * Create Test Templates Script
 * 
 * This script creates test meme templates when other methods fail
 */

// Include configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/database.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Create templates directory if it doesn't exist
$templatesDir = __DIR__ . '/../img/templates';
if (!file_exists($templatesDir)) {
    mkdir($templatesDir, 0755, true);
}

// If refresh parameter is set, clear existing templates
if (isset($_GET['refresh']) && $_GET['refresh'] === 'true') {
    // Delete existing template files that were created by this script
    $files = glob($templatesDir . '/*.jpg');
    foreach ($files as $file) {
        if (strpos(basename($file), 'placeholder-') === 0) {
            unlink($file);
        }
    }
}

// Function to create a placeholder image with text
function createPlaceholderImage($text, $width, $height, $savePath) {
    // Create image
    $image = imagecreatetruecolor($width, $height);
    
    // Colors
    $bg = imagecolorallocate($image, 200, 200, 200);
    $textColor = imagecolorallocate($image, 50, 50, 50);
    $borderColor = imagecolorallocate($image, 150, 150, 150);
    
    // Fill background
    imagefill($image, 0, 0, $bg);
    
    // Draw border
    imagerectangle($image, 0, 0, $width - 1, $height - 1, $borderColor);
    
    // Add text
    $fontSize = 5;
    $textWidth = imagefontwidth($fontSize) * strlen($text);
    $textHeight = imagefontheight($fontSize);
    
    // Center text
    $x = ($width - $textWidth) / 2;
    $y = ($height - $textHeight) / 2;
    
    imagestring($image, $fontSize, $x, $y, $text, $textColor);
    
    // Save image
    return imagejpeg($image, $savePath, 90);
}

// Extended list of popular meme templates to create
$memeTemplates = [
    ['name' => 'Drake Hotline Bling', 'category' => 'reaction', 'width' => 600, 'height' => 600],
    ['name' => 'Distracted Boyfriend', 'category' => 'reaction', 'width' => 800, 'height' => 533],
    ['name' => 'Two Buttons', 'category' => 'choices', 'width' => 600, 'height' => 908],
    ['name' => 'Change My Mind', 'category' => 'opinion', 'width' => 800, 'height' => 450],
    ['name' => 'Expanding Brain', 'category' => 'intelligence', 'width' => 500, 'height' => 900],
    ['name' => 'Woman Yelling at Cat', 'category' => 'reaction', 'width' => 800, 'height' => 400],
    ['name' => 'Surprised Pikachu', 'category' => 'reaction', 'width' => 680, 'height' => 600],
    ['name' => 'One Does Not Simply', 'category' => 'warning', 'width' => 600, 'height' => 500],
    ['name' => 'Ancient Aliens', 'category' => 'conspiracy', 'width' => 500, 'height' => 500],
    ['name' => 'Roll Safe', 'category' => 'clever', 'width' => 650, 'height' => 400],
    ['name' => 'Is This a Pigeon', 'category' => 'confusion', 'width' => 700, 'height' => 500],
    ['name' => 'Disaster Girl', 'category' => 'reaction', 'width' => 600, 'height' => 400],
    ['name' => 'Gru\'s Plan', 'category' => 'plan', 'width' => 700, 'height' => 900],
    ['name' => 'Hide the Pain Harold', 'category' => 'reaction', 'width' => 600, 'height' => 400],
    ['name' => 'Mocking SpongeBob', 'category' => 'mockery', 'width' => 600, 'height' => 400],
    ['name' => 'Epic Handshake', 'category' => 'agreement', 'width' => 800, 'height' => 450],
    ['name' => 'Sad Pablo Escobar', 'category' => 'lonely', 'width' => 700, 'height' => 400],
    ['name' => 'Left Exit 12 Off Ramp', 'category' => 'choices', 'width' => 700, 'height' => 500],
    ['name' => 'Running Away Balloon', 'category' => 'avoidance', 'width' => 650, 'height' => 700],
    ['name' => 'Always Has Been', 'category' => 'revelation', 'width' => 800, 'height' => 450],
    ['name' => 'Bernie Asking For Support', 'category' => 'plea', 'width' => 700, 'height' => 400],
    ['name' => 'Waiting Skeleton', 'category' => 'waiting', 'width' => 500, 'height' => 600],
    ['name' => 'Buff Doge vs Cheems', 'category' => 'comparison', 'width' => 800, 'height' => 500],
    ['name' => 'Anakin Padme 4 Panel', 'category' => 'misunderstanding', 'width' => 800, 'height' => 450],
    ['name' => 'Uno Draw 25 Cards', 'category' => 'choices', 'width' => 600, 'height' => 500]
];

// Randomize the order if requested
if (isset($_GET['random']) && $_GET['random'] === 'true') {
    shuffle($memeTemplates);
}

// Limit the number of templates if specified
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : count($memeTemplates);
$memeTemplates = array_slice($memeTemplates, 0, $limit);

$createdMemes = [];

try {
    foreach ($memeTemplates as $template) {
        $name = $template['name'];
        $category = $template['category'];
        $width = $template['width'];
        $height = $template['height'];
        
        // Create filename from meme name
        $filename = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $name));
        $filename = preg_replace('/-+/', '-', $filename);
        $filename = trim($filename, '-');
        
        // Add placeholder prefix to distinguish from real templates
        $filename = 'placeholder-' . $filename . '.jpg';
        
        $filePath = 'img/templates/' . $filename;
        $fullSavePath = __DIR__ . '/../' . $filePath;
        
        // Create placeholder image
        if (createPlaceholderImage($name, $width, $height, $fullSavePath)) {
            $createdMemes[] = [
                'name' => $name,
                'file_path' => $filePath,
                'width' => $width,
                'height' => $height,
                'category' => $category
            ];
            
            // Try to insert into database if available
            try {
                $db->insert(
                    "INSERT INTO meme_templates (name, file_path, category, tags, width, height, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, NOW())",
                    [$name, $filePath, $category, $category, $width, $height]
                );
            } catch (Exception $dbEx) {
                // Database might not be set up yet, just continue
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Successfully created ' . count($createdMemes) . ' template placeholders',
        'templates' => $createdMemes
    ]);
    
} catch (Exception $e) {
    // Handle error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 