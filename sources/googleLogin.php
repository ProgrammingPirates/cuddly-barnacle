<?php
require('google/http.php');
require('google/oauth_client.php');

// Get Google OAuth configuration from database
$Keys = $iN->iN_SocialLoginDetails('google');

// Check if Google OAuth is properly configured
if (empty($Keys['s_key_one']) || empty($Keys['s_key_two'])) {
    $_SESSION["e_msg"] = "Google OAuth is not configured. Please configure Google OAuth credentials in the admin panel.";
    header("location:" . $base_url);
    exit;
}

/* make sure the url end with a trailing slash */
define("SITE_URL", $base_url);
/* the page where you will be redirected for authorization */
define("REDIRECT_URL", SITE_URL."googleLogin.php");

/* * ***** Google related activities start ** */
define("CLIENT_ID", $Keys['s_key_one']);
define("CLIENT_SECRET", $Keys['s_key_two']);

/* permission - Updated to use latest Google OAuth 2.0 scopes */
define("SCOPE", 'https://www.googleapis.com/auth/userinfo.email '.
        'https://www.googleapis.com/auth/userinfo.profile');

/* logout both from google and your site **/
define("LOGOUT_URL", "https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=". urlencode(SITE_URL."logout.php"));

$client = new oauth_client_class;

// set the offline access only if you need to call an API
// when the user is not present and the token may expire
$client->offline = FALSE;

// Enhanced debugging for troubleshooting
$client->debug = true; // Enable debug for troubleshooting
$client->debug_http = true;
$client->redirect_uri = REDIRECT_URL;

$client->client_id = CLIENT_ID;
$application_line = __LINE__;
$client->client_secret = CLIENT_SECRET;

// Validate required credentials
if (strlen($client->client_id) == 0 || strlen($client->client_secret) == 0) {
    $_SESSION["e_msg"] = 'Google OAuth configuration is incomplete. Please configure Client ID and Client Secret in the admin panel. The redirect URI should be: ' . $client->redirect_uri;
    header("location:" . $base_url);
    exit;
}

/* API permissions */
$client->scope = SCOPE;

if (($success = $client->Initialize())) {
    if (($success = $client->Process())) {
        if (strlen($client->authorization_error)) {
            $client->error = $client->authorization_error;
            $success = false;
        } elseif (strlen($client->access_token)) {
            // Use the updated Google OAuth 2.0 API endpoint
            $success = $client->CallAPI(
                'https://www.googleapis.com/oauth2/v2/userinfo', 'GET', array(), array('FailOnAccessError' => true), $user);
        }
    }
    $success = $client->Finalize($success);
}

if ($client->exit)
    exit;

