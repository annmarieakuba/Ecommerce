<?php

require_once __DIR__ . '/../settings/db_class.php';

/**
 * Order class: handles order creation, order detail inserts and payments
 */
class Order extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Generate a unique invoice number (used as order reference)
     */
    public function generateInvoiceNumber($prefix = 'AGRO')
    {
        $random = strtoupper(bin2hex(random_bytes(3)));
        return sprintf('%s-%s%s', $prefix, date('Ymd'), $random);
    }

    /**
     * Create a new order row
     */
    public function createOrder($customerId, $invoiceNo, $status = 'pending', $totalAmount = 0.0)
    {
        $stmt = $this->db->prepare("
            INSERT INTO orders (customer_id, invoice_no, order_date, order_status, total_amount)
            VALUES (?, ?, NOW(), ?, ?)
        ");
        $stmt->bind_param("issd", $customerId, $invoiceNo, $status, $totalAmount);
        if (!$stmt->execute()) {
            throw new RuntimeException('Unable to create order: ' . $stmt->error);
        }
        return $this->db->insert_id;
    }

    /**
     * Insert an order detail record
     */
    public function addOrderDetail($orderId, $productId, $quantity, $unitPrice)
    {
        $stmt = $this->db->prepare("
            INSERT INTO orderdetails (order_id, product_id, qty, unit_price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiid", $orderId, $productId, $quantity, $unitPrice);
        if (!$stmt->execute()) {
            throw new RuntimeException('Unable to add order detail: ' . $stmt->error);
        }
        return true;
    }

    /**
     * Record a payment entry
     */
    public function recordPayment($orderId, $customerId, $amount, $currency = 'USD', $paymentMethod = 'Simulated', $paymentReference = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO payment (amt, customer_id, order_id, currency, payment_method, payment_reference, payment_date)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("diisss", $amount, $customerId, $orderId, $currency, $paymentMethod, $paymentReference);
        if (!$stmt->execute()) {
            throw new RuntimeException('Unable to record payment: ' . $stmt->error);
        }
        return $this->db->insert_id;
    }

    /**
     * Update order status
     */
    public function updateOrderStatus($orderId, $status)
    {
        $stmt = $this->db->prepare("
            UPDATE orders
            SET order_status = ?
            WHERE order_id = ?
        ");
        $stmt->bind_param("si", $status, $orderId);
        $stmt->execute();
        return $stmt->affected_rows >= 0;
    }

    /**
     * Get orders for a customer
     */
    public function getOrdersByCustomer($customerId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM orders
            WHERE customer_id = ?
            ORDER BY order_date DESC
        ");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Process checkout end-to-end within a transaction
     */
    public function processCheckout($customerId, array $cartPayload, $currency = 'USD', $paymentMethod = 'Simulated')
    {
        if (empty($cartPayload['items'])) {
            throw new InvalidArgumentException('Cannot checkout with an empty cart.');
        }

        $items = $cartPayload['items'];
        $summary = $cartPayload['summary'] ?? [];

        $subtotal = 0.0;
        foreach ($items as $item) {
            $qty = (int)($item['qty'] ?? 0);
            $unitPrice = (float)($item['product_price'] ?? 0);
            if ($qty <= 0) {
                throw new InvalidArgumentException('Invalid quantity detected in cart.');
            }
            $subtotal += $qty * $unitPrice;
        }
        $subtotal = round($subtotal, 2);

        $invoiceNo = $this->generateInvoiceNumber();
        $paymentReference = 'PAY-' . strtoupper(bin2hex(random_bytes(4)));

        $this->db->begin_transaction();
        try {
            $orderId = $this->createOrder($customerId, $invoiceNo, 'pending', $subtotal);

            foreach ($items as $item) {
                $this->addOrderDetail(
                    $orderId,
                    (int)$item['product_id'],
                    (int)$item['qty'],
                    (float)$item['product_price']
                );
            }

            $this->recordPayment(
                $orderId,
                $customerId,
                $subtotal,
                $currency,
                $paymentMethod,
                $paymentReference
            );

            $this->updateOrderStatus($orderId, 'completed');

            $this->db->commit();

            return [
                'order_id' => $orderId,
                'invoice_no' => $invoiceNo,
                'payment_reference' => $paymentReference,
                'total_amount' => $subtotal,
                'total_items' => $summary['total_items'] ?? array_sum(array_column($items, 'qty'))
            ];
        } catch (Throwable $th) {
            $this->db->rollback();
            throw $th;
        }
    }
}

