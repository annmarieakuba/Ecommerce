<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controllers/cart_controller.php';

function send_response($payload, $code = 200)
{
    http_response_code($code);
    echo json_encode($payload);
    exit;
}

$customerId = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
$guestKey = $_SESSION['cart_guest_key'] ?? ('guest:' . session_id());

try {
    empty_cart_ctr($customerId, $guestKey);
    $cartData = get_user_cart_ctr($customerId, $guestKey);

    send_response([
        'success' => true,
        'message' => 'Cart emptied successfully.',
        'cart' => $cartData
    ]);
} catch (Throwable $th) {
    send_response([
        'success' => false,
        'message' => $th->getMessage()
    ], 500);
}

