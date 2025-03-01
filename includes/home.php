<div class="hero-section">
    <div class="hero-content">
        <h1>Generate Hilarious Memes from <span class="highlight">Real-World News</span></h1>
        <p class="hero-description">Create, customize, and share AI-powered memes that capture the essence of trending stories.</p>
        <div class="hero-buttons">
            <a href="<?php echo SITE_URL; ?>?page=generate" class="btn btn-primary">Create a Meme</a>
            <a href="<?php echo SITE_URL; ?>?page=trending" class="btn btn-secondary">View Trending News</a>
        </div>
    </div>
    <div class="hero-image">
        <img src="<?php echo SITE_URL; ?>/img/hero-image.png" alt="MemeNews Generator" loading="lazy">
    </div>
</div>

<div class="features-section">
    <h2 class="section-title">How It Works</h2>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-newspaper"></i>
            </div>
            <h3>Choose News</h3>
            <p>Select from trending news stories or provide your own headline.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-robot"></i>
            </div>
            <h3>AI Generation</h3>
            <p>Our AI analyzes the news and generates witty captions for your meme.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-paint-brush"></i>
            </div>
            <h3>Customize</h3>
            <p>Edit text, fonts, colors, and image positioning to your liking.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-share-alt"></i>
            </div>
            <h3>Share & Download</h3>
            <p>Download your creation or share it directly on social media.</p>
        </div>
    </div>
</div>

<div class="trending-section">
    <h2 class="section-title">Latest Memes from Trending News</h2>
    <div class="trending-memes-grid">
        <?php
        $trendingNews = fetchTrendingNews(4);
        foreach ($trendingNews as $news) {
            if (isset($news['title'])) {
                // Get a random template for this news item
                $template = getRandomTemplate();
                
                // Template image path - use a real template
                $imagePath = SITE_URL . '/';
                
                // If we have a valid template, use its path
                if ($template && isset($template['file_path'])) {
                    $imagePath .= $template['file_path'];
                    $templateName = $template['name'];
                } else {
                    // Fallback to a specific template we know exists
                    $fallbackTemplates = array(
                        'img/templates/drake-hotline-bling.jpg',
                        'img/templates/distracted-boyfriend.jpg',
                        'img/templates/change-my-mind.jpg',
                        'img/templates/two-buttons.jpg',
                        'img/templates/placeholder-surprised-pikachu.jpg',
                        'img/templates/placeholder-ancient-aliens.jpg',
                        'img/templates/placeholder-roll-safe.jpg',
                        'img/templates/placeholder-one-does-not-simply.jpg',
                        'img/templates/placeholder-woman-yelling-at-cat.jpg',
                        'img/templates/placeholder-expanding-brain.jpg'
                    );
                    $randomIndex = array_rand($fallbackTemplates);
                    $imagePath .= $fallbackTemplates[$randomIndex];
                    $templateName = 'Meme Template';
                }
        ?>
        <div class="meme-card">
            <div class="meme-image">
                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($templateName . ' - ' . $news['title']); ?>" loading="lazy">
            </div>
            <div class="meme-info">
                <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                <p class="meme-source"><?php echo isset($news['source']['name']) ? htmlspecialchars($news['source']['name']) : 'Unknown source'; ?></p>
                <a href="<?php echo SITE_URL; ?>?page=generate&news=<?php echo urlencode($news['title']); ?>" class="btn btn-sm">Create Meme</a>
            </div>
        </div>
        <?php
            }
        }
        ?>
    </div>
    <div class="view-more-container">
        <a href="<?php echo SITE_URL; ?>?page=trending" class="btn btn-outline">View More Trending News</a>
    </div>
</div>

<div class="cta-section">
    <div class="cta-content">
        <h2>Ready to Create Your First Meme?</h2>
        <p>Generate hilarious memes based on the latest news and trending topics with our AI-powered meme generator.</p>
        <a href="<?php echo SITE_URL; ?>?page=generate" class="btn btn-primary">Get Started Now</a>
    </div>
</div> 