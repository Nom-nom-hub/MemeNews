-- Add template_categories table to MemeNews database

-- Create template_categories table
CREATE TABLE IF NOT EXISTS template_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    slug VARCHAR(50) NOT NULL UNIQUE,
    icon VARCHAR(50) DEFAULT 'fa-image',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add index for better performance
CREATE INDEX idx_template_categories_slug ON template_categories (slug);
CREATE INDEX idx_template_categories_display_order ON template_categories (display_order);

-- Insert default categories
INSERT INTO template_categories (name, description, slug, icon, display_order) VALUES
('General', 'General-purpose meme templates', 'general', 'fa-image', 1),
('Reaction', 'Templates for expressing reactions to events', 'reaction', 'fa-face-surprise', 2),
('Animals', 'Meme templates featuring animals', 'animals', 'fa-paw', 3),
('Politics', 'Templates for political memes', 'politics', 'fa-landmark', 4),
('Pop Culture', 'Templates from movies, TV shows, and celebrities', 'pop-culture', 'fa-film', 5),
('Sports', 'Sports-related meme templates', 'sports', 'fa-basketball', 6),
('Gaming', 'Video game meme templates', 'gaming', 'fa-gamepad', 7),
('Wholesome', 'Positive and uplifting meme templates', 'wholesome', 'fa-heart', 8),
('Classic', 'Timeless and iconic meme templates', 'classic', 'fa-star', 9),
('Trending', 'Currently popular meme templates', 'trending', 'fa-chart-line', 10);

-- Add foreign key to meme_templates table
ALTER TABLE meme_templates 
ADD COLUMN category_id INT DEFAULT NULL AFTER file_path,
ADD CONSTRAINT fk_meme_templates_category_id FOREIGN KEY (category_id) REFERENCES template_categories(id) ON DELETE SET NULL;

-- Convert existing category strings to category IDs
UPDATE meme_templates SET category_id = (SELECT id FROM template_categories WHERE slug = meme_templates.category LIMIT 1); 