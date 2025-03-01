<?php
/**
 * Fix Config Paths Script
 * 
 * This script corrects the paths in API files that incorrectly reference 
 * '../../includes/config.php' instead of '../../config/config.php'
 */

echo "===========================================\n";
echo "MemeNews - Fix Config Paths\n";
echo "===========================================\n\n";

// Define the directory to search
$apiDir = __DIR__ . '/api/user';

// Check if directory exists
if (!is_dir($apiDir)) {
    die("Error: API directory not found at {$apiDir}\n");
}

// Get all PHP files in the directory
$files = glob($apiDir . '/*.php');

if (empty($files)) {
    die("Error: No PHP files found in {$apiDir}\n");
}

$fixedFiles = 0;

// Process each file
foreach ($files as $file) {
    echo "Processing " . basename($file) . "... ";
    
    // Read file contents
    $content = file_get_contents($file);
    
    if ($content === false) {
        echo "ERROR: Unable to read file\n";
        continue;
    }
    
    // Check if the file contains the incorrect path
    if (strpos($content, "require_once '../../includes/config.php';") !== false) {
        // Replace the incorrect path with the correct one
        $newContent = str_replace(
            "require_once '../../includes/config.php';",
            "require_once '../../config/config.php';",
            $content
        );
        
        // Write the updated content back to the file
        if (file_put_contents($file, $newContent)) {
            echo "FIXED\n";
            $fixedFiles++;
        } else {
            echo "ERROR: Unable to write to file\n";
        }
    } else {
        echo "OK (no changes needed)\n";
    }
}

// Display summary
echo "\n===========================================\n";
echo "Fixed {$fixedFiles} file(s)\n";
echo "===========================================\n";
?> 