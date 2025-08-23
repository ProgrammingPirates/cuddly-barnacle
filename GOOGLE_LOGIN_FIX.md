# Google Login Issue - Complete Fix Guide

## Problem Summary
आपको Google से login नहीं हो रहा है (Google login is not working). यह issue कई कारणों से हो सकता है।

## Root Causes (मुख्य कारण)

### 1. Database Configuration Missing
- Database connection not configured in `includes/connect.php`
- Database tables not imported from `dizzy5.2.sql`
- Application cannot start without database

### 2. Google OAuth Credentials Missing
- Google Client ID not configured
- Google Client Secret not configured
- Google login not enabled in admin panel

### 3. Server Configuration Issues
- Apache modules not enabled (rewrite, headers)
- Web server not running
- PHP not installed

## Current Status ✅
- ✓ PHP 8.4 installed and working
- ✓ Apache web server running
- ✓ Required Apache modules enabled (rewrite, headers)
- ✓ All required files present
- ✓ Web server accessible at http://localhost

## Step-by-Step Fix Instructions

### Step 1: Configure Database
1. Edit `includes/connect.php`:
```php
define('DB_USERNAME', 'your_actual_username');
define('DB_PASSWORD', 'your_actual_password');
define('DB_DATABASE', 'your_actual_database_name');
```

2. Create MySQL database:
```bash
mysql -u root -p
CREATE DATABASE dizzy_social;
```

3. Import database schema:
```bash
mysql -u your_username -p dizzy_social < dizzy5.2.sql
```

### Step 2: Set Up Google OAuth
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create new project or select existing one
3. Enable Google+ API
4. Go to "Credentials" → "Create Credentials" → "OAuth 2.0 Client IDs"
5. Set Application Type: "Web application"
6. Set Authorized redirect URIs: `http://localhost/googleLogin.php`
7. Copy Client ID and Client Secret

### Step 3: Configure Google Login in Admin Panel
1. Access admin panel: `http://localhost/admin/`
2. Go to "Social Logins" section
3. Enter Google Client ID in "Google CLIEND ID" field
4. Enter Google Client Secret in "Google SECRET KEY" field
5. Enable Google login by checking the status checkbox
6. Save settings

### Step 4: Test Google Login
1. Go to main page: `http://localhost/`
2. Click on Google login button
3. Should redirect to Google OAuth consent screen
4. After authorization, should redirect back and log you in

## File Structure Check
```
✓ includes/connect.php - Database configuration
✓ includes/inc.php - Core application logic
✓ sources/googleLogin.php - Google login handler
✓ sources/google/oauth_client.php - OAuth client library
✓ sources/google/http.php - HTTP client library
✓ admin/default/sources/social_logins.php - Admin configuration
✓ dizzy5.2.sql - Database schema
```

## Common Issues and Solutions

### Issue: "Database connection failed"
**Solution**: Check database credentials in `includes/connect.php`

### Issue: "Google OAuth error"
**Solution**: Verify Client ID and Secret in admin panel

### Issue: "Redirect URI mismatch"
**Solution**: Ensure redirect URI in Google Console matches exactly

### Issue: "Google login not working"
**Solution**: Check if Google login is enabled in admin panel

## Testing Commands
```bash
# Test web server
curl http://localhost/test.php

# Test main page (will fail without database)
curl http://localhost/index.php

# Check Apache status
sudo apache2ctl status

# Check error logs
sudo tail -f /var/log/apache2/error.log
```

## Next Steps After Fix
1. Test Google login functionality
2. Configure other social logins if needed
3. Set up email verification
4. Configure site settings
5. Test user registration and login

## Support
If you still face issues after following these steps:
1. Check Apache error logs
2. Verify database connection
3. Ensure Google OAuth credentials are correct
4. Make sure all required files are present

---
**Note**: This application requires a working MySQL database and proper Google OAuth configuration to function correctly.