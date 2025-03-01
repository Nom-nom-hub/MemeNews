<?php
// Start session
session_start();

// Include configuration and functions
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = 'You must be logged in to upload templates.';
    header('Location: /login');
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    $_SESSION['error'] = 'Invalid request. Please try again.';
    header('Location: /upload-template');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user ID
    $user_id = getCurrentUserId();
    
    // Sanitize and validate input
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $tags = trim($_POST['tags'] ?? '');
    $text_positions = isset($_POST['text_positions']) ? $_POST['text_positions'] : [];
    $terms_agree = isset($_POST['terms_agree']) ? (bool)$_POST['terms_agree'] : false;
    
    // Validation
    $errors = [];
    
    // Validate name
    if (empty($name)) {
        $errors[] = 'Template name is required.';
    } elseif (strlen($name) > 100) {
        $errors[] = 'Template name cannot exceed 100 characters.';
    }
    
    // Validate description
    if (empty($description)) {
        $errors[] = 'Template description is required.';
    } elseif (strlen($description) > 500) {
        $errors[] = 'Template description cannot exceed 500 characters.';
    }
    
    // Validate category
    if ($category_id <= 0) {
        $errors[] = 'Please select a valid category.';
    } else {
        // Check if category exists
        $sql = "SELECT id FROM template_categories WHERE id = ?";
        $category = $db->fetchOne($sql, [$category_id]);
        if (!$category) {
            $errors[] = 'Selected category does not exist.';
        }
    }
    
    // Validate tags
    if (strlen($tags) > 200) {
        $errors[] = 'Tags cannot exceed 200 characters.';
    }
    
    // Validate terms agreement
    if (!$terms_agree) {
        $errors[] = 'You must agree to the terms and guidelines.';
    }
    
    // Validate file upload
    if (!isset($_FILES['image']) || $_FILES['image']['error'] != UPLOAD_ERR_OK) {
        if (isset($_FILES['image'])) {
            $upload_error_codes = [
                UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.',
                UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
            ];
            $error_code = $_FILES['image']['error'];
            $errors[] = 'File upload failed: ' . ($upload_error_codes[$error_code] ?? 'Unknown error.');
        } else {
            $errors[] = 'Template image is required.';
        }
    } else {
        // Check file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = 'Invalid file type. Only JPG, PNG, and GIF files are allowed.';
        }
        
        // Check file size (5MB max)
        $max_size = 5 * 1024 * 1024; // 5MB in bytes
        if ($_FILES['image']['size'] > $max_size) {
            $errors[] = 'File is too large. Maximum file size is 5MB.';
        }
        
        // Additional image validation (dimensions, etc.) could be added here
    }
    
    // If there are errors, redirect back with error messages
    if (!empty($errors)) {
        $_SESSION['error'] = implode('<br>', $errors);
        header('Location: /upload-template');
        exit;
    }
    
    // Process the image
    $upload_dir = '../../uploads/templates/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate a unique filename
    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $file_name = uniqid('template_') . '_' . time() . '.' . $file_extension;
    $file_path = $upload_dir . $file_name;
    
    // Move the uploaded file
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
        $_SESSION['error'] = 'Failed to upload image. Please try again.';
        header('Location: /upload-template');
        exit;
    }
    
    // Get image dimensions
    $image_info = getimagesize($file_path);
    $width = $image_info[0];
    $height = $image_info[1];
    
    // Generate relative URL for storage
    $image_url = '/uploads/templates/' . $file_name;
    
    // Prepare text positions for storage (JSON)
    $text_positions_json = json_encode($text_positions);
    
    // Start a transaction
    $db->beginTransaction();
    
    try {
        // Insert template into database
        $sql = "INSERT INTO templates (
                    user_id, 
                    category_id, 
                    name, 
                    description, 
                    image_url, 
                    width, 
                    height, 
                    text_positions, 
                    status, 
                    created_at, 
                    updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        // Status: 'pending' for moderation, 'approved' if auto-approved
        $status = 'pending';
        
        $result = $db->execute($sql, [
            $user_id,
            $category_id,
            $name,
            $description,
            $image_url,
            $width,
            $height,
            $text_positions_json,
            $status
        ]);
        
        if (!$result) {
            throw new Exception('Failed to insert template into database.');
        }
        
        // Get the new template ID
        $template_id = $db->lastInsertId();
        
        // Process tags if any
        if (!empty($tags)) {
            $tag_array = array_map('trim', explode(',', $tags));
            $tag_array = array_unique($tag_array);
            
            foreach ($tag_array as $tag_name) {
                if (empty($tag_name)) continue;
                
                // Check if tag exists
                $sql = "SELECT id FROM tags WHERE name = ?";
                $tag = $db->fetchOne($sql, [$tag_name]);
                
                $tag_id = 0;
                if ($tag) {
                    $tag_id = $tag['id'];
                } else {
                    // Create new tag
                    $sql = "INSERT INTO tags (name, created_at) VALUES (?, NOW())";
                    $db->execute($sql, [$tag_name]);
                    $tag_id = $db->lastInsertId();
                }
                
                // Associate tag with template
                if ($tag_id > 0) {
                    $sql = "INSERT INTO template_tags (template_id, tag_id) VALUES (?, ?)";
                    $db->execute($sql, [$template_id, $tag_id]);
                }
            }
        }
        
        // Commit the transaction
        $db->commit();
        
        // Log activity
        logActivity($user_id, 'upload_template', "Uploaded template '$name' (ID: $template_id)");
        
        $_SESSION['success'] = 'Template uploaded successfully! It will be available after review.';
        header('Location: /upload-template');
        exit;
    } catch (Exception $e) {
        // Rollback the transaction
        $db->rollback();
        
        // Delete the uploaded file
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
        header('Location: /upload-template');
        exit;
    }
} else {
    // Not a POST request
    header('Location: /upload-template');
    exit;
} 