<?php
/**
 * MemeNews - Add uploaded_by Column to meme_templates
 * 
 * This script adds the missing uploaded_by column to the meme_templates table.
 */

// Include configuration
require_once 'config/config.php';
require_once 'includes/database.php';

// Display header
echo "===========================================\n";
echo "MemeNews - Add uploaded_by Column Installer\n";
echo "===========================================\n\n";

// Read SQL file
$sqlFile = 'database/add_uploaded_by_column.sql';
if (!file_exists($sqlFile)) {
    die("Error: SQL file not found at {$sqlFile}\n");
}

$sql = file_get_contents($sqlFile);
if (!$sql) {
    die("Error: Unable to read SQL file\n");
}

// Split SQL into individual statements
$statements = explode(';', $sql);

// Database connection
try {
    // Create database connection
    echo "Connecting to database using MAMP settings...\n";
    
    // First, attempt to connect using socket
    $mysqli = null;
    if (defined('DB_SOCKET') && !empty(DB_SOCKET)) {
        echo "Trying socket connection: " . DB_SOCKET . "\n";
        $mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, null, DB_SOCKET);
    }
    
    // If socket connection fails, try with port
    if (!$mysqli || $mysqli->connect_errno) {
        if ($mysqli) {
            echo "Socket connection failed: " . $mysqli->connect_error . "\n";
        }
        
        if (defined('DB_PORT') && !empty(DB_PORT)) {
            echo "Trying port connection: " . DB_PORT . "\n";
            $mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        } else {
            $mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        }
    }
    
    // Check for connection errors
    if ($mysqli->connect_errno) {
        die("All connection attempts failed: " . $mysqli->connect_error . "\n");
    }
    
    echo "Connection successful!\n\n";
    
    // Execute each statement
    $successCount = 0;
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            // Get operation from statement (for display purposes)
            if (preg_match('/ALTER TABLE|CREATE INDEX|UPDATE|SELECT/i', $statement, $matches)) {
                $operation = $matches[0];
                echo "Executing: {$operation}... ";
            } else {
                echo "Executing statement... ";
            }
            
            // Execute the statement
            if ($mysqli->query($statement)) {
                echo "SUCCESS\n";
                $successCount++;
            } else {
                echo "ERROR: " . $mysqli->error . "\n";
            }
        }
    }
    
    // Display summary
    echo "\n===========================================\n";
    echo "Installation complete!\n";
    echo "{$successCount} statements executed successfully.\n";
    echo "===========================================\n";
    
    // Close connection
    $mysqli->close();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
} 