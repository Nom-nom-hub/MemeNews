# MemeNews Setup Instructions

## Database Setup

Since you're using MAMP, follow these steps to set up the database:

1. Open MAMP and start the servers
2. Open phpMyAdmin by clicking the "Open WebStart page" button and then selecting "phpMyAdmin" from the top menu
3. Create a new database named `meme_news`:
   - Click on "New" in the left sidebar
   - Enter "meme_news" as the database name
   - Select "utf8mb4_unicode_ci" as the collation
   - Click "Create"
4. Select the newly created `meme_news` database from the left sidebar
5. Click on the "Import" tab at the top
6. Click "Choose File" and select the `database/schema.sql` file from this project
7. Click "Go" to import the schema

## Application Configuration

The application is already configured to use your MAMP MySQL settings:
- Host: localhost
- Port: 8889
- Socket: /Applications/MAMP/tmp/mysql/mysql.sock
- Username: root
- Password: root
- Database: meme_news

## Running the Application

1. Start the PHP development server:
   ```
   php -S localhost:8000
   ```
2. Open your browser and navigate to http://localhost:8000

## Troubleshooting

If you encounter database connection issues, try these solutions:

1. Make sure MAMP servers are running
2. Verify database credentials in `config/config.php`
3. Check that the database `meme_news` exists
4. Ensure the MySQL socket path is correct for your MAMP installation 