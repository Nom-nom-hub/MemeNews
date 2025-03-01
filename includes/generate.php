<div class="page-header">
    <h1>Generate AI-Powered Meme</h1>
    <p>Select a news headline, choose a template, and let our AI create a hilarious meme for you.</p>
</div>

<div class="meme-generator-section">
    <div class="meme-generator-form">
        <form id="meme-form">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="news-select">Select News Headline</label>
                <select id="news-select" name="news_headline" class="form-control">
                    <option value="">-- Select a news headline --</option>
                    <?php
                    $trendingNews = fetchTrendingNews(10);
                    foreach ($trendingNews as $news) {
                        if (isset($news['title'])) {
                            $selected = '';
                            if (isset($_GET['news']) && $_GET['news'] === $news['title']) {
                                $selected = 'selected';
                            }
                            echo '<option value="' . htmlspecialchars($news['title']) . '" ' . $selected . '>' . htmlspecialchars($news['title']) . '</option>';
                        }
                    }
                    ?>
                </select>
                <div class="form-text">Or enter your own headline:</div>
                <input type="text" id="custom-headline" name="custom_headline" class="form-control" placeholder="Enter a custom news headline">
            </div>
            
            <div class="form-group">
                <label for="template-select">Choose Meme Template</label>
                <select id="template-select" name="template_id" class="form-control" required>
                    <option value="">-- Select a template --</option>
                    <?php
                    // Query to get all meme templates
                    $sql = "SELECT * FROM meme_templates ORDER BY name ASC";
                    $templates = $db->fetchAll($sql);
                    
                    foreach ($templates as $template) {
                        echo '<option value="' . $template['id'] . '">' . htmlspecialchars($template['name']) . '</option>';
                    }
                    ?>
                </select>
                <div class="form-text">Or upload your own image:</div>
                <input type="file" id="custom-image" name="custom_image" class="form-control" accept="image/jpeg,image/png,image/gif">
            </div>
            
            <div class="form-group">
                <button type="button" id="generate-caption-btn" class="btn btn-accent">Generate AI Caption</button>
            </div>
            
            <div class="form-group">
                <label for="top-text">Top Text</label>
                <input type="text" id="top-text" name="top_text" class="form-control" placeholder="Enter top text">
            </div>
            
            <div class="form-group">
                <label for="bottom-text">Bottom Text</label>
                <input type="text" id="bottom-text" name="bottom_text" class="form-control" placeholder="Enter bottom text">
            </div>
            
            <div class="form-group">
                <label for="font-select">Font Style</label>
                <select id="font-select" name="font" class="form-control">
                    <option value="Impact">Impact (Classic)</option>
                    <option value="Arial">Arial</option>
                    <option value="Comic Sans MS">Comic Sans MS</option>
                    <option value="Helvetica">Helvetica</option>
                    <option value="Tahoma">Tahoma</option>
                    <option value="'Bebas Neue', sans-serif">Bebas Neue</option>
                    <option value="'Anton', sans-serif">Anton</option>
                    <option value="'Bangers', cursive">Bangers</option>
                    <option value="'Permanent Marker', cursive">Permanent Marker</option>
                    <option value="'Bungee', cursive">Bungee</option>
                    <option value="'Press Start 2P', cursive">Press Start 2P</option>
                    <option value="'Creepster', cursive">Creepster</option>
                    <option value="'Lobster', cursive">Lobster</option>
                    <option value="'Special Elite', cursive">Special Elite</option>
                    <option value="'VT323', monospace">VT323</option>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="text-color">Text Color</label>
                    <input type="color" id="text-color" name="text_color" class="form-control" value="#ffffff">
                </div>
                <div class="form-group col-md-6">
                    <label for="outline-color">Outline Color</label>
                    <input type="color" id="outline-color" name="outline_color" class="form-control" value="#000000">
                </div>
            </div>
            
            <div class="form-group">
                <label for="text-size">Text Size</label>
                <div class="text-size-slider">
                    <input type="range" id="text-size" name="text_size" min="20" max="80" value="40" class="form-control">
                    <span id="text-size-value">40px</span>
                </div>
            </div>
            
            <div class="form-group">
                <label>Text Style</label>
                <div class="text-style-options">
                    <button type="button" class="btn-text-style" id="btn-text-bold" title="Bold">
                        <i class="fas fa-bold"></i>
                    </button>
                    <button type="button" class="btn-text-style" id="btn-text-italic" title="Italic">
                        <i class="fas fa-italic"></i>
                    </button>
                    <button type="button" class="btn-text-style" id="btn-text-uppercase" title="UPPERCASE">
                        <i class="fas fa-uppercase">Aa</i>
                    </button>
                    <button type="button" class="btn-text-style" id="btn-text-shadow" title="Text Shadow">
                        <i class="fas fa-clone"></i>
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <label for="image-filter">Image Filter</label>
                <select id="image-filter" name="image_filter" class="form-control">
                    <option value="none">None</option>
                    <option value="grayscale">Grayscale</option>
                    <option value="sepia">Sepia</option>
                    <option value="saturate">Saturate</option>
                    <option value="hue-rotate">Hue Rotate</option>
                    <option value="invert">Invert</option>
                    <option value="blur">Blur</option>
                    <option value="brightness">Brightness</option>
                    <option value="contrast">Contrast</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="filter-intensity">Filter Intensity</label>
                <div class="filter-slider">
                    <input type="range" id="filter-intensity" name="filter_intensity" min="0" max="100" value="50" class="form-control" disabled>
                    <span id="filter-intensity-value">50%</span>
                </div>
            </div>
        </form>
    </div>
    
    <div class="meme-preview-container">
        <h3>Meme Preview</h3>
        
        <div id="meme-preview" class="meme-preview">
            <img src="<?php echo SITE_URL; ?>/img/placeholder.svg" alt="Select a template" id="template-image">
            <div class="meme-text top-text" id="preview-top-text"></div>
            <div class="meme-text bottom-text" id="preview-bottom-text"></div>
        </div>
        
        <div class="meme-actions">
            <button id="download-meme-btn" class="btn btn-primary">Download Meme</button>
            <button id="share-meme-btn" class="btn btn-secondary">Share Meme</button>
        </div>
    </div>
</div>

<!-- Add Google Fonts for meme text -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Bangers&family=Bebas+Neue&family=Bungee&family=Creepster&family=Lobster&family=Permanent+Marker&family=Press+Start+2P&family=Special+Elite&family=VT323&display=swap" rel="stylesheet">

<!-- Load html2canvas library for meme downloading -->
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
    // Set site URL for JavaScript
    const siteUrl = '<?php echo SITE_URL; ?>';
    const csrfToken = '<?php echo generateCSRFToken(); ?>';
</script> 