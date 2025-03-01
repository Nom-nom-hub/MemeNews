<?php
/**
 * Forgot Password Page
 * 
 * Allows users to request a password reset
 */

// If user is already logged in, redirect to home
if (isLoggedIn()) {
    header("Location: " . SITE_URL);
    exit;
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generateCSRFToken();
}
?>

<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-form-container">
                <h2>Forgot Your Password?</h2>
                <p>Enter your email address below and we'll send you instructions to reset your password.</p>
                
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
                
                <form action="<?php echo SITE_URL; ?>/api/auth/forgot-password.php" method="post" class="auth-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
                    </div>
                </form>
                
                <div class="auth-links">
                    <p>Remembered your password? <a href="<?php echo SITE_URL; ?>?page=login">Back to Login</a></p>
                </div>
            </div>
            
            <div class="auth-info">
                <h3>Password Recovery</h3>
                <p>If you've forgotten your password, don't worry! We'll help you get back into your account securely.</p>
                
                <h4>How it works:</h4>
                <ol>
                    <li>Enter your email address in the form</li>
                    <li>Check your inbox for a password reset link</li>
                    <li>Click the link and create a new password</li>
                    <li>Log in with your new password</li>
                </ol>
                
                <p><strong>Note:</strong> The password reset link will expire in 24 hours for security reasons.</p>
                
                <p>If you don't receive an email, please check your spam folder or <a href="<?php echo SITE_URL; ?>?page=contact">contact our support team</a>.</p>
            </div>
        </div>
    </div>
</section>

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
        background-color: var(--color-background);
        color: var(--color-text);
    }
    
    .auth-form .btn-block {
        width: 100%;
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
    
    .auth-info h4 {
        margin: var(--spacing-md) 0 var(--spacing-sm);
    }
    
    .auth-info ol {
        padding-left: var(--spacing-lg);
        margin-bottom: var(--spacing-md);
    }
    
    .auth-info ol li {
        margin-bottom: var(--spacing-xs);
    }
    
    @media (max-width: 992px) {
        .auth-container {
            grid-template-columns: 1fr;
        }
        
        .auth-info {
            order: 2;
        }
    }
</style> 