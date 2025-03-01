<?php
// Get current user
$user = getUserById(getCurrentUserId());
if (!$user) {
    // User not found in database, redirect to logout
    header("Location: " . SITE_URL . "/api/auth/logout.php");
    exit;
}

// Get user stats
$user_stats = [
    'memes_created' => 0,
    'favorites' => 0,
    'uploads' => 0
];

// Count user memes
$sql = "SELECT COUNT(*) as count FROM user_memes WHERE user_id = ?";
$result = $db->fetchOne($sql, [$user['id']]);
$user_stats['memes_created'] = $result ? $result['count'] : 0;

// Count user favorites
$sql = "SELECT COUNT(*) as count FROM user_favorites WHERE user_id = ?";
$result = $db->fetchOne($sql, [$user['id']]);
$user_stats['favorites'] = $result ? $result['count'] : 0;

// Count template uploads
$sql = "SELECT COUNT(*) as count FROM meme_templates WHERE uploaded_by = ?";
$result = $db->fetchOne($sql, [$user['id']]);
$user_stats['uploads'] = $result ? $result['count'] : 0;
?>

<div class="page-header">
    <h1>My Profile</h1>
    <p>Manage your account and view your meme stats</p>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="profile-container">
    <div class="profile-sidebar">
        <div class="profile-card">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <h2 class="profile-username"><?php echo $user['username']; ?></h2>
            <p class="profile-joined">Member since <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
            
            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-value"><?php echo $user_stats['memes_created']; ?></div>
                    <div class="stat-label">Memes Created</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $user_stats['favorites']; ?></div>
                    <div class="stat-label">Favorites</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $user_stats['uploads']; ?></div>
                    <div class="stat-label">Uploads</div>
                </div>
            </div>
        </div>
        
        <div class="profile-navigation">
            <a href="#account-settings" class="profile-nav-item active" data-target="account-settings">
                <i class="fas fa-cog"></i> Account Settings
            </a>
            <a href="#notification-settings" class="profile-nav-item" data-target="notification-settings">
                <i class="fas fa-bell"></i> Notification Settings
            </a>
            <a href="#privacy-settings" class="profile-nav-item" data-target="privacy-settings">
                <i class="fas fa-lock"></i> Privacy Settings
            </a>
            <a href="#theme-settings" class="profile-nav-item" data-target="theme-settings">
                <i class="fas fa-palette"></i> Theme Settings
            </a>
        </div>
    </div>
    
    <div class="profile-content">
        <div class="profile-section active" id="account-settings">
            <h3>Account Settings</h3>
            
            <form action="api/user/update_profile.php" method="post" class="profile-form">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo $user['username']; ?>" disabled>
                    <small class="form-text">Usernames cannot be changed to maintain consistency.</small>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" class="form-control" rows="3"><?php echo isset($user['bio']) ? $user['bio'] : ''; ?></textarea>
                    <small class="form-text">Tell us about yourself and your meme style.</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
            
            <div class="section-divider"></div>
            
            <h3>Change Password</h3>
            
            <form action="api/user/change_password.php" method="post" class="profile-form">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                    <small class="form-text">Minimum 8 characters, include uppercase, lowercase and numbers.</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
        
        <div class="profile-section" id="notification-settings">
            <h3>Notification Settings</h3>
            
            <form action="api/user/update_notifications.php" method="post" class="profile-form">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group switch-group">
                    <label class="switch-label">
                        Email Notifications
                        <div class="form-switch">
                            <input type="checkbox" name="email_notifications" id="email_notifications" <?php echo (isset($user['email_notifications']) && $user['email_notifications']) ? 'checked' : ''; ?>>
                            <label for="email_notifications"></label>
                        </div>
                    </label>
                    <small class="form-text">Receive email updates about your account and new features.</small>
                </div>
                
                <div class="form-group switch-group">
                    <label class="switch-label">
                        Trending News Alerts
                        <div class="form-switch">
                            <input type="checkbox" name="news_alerts" id="news_alerts" <?php echo (isset($user['news_alerts']) && $user['news_alerts']) ? 'checked' : ''; ?>>
                            <label for="news_alerts"></label>
                        </div>
                    </label>
                    <small class="form-text">Get notified when trending news matches your interests.</small>
                </div>
                
                <div class="form-group switch-group">
                    <label class="switch-label">
                        Competition Announcements
                        <div class="form-switch">
                            <input type="checkbox" name="contest_notifications" id="contest_notifications" <?php echo (isset($user['contest_notifications']) && $user['contest_notifications']) ? 'checked' : ''; ?>>
                            <label for="contest_notifications"></label>
                        </div>
                    </label>
                    <small class="form-text">Receive notifications about new meme competitions and contests.</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Preferences
                    </button>
                </div>
            </form>
        </div>
        
        <div class="profile-section" id="privacy-settings">
            <h3>Privacy Settings</h3>
            
            <form action="api/user/update_privacy.php" method="post" class="profile-form">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group switch-group">
                    <label class="switch-label">
                        Public Profile
                        <div class="form-switch">
                            <input type="checkbox" name="public_profile" id="public_profile" <?php echo (isset($user['public_profile']) && $user['public_profile']) ? 'checked' : ''; ?>>
                            <label for="public_profile"></label>
                        </div>
                    </label>
                    <small class="form-text">Allow others to view your profile and meme collection.</small>
                </div>
                
                <div class="form-group switch-group">
                    <label class="switch-label">
                        Show Username on Memes
                        <div class="form-switch">
                            <input type="checkbox" name="show_username_on_memes" id="show_username_on_memes" <?php echo (isset($user['show_username_on_memes']) && $user['show_username_on_memes']) ? 'checked' : ''; ?>>
                            <label for="show_username_on_memes"></label>
                        </div>
                    </label>
                    <small class="form-text">Display your username on memes you create in the gallery.</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </form>
            
            <div class="section-divider"></div>
            
            <h3>Data Management</h3>
            
            <div class="data-management">
                <div class="data-action">
                    <h4>Download Your Data</h4>
                    <p>Get a copy of all data associated with your account.</p>
                    <a href="api/user/export_data.php" class="btn btn-outline">
                        <i class="fas fa-download"></i> Export Data
                    </a>
                </div>
                
                <div class="data-action delete-account">
                    <h4>Delete Account</h4>
                    <p>Permanently delete your account and all associated data. This action cannot be undone.</p>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAccountModal">
                        <i class="fas fa-trash-alt"></i> Delete Account
                    </button>
                </div>
            </div>
        </div>
        
        <div class="profile-section" id="theme-settings">
            <h3>Theme Settings</h3>
            
            <form action="api/user/update_theme.php" method="post" class="profile-form">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="theme-options">
                    <div class="theme-option" data-theme="system">
                        <div class="theme-preview system-theme">
                            <div class="preview-day"></div>
                            <div class="preview-night"></div>
                        </div>
                        <div class="theme-info">
                            <h4>System Default</h4>
                            <p>Follow your device's appearance settings</p>
                        </div>
                        <div class="theme-select">
                            <input type="radio" name="theme" id="system-theme" value="system" <?php echo (!isset($user['theme']) || $user['theme'] == 'system') ? 'checked' : ''; ?>>
                            <label for="system-theme"></label>
                        </div>
                    </div>
                    
                    <div class="theme-option" data-theme="light">
                        <div class="theme-preview light-theme"></div>
                        <div class="theme-info">
                            <h4>Light Mode</h4>
                            <p>Bright and clean appearance</p>
                        </div>
                        <div class="theme-select">
                            <input type="radio" name="theme" id="light-theme" value="light" <?php echo (isset($user['theme']) && $user['theme'] == 'light') ? 'checked' : ''; ?>>
                            <label for="light-theme"></label>
                        </div>
                    </div>
                    
                    <div class="theme-option" data-theme="dark">
                        <div class="theme-preview dark-theme"></div>
                        <div class="theme-info">
                            <h4>Dark Mode</h4>
                            <p>Easy on the eyes in low light</p>
                        </div>
                        <div class="theme-select">
                            <input type="radio" name="theme" id="dark-theme" value="dark" <?php echo (isset($user['theme']) && $user['theme'] == 'dark') ? 'checked' : ''; ?>>
                            <label for="dark-theme"></label>
                        </div>
                    </div>
                </div>
                
                <div class="section-divider"></div>
                
                <h3>Font Size</h3>
                
                <div class="font-size-settings">
                    <div class="font-size-preview">
                        <p class="font-size-sample">The quick brown fox jumps over the lazy dog.</p>
                    </div>
                    
                    <div class="font-size-controls">
                        <button type="button" class="btn btn-sm" id="decrease-font">
                            <i class="fas fa-minus"></i>
                        </button>
                        <div class="font-size-value" id="font-size-value">100%</div>
                        <input type="hidden" name="font_size" id="font-size-input" value="100">
                        <button type="button" class="btn btn-sm" id="increase-font">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Theme Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal" id="deleteAccountModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="delete-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    This action <strong>cannot be undone</strong>. All your data will be permanently deleted.
                </p>
                <p>To confirm, please enter your password below:</p>
                
                <form id="delete-account-form" action="api/user/delete_account.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" form="delete-account-form" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i> Delete Permanently
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-container {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: var(--spacing-xl);
        margin-bottom: var(--spacing-xxl);
    }
    
    .profile-sidebar {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-lg);
    }
    
    .profile-card {
        background-color: var(--color-card);
        border-radius: var(--border-radius-md);
        padding: var(--spacing-lg);
        box-shadow: var(--shadow-md);
        text-align: center;
    }
    
    .profile-avatar {
        font-size: 4rem;
        color: var(--color-primary);
        margin-bottom: var(--spacing-md);
    }
    
    .profile-username {
        font-size: 1.5rem;
        margin-bottom: var(--spacing-xs);
    }
    
    .profile-joined {
        font-size: 0.9rem;
        color: var(--color-text-muted);
        margin-bottom: var(--spacing-md);
    }
    
    .profile-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: var(--spacing-sm);
        padding-top: var(--spacing-md);
        border-top: 1px solid var(--color-border);
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--color-primary);
    }
    
    .stat-label {
        font-size: 0.8rem;
        color: var(--color-text-muted);
    }
    
    .profile-navigation {
        background-color: var(--color-card);
        border-radius: var(--border-radius-md);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }
    
    .profile-nav-item {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        padding: var(--spacing-md);
        color: var(--color-text);
        text-decoration: none;
        border-left: 3px solid transparent;
        transition: background-color var(--transition-fast);
    }
    
    .profile-nav-item i {
        width: 20px;
        text-align: center;
    }
    
    .profile-nav-item:hover {
        background-color: rgba(var(--color-primary-rgb), 0.05);
    }
    
    .profile-nav-item.active {
        background-color: rgba(var(--color-primary-rgb), 0.1);
        border-left-color: var(--color-primary);
        font-weight: 500;
    }
    
    .profile-content {
        background-color: var(--color-card);
        border-radius: var(--border-radius-md);
        padding: var(--spacing-xl);
        box-shadow: var(--shadow-md);
    }
    
    .profile-section {
        display: none;
    }
    
    .profile-section.active {
        display: block;
    }
    
    .profile-section h3 {
        margin-bottom: var(--spacing-md);
        color: var(--color-primary);
        position: relative;
        padding-bottom: var(--spacing-xs);
    }
    
    .profile-section h3::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(to right, var(--color-primary), var(--color-secondary));
        border-radius: var(--border-radius-full);
    }
    
    .section-divider {
        height: 1px;
        background-color: var(--color-border);
        margin: var(--spacing-xl) 0;
    }
    
    .profile-form {
        margin-top: var(--spacing-lg);
    }
    
    .profile-form .form-group {
        margin-bottom: var(--spacing-lg);
    }
    
    .profile-form label {
        display: block;
        margin-bottom: var(--spacing-xs);
        font-weight: 500;
    }
    
    .profile-form .form-control {
        width: 100%;
        padding: var(--spacing-sm);
        border: 1px solid var(--color-border);
        border-radius: var(--border-radius-sm);
        font-size: 1rem;
        background-color: var(--color-input-bg);
        color: var(--color-text);
    }
    
    .profile-form .form-text {
        display: block;
        margin-top: 5px;
        font-size: 0.85rem;
        color: var(--color-text-muted);
    }
    
    .form-actions {
        margin-top: var(--spacing-lg);
    }
    
    .switch-group {
        display: flex;
        flex-direction: column;
    }
    
    .switch-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 500;
    }
    
    .form-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
    }
    
    .form-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .form-switch label {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--color-border);
        transition: .4s;
        border-radius: 24px;
    }
    
    .form-switch label:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    
    .form-switch input:checked + label {
        background-color: var(--color-primary);
    }
    
    .form-switch input:checked + label:before {
        transform: translateX(24px);
    }
    
    .data-management {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-lg);
        margin-top: var(--spacing-lg);
    }
    
    .data-action {
        background-color: var(--color-background);
        border-radius: var(--border-radius-md);
        padding: var(--spacing-lg);
        border: 1px solid var(--color-border);
    }
    
    .data-action h4 {
        margin-bottom: var(--spacing-sm);
    }
    
    .data-action p {
        margin-bottom: var(--spacing-md);
        color: var(--color-text-muted);
    }
    
    .delete-account {
        border-color: var(--color-danger);
    }
    
    .delete-warning {
        color: var(--color-danger);
        background-color: rgba(var(--color-danger-rgb), 0.1);
        padding: var(--spacing-md);
        border-radius: var(--border-radius-sm);
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }
    
    .theme-options {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: var(--spacing-lg);
        margin-top: var(--spacing-lg);
    }
    
    .theme-option {
        background-color: var(--color-background);
        border-radius: var(--border-radius-md);
        padding: var(--spacing-md);
        border: 2px solid var(--color-border);
        cursor: pointer;
        transition: border-color var(--transition-fast);
    }
    
    .theme-option:hover {
        border-color: var(--color-primary-light);
    }
    
    .theme-preview {
        height: 100px;
        border-radius: var(--border-radius-sm);
        margin-bottom: var(--spacing-sm);
    }
    
    .light-theme {
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
    }
    
    .dark-theme {
        background-color: #212121;
        border: 1px solid #424242;
    }
    
    .system-theme {
        display: flex;
    }
    
    .preview-day, .preview-night {
        width: 50%;
        height: 100%;
    }
    
    .preview-day {
        background-color: #ffffff;
        border-top-left-radius: var(--border-radius-sm);
        border-bottom-left-radius: var(--border-radius-sm);
        border: 1px solid #e0e0e0;
        border-right: none;
    }
    
    .preview-night {
        background-color: #212121;
        border-top-right-radius: var(--border-radius-sm);
        border-bottom-right-radius: var(--border-radius-sm);
        border: 1px solid #424242;
        border-left: none;
    }
    
    .theme-info {
        margin-bottom: var(--spacing-sm);
    }
    
    .theme-info h4 {
        margin-bottom: 5px;
    }
    
    .theme-info p {
        font-size: 0.9rem;
        color: var(--color-text-muted);
        margin: 0;
    }
    
    .theme-select {
        text-align: center;
    }
    
    .theme-select input[type="radio"] {
        display: none;
    }
    
    .theme-select label {
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid var(--color-border);
        position: relative;
        cursor: pointer;
    }
    
    .theme-select input[type="radio"]:checked + label::after {
        content: '';
        position: absolute;
        width: 10px;
        height: 10px;
        background-color: var(--color-primary);
        border-radius: 50%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    
    .font-size-settings {
        margin-top: var(--spacing-lg);
    }
    
    .font-size-preview {
        background-color: var(--color-background);
        border-radius: var(--border-radius-md);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-md);
        border: 1px solid var(--color-border);
    }
    
    .font-size-sample {
        margin: 0;
        text-align: center;
    }
    
    .font-size-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-lg);
    }
    
    .font-size-value {
        font-weight: bold;
        min-width: 50px;
        text-align: center;
    }
    
    @media (max-width: 992px) {
        .profile-container {
            grid-template-columns: 1fr;
        }
        
        .profile-sidebar {
            margin-bottom: var(--spacing-lg);
        }
        
        .theme-options {
            grid-template-columns: 1fr;
        }
        
        .data-management {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab navigation
        const navItems = document.querySelectorAll('.profile-nav-item');
        const sections = document.querySelectorAll('.profile-section');
        
        navItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('data-target');
                
                // Remove active class from all items and sections
                navItems.forEach(navItem => navItem.classList.remove('active'));
                sections.forEach(section => section.classList.remove('active'));
                
                // Add active class to clicked item and corresponding section
                this.classList.add('active');
                document.getElementById(targetId).classList.add('active');
            });
        });
        
        // Theme selection
        const themeOptions = document.querySelectorAll('.theme-option');
        
        themeOptions.forEach(option => {
            option.addEventListener('click', function() {
                const themeRadio = this.querySelector('input[type="radio"]');
                themeRadio.checked = true;
            });
        });
        
        // Font size controls
        const decreaseBtn = document.getElementById('decrease-font');
        const increaseBtn = document.getElementById('increase-font');
        const fontSizeValue = document.getElementById('font-size-value');
        const fontSizeInput = document.getElementById('font-size-input');
        const fontSizeSample = document.querySelector('.font-size-sample');
        
        let fontSize = <?php echo isset($user['font_size']) ? $user['font_size'] : 100; ?>;
        updateFontSize();
        
        decreaseBtn.addEventListener('click', function() {
            if (fontSize > 70) {
                fontSize -= 10;
                updateFontSize();
            }
        });
        
        increaseBtn.addEventListener('click', function() {
            if (fontSize < 150) {
                fontSize += 10;
                updateFontSize();
            }
        });
        
        function updateFontSize() {
            fontSizeValue.textContent = fontSize + '%';
            fontSizeSample.style.fontSize = fontSize + '%';
            fontSizeInput.value = fontSize;
        }
    });
</script> 