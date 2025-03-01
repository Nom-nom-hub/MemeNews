<?php
/**
 * Utility Functions
 * Contains general utility functions used throughout the application
 */

/**
 * Sanitizes user input to prevent XSS attacks
 * 
 * @param string $data The input data to sanitize
 * @return string Sanitized data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Generates a CSRF token
 * 
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Validates a CSRF token
 * 
 * @param string $token Token to validate
 * @return bool Whether token is valid
 */
function validateCSRFToken($token) {
    if (!isset($_SESSION[CSRF_TOKEN_NAME]) || $token !== $_SESSION[CSRF_TOKEN_NAME]) {
        return false;
    }
    return true;
}

/**
 * Redirects to a specified page
 * 
 * @param string $page Page to redirect to
 * @return void
 */
function redirect($page) {
    header("Location: " . SITE_URL . "/" . $page);
    exit;
}

/**
 * Fetches trending news from the News API
 * 
 * @param int $count Number of news items to fetch
 * @param string $category News category
 * @return array News articles
 */
function fetchTrendingNews($count = 10, $category = 'general') {
    try {
        $url = NEWS_API_URL . "top-headlines?country=us&category={$category}&apiKey=" . NEWS_API_KEY . "&pageSize={$count}";
        $context = stream_context_create([
            'http' => [
                'timeout' => 5, // 5 second timeout
                'user_agent' => 'MemeNews/1.0'
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            // Handle network error
            error_log("Failed to fetch news: Network error or API timeout");
            return getMockNewsArticles($count);
        }
        
        $news = json_decode($response, true);
        
        if (!isset($news['articles']) || empty($news['articles'])) {
            // Handle empty or invalid response
            error_log("News API returned no articles or invalid response: " . print_r($news, true));
            return getMockNewsArticles($count);
        }
        
        return $news['articles'];
    } catch (Exception $e) {
        error_log("Exception in fetchTrendingNews: " . $e->getMessage());
        return getMockNewsArticles($count);
    }
}

/**
 * Returns mock news articles for when the API fails
 * 
 * @param int $count Number of mock articles to return
 * @return array Mock news articles
 */
function getMockNewsArticles($count = 10) {
    $mockArticles = [
        [
            'title' => 'Global Tech Conference Announces Revolutionary AI Product',
            'description' => 'Industry leaders unveil groundbreaking artificial intelligence technology that promises to transform multiple sectors.',
            'source' => ['name' => 'Tech Daily'],
            'publishedAt' => date('Y-m-d H:i:s'),
            'urlToImage' => SITE_URL . '/img/placeholder.jpg'
        ],
        [
            'title' => 'Scientists Discover New Species in Remote Rainforest',
            'description' => 'Research team identifies previously unknown plant and animal species during expedition to unexplored regions.',
            'source' => ['name' => 'Science Today'],
            'publishedAt' => date('Y-m-d H:i:s'),
            'urlToImage' => SITE_URL . '/img/placeholder.jpg'
        ],
        [
            'title' => 'Stock Markets Reach Record High Amid Economic Recovery',
            'description' => 'Global markets surge as economic indicators show strong post-pandemic growth and consumer confidence.',
            'source' => ['name' => 'Financial Times'],
            'publishedAt' => date('Y-m-d H:i:s'),
            'urlToImage' => SITE_URL . '/img/placeholder.jpg'
        ],
        [
            'title' => 'New Study Reveals Benefits of Mediterranean Diet',
            'description' => 'Comprehensive research confirms significant health improvements associated with Mediterranean eating patterns.',
            'source' => ['name' => 'Health Journal'],
            'publishedAt' => date('Y-m-d H:i:s'),
            'urlToImage' => SITE_URL . '/img/placeholder.jpg'
        ],
        [
            'title' => 'Major Film Studio Announces Sequel to Blockbuster Movie',
            'description' => 'Fans celebrate news of upcoming sequel to last year\'s highest-grossing film, with original cast set to return.',
            'source' => ['name' => 'Entertainment Weekly'],
            'publishedAt' => date('Y-m-d H:i:s'),
            'urlToImage' => SITE_URL . '/img/placeholder.jpg'
        ],
    ];
    
    // Duplicate articles if we need more than we have
    while (count($mockArticles) < $count) {
        $mockArticles = array_merge($mockArticles, $mockArticles);
    }
    
    // Return only the requested number
    return array_slice($mockArticles, 0, $count);
}

/**
 * Generates a meme caption using OpenRouter AI
 * 
 * @param string $newsHeadline The news headline to base the meme on
 * @return string Generated meme caption
 */
function generateMemeCaption($newsHeadline) {
    $url = OPENROUTER_API_URL . '/chat/completions';
    $data = [
        'model' => OPENROUTER_MODEL,
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a witty meme caption generator. Create a funny, creative caption for a meme based on this news headline. Keep it short, punchy, and humorous.'
            ],
            [
                'role' => 'user',
                'content' => $newsHeadline
            ]
        ],
        'max_tokens' => 50
    ];
    
    $options = [
        'http' => [
            'header' => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . OPENROUTER_API_KEY
            ],
            'method' => 'POST',
            'content' => json_encode($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        return "Failed to generate caption";
    }
    
    $result = json_decode($response, true);
    
    if (!isset($result['choices'][0]['message']['content'])) {
        return "Failed to generate caption";
    }
    
    return trim($result['choices'][0]['message']['content']);
}

/**
 * Gets a random meme template from the database
 * 
 * @param string $category Template category
 * @return array|null Template data or null if none found
 */
function getRandomTemplate($category = 'general') {
    global $db;
    
    // Try to get a template from the specified category
    $sql = "SELECT * FROM meme_templates WHERE category = ? ORDER BY RAND() LIMIT 1";
    $template = $db->fetchOne($sql, [$category]);
    
    // If no template found in the specified category, get any random template
    if (!$template) {
        $sql = "SELECT * FROM meme_templates ORDER BY RAND() LIMIT 1";
        $template = $db->fetchOne($sql);
    }
    
    // If still no template found from database, create a template object with a known valid file
    if (!$template) {
        $fallbackTemplates = array(
            'img/templates/drake-hotline-bling.jpg',
            'img/templates/distracted-boyfriend.jpg',
            'img/templates/change-my-mind.jpg',
            'img/templates/two-buttons.jpg',
            'img/templates/placeholder-surprised-pikachu.jpg',
            'img/templates/placeholder-ancient-aliens.jpg',
            'img/templates/placeholder-roll-safe.jpg',
            'img/templates/placeholder-one-does-not-simply.jpg'
        );
        $randomIndex = array_rand($fallbackTemplates);
        $filePath = $fallbackTemplates[$randomIndex];
        
        $template = array(
            'id' => 0,
            'name' => 'Fallback Template',
            'file_path' => $filePath,
            'category' => $category,
            'tags' => $category,
            'width' => 600,
            'height' => 500
        );
    }
    
    return $template;
}

/**
 * Logs user activity
 * 
 * @param int $user_id User ID (0 for guests)
 * @param string $action Action performed
 * @param string $details Additional details
 * @return void
 */
function logActivity($user_id, $action, $details = '') {
    global $db;
    
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    $sql = "INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?)";
    
    $db->query($sql, [$user_id, $action, $details, $ip, $user_agent]);
}

/**
 * Checks if user is logged in
 * 
 * @return bool Whether user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Gets current user ID
 * 
 * @return int|null User ID or null if not logged in
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Gets current username
 * 
 * @return string|null Username or null if not logged in
 */
function getCurrentUsername() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

/**
 * Checks if user session has timed out
 * 
 * @return bool Whether session has timed out
 */
function isSessionExpired() {
    $max_lifetime = 3600; // 1 hour
    
    if (!isset($_SESSION['last_activity'])) {
        return true;
    }
    
    if (time() - $_SESSION['last_activity'] > $max_lifetime) {
        return true;
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    return false;
}

/**
 * Attempts to authenticate user via remember me cookie
 * 
 * @return bool Whether authentication was successful
 */
function authenticateFromRememberCookie() {
    global $db;
    
    if (!isset($_COOKIE['remember_user']) || !isset($_COOKIE['remember_token'])) {
        return false;
    }
    
    $user_id = $_COOKIE['remember_user'];
    $token = $_COOKIE['remember_token'];
    
    // Get token from database
    $sql = "SELECT * FROM user_tokens WHERE user_id = ? AND expires > NOW()";
    $stored = $db->fetchOne($sql, [$user_id]);
    
    if (!$stored) {
        return false;
    }
    
    // Verify token
    if (!password_verify($token, $stored['token'])) {
        return false;
    }
    
    // Get user
    $sql = "SELECT * FROM users WHERE id = ?";
    $user = $db->fetchOne($sql, [$user_id]);
    
    if (!$user) {
        return false;
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['last_activity'] = time();
    
    // Log activity
    logActivity($user['id'], 'auto_login', 'User automatically logged in via remember me cookie');
    
    return true;
}

/**
 * Gets user by ID
 * 
 * @param int $user_id User ID
 * @return array|null User data or null if not found
 */
function getUserById($user_id) {
    global $db;
    
    $sql = "SELECT * FROM users WHERE id = ?";
    return $db->fetchOne($sql, [$user_id]);
}

/**
 * Checks if a user has favorited a template
 * 
 * @param int $user_id User ID
 * @param int $template_id Template ID
 * @return bool Whether template is favorited
 */
function isTemplateFavorited($user_id, $template_id) {
    global $db;
    
    $sql = "SELECT COUNT(*) as count FROM user_favorites WHERE user_id = ? AND template_id = ?";
    $result = $db->fetchOne($sql, [$user_id, $template_id]);
    
    return $result['count'] > 0;
}

/**
 * Adds a template to user's favorites
 * 
 * @param int $user_id User ID
 * @param int $template_id Template ID
 * @return bool Whether operation was successful
 */
function addTemplateToFavorites($user_id, $template_id) {
    global $db;
    
    // Check if already favorited
    if (isTemplateFavorited($user_id, $template_id)) {
        return true;
    }
    
    $sql = "INSERT INTO user_favorites (user_id, template_id, created_at) VALUES (?, ?, NOW())";
    $result = $db->query($sql, [$user_id, $template_id]);
    
    if ($result) {
        logActivity($user_id, 'favorite_template', "User favorited template ID: $template_id");
        return true;
    }
    
    return false;
}

/**
 * Removes a template from user's favorites
 * 
 * @param int $user_id User ID
 * @param int $template_id Template ID
 * @return bool Whether operation was successful
 */
function removeTemplateFromFavorites($user_id, $template_id) {
    global $db;
    
    $sql = "DELETE FROM user_favorites WHERE user_id = ? AND template_id = ?";
    $result = $db->query($sql, [$user_id, $template_id]);
    
    if ($result) {
        logActivity($user_id, 'unfavorite_template', "User removed template ID: $template_id from favorites");
        return true;
    }
    
    return false;
}

/**
 * Gets user's favorite templates
 * 
 * @param int $user_id User ID
 * @param int $limit Number of templates to fetch
 * @param int $offset Offset for pagination
 * @return array Templates data
 */
function getUserFavoriteTemplates($user_id, $limit = 10, $offset = 0) {
    global $db;
    
    $sql = "SELECT t.* FROM meme_templates t
            JOIN user_favorites f ON t.id = f.template_id
            WHERE f.user_id = ?
            ORDER BY f.created_at DESC
            LIMIT ? OFFSET ?";
    
    return $db->fetchAll($sql, [$user_id, $limit, $offset]);
}

/**
 * Gets user's created memes
 * 
 * @param int $user_id User ID
 * @param int $limit Number of memes to fetch
 * @param int $offset Offset for pagination
 * @return array Memes data
 */
function getUserMemes($user_id, $limit = 10, $offset = 0) {
    global $db;
    
    $sql = "SELECT m.*, t.name as template_name FROM user_memes m
            LEFT JOIN meme_templates t ON m.template_id = t.id
            WHERE m.user_id = ?
            ORDER BY m.created_at DESC
            LIMIT ? OFFSET ?";
    
    return $db->fetchAll($sql, [$user_id, $limit, $offset]);
}

/**
 * Generates a unique filename for uploads
 * 
 * @param string $extension File extension
 * @return string Unique filename
 */
function generateUniqueFilename($extension) {
    return uniqid('meme_') . '_' . time() . '.' . $extension;
}

/**
 * Checks if file upload is valid
 * 
 * @param array $file File data from $_FILES
 * @return bool|string True if valid, error message if not
 */
function validateImageUpload($file) {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "Upload failed with error code: " . $file['error'];
    }
    
    // Check file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return "File is too large. Maximum size is " . (MAX_UPLOAD_SIZE / 1024 / 1024) . "MB";
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $fileType = $finfo->file($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        return "Invalid file type. Allowed types: JPEG, PNG, GIF";
    }
    
    return true;
}
?> 