<?php
ob_start();
header('Content-Type: application/json');
session_start();

$response = array();

$logFile = '../test_error/errorlog.txt';
function append_log($msg) {
    global $logFile;
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

// If already logged in, check if user is admin (role 1) 
if (isset($_SESSION['customer_id'])) {
    // Allow admin users (role 1) to create new accounts
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
        $response['status'] = 'error';
        $response['message'] = 'You are already logged in. Please logout first or use an admin account.';
        // clear buffer and return
        ob_end_clean();
        echo json_encode($response);
        exit();
    }
}

// Basic input validation 
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$country = isset($_POST['country']) ? trim($_POST['country']) : '';
$city = isset($_POST['city']) ? trim($_POST['city']) : '';
$contact_number = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
$user_role = 2; // Default to customer

$missing = [];
if ($full_name === '') $missing[] = 'full_name';
if ($email === '') $missing[] = 'email';
if ($password === '') $missing[] = 'password';
if ($country === '') $missing[] = 'country';
if ($city === '') $missing[] = 'city';
if ($contact_number === '') $missing[] = 'contact_number';

if (!empty($missing)) {
    $response['status'] = 'error';
    $response['message'] = 'Missing fields: ' . implode(', ', $missing);
    ob_end_clean();
    echo json_encode($response);
    exit();
}

// Require controller and execute registration inside a try-catch block
try {
    $controllerPath = '../controllers/customer_controller.php';
    if (!file_exists($controllerPath)) {
        append_log('Controller missing: ' . $controllerPath);
        $response['status'] = 'error';
        $response['message'] = 'Server configuration error';
        ob_end_clean();
        echo json_encode($response);
        exit();
    }

    require_once $controllerPath;

    $customer_id = register_customer_ctr($full_name, $email, $password, $country, $city, $contact_number, $user_role);

    if ($customer_id && is_numeric($customer_id)) {
        $response['status'] = 'success';
        $response['message'] = 'Registered successfully';
        $response['customer_id'] = $customer_id;
    } else {
        $response['status'] = 'error';
        if ($customer_id === 'exists') {
            $response['message'] = 'Email already registered';
        } elseif (is_string($customer_id) && $customer_id !== '') {
            // DB returned an error message; log it
            append_log('DB error during registration: ' . $customer_id);
            $response['message'] = 'Failed to register: ' . $customer_id;
        } else {
            // capture any buffered output (warnings)
            $buf = ob_get_clean();
            if ($buf) {
                append_log('Buffered output during registration: ' . $buf);
            }
            $response['message'] = 'Failed to register';
        }
    }
} catch (Throwable $t) {
    // catch unexpected errors and log them
    append_log('Exception during registration: ' . $t->getMessage() . ' in ' . $t->getFile() . ':' . $t->getLine());
    $response['status'] = 'error';
    $response['message'] = 'An internal error occurred';
}

// Ensure buffer is clean before outputting JSON
if (ob_get_level()) {
    ob_end_clean();
}

echo json_encode($response);
?>