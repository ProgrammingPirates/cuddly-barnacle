<?php
// +------------------------------------------------------------------------+
// | @author Mustafa Öztürk (mstfoztrk)
// | @author_url 1: http://www.duhovit.com
// | @author_url 2: http://codecanyon.net/user/mstfoztrk
// | @author_email: socialmaterial@hotmail.com
// +------------------------------------------------------------------------+
// | dizzy Support Creators Content Script
// | Copyright (c) 2021 mstfoztrk. All rights reserved.
// +------------------------------------------------------------------------+
define('APP_DEBUG', false);
// --------------------------------------------------------------------------
// DATABASE CONFIGURATION
// --------------------------------------------------------------------------
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'YOUR_DB_USERNAME');
define('DB_PASSWORD', 'YOUR_DB_PASSWORD');
define('DB_DATABASE', 'YOUR_DB_NAME');

// --------------------------------------------------------------------------
// DATABASE CONNECTION WITH ERROR HANDLING
// --------------------------------------------------------------------------
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
    mysqli_select_db($db, DB_DATABASE);
} catch (mysqli_sql_exception $e) {
    $error = $e->getMessage();
    if (APP_DEBUG === true) {
        echo '<pre>
====================================================
 DATABASE CONNECTION ERROR
====================================================

We were unable to connect to your MySQL database using the
credentials provided in the connect.php file.

Error details:
' . htmlspecialchars($error) . '

Possible causes:
- Incorrect database name
- Invalid username or password
- Database does not exist on the server
- Database server is not running or refusing connection

 To fix this, open the file:
  /includes/connect.php

And check the following values:
  - DB_SERVER
  - DB_USERNAME
  - DB_PASSWORD
  - DB_DATABASE

Once you enter the correct credentials, reload the page.
====================================================
</pre>';

        exit();
    }
}

// --------------------------------------------------------------------------
// MYSQL CHARACTER SET
// --------------------------------------------------------------------------
mysqli_query($db, 'SET character_set_results="utf8mb4"');
mysqli_query($db, "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
mysqli_query($db, "SET CHARACTER SET utf8mb4");

// --------------------------------------------------------------------------
// BASE URL DETECTION (proxy-aware, PHP 5.x safe)
// --------------------------------------------------------------------------
$protoHeader = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : null;
if ($protoHeader && strpos($protoHeader, ',') !== false) {
    $protoHeader = substr($protoHeader, 0, strpos($protoHeader, ','));
}
$protocol = $protoHeader ? $protoHeader : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');

$hostHeader = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : null;
if ($hostHeader && strpos($hostHeader, ',') !== false) {
    $hostHeader = substr($hostHeader, 0, strpos($hostHeader, ','));
}
$host = $hostHeader ? $hostHeader : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost'));

$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$rootPath = rtrim(preg_replace('/(\/requests|\/includes|\/themes|\/langs|\/src|\/ajax|\/admin|\/panel).*/i', '', $scriptName), '/');
$base_url = $protocol . '://' . $host . $rootPath . '/';

// --------------------------------------------------------------------------
// FILE SYSTEM PATHS
// --------------------------------------------------------------------------
$serverDocumentRoot = realpath($_SERVER['DOCUMENT_ROOT']);
$projectRoot        = realpath(dirname(__FILE__));
$relativePath       = str_replace($serverDocumentRoot, '', $projectRoot);
$relativePath       = trim(str_replace('\\', '/', $relativePath), '/');
$fullUploadPath     = $serverDocumentRoot . ($relativePath ? "/$relativePath" : '');

$uploadFile     = $serverDocumentRoot . '/uploads/files/';
$xVideos        = $serverDocumentRoot . '/uploads/xvideos/';
$xImages        = $serverDocumentRoot . '/uploads/pixel/';
$uploadCover    = $serverDocumentRoot . '/uploads/covers/';
$uploadAvatar   = $serverDocumentRoot . '/uploads/avatars/';
$uploadIconLogo = $serverDocumentRoot . '/img/';
$uploadAdsImage = $serverDocumentRoot . '/uploads/spImages/';

// --------------------------------------------------------------------------
// META BASE & COOKIE
// --------------------------------------------------------------------------
$metaBaseUrl = $base_url;
$cookieName  = 'dizzy';

// --------------------------------------------------------------------------
// OPTIONAL: CLOSE DB ON SHUTDOWN
// --------------------------------------------------------------------------
register_shutdown_function(function () use ($db) {
    if ($db instanceof mysqli) {
        mysqli_close($db);
    }
});
