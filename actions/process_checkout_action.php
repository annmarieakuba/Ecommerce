<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../controllers/order_controller.php';

function read_input()
{
    $raw = file_get_contents('php://input');
    if (!empty($raw)) {
        $data = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $data;
        }
    }
    return $_POST;
}

function respond_checkout($payload, $code = 200)
{
    http_response_code($code);
    echo json_encode($payload);
    exit;
}

if (!isset($_SESSION['customer_id'])) {
    respond_checkout([
        'success' => false,
        'status' => 'unauthenticated',
        'message' => 'Please login before checking out.'
    ], 401);
}

$customerId = (int)$_SESSION['customer_id'];
$guestKey = $_SESSION['cart_guest_key'] ?? ('guest:' . session_id());

$payload = read_input();
$paymentMethod = isset($payload['payment_method']) ? trim($payload['payment_method']) : 'Simulated';
$currency = isset($payload['currency']) ? strtoupper(trim($payload['currency'])) : 'USD';

try {
    $cartData = get_user_cart_ctr($customerId, $guestKey);
    if (empty($cartData['items'])) {
        respond_checkout([
            'success' => false,
            'status' => 'empty_cart',
            'message' => 'Your cart is empty. Add items before checking out.'
        ], 400);
    }

    $checkoutResult = process_checkout_ctr($customerId, $cartData, $currency, $paymentMethod);

    // Empty the cart once order is processed
    empty_cart_ctr($customerId, $guestKey);

    respond_checkout([
        'success' => true,
        'status' => 'completed',
        'message' => 'Payment confirmed. Order created successfully.',
        'order' => [
            'order_id' => $checkoutResult['order_id'],
            'reference' => $checkoutResult['invoice_no'],
            'payment_reference' => $checkoutResult['payment_reference'],
            'total_amount' => $checkoutResult['total_amount'],
            'currency' => $currency,
            'total_items' => $checkoutResult['total_items']
        ]
    ]);
} catch (Throwable $th) {
    respond_checkout([
        'success' => false,
        'status' => 'error',
        'message' => $th->getMessage()
    ], 500);
}

