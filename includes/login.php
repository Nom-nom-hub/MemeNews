<div class="page-header">
    <h1>Log In</h1>
    <p>Access your MemeNews account</p>
</div>

<div class="auth-container">
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <div class="auth-form-container">
        <form class="auth-form" action="api/auth/login.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" class="form-control" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <div class="form-group remember-me">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Remember me</label>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Log In
                </button>
            </div>
        </form>
        
        <div class="auth-links">
            <p>Don't have an account? <a href="<?php echo SITE_URL; ?>?page=register">Register</a></p>
            <p><a href="<?php echo SITE_URL; ?>?page=forgot-password">Forgot Password?</a></p>
        </div>
    </div>
    
    <div class="auth-info">
        <h3>Why Create an Account?</h3>
        <ul class="feature-list">
            <li><i class="fas fa-save"></i> Save your favorite meme templates</li>
            <li><i class="fas fa-folder"></i> Build a personal meme collection</li>
            <li><i class="fas fa-share-alt"></i> Share your creations on social media</li>
            <li><i class="fas fa-bell"></i> Get notified about trending news in your interests</li>
            <li><i class="fas fa-users"></i> Join meme contests and community events</li>
        </ul>
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
    
    .auth-form .btn-block {
        width: 100%;
    }
    
    .remember-me {
        display: flex;
        align-items: center;
    }
    
    .remember-me input {
        margin-right: var(--spacing-xs);
    }
    
    .remember-me label {
        margin-bottom: 0;
    }
    
    .auth-links {
        margin-top: var(--spacing-lg);
        text-align: center;
    }
    
    .auth-links p {
        margin-bottom: var(--spacing-xs);
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
    
    .alert {
        padding: var(--spacing-md);
        margin-bottom: var(--spacing-md);
        border-radius: var(--border-radius-sm);
    }
    
    .alert-danger {
        background-color: rgba(var(--color-danger-rgb), 0.1);
        border: 1px solid rgba(var(--color-danger-rgb), 0.2);
        color: var(--color-danger);
    }
    
    .alert-success {
        background-color: rgba(var(--color-success-rgb), 0.1);
        border: 1px solid rgba(var(--color-success-rgb), 0.2);
        color: var(--color-success);
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