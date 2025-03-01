<div class="page-header">
    <h1>Trending News</h1>
    <p>Stay updated with the latest news and create memes from trending headlines.</p>
</div>

<div class="trending-filters">
    <form class="filter-form">
        <div class="filter-group">
            <label for="category-filter">News Category:</label>
            <select id="category-filter" name="category" class="form-control">
                <option value="general" <?php echo (!isset($_GET['category']) || $_GET['category'] === 'general') ? 'selected' : ''; ?>>General</option>
                <option value="business" <?php echo (isset($_GET['category']) && $_GET['category'] === 'business') ? 'selected' : ''; ?>>Business</option>
                <option value="technology" <?php echo (isset($_GET['category']) && $_GET['category'] === 'technology') ? 'selected' : ''; ?>>Technology</option>
                <option value="entertainment" <?php echo (isset($_GET['category']) && $_GET['category'] === 'entertainment') ? 'selected' : ''; ?>>Entertainment</option>
                <option value="sports" <?php echo (isset($_GET['category']) && $_GET['category'] === 'sports') ? 'selected' : ''; ?>>Sports</option>
                <option value="science" <?php echo (isset($_GET['category']) && $_GET['category'] === 'science') ? 'selected' : ''; ?>>Science</option>
                <option value="health" <?php echo (isset($_GET['category']) && $_GET['category'] === 'health') ? 'selected' : ''; ?>>Health</option>
            </select>
        </div>
        
        <div class="filter-group">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </div>
    </form>
</div>

<div class="trending-news-grid">
    <?php
    // Get the selected category
    $category = isset($_GET['category']) ? sanitize($_GET['category']) : 'general';
    
    // Fetch trending news
    $trendingNews = fetchTrendingNews(20, $category);
    
    if (empty($trendingNews)) {
        echo '<div class="alert alert-info">No trending news found. Please try again later or select a different category.</div>';
    } else {
        foreach ($trendingNews as $news) {
            if (isset($news['title'])) {
    ?>
    <div class="news-card">
        <div class="news-image">
            <?php if (isset($news['urlToImage']) && !empty($news['urlToImage'])): ?>
                <img src="<?php echo htmlspecialchars($news['urlToImage']); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" loading="lazy">
            <?php else: ?>
                <?php
                // Get a random template for this news item when there's no image
                $template = getRandomTemplate();
                
                // Template image path - use a real template
                $imagePath = SITE_URL . '/';
                
                // If we have a valid template, use its path
                if ($template && isset($template['file_path'])) {
                    $imagePath .= $template['file_path'];
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
                }
                ?>
                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" loading="lazy">
            <?php endif; ?>
        </div>
        
        <div class="news-content">
            <h3><?php echo htmlspecialchars($news['title']); ?></h3>
            
            <?php if (isset($news['description']) && !empty($news['description'])): ?>
                <p class="news-description"><?php echo htmlspecialchars($news['description']); ?></p>
            <?php endif; ?>
            
            <div class="news-meta">
                <span class="news-source"><?php echo isset($news['source']['name']) ? htmlspecialchars($news['source']['name']) : 'Unknown source'; ?></span>
                <?php if (isset($news['publishedAt'])): ?>
                    <span class="news-date"><?php echo date('M j, Y', strtotime($news['publishedAt'])); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="news-actions">
                <a href="<?php echo SITE_URL; ?>?page=generate&news=<?php echo urlencode($news['title']); ?>" class="btn btn-primary">Create Meme</a>
                
                <?php if (isset($news['url']) && !empty($news['url'])): ?>
                    <a href="<?php echo htmlspecialchars($news['url']); ?>" target="_blank" class="btn btn-outline">Read Full Article</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
            }
        }
    }
    ?>
</div>

<style>
    .trending-filters {
        margin-bottom: var(--spacing-xl);
        background-color: var(--color-card);
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-md);
    }
    
    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: var(--spacing-md);
        align-items: flex-end;
    }
    
    .filter-group {
        flex: 1;
        min-width: 200px;
    }
    
    .filter-group label {
        display: block;
        margin-bottom: var(--spacing-sm);
        font-weight: 500;
    }
    
    .trending-news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: var(--spacing-xl);
    }
    
    .news-card {
        background-color: var(--color-card);
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        transition: all var(--transition-normal);
    }
    
    .news-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-lg);
    }
    
    .news-image {
        position: relative;
        overflow: hidden;
        height: 0;
        padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
    }
    
    .news-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform var(--transition-normal);
    }
    
    .news-card:hover .news-image img {
        transform: scale(1.05);
    }
    
    .news-content {
        padding: var(--spacing-lg);
    }
    
    .news-content h3 {
        margin-bottom: var(--spacing-md);
        line-height: 1.3;
    }
    
    .news-description {
        color: var(--color-text-muted);
        margin-bottom: var(--spacing-lg);
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .news-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: var(--spacing-md);
        color: var(--color-text-muted);
        font-size: 0.875rem;
    }
    
    .news-actions {
        display: flex;
        gap: var(--spacing-md);
    }
    
    .alert {
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-md);
        margin-bottom: var(--spacing-lg);
    }
    
    .alert-info {
        background-color: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }
    
    @media screen and (max-width: 768px) {
        .trending-news-grid {
            grid-template-columns: 1fr;
        }
        
        .filter-form {
            flex-direction: column;
        }
        
        .filter-group {
            width: 100%;
        }
    }
</style> 