if ($success) {
    // Validate that we received user data
    if (!isset($user->email) || !isset($user->name)) {
        $_SESSION["e_msg"] = "Unable to retrieve user information from Google. Please try again.";
        header("location:" . $base_url);
        exit;
    }

    $GoogleAccountFullName = mysqli_real_escape_string($db, $user->name);
    $GoogleAccountEmail = mysqli_real_escape_string($db, $user->email);
    $GoogleAccountProfileImage = isset($user->picture) ? mysqli_real_escape_string($db, $user->picture) : '';
    $UserGender = 'male';
    
    function getMe($email){
        preg_match('/(\S+)(@(\S+))/', $email, $match);
        return $match[1];
    }
    
    /*Get value before @ for username*/
    $GoogleAccountRegisterUserName = getMe($GoogleAccountEmail);
    $generatePassword = sha1(md5($GoogleAccountRegisterUserName.'_'.$GoogleAccountRegisterUserName));
    $GoogleAccountRegisterUserName = trim($GoogleAccountRegisterUserName);
    $GoogleAccountEmail = trim($GoogleAccountEmail);
    
    // Check if username already exists
    $checkUserExist = mysqli_query($db, "SELECT * FROM i_users WHERE i_username = '$GoogleAccountRegisterUserName'") or die(mysqli_error($db));
    // Check if email already exists
    $checkEmail = mysqli_query($db,"SELECT * FROM i_users WHERE i_user_email = '$GoogleAccountEmail'")  or die(mysqli_error($db));
    
    if(mysqli_num_rows($checkUserExist) == 0 && mysqli_num_rows($checkEmail) == 0 && isset($GoogleAccountEmail)){
        // Register new user
        $time = time();
        mysqli_query($db, "SET character_set_client=utf8mb4") or die(mysqli_error($db));
        mysqli_query($db, "SET character_set_connection=utf8mb4") or die(mysqli_error($db));
        $defaultLanguage = strtolower($defaultLanguage);
        
        $register = mysqli_query($db,"INSERT INTO i_users(i_user_fullname, i_user_email, user_gender, user_avatar, i_username, registered,i_password, login_with,lang,email_verify_status)VALUES('$GoogleAccountFullName','$GoogleAccountEmail','male','$GoogleAccountProfileImage','$GoogleAccountRegisterUserName','$time','$generatePassword','google','$defaultLanguage','yes')") or die(mysqli_error($db));
        
        if($register){
            $getAccountDetails = mysqli_query($db,"SELECT * FROM i_users WHERE i_username = '$GoogleAccountRegisterUserName'") or die(mysqli_error($db));
            $uData = mysqli_fetch_array($getAccountDetails, MYSQLI_ASSOC);
            if(mysqli_num_rows($getAccountDetails) == '1'){
                $userID = $uData['iuid'];
                $userUsername = $uData['i_username'];
                $userEmail = $uData['i_user_email'];
                $userPassword = $uData['i_password'];
                $time = time();
                mysqli_query($db, "UPDATE i_users SET last_login_time = '$time' WHERE iuid = '$userID'") or die(mysqli_error($db));
                $hash = sha1($userUsername).$time;
                setcookie($cookieName,$hash,time()+31556926 ,'/');
                $saveLogin = mysqli_query($db, "INSERT INTO `i_sessions`(session_uid, session_key, session_time) VALUES ('$userID','$hash', '$time')") or die(mysqli_error($db));
                mysqli_query($db,"INSERT INTO `i_friends` (fr_one,fr_two,fr_time,fr_status)VALUES('$userID','$userID','$time', 'me')");
                $_SESSION['iuid'] = $userID;
                if($saveLogin){
                    $redirect = $base_url.'settings';
                    header("Location:$redirect");
                    exit;
                }
            }
        }
    }else if(mysqli_num_rows($checkUserExist) == 1 || mysqli_num_rows($checkEmail) == 1){
        // Login existing user
        $getAccountDetails = mysqli_query($db,"SELECT * FROM i_users WHERE i_user_email = '$GoogleAccountEmail'") or die(mysqli_error($db));
        $uData = mysqli_fetch_array($getAccountDetails, MYSQLI_ASSOC);
        if(mysqli_num_rows($getAccountDetails) == '1'){
            $userID = $uData['iuid'];
            $userUsername = $uData['i_username'];
            $userEmail = $uData['i_user_email'];
            $userPassword = $uData['i_password'];
            $time = time();
            mysqli_query($db, "UPDATE i_users SET last_login_time = '$time' WHERE iuid = '$userID'") or die(mysqli_error($db));
            $hash = sha1($userUsername).$time;
            setcookie($cookieName,$hash,time()+31556926 ,'/');
            $saveLogin = mysqli_query($db, "INSERT INTO `i_sessions`(session_uid, session_key, session_time) VALUES ('$userID','$hash', '$time')") or die(mysqli_error($db));
            $_SESSION['iuid'] = $userID;
            if($saveLogin){
                $redirect = $base_url.$userUsername;
                header("Location: $redirect");
                exit;
            }
        }
    }
} else {
    // Enhanced error handling
    $error_message = "Google login failed";
    if (!empty($client->error)) {
        $error_message .= ": " . $client->error;
    }
    $_SESSION["e_msg"] = $error_message;
    
    // Log error for debugging (in production, log to file instead)
    error_log("Google OAuth Error: " . $client->error);
}

header("location:".$base_url);
exit;
?>