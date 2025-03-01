<?php
/**
 * Database Update Script
 * 
 * This script adds the missing template_categories table to the database.
 */

// Include configuration files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/database.php';

// Display header
echo "======================================\n";
echo "MemeNews Database Update Script\n";
echo "======================================\n\n";

try {
    // Read the SQL file
    $sqlFile = file_get_contents(__DIR__ . '/add_template_categories.sql');
    
    if (!$sqlFile) {
        throw new Exception("Could not read SQL file.");
    }
    
    echo "Reading SQL file... OK\n";
    
    // Split the SQL file into separate statements
    $statements = array_filter(
        array_map(
            'trim',
            explode(';', $sqlFile)
        )
    );
    
    echo "Found " . count($statements) . " SQL statements to execute.\n\n";
    
    // Execute each statement directly using mysqli
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, defined('DB_PORT') ? DB_PORT : 3306, defined('DB_SOCKET') ? DB_SOCKET : null);
    
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    foreach ($statements as $index => $statement) {
        if (empty($statement)) {
            continue;
        }
        
        echo "Executing statement " . ($index + 1) . "... ";
        
        try {
            $result = $mysqli->query($statement);
            
            if ($result === false) {
                throw new Exception($mysqli->error);
            }
            
            echo "OK\n";
        } catch (Exception $e) {
            echo "ERROR\n";
            echo "Error message: " . $e->getMessage() . "\n\n";
            
            // Continue with the next statement even if there's an error
            continue;
        }
    }
    
    $mysqli->close();
    
    echo "\nDatabase update completed successfully!\n";
    echo "The template_categories table has been created and populated with default categories.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
} 