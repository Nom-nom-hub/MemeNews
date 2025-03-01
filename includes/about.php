<div class="page-header">
    <h1>About MemeNews</h1>
    <p>Where AI, Current Events, and Humor Collide</p>
</div>

<div class="about-section">
    <div class="about-content">
        <div class="about-intro">
            <div class="about-icon">
                <i class="fas fa-rocket"></i>
            </div>
            <div class="about-intro-text">
                <h2>Our Mission</h2>
                <p class="highlight-text">MemeNews bridges the gap between serious news and internet culture, giving you a fun way to engage with current events.</p>
            </div>
        </div>
        
        <p>In a world of information overload, we believe humor is one of the most powerful ways to connect with what's happening around us. Our AI-powered platform transforms trending headlines into shareable memes, making news more engaging and accessible to everyone.</p>
        
        <div class="feature-blocks">
            <div class="feature-block">
                <i class="fas fa-newspaper"></i>
                <h3>Always Fresh</h3>
                <p>Our platform continuously pulls the latest headlines from trusted news sources around the world, ensuring you're always meme-ing with the freshest content.</p>
            </div>
            
            <div class="feature-block">
                <i class="fas fa-robot"></i>
                <h3>AI-Powered</h3>
                <p>Advanced artificial intelligence analyzes news context and generates witty, relevant captions that transform serious headlines into humor gold.</p>
            </div>
            
            <div class="feature-block">
                <i class="fas fa-share-alt"></i>
                <h3>Easily Shareable</h3>
                <p>Create, customize, and share your news memes across all social platforms with just a few clicks, spreading laughs and awareness simultaneously.</p>
            </div>
        </div>
        
        <div class="about-details">
            <h2>How It Works</h2>
            <div class="workflow-steps">
                <div class="workflow-step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h4>News Gathering</h4>
                        <p>We fetch trending stories from various reliable sources across different categories.</p>
                    </div>
                </div>
                
                <div class="workflow-step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h4>AI Analysis</h4>
                        <p>Our AI analyzes headlines and content to understand context and sentiment.</p>
                    </div>
                </div>
                
                <div class="workflow-step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h4>Caption Generation</h4>
                        <p>The system creates witty captions that transform news into humor.</p>
                    </div>
                </div>
                
                <div class="workflow-step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h4>Template Matching</h4>
                        <p>Captions are paired with appropriate meme templates from our extensive library.</p>
                    </div>
                </div>
                
                <div class="workflow-step">
                    <div class="step-number">5</div>
                    <div class="step-content">
                        <h4>Your Touch</h4>
                        <p>You can customize and share the results on your favorite platforms.</p>
                    </div>
                </div>
            </div>
            
            <h2>Our Technology</h2>
            <div class="tech-stack">
                <div class="tech-item">
                    <i class="fas fa-server"></i>
                    <h4>Backend</h4>
                    <p>PHP powers our server-side processing</p>
                </div>
                
                <div class="tech-item">
                    <i class="fas fa-code"></i>
                    <h4>Frontend</h4>
                    <p>HTML5, CSS3, and JavaScript create our responsive interface</p>
                </div>
                
                <div class="tech-item">
                    <i class="fas fa-database"></i>
                    <h4>Database</h4>
                    <p>MySQL stores templates and user data securely</p>
                </div>
                
                <div class="tech-item">
                    <i class="fas fa-brain"></i>
                    <h4>AI</h4>
                    <p>Advanced NLP via OpenRouter API generates captions</p>
                </div>
                
                <div class="tech-item">
                    <i class="fas fa-rss"></i>
                    <h4>News</h4>
                    <p>NewsAPI integration delivers up-to-date headlines</p>
                </div>
            </div>
        </div>
        
        <div class="about-values">
            <h2>Our Values</h2>
            <ul class="values-list">
                <li><strong>Humor with Respect:</strong> We believe in funny, not offensive content</li>
                <li><strong>Information Accessibility:</strong> Making news more approachable through humor</li>
                <li><strong>Creative Freedom:</strong> Empowering users to express their perspectives</li>
                <li><strong>Privacy First:</strong> Your data stays yours - we don't track or sell information</li>
                <li><strong>Continuous Improvement:</strong> We're always enhancing our platform based on feedback</li>
            </ul>
        </div>
        
        <div class="privacy-terms">
            <h2>Privacy & Terms</h2>
            <p>MemeNews respects your privacy and data. We only collect necessary information to improve your experience and do not share your data with third parties. All memes generated using our platform are for personal use, and users are responsible for ensuring they comply with copyright and fair use guidelines when sharing content.</p>
        </div>
    </div>
    
    <div class="contact-section">
        <h2>Get In Touch</h2>
        <p>Have questions, suggestions, or feedback? We'd love to hear from you! Drop us a message and we'll get back to you as soon as possible.</p>
        
        <form class="contact-form">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="contact-name">Your Name</label>
                <input type="text" id="contact-name" name="name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="contact-email">Email Address</label>
                <input type="email" id="contact-email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="contact-subject">Subject</label>
                <input type="text" id="contact-subject" name="subject" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="contact-message">Message</label>
                <textarea id="contact-message" name="message" class="form-control" rows="5" required></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .about-section {
        margin-bottom: var(--spacing-xl);
    }
    
    .about-content {
        background-color: var(--color-card);
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-md);
        margin-bottom: var(--spacing-xl);
        box-shadow: var(--shadow-md);
    }
    
    .about-intro {
        display: flex;
        align-items: center;
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
        padding-bottom: var(--spacing-lg);
        border-bottom: 1px solid var(--color-light);
    }
    
    .about-icon {
        font-size: 3rem;
        color: var(--color-primary);
        background: linear-gradient(135deg, var(--color-primary-light), var(--color-primary));
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: var(--shadow-md);
        color: white;
        flex-shrink: 0;
    }
    
    .about-intro-text {
        flex: 1;
    }
    
    .highlight-text {
        font-size: 1.25rem;
        color: var(--color-text);
        font-weight: 500;
        line-height: 1.6;
    }
    
    .about-content h2 {
        color: var(--color-primary);
        margin-top: var(--spacing-xl);
        margin-bottom: var(--spacing-md);
        position: relative;
        padding-bottom: var(--spacing-xs);
    }
    
    .about-content h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(to right, var(--color-primary), var(--color-secondary));
        border-radius: var(--border-radius-full);
    }
    
    .about-content h2:first-child {
        margin-top: 0;
    }
    
    .feature-blocks {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--spacing-lg);
        margin: var(--spacing-xl) 0;
    }
    
    .feature-block {
        background-color: var(--color-background);
        border-radius: var(--border-radius-md);
        padding: var(--spacing-lg);
        text-align: center;
        box-shadow: var(--shadow-sm);
        transition: transform var(--transition-normal), box-shadow var(--transition-normal);
        border: 1px solid var(--color-light);
    }
    
    .feature-block:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .feature-block i {
        font-size: 2.5rem;
        color: var(--color-primary);
        margin-bottom: var(--spacing-sm);
    }
    
    .feature-block h3 {
        margin-bottom: var(--spacing-sm);
        color: var(--color-primary);
    }
    
    .workflow-steps {
        margin: var(--spacing-lg) 0;
    }
    
    .workflow-step {
        display: flex;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-md);
        align-items: flex-start;
    }
    
    .step-number {
        background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        flex-shrink: 0;
    }
    
    .step-content {
        flex: 1;
    }
    
    .step-content h4 {
        margin-bottom: var(--spacing-xs);
        color: var(--color-text);
    }
    
    .tech-stack {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: var(--spacing-md);
        margin: var(--spacing-lg) 0;
    }
    
    .tech-item {
        text-align: center;
        padding: var(--spacing-md);
        border-radius: var(--border-radius-md);
        background-color: var(--color-background);
        transition: transform var(--transition-fast);
    }
    
    .tech-item:hover {
        transform: translateY(-3px);
    }
    
    .tech-item i {
        font-size: 2rem;
        color: var(--color-primary);
        margin-bottom: var(--spacing-sm);
    }
    
    .tech-item h4 {
        margin-bottom: var(--spacing-xs);
        color: var(--color-text);
    }
    
    .tech-item p {
        font-size: 0.9rem;
        margin-bottom: 0;
    }
    
    .values-list {
        list-style: none;
        padding: 0;
        margin: var(--spacing-lg) 0;
    }
    
    .values-list li {
        margin-bottom: var(--spacing-md);
        padding-left: 30px;
        position: relative;
    }
    
    .values-list li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: var(--color-primary);
        font-weight: bold;
    }
    
    .privacy-terms {
        background-color: var(--color-background);
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-md);
        margin-top: var(--spacing-xl);
        border-left: 4px solid var(--color-primary);
    }
    
    .contact-section {
        background-color: var(--color-card);
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-md);
        box-shadow: var(--shadow-md);
    }
    
    .contact-section h2 {
        color: var(--color-primary);
        margin-bottom: var(--spacing-md);
        position: relative;
        padding-bottom: var(--spacing-xs);
    }
    
    .contact-section h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(to right, var(--color-primary), var(--color-secondary));
        border-radius: var(--border-radius-full);
    }
    
    .contact-form {
        margin-top: var(--spacing-lg);
    }
    
    @media screen and (max-width: 768px) {
        .about-intro {
            flex-direction: column;
            text-align: center;
        }
        
        .about-content h2::after {
            left: 50%;
            transform: translateX(-50%);
        }
        
        .contact-section h2::after {
            left: 50%;
            transform: translateX(-50%);
        }
        
        .workflow-step {
            flex-direction: column;
            text-align: center;
        }
        
        .step-number {
            margin: 0 auto var(--spacing-sm);
        }
        
        .values-list li {
            padding-left: 0;
            padding-top: 25px;
        }
        
        .values-list li::before {
            top: 0;
            left: 50%;
            transform: translateX(-50%);
        }
    }
</style> 