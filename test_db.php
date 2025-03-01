<?php
// Initialize session
session_start();

// Include configuration and database connection
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';

// Check if we have a database connection
if (!$db) {
    echo "Error: Database connection failed.<br>";
    exit;
}

echo "Database connection successful!<br>";

// Test update method
try {
    $sql = "CREATE TABLE IF NOT EXISTS test_table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $result = $db->query($sql);
    echo "Test table created or already exists.<br>";
    
    // Test insert
    $sql = "INSERT INTO test_table (name) VALUES (?)";
    $result = $db->insert($sql, ["Test entry " . time()]);
    
    if ($result) {
        echo "Insert test successful! ID: " . $result . "<br>";
    } else {
        echo "Insert test failed.<br>";
    }
    
    // Test update
    $sql = "UPDATE test_table SET name = ? WHERE id = ?";
    $result = $db->update($sql, ["Updated at " . time(), $result]);
    
    if ($result) {
        echo "Update test successful!<br>";
    } else {
        echo "Update test failed.<br>";
    }
    
    echo "All tests completed successfully!";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} 