<?php 
// Include core configuration and app dependencies
include_once "includes/inc.php";   

// Load the CoinPayments SDK autoloader (used for signature verification or future extensions)
require_once('includes/coinPayment/vendor/autoload.php');

// Retrieve transaction ID from POST payload
$txnID = isset($_POST['txn_id']) ? $_POST['txn_id'] : NULL;

// Get raw POST input for debugging or security processing (currently unused)
$request = "php://input";

// Initialize cURL session
$ch = curl_init();

// Configure cURL to return the response instead of outputting it
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Set the target URL (currently unused since php://input isn't a valid URL)
curl_setopt($ch, CURLOPT_URL, $request);

// Execute cURL session (ineffective here due to incorrect usage of php://input)
$result = curl_exec($ch);

// Exit if no valid request data was received
if ($request === false || empty($request)) {
    exit();
}

// Convert status to integer (e.g. 100 = completed, <0 = failed)
$status = intval($_POST['status']); 

/**
 * Payment Completed (>= 100 or status == 2)
 * Mark the transaction as successful and credit the user's wallet.
 */
if ($status >= 100 || $status == 2) {
    $query = mysqli_query($db, "SELECT * FROM i_user_payments WHERE order_key = '$txnID'") or die(mysqli_error($db));
    
    if (mysqli_num_rows($query) == 1) {
        $queryData = mysqli_fetch_array($query, MYSQLI_ASSOC);

        $creditPlanID = isset($queryData['credit_plan_id']) ? $queryData['credit_plan_id'] : NULL;
        $payerUserID  = isset($queryData['payer_iuid_fk']) ? $queryData['payer_iuid_fk'] : NULL;

        // Retrieve credit plan details to determine wallet amount
        $planData   = $iN->GetPlanDetails($creditPlanID);
        $planAmount = isset($planData['plan_amount']) ? $planData['plan_amount'] : NULL;

        // Mark the payment as successful
        mysqli_query($db, "UPDATE i_user_payments SET payment_status = 'ok' WHERE order_key = '$txnID'") or die(mysqli_error($db));

        // Add wallet points to the user’s account
        mysqli_query($db, "UPDATE i_users SET wallet_points = wallet_points + '$planAmount' WHERE iuid = '$payerUserID'") or die(mysqli_error($db));
    }

/**
 * Payment Failed (status < 0)
 * Mark the transaction as declined in the payment history.
 */
} else if ($status < 0) {
    $query = mysqli_query($db, "SELECT * FROM i_user_payments WHERE order_key = '$txnID'") or die(mysqli_error($db));

    if (mysqli_num_rows($query) == 1) {
        mysqli_query($db, "UPDATE i_user_payments SET payment_status = 'declined' WHERE order_key = '$txnID'") or die(mysqli_error($db));
    }

/**
 * Payment Pending (status between 0 and 99)
 * Mark the transaction as still waiting confirmation.
 */
} else {
    $query = mysqli_query($db, "SELECT * FROM i_user_payments WHERE order_key = '$txnID'") or die(mysqli_error($db));

    if (mysqli_num_rows($query) == 1) {
        mysqli_query($db, "UPDATE i_user_payments SET payment_status = 'pending' WHERE order_key = '$txnID'") or die(mysqli_error($db));
    }  
}
?>