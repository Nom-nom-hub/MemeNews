<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - AI-Powered Meme Generator</title>
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="Generate hilarious memes based on trending news with AI technology">
    <meta name="keywords" content="meme generator, AI memes, news memes, trending memes">
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo SITE_URL; ?>/img/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/normalize.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/styles.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Additional header styles for mobile */
        @media (max-width: 576px) {
            .header-right {
                gap: var(--spacing-sm);
            }
            
            .header-auth-buttons .btn {
                padding: var(--spacing-xs) var(--spacing-sm);
                font-size: 0.8rem;
            }
            
            .header-auth-buttons .btn i {
                margin-right: 0;
            }
            
            .header-auth-buttons .register-btn span {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <header class="site-header">
            <div class="container">
                <div class="logo-container">
                    <a href="<?php echo SITE_URL; ?>" class="logo-link">
                        <img src="<?php echo SITE_URL; ?>/img/logo.png" alt="<?php echo SITE_NAME; ?> Logo" class="logo" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="text-logo">
                            <i class="fas fa-laugh-squint"></i>
                            <span><?php echo SITE_NAME; ?></span>
                        </div>
                    </a>
                </div>
                
                <div class="header-right">
                    <?php if (!isLoggedIn()): ?>
                        <div class="header-auth-buttons">
                            <a href="<?php echo SITE_URL; ?>?page=login" class="btn btn-primary btn-sm login-btn">
                                <i class="fas fa-sign-in-alt"></i> <span>Log In</span>
                            </a>
                            <a href="<?php echo SITE_URL; ?>?page=register" class="btn btn-outline btn-sm register-btn">
                                <i class="fas fa-user-plus"></i> <span>Register</span>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="theme-toggle">
                        <button id="theme-toggle-btn" aria-label="Toggle dark/light mode">
                            <i class="fas fa-moon"></i>
                            <i class="fas fa-sun"></i>
                        </button>
                    </div>
                </div>
            </div>
        </header>
        
        <div class="main-content">
            <div class="container">
            <!-- Main content starts here --> 