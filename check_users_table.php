<?php
// Script to check users table structure

// Include configuration and database connection
require_once 'config/config.php';
require_once 'includes/database.php';

echo "======================================\n";
echo "MemeNews - Users Table Structure\n";
echo "======================================\n\n";

// Connect to database
echo "Connecting to database...\n";
try {
    $db = new Database();
    echo "Connected to database successfully.\n\n";
} catch (Exception $e) {
    echo "Database connection error: " . $e->getMessage() . "\n";
    exit(1);
}

// SQL statement to check table structure
$sql = "DESCRIBE users";

// Execute SQL statement
echo "Retrieving users table structure...\n\n";
try {
    $result = $db->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    
    // Display column information
    echo "USERS TABLE COLUMNS:\n";
    echo "--------------------\n";
    foreach ($rows as $row) {
        echo "Field: " . $row['Field'] . "\n";
        echo "Type: " . $row['Type'] . "\n";
        echo "Null: " . $row['Null'] . "\n";
        echo "Key: " . $row['Key'] . "\n";
        echo "Default: " . ($row['Default'] ?? 'NULL') . "\n";
        echo "Extra: " . $row['Extra'] . "\n";
        echo "--------------------\n";
    }
} catch (Exception $e) {
    echo "Error executing SQL: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nDatabase check completed.\n";
echo "======================================\n"; 