<?php

require_once __DIR__ . '/../classes/order_class.php';

function order_instance()
{
    static $order = null;
    if ($order === null) {
        $order = new Order();
    }
    return $order;
}

function create_order_ctr($customerId, $invoiceNo, $status = 'pending', $totalAmount = 0.0)
{
    return order_instance()->createOrder($customerId, $invoiceNo, $status, $totalAmount);
}

/**
 * @param int   $orderId
 * @param array $details Array of ['product_id' => int, 'qty' => int, 'unit_price' => float]
 */
function add_order_details_ctr($orderId, array $details)
{
    $order = order_instance();
    foreach ($details as $detail) {
        $order->addOrderDetail(
            (int)$orderId,
            (int)$detail['product_id'],
            (int)$detail['qty'],
            (float)$detail['unit_price']
        );
    }
    return true;
}

function record_payment_ctr($orderId, $customerId, $amount, $currency = 'USD', $paymentMethod = 'Simulated', $reference = null)
{
    return order_instance()->recordPayment($orderId, $customerId, $amount, $currency, $paymentMethod, $reference);
}

function process_checkout_ctr($customerId, array $cartPayload, $currency = 'USD', $paymentMethod = 'Simulated')
{
    return order_instance()->processCheckout($customerId, $cartPayload, $currency, $paymentMethod);
}

function get_customer_orders_ctr($customerId)
{
    return order_instance()->getOrdersByCustomer($customerId);
}

function generate_invoice_no_ctr()
{
    return order_instance()->generateInvoiceNumber();
}

