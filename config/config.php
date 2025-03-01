<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '8889'); // Added MAMP MySQL port
define('DB_SOCKET', '/Applications/MAMP/tmp/mysql/mysql.sock'); // MAMP MySQL socket
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'meme_news');

// Application Configuration
define('SITE_NAME', 'MemeNews');
define('SITE_URL', 'http://localhost:8000');
define('UPLOAD_DIR', 'uploads/');
define('MAX_UPLOAD_SIZE', 5242880); // 5MB

// OpenRouter API Configuration
define('OPENROUTER_API_KEY', 'YOUR OPENROUTER API');
define('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1');
define('OPENROUTER_MODEL', 'openai/gpt-3.5-turbo'); // Default model

// News API Configuration
define('NEWS_API_KEY', 'YOUR NEWS API');
define('NEWS_API_URL', 'https://newsapi.org/v2/');

// Security Configuration
define('CSRF_TOKEN_NAME', 'memenews_csrf_token');
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('PASSWORD_PEPPER', 'change_this_to_a_random_string');

// Debug Mode (set to false in production)
define('DEBUG_MODE', true);
?> 
