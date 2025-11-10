<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controllers/cart_controller.php';

function read_request_payload()
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

function respond($payload, $code = 200)
{
    http_response_code($code);
    echo json_encode($payload);
    exit;
}

$input = read_request_payload();
$productId = isset($input['product_id']) ? (int)$input['product_id'] : 0;
$quantity = isset($input['quantity']) ? (int)$input['quantity'] : 1;

if ($productId <= 0) {
    respond([
        'success' => false,
        'message' => 'Invalid product selected.'
    ], 400);
}

$customerId = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
$guestKey = $_SESSION['cart_guest_key'] ?? null;
if (!$guestKey) {
    $guestKey = 'guest:' . session_id();
    $_SESSION['cart_guest_key'] = $guestKey;
}

try {
    $cartId = add_to_cart_ctr($productId, $quantity, $customerId, $guestKey);
    $cartData = get_user_cart_ctr($customerId, $guestKey);

    respond([
        'success' => true,
        'message' => 'Item added to cart.',
        'cart_id' => $cartId,
        'cart' => $cartData
    ]);
} catch (Throwable $th) {
    respond([
        'success' => false,
        'message' => $th->getMessage()
    ], 500);
}

