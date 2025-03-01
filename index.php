<?php
// Start the session
session_start();

// Include configuration files
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/database.php';

// Check if user can be authenticated from remember me cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user']) && isset($_COOKIE['remember_token'])) {
    authenticateFromRememberCookie();
}

// Check if session has expired
if (isset($_SESSION['user_id']) && isSessionExpired()) {
    // Session expired, redirect to logout
    header("Location: " . SITE_URL . "/api/auth/logout.php");
    exit;
}

// Check if user is accessing a specific page
$page = isset($_GET['page']) ? sanitize($_GET['page']) : 'home';

// Header
include_once 'includes/header.php';

// Navigation
include_once 'includes/navigation.php';

// Page content
switch ($page) {
    case 'home':
        include_once 'includes/home.php';
        break;
    case 'generate':
        include_once 'includes/generate.php';
        break;
    case 'templates':
        include_once 'includes/templates.php';
        break;
    case 'trending':
        include_once 'includes/trending.php';
        break;
    case 'about':
        include_once 'includes/about.php';
        break;
    case 'login':
        include_once 'includes/login.php';
        break;
    case 'register':
        include_once 'includes/register.php';
        break;
    case 'profile':
        // Redirect to login if not logged in
        if (!isLoggedIn()) {
            $_SESSION['error'] = "You must be logged in to view your profile.";
            header("Location: " . SITE_URL . "?page=login");
            exit;
        }
        include_once 'includes/profile.php';
        break;
    case 'favorites':
        // Redirect to login if not logged in
        if (!isLoggedIn()) {
            $_SESSION['error'] = "You must be logged in to view your favorites.";
            header("Location: " . SITE_URL . "?page=login");
            exit;
        }
        include_once 'includes/favorites.php';
        break;
    case 'collection':
        // Redirect to login if not logged in
        if (!isLoggedIn()) {
            $_SESSION['error'] = "You must be logged in to view your collection.";
            header("Location: " . SITE_URL . "?page=login");
            exit;
        }
        include_once 'includes/collection.php';
        break;
    case 'upload-template':
        // Redirect to login if not logged in
        if (!isLoggedIn()) {
            $_SESSION['error'] = "You must be logged in to upload templates.";
            header("Location: " . SITE_URL . "?page=login");
            exit;
        }
        include_once 'includes/upload-template.php';
        break;
    case 'gallery':
        include_once 'includes/gallery.php';
        break;
    case 'forgot-password':
        include_once 'includes/forgot-password.php';
        break;
    default:
        include_once 'includes/home.php';
        break;
}

// Footer
include_once 'includes/footer.php';
?> 