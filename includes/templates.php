<div class="page-header">
    <h1>Meme Templates</h1>
    <p>Browse our collection of meme templates or upload your own.</p>
    <div class="template-actions">
        <a href="fetch-templates.html" class="btn btn-accent" style="margin-right: 10px;">Fetch New Templates</a>
        <a href="api/fetch-templates.php?refresh=true&limit=50" class="btn btn-secondary" id="refresh-templates">Refresh Templates</a>
    </div>
</div>

<div class="template-filters">
    <form class="filter-form">
        <div class="filter-group">
            <label for="category-filter">Category:</label>
            <select id="category-filter" name="category" class="form-control">
                <option value="all" <?php echo (!isset($_GET['category']) || $_GET['category'] === 'all') ? 'selected' : ''; ?>>All Categories</option>
                <option value="reaction" <?php echo (isset($_GET['category']) && $_GET['category'] === 'reaction') ? 'selected' : ''; ?>>Reaction</option>
                <option value="animals" <?php echo (isset($_GET['category']) && $_GET['category'] === 'animals') ? 'selected' : ''; ?>>Animals</option>
                <option value="opinion" <?php echo (isset($_GET['category']) && $_GET['category'] === 'opinion') ? 'selected' : ''; ?>>Opinion</option>
                <option value="choices" <?php echo (isset($_GET['category']) && $_GET['category'] === 'choices') ? 'selected' : ''; ?>>Choices</option>
                <option value="intelligence" <?php echo (isset($_GET['category']) && $_GET['category'] === 'intelligence') ? 'selected' : ''; ?>>Intelligence</option>
                <option value="clever" <?php echo (isset($_GET['category']) && $_GET['category'] === 'clever') ? 'selected' : ''; ?>>Clever</option>
                <option value="warning" <?php echo (isset($_GET['category']) && $_GET['category'] === 'warning') ? 'selected' : ''; ?>>Warning</option>
                <option value="conspiracy" <?php echo (isset($_GET['category']) && $_GET['category'] === 'conspiracy') ? 'selected' : ''; ?>>Conspiracy</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label for="search-filter">Search:</label>
            <input type="text" id="search-filter" name="search" class="form-control" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Search templates...">
        </div>
        
        <div class="filter-group">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </div>
    </form>
</div>

<div class="upload-template-section">
    <h3>Upload Your Own Template</h3>
    <form action="<?php echo SITE_URL; ?>/api/upload-template.php" method="post" enctype="multipart/form-data" class="upload-form">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        
        <div class="form-group">
            <label for="template-name">Template Name:</label>
            <input type="text" id="template-name" name="name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="template-category">Category:</label>
            <select id="template-category" name="category" class="form-control">
                <option value="reaction">Reaction</option>
                <option value="animals">Animals</option>
                <option value="opinion">Opinion</option>
                <option value="choices">Choices</option>
                <option value="intelligence">Intelligence</option>
                <option value="clever">Clever</option>
                <option value="warning">Warning</option>
                <option value="conspiracy">Conspiracy</option>
                <option value="general">General</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="template-tags">Tags (comma separated):</label>
            <input type="text" id="template-tags" name="tags" class="form-control" placeholder="funny, trending, current">
        </div>
        
        <div class="form-group">
            <label for="template-image">Template Image:</label>
            <input type="file" id="template-image" name="image" class="form-control" accept="image/jpeg,image/png,image/gif" required>
            <div class="form-text">Max file size: <?php echo MAX_UPLOAD_SIZE / 1024 / 1024; ?>MB. Accepted formats: JPEG, PNG, GIF</div>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-accent">Upload Template</button>
        </div>
    </form>
</div>

