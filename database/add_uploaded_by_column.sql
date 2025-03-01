-- Add uploaded_by column to meme_templates table

-- Add the uploaded_by column
ALTER TABLE meme_templates ADD COLUMN uploaded_by INT NULL;

-- Add foreign key constraint
ALTER TABLE meme_templates ADD CONSTRAINT fk_meme_templates_uploaded_by FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL;

-- Create index for better performance
CREATE INDEX idx_meme_templates_uploaded_by ON meme_templates (uploaded_by);

-- Update existing templates with NULL value for uploaded_by
UPDATE meme_templates SET uploaded_by = NULL;

-- Output confirmation
SELECT 'Column uploaded_by added to meme_templates table successfully.' AS result; 