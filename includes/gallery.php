<?php
/**
 * Gallery Page
 * 
 * Displays a gallery of user-generated memes
 */

// Check if we have a specific filter
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'latest';
$page_num = isset($_GET['page_num']) ? intval($_GET['page_num']) : 1;
$items_per_page = 12;
$offset = ($page_num - 1) * $items_per_page;

// Set up query based on filter
switch ($filter) {
    case 'popular':
        $sql = "SELECT m.*, u.username 
                FROM user_memes m 
                LEFT JOIN users u ON m.user_id = u.id 
                WHERE m.is_public = 1 
                ORDER BY m.view_count DESC 
                LIMIT ? OFFSET ?";
        $params = [$items_per_page, $offset];
        $title = "Popular Memes";
        break;
    case 'trending':
        $sql = "SELECT m.*, u.username 
                FROM user_memes m 
                LEFT JOIN users u ON m.user_id = u.id 
                WHERE m.is_public = 1 
                ORDER BY (m.view_count + m.share_count * 2) DESC, m.created_at DESC 
                LIMIT ? OFFSET ?";
        $params = [$items_per_page, $offset];
        $title = "Trending Memes";
        break;
    default: // latest
        $sql = "SELECT m.*, u.username 
                FROM user_memes m 
                LEFT JOIN users u ON m.user_id = u.id 
                WHERE m.is_public = 1 
                ORDER BY m.created_at DESC 
                LIMIT ? OFFSET ?";
        $params = [$items_per_page, $offset];
        $title = "Latest Memes";
        break;
}

// Fetch memes
try {
    $memes = $db->fetchAll($sql, $params);
    
    // Get total count for pagination
    $count_sql = "SELECT COUNT(*) as total FROM user_memes WHERE is_public = 1";
    $result = $db->fetchOne($count_sql);
    $total_items = $result['total'] ?? 0;
    $total_pages = ceil($total_items / $items_per_page);
} catch (Exception $e) {
    // Handle error
    error_log("Error fetching gallery memes: " . $e->getMessage());
    $memes = [];
    $total_pages = 0;
}
?>

<section class="gallery-section section-padding">
    <div class="container">
        <h2 class="section-title"><?php echo $title; ?></h2>
        
        <div class="gallery-filters">
            <ul class="filter-tabs">
                <li <?php echo $filter == 'latest' ? 'class="active"' : ''; ?>>
                    <a href="<?php echo SITE_URL; ?>?page=gallery&filter=latest">Latest</a>
                </li>
                <li <?php echo $filter == 'popular' ? 'class="active"' : ''; ?>>
                    <a href="<?php echo SITE_URL; ?>?page=gallery&filter=popular">Popular</a>
                </li>
                <li <?php echo $filter == 'trending' ? 'class="active"' : ''; ?>>
                    <a href="<?php echo SITE_URL; ?>?page=gallery&filter=trending">Trending</a>
                </li>
            </ul>
        </div>
        
        <?php if (empty($memes)): ?>
            <div class="empty-state">
                <i class="fas fa-images"></i>
                <p>No memes available. Be the first to create and share one!</p>
                <a href="<?php echo SITE_URL; ?>?page=generate" class="btn btn-primary">Create Meme</a>
            </div>
        <?php else: ?>
            <div class="meme-gallery-grid">
                <?php foreach ($memes as $meme): ?>
                    <div class="gallery-item">
                        <div class="gallery-item-image">
                            <img src="<?php echo SITE_URL . '/' . $meme['file_path']; ?>" 
                                 alt="<?php echo htmlspecialchars($meme['top_text'] . ' ' . $meme['bottom_text']); ?>">
                        </div>
                        <div class="gallery-item-info">
                            <div class="stats">
                                <span><i class="fas fa-eye"></i> <?php echo number_format($meme['view_count']); ?></span>
                                <span><i class="fas fa-share-alt"></i> <?php echo number_format($meme['share_count']); ?></span>
                            </div>
                            <?php if (isset($meme['username']) && $meme['username']): ?>
                                <div class="author">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($meme['username']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="timestamp">
                                <i class="far fa-clock"></i> 
                                <?php echo date('M j, Y', strtotime($meme['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page_num > 1): ?>
                        <a href="<?php echo SITE_URL; ?>?page=gallery&filter=<?php echo $filter; ?>&page_num=<?php echo $page_num - 1; ?>" class="pagination-item">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page_num - 2); $i <= min($total_pages, $page_num + 2); $i++): ?>
                        <a href="<?php echo SITE_URL; ?>?page=gallery&filter=<?php echo $filter; ?>&page_num=<?php echo $i; ?>" 
                           class="pagination-item <?php echo $i == $page_num ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page_num < $total_pages): ?>
                        <a href="<?php echo SITE_URL; ?>?page=gallery&filter=<?php echo $filter; ?>&page_num=<?php echo $page_num + 1; ?>" class="pagination-item">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<script>
    // Add animation to gallery items
    document.addEventListener('DOMContentLoaded', function() {
        const galleryItems = document.querySelectorAll('.gallery-item');
        
        // Animate items as they appear in viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        galleryItems.forEach(item => {
            observer.observe(item);
        });
    });
</script> 