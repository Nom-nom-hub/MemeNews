<?php
/**
 * API Endpoint: Forgot Password
 * 
 * Handles password reset requests by sending email with reset token
 */

// Start session
session_start();

// Include configuration and functions
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    $_SESSION['error'] = "Invalid request. Please try again.";
    header("Location: " . SITE_URL . "?page=forgot-password");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize email
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address.";
        header("Location: " . SITE_URL . "?page=forgot-password");
        exit;
    }
    
    // Check if email exists in database
    $sql = "SELECT id, username FROM users WHERE email = ?";
    $user = $db->fetchOne($sql, [$email]);
    
    if (!$user) {
        // For security, don't reveal that the email doesn't exist
        // Just show general success message as if email was sent
        $_SESSION['success'] = "If your email exists in our system, you will receive a password reset link shortly.";
        header("Location: " . SITE_URL . "?page=forgot-password");
        exit;
    }
    
    // Generate unique token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 86400); // 24 hours
    
    // Delete any existing reset tokens for this user
    $sql = "DELETE FROM password_reset_tokens WHERE user_id = ?";
    $db->update($sql, [$user['id']]);
    
    // Store token in database
    $sql = "INSERT INTO password_reset_tokens (user_id, token, expires) VALUES (?, ?, ?)";
    $result = $db->insert($sql, [
        $user['id'],
        password_hash($token, PASSWORD_DEFAULT), // Store hashed token
        $expires
    ]);
    
    if ($result) {
        // Construct reset URL
        $reset_url = SITE_URL . "?page=reset-password&token=" . $token . "&email=" . urlencode($email);
        
        // In a real application, you would send an email here
        // For this example, we'll just log it
        logActivity(0, 'password_reset_request', "Reset request for user ID: {$user['id']}, Email: {$email}");
        error_log("Password reset URL for {$email}: {$reset_url}");
        
        // Show a message with the URL for testing purposes
        if (DEBUG_MODE) {
            $_SESSION['success'] = "A password reset link has been generated. In a production environment, this would be sent via email.<br><br>For testing purposes: <a href='{$reset_url}'>{$reset_url}</a>";
        } else {
            $_SESSION['success'] = "If your email exists in our system, you will receive a password reset link shortly.";
        }
        
        header("Location: " . SITE_URL . "?page=forgot-password");
        exit;
    } else {
        $_SESSION['error'] = "An error occurred. Please try again.";
        header("Location: " . SITE_URL . "?page=forgot-password");
        exit;
    }
} else {
    // Not a POST request
    header("Location: " . SITE_URL . "?page=forgot-password");
    exit;
} 