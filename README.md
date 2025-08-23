# Dizzy Social Media Platform

## Google OAuth Setup Guide

### Prerequisites
1. MySQL database server running
2. Web server (Apache/Nginx) with PHP support
3. Google Cloud Console account

### Step 1: Database Configuration

1. Edit `/includes/connect.php` and update database credentials:
```php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'your_db_username');
define('DB_PASSWORD', 'your_db_password');
define('DB_DATABASE', 'your_db_name');
```

2. Import the database schema:
```bash
mysql -u your_username -p your_database_name < dizzy5.2.sql
```

### Step 2: Google OAuth Configuration

1. **Create Google Cloud Project:**
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Create a new project or select existing one

2. **Enable APIs:**
   - Navigate to "APIs & Services" > "Library"
   - Enable "Google+ API" or "People API"
   - Enable "OAuth2 API"

3. **Create OAuth 2.0 Credentials:**
   - Go to "APIs & Services" > "Credentials"
   - Click "Create Credentials" > "OAuth 2.0 Client ID"
   - Choose "Web application"
   - Add authorized redirect URI: `https://yourdomain.com/googleLogin.php`
   - Copy Client ID and Client Secret

4. **Configure in Admin Panel:**
   - Login to your admin panel
   - Navigate to Social Logins section
   - Enter Google Client ID and Client Secret
   - Enable Google login status

### Step 3: Troubleshooting

**Common Issues:**

1. **"Google OAuth is not configured" error:**
   - Ensure Client ID and Client Secret are saved in admin panel
   - Check database `i_social_logins` table for Google credentials

2. **Redirect URI mismatch:**
   - Verify redirect URI in Google Console matches exactly: `https://yourdomain.com/googleLogin.php`
   - Ensure your domain has SSL certificate for HTTPS

3. **Database connection errors:**
   - Verify database credentials in `/includes/connect.php`
   - Ensure MySQL server is running
   - Check database permissions

4. **OAuth API errors:**
   - Ensure Google APIs are enabled in Google Cloud Console
   - Verify OAuth consent screen is configured
   - Check API quotas and limits

### Step 4: Testing

1. Navigate to your site's login page
2. Click "Login with Google" button
3. You should be redirected to Google's authorization page
4. After authorization, you should be redirected back and logged in

### Security Notes

- Always use HTTPS in production
- Keep your Client Secret secure and never expose it in frontend code
- Regularly review OAuth consent screen and permissions
- Monitor API usage and quotas

### Support

If you encounter issues:
1. Check browser console for JavaScript errors
2. Check server error logs
3. Enable debug mode in `googleLogin.php` temporarily
4. Verify all configuration steps above

## Database Schema

The application uses the following main tables:
- `i_users` - User accounts
- `i_social_logins` - OAuth provider configurations
- `i_sessions` - User sessions

Make sure all tables are properly created from the SQL file before starting.