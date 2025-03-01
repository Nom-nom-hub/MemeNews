-- MemeNews Database Schema

-- Drop existing tables if they exist
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS user_memes;
DROP TABLE IF EXISTS meme_templates;
DROP TABLE IF EXISTS users;

-- Create tables
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE meme_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    category VARCHAR(50) DEFAULT 'general',
    tags VARCHAR(255) DEFAULT NULL,
    width INT NOT NULL,
    height INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE user_memes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    template_id INT NOT NULL,
    top_text TEXT,
    bottom_text TEXT,
    custom_image_path VARCHAR(255) DEFAULT NULL,
    news_headline TEXT DEFAULT NULL,
    file_path VARCHAR(255) NOT NULL,
    is_public BOOLEAN DEFAULT TRUE,
    view_count INT DEFAULT 0,
    share_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_memes_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_user_memes_template_id FOREIGN KEY (template_id) REFERENCES meme_templates(id) ON DELETE CASCADE
);

CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT 0,
    action VARCHAR(50) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for better performance
CREATE INDEX idx_meme_templates_category ON meme_templates (category);
CREATE INDEX idx_user_memes_user_id ON user_memes (user_id);
CREATE INDEX idx_user_memes_template_id ON user_memes (template_id);
CREATE INDEX idx_user_memes_created_at ON user_memes (created_at DESC);
CREATE INDEX idx_activity_logs_user_id ON activity_logs (user_id);
CREATE INDEX idx_activity_logs_action ON activity_logs (action);
CREATE INDEX idx_activity_logs_created_at ON activity_logs (created_at DESC);

-- Insert sample meme templates
INSERT INTO meme_templates (name, file_path, category, tags, width, height) VALUES
('Drake Hotline Bling', 'img/templates/drake-hotline-bling.jpg', 'reaction', 'drake,hotline,bling,approve,disapprove', 1200, 1200),
('Distracted Boyfriend', 'img/templates/distracted-boyfriend.jpg', 'reaction', 'distracted,boyfriend,jealous,looking', 1200, 800),
('Two Buttons', 'img/templates/two-buttons.jpg', 'choices', 'buttons,decision,sweat,difficult', 600, 908),
('Change My Mind', 'img/templates/change-my-mind.jpg', 'opinion', 'change,mind,debate,crowder', 924, 583),
('Expanding Brain', 'img/templates/expanding-brain.jpg', 'intelligence', 'brain,expand,galaxy,mind,smart', 857, 1202),
('Woman Yelling at Cat', 'img/templates/woman-yelling-at-cat.jpg', 'animals', 'cat,woman,yelling,confused', 1200, 628),
('Surprised Pikachu', 'img/templates/surprised-pikachu.jpg', 'reaction', 'pikachu,surprised,shocked,pokemon', 1893, 1893),
('Roll Safe Think About It', 'img/templates/roll-safe.jpg', 'clever', 'smart,thinking,roll safe,tap head', 702, 395),
('One Does Not Simply', 'img/templates/one-does-not-simply.jpg', 'warning', 'boromir,mordor,lord of the rings,lotr', 568, 335),
('Ancient Aliens Guy', 'img/templates/ancient-aliens.jpg', 'conspiracy', 'aliens,history channel,theory', 500, 437); 