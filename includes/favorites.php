<?php
// Get current user
$user_id = getCurrentUserId();
if (!$user_id) {
    // User not logged in, redirect to login
    header("Location: " . SITE_URL . "/login");
    exit;
}

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Get total favorites count
$count_sql = "SELECT COUNT(*) as total FROM user_favorites WHERE user_id = ?";
$count_result = $db->fetchOne($count_sql, [$user_id]);
$total_favorites = $count_result ? $count_result['total'] : 0;
$total_pages = ceil($total_favorites / $per_page);

// Get user favorites with template info
$sql = "SELECT 
            uf.id, 
            uf.template_id, 
            mt.name, 
            mt.file_path, 
            mt.category, 
            mt.tags,
            uf.created_at
        FROM 
            user_favorites uf
        JOIN 
            meme_templates mt ON uf.template_id = mt.id
        WHERE 
            uf.user_id = ? 
        ORDER BY 
            uf.created_at DESC
        LIMIT ? OFFSET ?";

$favorites = $db->fetchAll($sql, [$user_id, $per_page, $offset]);
?>

<div class="page-header">
    <h1>My Favorites</h1>
    <p>Templates you've saved for quick access</p>
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

<div class="favorites-container">
    <?php if (empty($favorites)): ?>
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-heart-broken"></i>
            </div>
            <h2>No Favorites Yet</h2>
            <p>You haven't added any templates to your favorites.</p>
            <a href="<?php echo SITE_URL; ?>/templates" class="btn btn-primary">
                <i class="fas fa-search"></i> Browse Templates
            </a>
        </div>
    <?php else: ?>
        <div class="favorites-grid">
            <?php foreach ($favorites as $favorite): ?>
                <div class="favorite-card" data-id="<?php echo $favorite['id']; ?>">
                    <div class="favorite-actions">
                        <button class="btn-icon remove-favorite" data-template-id="<?php echo $favorite['template_id']; ?>" title="Remove from favorites">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    
                    <a href="<?php echo SITE_URL; ?>/generate?template=<?php echo $favorite['template_id']; ?>" class="favorite-link">
                        <div class="favorite-image">
                            <img src="<?php echo SITE_URL . '/' . $favorite['file_path']; ?>" alt="<?php echo $favorite['name']; ?>">
                        </div>
                        
                        <div class="favorite-info">
                            <h3 class="favorite-name"><?php echo $favorite['name']; ?></h3>
                            
                            <?php if (!empty($favorite['category'])): ?>
                                <div class="favorite-category">
                                    <span class="category-badge"><?php echo ucfirst($favorite['category']); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($favorite['tags'])): ?>
                                <div class="favorite-tags">
                                    <?php 
                                    $tags = explode(',', $favorite['tags']);
                                    foreach (array_slice($tags, 0, 3) as $tag): ?>
                                        <span class="tag-badge"><?php echo trim($tag); ?></span>
                                    <?php endforeach; ?>
                                    
                                    <?php if (count($tags) > 3): ?>
                                        <span class="tag-badge more-tags">+<?php echo count($tags) - 3; ?> more</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="favorite-date">
                                <i class="far fa-clock"></i> Saved <?php echo date('M d, Y', strtotime($favorite['created_at'])); ?>
                            </div>
                        </div>
                    </a>
                    
                    <div class="favorite-buttons">
                        <a href="<?php echo SITE_URL; ?>/generate?template=<?php echo $favorite['template_id']; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Create Meme
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="<?php echo SITE_URL; ?>/favorites?page=<?php echo $page - 1; ?>" class="pagination-item">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                <?php endif; ?>
                
                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                
                if ($start_page > 1): ?>
                    <a href="<?php echo SITE_URL; ?>/favorites?page=1" class="pagination-item">1</a>
                    <?php if ($start_page > 2): ?>
                        <span class="pagination-ellipsis">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="<?php echo SITE_URL; ?>/favorites?page=<?php echo $i; ?>" class="pagination-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                        <span class="pagination-ellipsis">...</span>
                    <?php endif; ?>
                    <a href="<?php echo SITE_URL; ?>/favorites?page=<?php echo $total_pages; ?>" class="pagination-item">
                        <?php echo $total_pages; ?>
                    </a>
                <?php endif; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="<?php echo SITE_URL; ?>/favorites?page=<?php echo $page + 1; ?>" class="pagination-item">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
    .favorites-container {
        margin-bottom: var(--spacing-xxl);
    }
    
    .favorites-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
    }
    
    .favorite-card {
        position: relative;
        background-color: var(--color-card);
        border-radius: var(--border-radius-md);
        box-shadow: var(--shadow-md);
        transition: transform var(--transition-fast), box-shadow var(--transition-fast);
        overflow: hidden;
    }
    
    .favorite-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .favorite-actions {
        position: absolute;
        top: var(--spacing-sm);
        right: var(--spacing-sm);
        z-index: 2;
        opacity: 0;
        transition: opacity var(--transition-fast);
    }
    
    .favorite-card:hover .favorite-actions {
        opacity: 1;
    }
    
    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 0, 0, 0.7);
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color var(--transition-fast);
    }
    
    .btn-icon:hover {
        background-color: var(--color-danger);
    }
    
    .favorite-link {
        display: block;
        text-decoration: none;
        color: var(--color-text);
    }
    
    .favorite-image {
        height: 180px;
        overflow: hidden;
    }
    
    .favorite-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform var(--transition-fast);
    }
    
    .favorite-card:hover .favorite-image img {
        transform: scale(1.05);
    }
    
    .favorite-info {
        padding: var(--spacing-md);
    }
    
    .favorite-name {
        font-size: 1.1rem;
        margin-bottom: var(--spacing-xs);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .favorite-category {
        margin-bottom: var(--spacing-xs);
    }
    
    .category-badge {
        display: inline-block;
        background-color: var(--color-primary-light);
        color: var(--color-primary-dark);
        padding: 2px 8px;
        border-radius: var(--border-radius-full);
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .favorite-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-bottom: var(--spacing-xs);
    }
    
    .tag-badge {
        display: inline-block;
        background-color: var(--color-background);
        border: 1px solid var(--color-border);
        color: var(--color-text-muted);
        padding: 0 6px;
        border-radius: var(--border-radius-full);
        font-size: 0.75rem;
    }
    
    .more-tags {
        background-color: var(--color-border);
    }
    
    .favorite-date {
        font-size: 0.8rem;
        color: var(--color-text-muted);
    }
    
    .favorite-buttons {
        padding: 0 var(--spacing-md) var(--spacing-md);
        display: flex;
        justify-content: center;
    }
    
    .empty-state {
        text-align: center;
        background-color: var(--color-card);
        border-radius: var(--border-radius-md);
        padding: var(--spacing-xxl) var(--spacing-xl);
        box-shadow: var(--shadow-md);
    }
    
    .empty-icon {
        font-size: 4rem;
        color: var(--color-text-muted);
        margin-bottom: var(--spacing-lg);
    }
    
    .empty-state h2 {
        margin-bottom: var(--spacing-md);
    }
    
    .empty-state p {
        margin-bottom: var(--spacing-lg);
        color: var(--color-text-muted);
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--spacing-xs);
        margin-top: var(--spacing-xl);
    }
    
    .pagination-item {
        display: inline-block;
        min-width: 36px;
        height: 36px;
        line-height: 36px;
        text-align: center;
        background-color: var(--color-card);
        border-radius: var(--border-radius-md);
        color: var(--color-text);
        text-decoration: none;
        transition: background-color var(--transition-fast);
        padding: 0 var(--spacing-sm);
    }
    
    .pagination-item:hover {
        background-color: var(--color-primary-light);
    }
    
    .pagination-item.active {
        background-color: var(--color-primary);
        color: white;
    }
    
    .pagination-ellipsis {
        display: inline-block;
        min-width: 36px;
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .favorites-grid {
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        }
    }
    
    @media (max-width: 480px) {
        .favorites-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle remove from favorites
        const removeButtons = document.querySelectorAll('.remove-favorite');
        
        removeButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const templateId = this.getAttribute('data-template-id');
                const favoriteCard = this.closest('.favorite-card');
                
                if (confirm('Are you sure you want to remove this template from your favorites?')) {
                    // Send request to remove from favorites
                    fetch('api/user/remove_favorite.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            'csrf_token': '<?php echo generateCSRFToken(); ?>',
                            'template_id': templateId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the card with animation
                            favoriteCard.style.opacity = '0';
                            favoriteCard.style.transform = 'scale(0.8)';
                            
                            setTimeout(() => {
                                favoriteCard.remove();
                                
                                // Check if there are no favorites left
                                const remainingFavorites = document.querySelectorAll('.favorite-card');
                                if (remainingFavorites.length === 0) {
                                    location.reload(); // Reload to show empty state
                                }
                            }, 300);
                        } else {
                            alert('Error removing template from favorites: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while removing the template from favorites.');
                    });
                }
            });
        });
    });
</script> 