<div class="page-header">
    <h1>Create Account</h1>
    <p>Join MemeNews and start creating amazing memes</p>
</div>

<div class="auth-container">
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <div class="auth-form-container">
        <form class="auth-form" action="api/auth/register.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required autofocus>
                <small class="form-text">Choose a unique username (3-20 characters, letters and numbers only)</small>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <small class="form-text">Minimum 8 characters, include uppercase, lowercase and numbers</small>
            </div>
            
            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
            </div>
            
            <div class="form-group terms-checkbox">
                <input type="checkbox" id="terms" name="terms" value="1" required>
                <label for="terms">I agree to the <a href="#" data-toggle="modal" data-target="#termsModal">Terms of Service</a> and <a href="#" data-toggle="modal" data-target="#privacyModal">Privacy Policy</a></label>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </div>
        </form>
        
        <div class="auth-links">
            <p>Already have an account? <a href="<?php echo SITE_URL; ?>?page=login">Log In</a></p>
        </div>
    </div>
    
    <div class="auth-info">
        <h3>What You'll Get</h3>
        <ul class="feature-list">
            <li><i class="fas fa-save"></i> Personal collection of created memes</li>
            <li><i class="fas fa-heart"></i> Save your favorite templates</li>
            <li><i class="fas fa-upload"></i> Upload your own custom templates</li>
            <li><i class="fas fa-trophy"></i> Participate in weekly meme contests</li>
            <li><i class="fas fa-cog"></i> Customize text styles and effects</li>
            <li><i class="fas fa-search"></i> Search across all meme templates</li>
        </ul>
        
        <div class="community-join">
            <i class="fas fa-users community-icon"></i>
            <p>Join thousands of meme creators in our growing community!</p>
        </div>
    </div>
</div>

<!-- Terms Modal -->
<div class="modal" id="termsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms of Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4>1. User Content</h4>
                <p>When creating and sharing memes on our platform, you retain ownership of your original content. However, you grant MemeNews a non-exclusive license to use, display, and distribute your content on our services.</p>
                
                <h4>2. Acceptable Use</h4>
                <p>You agree not to use MemeNews to create or share content that is illegal, harmful, threatening, abusive, harassing, defamatory, or otherwise objectionable.</p>
                
                <h4>3. Content Removal</h4>
                <p>MemeNews reserves the right to remove any content that violates these terms or is deemed inappropriate.</p>
                
                <h4>4. Account Termination</h4>
                <p>MemeNews may terminate or suspend access to your account at any time, without prior notice, for conduct that violates these Terms.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Modal -->
<div class="modal" id="privacyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Privacy Policy</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4>1. Information Collection</h4>
                <p>We collect information you provide when you create an account (username, email) and information about how you use MemeNews.</p>
                
                <h4>2. How We Use Information</h4>
                <p>We use your information to provide and improve our services, communicate with you, and ensure security.</p>
                
                <h4>3. Information Sharing</h4>
                <p>We do not sell your personal information. We may share information with third-party service providers that help us operate MemeNews.</p>
                
                <h4>4. Data Security</h4>
                <p>We implement security measures to protect your personal information.</p>
                
                <h4>5. Your Choices</h4>
                <p>You can manage your account settings and notification preferences at any time.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .auth-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-xl);
        margin-bottom: var(--spacing-xxl);
    }
    
    .auth-form-container {
        background-color: var(--color-card);
        border-radius: var(--border-radius-md);
        padding: var(--spacing-lg);
        box-shadow: var(--shadow-md);
    }
    
    .auth-form .form-group {
        margin-bottom: var(--spacing-md);
    }
    
    .auth-form label {
        display: block;
        margin-bottom: var(--spacing-xs);
        font-weight: 500;
    }
    
    .auth-form .form-control {
        width: 100%;
        padding: var(--spacing-sm);
        border: 1px solid var(--color-border);
        border-radius: var(--border-radius-sm);
        font-size: 1rem;
        background-color: var(--color-input-bg);
        color: var(--color-text);
    }
    
    .auth-form .form-text {
        display: block;
        margin-top: 5px;
        font-size: 0.85rem;
        color: var(--color-text-muted);
    }
    
    .auth-form .btn-block {
        width: 100%;
    }
    
    .terms-checkbox {
        display: flex;
        align-items: flex-start;
    }
    
    .terms-checkbox input {
        margin-right: var(--spacing-xs);
        margin-top: 5px;
    }
    
    .auth-links {
        margin-top: var(--spacing-lg);
        text-align: center;
    }
    
    .auth-info {
        background-color: var(--color-card);
        border-radius: var(--border-radius-md);
        padding: var(--spacing-lg);
        box-shadow: var(--shadow-md);
    }
    
    .auth-info h3 {
        margin-bottom: var(--spacing-md);
        color: var(--color-primary);
        position: relative;
        padding-bottom: var(--spacing-xs);
    }
    
    .auth-info h3::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(to right, var(--color-primary), var(--color-secondary));
        border-radius: var(--border-radius-full);
    }
    
    .feature-list {
        list-style: none;
        padding: 0;
    }
    
    .feature-list li {
        margin-bottom: var(--spacing-md);
        display: flex;
        align-items: center;
    }
    
    .feature-list li i {
        color: var(--color-primary);
        margin-right: var(--spacing-sm);
        font-size: 1.2rem;
        width: 24px;
        text-align: center;
    }
    
    .community-join {
        margin-top: var(--spacing-xl);
        padding: var(--spacing-md);
        background-color: rgba(var(--color-primary-rgb), 0.1);
        border-radius: var(--border-radius-md);
        text-align: center;
    }
    
    .community-icon {
        font-size: 2.5rem;
        color: var(--color-primary);
        margin-bottom: var(--spacing-sm);
    }
    
    .alert {
        padding: var(--spacing-md);
        margin-bottom: var(--spacing-md);
        border-radius: var(--border-radius-sm);
        grid-column: 1 / -1;
    }
    
    .alert-danger {
        background-color: rgba(var(--color-danger-rgb), 0.1);
        border: 1px solid rgba(var(--color-danger-rgb), 0.2);
        color: var(--color-danger);
    }
    
    .modal-content {
        background-color: var(--color-card);
        color: var(--color-text);
    }
    
    .modal-header, .modal-footer {
        border-color: var(--color-border);
    }
    
    @media (max-width: 768px) {
        .auth-container {
            grid-template-columns: 1fr;
        }
        
        .auth-info {
            order: -1;
            margin-bottom: var(--spacing-lg);
        }
    }
</style> 