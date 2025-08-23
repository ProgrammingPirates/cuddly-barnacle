<?php
/**
 * Google OAuth Diagnostic Tool
 * Use this script to diagnose Google OAuth configuration issues
 * 
 * WARNING: Remove this file after diagnosis to avoid security risks
 */

// Include configuration
include_once "includes/inc.php";

echo "<!DOCTYPE html>
<html>
<head>
    <title>Google OAuth Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        code { background: #f5f5f5; padding: 2px 5px; }
    </style>
</head>
<body>";

echo "<h1>Google OAuth Diagnostic Tool</h1>";

// Check 1: Database Connection
echo "<div class='section'>";
echo "<h2>1. Database Connection</h2>";
if ($db && mysqli_ping($db)) {
    echo "<p class='success'>✓ Database connection successful</p>";
} else {
    echo "<p class='error'>✗ Database connection failed</p>";
    echo "<p>Check your database credentials in <code>includes/connect.php</code></p>";
}
echo "</div>";

// Check 2: Base URL
echo "<div class='section'>";
echo "<h2>2. Base URL Configuration</h2>";
echo "<p class='info'>Current base URL: <code>$base_url</code></p>";
echo "<p class='info'>Redirect URL will be: <code>{$base_url}googleLogin.php</code></p>";
if (strpos($base_url, 'https://') === 0) {
    echo "<p class='success'>✓ Using HTTPS (recommended for production)</p>";
} else {
    echo "<p class='warning'>⚠ Using HTTP (switch to HTTPS for production)</p>";
}
echo "</div>";

// Check 3: Google OAuth Configuration
echo "<div class='section'>";
echo "<h2>3. Google OAuth Configuration</h2>";
if ($db) {
    $query = mysqli_query($db, "SELECT * FROM i_social_logins WHERE s_key = 'google'");
    if ($query && mysqli_num_rows($query) > 0) {
        $googleConfig = mysqli_fetch_array($query, MYSQLI_ASSOC);
        
        echo "<p><strong>Status:</strong> " . ($googleConfig['s_status'] == '1' ? 
            "<span class='success'>Enabled</span>" : 
            "<span class='error'>Disabled</span>") . "</p>";
        
        echo "<p><strong>Client ID:</strong> " . 
            (empty($googleConfig['s_key_one']) ? 
                "<span class='error'>Not configured</span>" : 
                "<span class='success'>Configured (" . substr($googleConfig['s_key_one'], 0, 20) . "...)</span>") . "</p>";
        
        echo "<p><strong>Client Secret:</strong> " . 
            (empty($googleConfig['s_key_two']) ? 
                "<span class='error'>Not configured</span>" : 
                "<span class='success'>Configured (" . substr($googleConfig['s_key_two'], 0, 10) . "...)</span>") . "</p>";
        
        if (empty($googleConfig['s_key_one']) || empty($googleConfig['s_key_two'])) {
            echo "<p class='error'>⚠ Google OAuth credentials are missing. Please configure them in the admin panel.</p>";
        } else {
            echo "<p class='success'>✓ Google OAuth credentials are configured</p>";
        }
    } else {
        echo "<p class='error'>✗ Google OAuth configuration not found in database</p>";
    }
} else {
    echo "<p class='error'>Cannot check - database connection failed</p>";
}
echo "</div>";

// Check 4: Required Files
echo "<div class='section'>";
echo "<h2>4. Required Files</h2>";
$requiredFiles = [
    'sources/googleLogin.php',
    'sources/google/oauth_client.php',
    'sources/google/http.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "<p class='success'>✓ $file exists</p>";
    } else {
        echo "<p class='error'>✗ $file missing</p>";
    }
}
echo "</div>";

// Check 5: PHP Extensions
echo "<div class='section'>";
echo "<h2>5. PHP Requirements</h2>";
$requiredExtensions = ['curl', 'openssl', 'json', 'mysqli'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='success'>✓ $ext extension loaded</p>";
    } else {
        echo "<p class='error'>✗ $ext extension missing</p>";
    }
}
echo "</div>";

// Check 6: Configuration Summary
echo "<div class='section'>";
echo "<h2>6. Configuration Summary</h2>";
echo "<p><strong>Redirect URI for Google Console:</strong></p>";
echo "<code>{$base_url}googleLogin.php</code>";
echo "<p><strong>Required Google Cloud Console Setup:</strong></p>";
echo "<ul>";
echo "<li>Enable Google+ API or People API</li>";
echo "<li>Create OAuth 2.0 Client ID (Web application)</li>";
echo "<li>Add the redirect URI above to authorized redirect URIs</li>";
echo "<li>Copy Client ID and Secret to admin panel</li>";
echo "</ul>";
echo "</div>";

// Security Warning
echo "<div class='section' style='background-color: #fff3cd; border-color: #ffeaa7;'>";
echo "<h2>⚠ Security Warning</h2>";
echo "<p><strong>Important:</strong> Delete this diagnostic file (<code>google_oauth_diagnostic.php</code>) after use to avoid security risks.</p>";
echo "</div>";

echo "</body></html>";
?>