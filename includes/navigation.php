<nav class="main-nav">
    <div class="nav-container">
        <ul class="nav-menu" id="mobile-menu">
            <li class="<?php echo $page === 'home' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>
            <li class="<?php echo $page === 'generate' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>?page=generate">
                    <i class="fas fa-magic"></i> Generate Meme
                </a>
            </li>
            <li class="<?php echo $page === 'templates' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>?page=templates">
                    <i class="fas fa-images"></i> Templates
                </a>
            </li>
            <li class="<?php echo $page === 'trending' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>?page=trending">
                    <i class="fas fa-chart-line"></i> Trending News
                </a>
            </li>
            <li class="<?php echo $page === 'gallery' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>?page=gallery">
                    <i class="fas fa-photo-video"></i> Meme Gallery
                </a>
            </li>
            <li class="<?php echo $page === 'about' ? 'active' : ''; ?>">
                <a href="<?php echo SITE_URL; ?>?page=about">
                    <i class="fas fa-info-circle"></i> About
                </a>
            </li>
        </ul>
        
        <div class="user-nav">
            <?php if (isLoggedIn()): ?>
                <div class="user-dropdown">
                    <button class="user-dropdown-btn">
                        <i class="fas fa-user-circle"></i> <?php echo getCurrentUsername(); ?>
                        <i class="fas fa-caret-down"></i>
                    </button>
                    <div class="user-dropdown-content">
                        <a href="<?php echo SITE_URL; ?>?page=profile">
                            <i class="fas fa-id-card"></i> My Profile
                        </a>
                        <a href="<?php echo SITE_URL; ?>?page=collection">
                            <i class="fas fa-folder"></i> My Collections
                        </a>
                        <a href="<?php echo SITE_URL; ?>?page=favorites">
                            <i class="fas fa-heart"></i> My Favorites
                        </a>
                        <a href="<?php echo SITE_URL; ?>?page=upload-template">
                            <i class="fas fa-upload"></i> Upload Template
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo SITE_URL; ?>/api/auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="mobile-nav-toggle">
            <button id="mobile-menu-toggle" aria-expanded="false" aria-label="Toggle mobile navigation">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</nav>

<style>
    /* Main navigation container */
    .main-nav {
        width: 100%;
        padding: var(--spacing-sm) 0;
        background-color: var(--color-card);
        border-radius: var(--border-radius-lg);
        margin-bottom: var(--spacing-lg);
        box-shadow: var(--shadow-sm);
    }
    
    /* Container for navigation elements */
    .nav-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0 var(--spacing-md);
    }
    
    /* Main menu styling */
    .nav-menu {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        flex-wrap: wrap;
    }
    
    .nav-menu li {
        position: relative;
        margin-right: var(--spacing-md);
    }
    
    .nav-menu li:after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background-color: var(--color-primary);
        transition: width var(--transition-fast);
    }
    
    .nav-menu li:hover:after,
    .nav-menu li.active:after {
        width: 80%;
    }
    
    .nav-menu a {
        display: flex;
        align-items: center;
        color: var(--color-text);
        text-decoration: none;
        padding: var(--spacing-sm) var(--spacing-sm);
        transition: color var(--transition-fast);
        white-space: nowrap;
    }
    
    .nav-menu a i {
        margin-right: var(--spacing-sm);
        font-size: 1rem;
    }
    
    .nav-menu a:hover,
    .nav-menu li.active a {
        color: var(--color-primary);
    }
    
    /* User navigation styling */
    .user-nav {
        display: flex;
        align-items: center;
        margin-left: auto;
    }
    
    /* User dropdown for logged in users */
    .user-dropdown {
        position: relative;
        display: inline-block;
    }
    
    .user-dropdown-btn {
        background-color: transparent;
        border: none;
        color: var(--color-text);
        padding: var(--spacing-sm) var(--spacing-md);
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: var(--spacing-xs);
        border-radius: var(--border-radius-md);
        transition: background-color var(--transition-fast);
    }
    
    .user-dropdown-btn:hover {
        background-color: rgba(var(--color-primary-rgb), 0.1);
    }
    
    .user-dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        min-width: 240px;
        z-index: 1000;
        background-color: var(--color-card);
        border-radius: var(--border-radius-md);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }
    
    .user-dropdown-content a {
        color: var(--color-text);
        padding: var(--spacing-md);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        transition: background-color var(--transition-fast);
    }
    
    .user-dropdown-content a i {
        width: 16px;
        text-align: center;
    }
    
    .user-dropdown-content a:hover {
        background-color: rgba(var(--color-primary-rgb), 0.1);
    }
    
    .dropdown-divider {
        height: 1px;
        background-color: var(--color-border);
        margin: var(--spacing-xs) 0;
    }
    
    .user-dropdown:hover .user-dropdown-content {
        display: block;
    }
    
    /* Mobile menu toggle */
    .mobile-nav-toggle {
        display: none;
    }
    
    #mobile-menu-toggle {
        background: transparent;
        border: none;
        color: var(--color-text);
        font-size: 1.5rem;
        cursor: pointer;
        padding: var(--spacing-sm);
    }
    
    /* Responsive styles */
    @media (max-width: 992px) {
        .nav-container {
            justify-content: space-between;
            padding: var(--spacing-sm);
        }
        
        .mobile-nav-toggle {
            display: block;
            order: 3;
        }
        
        .nav-menu {
            display: none;
            flex-direction: column;
            width: 100%;
            order: 4;
            margin-top: var(--spacing-md);
        }
        
        .nav-menu.active {
            display: flex;
        }
        
        .nav-menu li {
            width: 100%;
            margin-right: 0;
            margin-bottom: var(--spacing-sm);
        }
        
        .nav-menu li:after {
            display: none;
        }
        
        .nav-menu a {
            padding: var(--spacing-md);
            border-radius: var(--border-radius-md);
        }
        
        .nav-menu a:hover {
            background-color: rgba(var(--color-primary-rgb), 0.1);
        }
        
        .user-nav {
            order: 2;
        }
    }
    
    @media (max-width: 768px) {
        .user-dropdown-content {
            right: 0;
            width: 100%;
            min-width: 200px;
        }
    }
</style>

<!-- Mobile navigation is handled by main.js --> 