-- Add missing tables for MemeNews

-- Create user_favorites table
CREATE TABLE IF NOT EXISTS user_favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    template_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_favorites_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_favorites_template_id FOREIGN KEY (template_id) REFERENCES meme_templates(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_template (user_id, template_id)
);

-- Create user_tokens table (for "remember me" functionality)
CREATE TABLE IF NOT EXISTS user_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_tokens_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create user_collections table
CREATE TABLE IF NOT EXISTS user_collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_collections_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create collection_memes table
CREATE TABLE IF NOT EXISTS collection_memes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    collection_id INT NOT NULL,
    meme_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_collection_memes_collection_id FOREIGN KEY (collection_id) REFERENCES user_collections(id) ON DELETE CASCADE,
    CONSTRAINT fk_collection_memes_meme_id FOREIGN KEY (meme_id) REFERENCES user_memes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_collection_meme (collection_id, meme_id)
);

-- Create user_preferences table
CREATE TABLE IF NOT EXISTS user_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email_notifications BOOLEAN DEFAULT TRUE,
    news_alerts BOOLEAN DEFAULT TRUE,
    contest_notifications BOOLEAN DEFAULT TRUE,
    public_profile BOOLEAN DEFAULT TRUE,
    show_username_on_memes BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_preferences_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create indexes
CREATE INDEX idx_user_favorites_user_id ON user_favorites (user_id);
CREATE INDEX idx_user_favorites_template_id ON user_favorites (template_id);
CREATE INDEX idx_user_tokens_user_id ON user_tokens (user_id);
CREATE INDEX idx_user_collections_user_id ON user_collections (user_id);
CREATE INDEX idx_collection_memes_collection_id ON collection_memes (collection_id);
CREATE INDEX idx_collection_memes_meme_id ON collection_memes (meme_id);
CREATE INDEX idx_user_preferences_user_id ON user_preferences (user_id); 