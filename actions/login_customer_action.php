<?php
// Buffer output so any unexpected warnings don't break JSON
ob_start();
header('Content-Type: application/json');
session_start();

$response = array();

$logFile = __DIR__ . '/../error/php-error.log';
function append_log($msg) {
    global $logFile;
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

// If  user is already logged in, return an error JSON
if (isset($_SESSION['customer_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    ob_end_clean();
    echo json_encode($response);
    exit();
}

//  input validation 
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

//  This is used to validate required fields 
if ($email === '' || $password === '') {
    $response['status'] = 'error';
    $response['message'] = 'Please provide both email and password';
    ob_end_clean();
    echo json_encode($response);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['status'] = 'error';
    $response['message'] = 'Please provide a valid email address';
    ob_end_clean();
    echo json_encode($response);
    exit();
}


try {
    $controllerPath = __DIR__ . '/../controllers/customer_controller.php';
    if (!file_exists($controllerPath)) {
        append_log('Controller missing: ' . $controllerPath);
        $response['status'] = 'error';
        $response['message'] = 'Server configuration error';
        ob_end_clean();
        echo json_encode($response);
        exit();
    }

    require_once $controllerPath;

    $login_result = login_customer_ctr($email, $password);

    if ($login_result && is_array($login_result) && isset($login_result['id'])) {
        // Login successful(sessions)
        $_SESSION['customer_id'] = $login_result['id'];
        $_SESSION['customer_name'] = $login_result['full_name'];
        $_SESSION['customer_email'] = $login_result['email'];
        $_SESSION['user_role'] = $login_result['user_role'];
        $_SESSION['customer_country'] = $login_result['country'];
        $_SESSION['customer_city'] = $login_result['city'];
        $_SESSION['customer_contact'] = $login_result['contact_number'];
        
        $response['status'] = 'success';
        $response['message'] = 'Login successful';
        $response['customer'] = [
            'id' => $login_result['id'],
            'name' => $login_result['full_name'],
            'email' => $login_result['email'],
            'role' => $login_result['user_role']
        ];
    } else {
        $response['status'] = 'error';
        if ($login_result === 'invalid_credentials') {
            $response['message'] = 'Invalid email or password';
        } elseif ($login_result === 'user_not_found') {
            $response['message'] = 'No account found with this email address';
        } else {
            $response['message'] = 'Login failed. Please try again.';
        }
    }
} catch (Throwable $t) {
    // catch unexpected errors 
    append_log('Exception during login: ' . $t->getMessage() . ' in ' . $t->getFile() . ':' . $t->getLine());
    $response['status'] = 'error';
    $response['message'] = 'An internal error occurred';
}


if (ob_get_level()) {
    ob_end_clean();
}

echo json_encode($response);
?>
