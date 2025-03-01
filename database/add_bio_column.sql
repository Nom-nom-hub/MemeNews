-- Add bio column to users table
ALTER TABLE users ADD COLUMN bio TEXT NULL AFTER email;

-- Output confirmation
SELECT 'Column bio added to users table successfully.' AS result; 