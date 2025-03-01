# MemeNews - AI-Powered Meme Generator

MemeNews is a premium-quality PHP meme generator that creates humorous memes based on real-world events and trending news using AI technology.

## Features

- **AI-Powered Meme Generation**: Automatically generates witty captions based on current news
- **News Integration**: Fetches trending news and events in real-time
- **Template Library**: Extensive collection of meme templates
- **Customization Options**: Edit text, fonts, colors, and image positioning
- **Sharing & Downloading**: Easy social media sharing and downloading options
- **Open Router AI Integration**: Leverages free open router AI models for creative content

## Tech Stack

- **Backend**: PHP (No Composer)
- **Frontend**: HTML, CSS, JavaScript
- **Database**: MySQL
- **AI**: OpenRouter API integration

## Setup Instructions

1. Clone this repository to your web server
2. Import the database schema from `database/schema.sql`
3. Run additional database scripts in this order:
   - `database/add_missing_tables.sql`
   - `database/add_bio_column.sql`
   - `database/add_uploaded_by_column.sql`
   - `database/add_template_categories.sql` (or run `php database/update_db.php`)
4. Configure your database connection in `config/database.php`
5. Set up your OpenRouter API key in `config/api.php`
6. Access the application through your web browser

## Troubleshooting

### Missing Template Categories Table

If you encounter the error "Table 'meme_news.template_categories' doesn't exist", run the database update script:

```bash
cd /path/to/memenews
php database/update_db.php
```

This will create the missing `template_categories` table and populate it with default categories.

## Security Features

- Input validation and sanitization
- Protection against XSS and SQL injection
- Rate limiting for API requests
- Secure file uploads

## License

This project is intended for educational purposes only. Use responsibly. 