<div class="templates-grid">
    <?php
    // Get the selected category
    $category = isset($_GET['category']) && $_GET['category'] !== 'all' ? sanitize($_GET['category']) : null;
    $search = isset($_GET['search']) ? sanitize($_GET['search']) : null;
    
    // Check if templates directory exists and has files
    $templatesDir = __DIR__ . '/../img/templates';
    $hasTemplateFiles = is_dir($templatesDir) && count(glob($templatesDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE)) > 0;
    
    try {
        $templates = [];
        
        // Query to get templates based on filters
        if ($hasTemplateFiles) {
            $params = [];
            $sql = "SELECT * FROM meme_templates WHERE 1=1";
            
            if ($category) {
                $sql .= " AND category = ?";
                $params[] = $category;
            }
            
            if ($search) {
                $sql .= " AND (name LIKE ? OR tags LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $sql .= " ORDER BY name ASC";
            
            try {
                $templates = $db->fetchAll($sql, $params);
            } catch (Exception $e) {
                // If database query fails, fall back to file system
                $templates = [];
            }
        }
        
        // If no templates in database or database not set up, scan directory
        if (empty($templates) && $hasTemplateFiles) {
            $files = glob($templatesDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
            
            foreach ($files as $file) {
                $basename = basename($file);
                $name = ucwords(str_replace(['-', '_', '.jpg', '.jpeg', '.png', '.gif'], [' ', ' ', '', '', '', ''], $basename));
                
                // Skip files that don't match search term
                if ($search && stripos($name, $search) === false) {
                    continue;
                }
                
                // Determine category from filename
                $tempCategory = 'general';
                if (stripos($basename, 'reaction') !== false || 
                    stripos($basename, 'drake') !== false || 
                    stripos($basename, 'pikachu') !== false) {
                    $tempCategory = 'reaction';
                } elseif (stripos($basename, 'button') !== false) {
                    $tempCategory = 'choices';
                } elseif (stripos($basename, 'mind') !== false) {
                    $tempCategory = 'opinion';
                } elseif (stripos($basename, 'brain') !== false) {
                    $tempCategory = 'intelligence';
                }
                
                // Skip if category filter is active and doesn't match
                if ($category && $tempCategory !== $category) {
                    continue;
                }
                
                $templates[] = [
                    'id' => md5($basename),
                    'name' => $name,
                    'file_path' => 'img/templates/' . $basename,
                    'category' => $tempCategory
                ];
            }
        }
        
        if (empty($templates) && !$hasTemplateFiles) {
            // Show message if no templates found
            echo '<div class="alert alert-info">No templates found. Please click the "Fetch New Templates" button at the top of the page to add templates.</div>';
            
            // Display some placeholder examples
            $placeholderTemplates = [
                ['name' => 'Drake Hotline Bling', 'category' => 'reaction'],
                ['name' => 'Distracted Boyfriend', 'category' => 'reaction'],
                ['name' => 'Two Buttons', 'category' => 'choices'],
                ['name' => 'Change My Mind', 'category' => 'opinion'],
                ['name' => 'Expanding Brain', 'category' => 'intelligence']
            ];
            
            foreach ($placeholderTemplates as $template) {
                ?>
                <div class="template-card">
                    <div class="template-image">
                        <img src="<?php echo SITE_URL; ?>/img/placeholder.jpg" alt="<?php echo htmlspecialchars($template['name']); ?>" loading="lazy">
                    </div>
                    <div class="template-info">
                        <h3><?php echo htmlspecialchars($template['name']); ?></h3>
                        <span class="template-category"><?php echo htmlspecialchars($template['category']); ?></span>
                        <a href="<?php echo SITE_URL; ?>?page=generate" class="btn btn-primary">Use Template</a>
                    </div>
                </div>
                <?php
            }
        } elseif (empty($templates) && $hasTemplateFiles) {
            // Show message if filters return no results
            echo '<div class="alert alert-info">No templates found matching your filters. Please try different criteria.</div>';
        } else {
            // Display actual templates
            foreach ($templates as $template) {
                ?>
                <div class="template-card">
                    <div class="template-image">
                        <img src="<?php echo SITE_URL . '/' . htmlspecialchars($template['file_path']); ?>" alt="<?php echo htmlspecialchars($template['name']); ?>" loading="lazy">
                    </div>
                    <div class="template-info">
                        <h3><?php echo htmlspecialchars($template['name']); ?></h3>
                        <span class="template-category"><?php echo htmlspecialchars($template['category']); ?></span>
                        <a href="<?php echo SITE_URL; ?>?page=generate&template=<?php echo $template['id']; ?>" class="btn btn-primary">Use Template</a>
                    </div>
                </div>
                <?php
            }
        }
    } catch (Exception $e) {
        // Show a friendlier error if something goes wrong
        echo '<div class="alert alert-warning">Error loading templates. Please try refreshing the page or click the "Fetch New Templates" button.</div>';
    }
    ?>
</div>

<style>
    .template-filters {
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
    
    .upload-template-section {
        margin: var(--spacing-xl) 0;
        background-color: var(--color-card);
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-md);
    }
    
    .upload-form {
        margin-top: var(--spacing-md);
    }
    
    .templates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: var(--spacing-lg);
    }
    
    .template-card {
        background-color: var(--color-card);
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        transition: all var(--transition-normal);
    }
    
    .template-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .template-image {
        position: relative;
        overflow: hidden;
        height: 0;
        padding-bottom: 100%; /* Square aspect ratio */
    }
    
    .template-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform var(--transition-normal);
    }
    
    .template-card:hover .template-image img {
        transform: scale(1.05);
    }
    
    .template-info {
        padding: var(--spacing-md);
    }
    
    .template-info h3 {
        margin-bottom: var(--spacing-sm);
        font-size: 1.1rem;
    }
    
    .template-category {
        display: inline-block;
        background-color: var(--color-accent-light);
        color: var(--color-accent);
        padding: 0.25rem 0.5rem;
        border-radius: var(--border-radius-sm);
        font-size: 0.8rem;
        margin-bottom: var(--spacing-md);
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
    
    .alert-warning {
        background-color: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.2);
    }
    
    @media screen and (max-width: 768px) {
        .templates-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        }
        
        .filter-form {
            flex-direction: column;
        }
        
        .filter-group {
            width: 100%;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener for the refresh templates button
    const refreshBtn = document.getElementById('refresh-templates');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('This will refresh all templates and may take a moment. Continue?')) {
                const loadingMsg = document.createElement('div');
                loadingMsg.className = 'alert alert-info';
                loadingMsg.textContent = 'Refreshing templates... This may take a moment.';
                
                document.querySelector('.page-header').appendChild(loadingMsg);
                
                fetch(this.getAttribute('href'))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Successfully refreshed ' + data.templates.length + ' templates!');
                            window.location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Error: ' + error.message);
                    })
                    .finally(() => {
                        loadingMsg.remove();
                    });
            }
        });
    }
});
</script> 