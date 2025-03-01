<?php
// Script to add bio column to users table

// Include configuration and database connection
require_once 'config/config.php';
require_once 'includes/database.php';

echo "======================================\n";
echo "MemeNews - Adding Bio Column to Users Table\n";
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

// SQL statement to add bio column
$sql = "ALTER TABLE users ADD COLUMN bio TEXT NULL AFTER email";

// Execute SQL statement
echo "Adding bio column to users table...\n";
try {
    $result = $db->query($sql);
    echo "Bio column added successfully!\n\n";
} catch (Exception $e) {
    // Check if the error is because the column already exists
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "The bio column already exists in the users table.\n\n";
    } else {
        echo "Error executing SQL: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "Database update completed.\n";
echo "======================================\n"; 