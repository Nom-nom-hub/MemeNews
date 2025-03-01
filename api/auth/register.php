<?php
session_start();
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    $_SESSION['error'] = "Invalid request. Please try again.";
    header("Location: " . SITE_URL . "?page=register");
    exit;
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
    $terms = isset($_POST['terms']) ? (bool)$_POST['terms'] : false;
    
    // Validate input
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (!preg_match('/^[a-zA-Z0-9]{3,20}$/', $username)) {
        $errors[] = "Username must be 3-20 characters and contain only letters and numbers.";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must include uppercase, lowercase, and numbers.";
    }
    
    if ($password !== $password_confirm) {
        $errors[] = "Passwords do not match.";
    }
    
    if (!$terms) {
        $errors[] = "You must agree to the Terms of Service and Privacy Policy.";
    }
    
    // Check if username already exists
    $sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
    $result = $db->fetchOne($sql, [$username]);
    if ($result['count'] > 0) {
        $errors[] = "Username already exists. Please choose another.";
    }
    
    // Check if email already exists
    $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
    $result = $db->fetchOne($sql, [$email]);
    if ($result['count'] > 0) {
        $errors[] = "Email already registered. Please use another email or login.";
    }
    
    // If there are errors, redirect back to registration form
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: " . SITE_URL . "?page=register");
        exit;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $sql = "INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())";
    $user_id = $db->insert($sql, [$username, $email, $hashed_password]);
    
    if (!$user_id) {
        $_SESSION['error'] = "Registration failed. Please try again.";
        header("Location: " . SITE_URL . "?page=register");
        exit;
    }
    
    // Log activity
    logActivity($user_id, 'register', 'New user registered');
    
    // Set success message
    $_SESSION['success'] = "Registration successful! You can now log in.";
    
    // Redirect to login page
    header("Location: " . SITE_URL . "?page=login");
    exit;
} else {
    // If not POST request, redirect to registration page
    header("Location: " . SITE_URL . "?page=register");
    exit;
}
?> 