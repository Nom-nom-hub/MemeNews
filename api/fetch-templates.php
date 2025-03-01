<?php
/**
 * Fetch Templates Script
 * 
 * This script fetches popular meme templates from ImgFlip API and
 * creates a local database of templates we can use.
 */

// Include configuration files
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Set headers for JSON response
header('Content-Type: application/json');

// API URL for ImgFlip
$imgflipApiUrl = 'https://api.imgflip.com/get_memes';

/**
 * Downloads an image from a URL and saves it to a local path
 * 
 * @param string $url URL of the image to download
 * @param string $savePath Path to save the image to
 * @return bool Whether the download was successful
 */
function downloadImage($url, $savePath) {
    try {
        // Set a timeout for the request to avoid long-running downloads
        $context = stream_context_create([
            'http' => [
                'timeout' => 5 // 5 second timeout
            ]
        ]);
        
        // Get the image content
        $imageContent = @file_get_contents($url, false, $context);
        
        if ($imageContent === false) {
            error_log("Failed to download image from: $url");
            return false;
        }
        
        // Save the image to the local path
        return file_put_contents($savePath, $imageContent) !== false;
    } catch (Exception $e) {
        error_log("Exception downloading image: " . $e->getMessage());
        return false;
    }
}

// Create templates directory if it doesn't exist
$templatesDir = __DIR__ . '/../img/templates';
if (!file_exists($templatesDir)) {
    mkdir($templatesDir, 0755, true);
}

// If refresh parameter is set, clear existing templates
if (isset($_GET['refresh']) && $_GET['refresh'] === 'true') {
    // Delete existing template files
    $files = glob($templatesDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    foreach ($files as $file) {
        unlink($file);
    }
    
    // Clear database entries if connected
    try {
        $db->query("DELETE FROM meme_templates");
    } catch (Exception $e) {
        // Database might not be set up yet, just continue
    }
}

try {
    // Fetch memes from ImgFlip API
    $response = @file_get_contents($imgflipApiUrl);
    
    if ($response === false) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to connect to ImgFlip API'
        ]);
        exit;
    }
    
    $data = json_decode($response, true);
    
    if (!isset($data['success']) || $data['success'] !== true) {
        throw new Exception("API returned error: " . ($data['error_message'] ?? 'Unknown error'));
    }
    
    $memes = $data['data']['memes'] ?? [];
    
    // Randomize the memes to get different ones each time
    shuffle($memes);
    
    $savedMemes = [];
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50; // Increase default to 50, allow custom limit
    
    // Process each meme
    foreach ($memes as $index => $meme) {
        // Respect the limit
        if ($index >= $limit) break;
        
        $memeName = $meme['name'];
        $memeUrl = $meme['url'];
        $memeWidth = $meme['width'];
        $memeHeight = $meme['height'];
        
        // Create filename from meme name
        $filename = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $memeName));
        $filename = preg_replace('/-+/', '-', $filename);
        $filename = trim($filename, '-');
        
        // Determine image extension from URL
        $extension = pathinfo(parse_url($memeUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
        if (empty($extension) || !in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $extension = 'jpg'; // Default extension
        }
        
        $filename .= '.' . $extension;
        $filePath = 'img/templates/' . $filename; // Use consistent path
        $fullSavePath = __DIR__ . '/../' . $filePath;
        
        // Download the image
        if (downloadImage($memeUrl, $fullSavePath)) {
            $savedMemes[] = [
                'name' => $memeName,
                'file_path' => $filePath,
                'width' => $memeWidth,
                'height' => $memeHeight
            ];
            
            // Determine a category based on the meme name
            $category = 'general';
            
            $reactionKeywords = ['guys', 'face', 'girl', 'kid', 'baby', 'look', 'reaction'];
            $animalKeywords = ['cat', 'dog', 'bird', 'frog', 'animal', 'bear', 'monkey'];
            $opinionKeywords = ['opinion', 'change', 'mind', 'agree', 'think', 'change my mind'];
            $choicesKeywords = ['button', 'choice', 'choose', 'pick', 'decide'];
            $intelligenceKeywords = ['smart', 'brain', 'expand', 'intelligent', 'genius'];
            
            $lowerName = strtolower($memeName);
            
            if (str_contains($lowerName, 'button')) {
                $category = 'choices';
            } elseif (str_contains($lowerName, 'change my mind')) {
                $category = 'opinion';
            } elseif (str_contains($lowerName, 'expanding brain')) {
                $category = 'intelligence';
            } elseif (str_contains($lowerName, 'drake')) {
                $category = 'reaction';
            } else {
                foreach ($reactionKeywords as $keyword) {
                    if (str_contains($lowerName, $keyword)) {
                        $category = 'reaction';
                        break;
                    }
                }
                
                if ($category === 'general') {
                    foreach ($animalKeywords as $keyword) {
                        if (str_contains($lowerName, $keyword)) {
                            $category = 'animals';
                            break;
                        }
                    }
                }
                
                if ($category === 'general') {
                    foreach ($opinionKeywords as $keyword) {
                        if (str_contains($lowerName, $keyword)) {
                            $category = 'opinion';
                            break;
                        }
                    }
                }
                
                if ($category === 'general') {
                    foreach ($choicesKeywords as $keyword) {
                        if (str_contains($lowerName, $keyword)) {
                            $category = 'choices';
                            break;
                        }
                    }
                }
                
                if ($category === 'general') {
                    foreach ($intelligenceKeywords as $keyword) {
                        if (str_contains($lowerName, $keyword)) {
                            $category = 'intelligence';
                            break;
                        }
                    }
                }
            }
            
            // Try to insert into database if available
            try {
                $db->insert(
                    "INSERT INTO meme_templates (name, file_path, category, width, height, created_at) 
                     VALUES (?, ?, ?, ?, ?, NOW())",
                    [$memeName, $filePath, $category, $memeWidth, $memeHeight]
                );
            } catch (Exception $dbEx) {
                // Database might not be set up yet, just continue
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Successfully fetched ' . count($savedMemes) . ' templates',
        'templates' => $savedMemes
    ]);
    
} catch (Exception $e) {
    // Handle error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 