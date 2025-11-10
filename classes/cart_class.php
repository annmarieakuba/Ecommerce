<?php

require_once __DIR__ . '/../settings/db_class.php';

/**
 * Cart class: handles CRUD operations for shopping cart items
 */
class Cart extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Normalize quantity values and enforce sane boundaries
     */
    private function normalizeQuantity($quantity)
    {
        $qty = (int)$quantity;
        if ($qty <= 0) {
            $qty = 1;
        }
        if ($qty > 999) {
            $qty = 999;
        }
        return $qty;
    }

    /**
     * Resolve the guest cart identifier (session-based fallback)
     */
    private function resolveGuestKey($guestKey = null)
    {
        if ($guestKey) {
            return substr($guestKey, 0, 250);
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return substr('guest:' . session_id(), 0, 250);
    }

    /**
     * Ensure any guest cart items move to a logged-in customer cart
     */
    public function mergeGuestCart($guestKey, $customerId)
    {
        $customerId = (int)$customerId;
        $guestIdentifier = $this->resolveGuestKey($guestKey);

        if ($customerId <= 0 || empty($guestIdentifier)) {
            return false;
        }

        $this->db->begin_transaction();
        try {
            $selectGuest = $this->db->prepare("
                SELECT c_id, p_id, qty
                FROM cart
                WHERE c_id IS NULL AND ip_add = ?
            ");
            $selectGuest->bind_param("s", $guestIdentifier);
            $selectGuest->execute();
            $guestItems = $selectGuest->get_result()->fetch_all(MYSQLI_ASSOC);

            if (empty($guestItems)) {
                $this->db->commit();
                return false;
            }

            $selectExisting = $this->db->prepare("
                SELECT c_id, qty
                FROM cart
                WHERE c_id = ? AND p_id = ?
                LIMIT 1
            ");

            $updateExisting = $this->db->prepare("
                UPDATE cart
                SET qty = ?, updated_at = NOW()
                WHERE c_id = ?
            ");

            $insertNew = $this->db->prepare("
                INSERT INTO cart (p_id, ip_add, c_id, qty)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($guestItems as $item) {
                $productId = (int)$item['p_id'];
                $quantity = $this->normalizeQuantity($item['qty']);

                $selectExisting->bind_param("ii", $customerId, $productId);
                $selectExisting->execute();
                $existing = $selectExisting->get_result()->fetch_assoc();

                if ($existing) {
                    $newQty = $this->normalizeQuantity($existing['qty'] + $quantity);
                    $updateExisting->bind_param("ii", $newQty, $existing['c_id']);
                    $updateExisting->execute();
                } else {
                    $insertNew->bind_param("isii", $productId, $guestIdentifier, $customerId, $quantity);
                    $insertNew->execute();
                }
            }

            $deleteGuest = $this->db->prepare("DELETE FROM cart WHERE c_id = ?");
            foreach ($guestItems as $item) {
                $cartId = (int)$item['c_id'];
                $deleteGuest->bind_param("i", $cartId);
                $deleteGuest->execute();
            }

            $this->db->commit();
            return true;
        } catch (Throwable $th) {
            $this->db->rollback();
            throw $th;
        }
    }

    /**
     * Add a product to the cart (increments quantity when item exists)
     */
    public function addToCart($productId, $quantity, $customerId = null, $guestKey = null)
    {
        $productId = (int)$productId;
        if ($productId <= 0) {
            throw new InvalidArgumentException('Invalid product ID supplied.');
        }

        $qty = $this->normalizeQuantity($quantity);
        $guestIdentifier = $this->resolveGuestKey($guestKey);
        $customerId = $customerId !== null ? (int)$customerId : null;

        // ensure product exists
        $productStmt = $this->db->prepare("
            SELECT product_id, product_price
            FROM products
            WHERE product_id = ?
            LIMIT 1
        ");
        $productStmt->bind_param("i", $productId);
        $productStmt->execute();
        $product = $productStmt->get_result()->fetch_assoc();

        if (!$product) {
            throw new RuntimeException('Product not found.');
        }

        if ($customerId && $customerId > 0) {
            $checkStmt = $this->db->prepare("
                SELECT c_id, qty
                FROM cart
                WHERE c_id = ? AND p_id = ?
                LIMIT 1
            ");
            $checkStmt->bind_param("ii", $customerId, $productId);
        } else {
            $checkStmt = $this->db->prepare("
                SELECT c_id, qty
                FROM cart
                WHERE c_id IS NULL AND ip_add = ? AND p_id = ?
                LIMIT 1
            ");
            $checkStmt->bind_param("si", $guestIdentifier, $productId);
        }

        $checkStmt->execute();
        $existing = $checkStmt->get_result()->fetch_assoc();

        if ($existing) {
            $newQty = $this->normalizeQuantity($existing['qty'] + $qty);
            $updateStmt = $this->db->prepare("
                UPDATE cart
                SET qty = ?, updated_at = NOW()
                WHERE c_id = ?
            ");
            $updateStmt->bind_param("ii", $newQty, $existing['c_id']);
            $updateStmt->execute();
            return $existing['c_id'];
        }

        if ($customerId && $customerId > 0) {
            $insertStmt = $this->db->prepare("
                INSERT INTO cart (p_id, ip_add, c_id, qty)
                VALUES (?, ?, ?, ?)
            ");
            $insertStmt->bind_param("isii", $productId, $guestIdentifier, $customerId, $qty);
        } else {
            $insertStmt = $this->db->prepare("
                INSERT INTO cart (p_id, ip_add, c_id, qty)
                VALUES (?, ?, NULL, ?)
            ");
            $insertStmt->bind_param("isi", $productId, $guestIdentifier, $qty);
        }

        $insertStmt->execute();
        return $this->db->insert_id;
    }

    /**
     * Update the quantity of a cart item
     */
    public function updateCartItem($cartId, $quantity, $customerId = null, $guestKey = null)
    {
        $cartId = (int)$cartId;
        if ($cartId <= 0) {
            throw new InvalidArgumentException('Invalid cart item specified.');
        }

        $qty = $this->normalizeQuantity($quantity);
        $guestIdentifier = $this->resolveGuestKey($guestKey);
        $customerId = $customerId !== null ? (int)$customerId : null;

        if ($customerId && $customerId > 0) {
            $updateStmt = $this->db->prepare("
                UPDATE cart
                SET qty = ?, updated_at = NOW()
                WHERE c_id = ? AND c_id = ?
            ");
            $updateStmt->bind_param("iii", $qty, $cartId, $customerId);
        } else {
            $updateStmt = $this->db->prepare("
                UPDATE cart
                SET qty = ?, updated_at = NOW()
                WHERE c_id = ? AND c_id IS NULL AND ip_add = ?
            ");
            $updateStmt->bind_param("iis", $qty, $cartId, $guestIdentifier);
        }

        $updateStmt->execute();

        if ($updateStmt->affected_rows === 0) {
            // Item might exist but quantity unchanged; verify ownership
            if ($customerId && $customerId > 0) {
                $checkStmt = $this->db->prepare("SELECT c_id FROM cart WHERE c_id = ? AND c_id = ?");
                $checkStmt->bind_param("ii", $cartId, $customerId);
            } else {
                $checkStmt = $this->db->prepare("SELECT c_id FROM cart WHERE c_id = ? AND c_id IS NULL AND ip_add = ?");
                $checkStmt->bind_param("is", $cartId, $guestIdentifier);
            }

            $checkStmt->execute();
            if (!$checkStmt->get_result()->fetch_assoc()) {
                throw new RuntimeException('Unable to update cart item. Item not found or access denied.');
            }
        }

        return true;
    }

    /**
     * Remove a cart item completely
     */
    public function removeCartItem($cartId, $customerId = null, $guestKey = null)
    {
        $cartId = (int)$cartId;
        if ($cartId <= 0) {
            throw new InvalidArgumentException('Invalid cart item specified.');
        }

        $guestIdentifier = $this->resolveGuestKey($guestKey);
        $customerId = $customerId !== null ? (int)$customerId : null;

        if ($customerId && $customerId > 0) {
            $deleteStmt = $this->db->prepare("
                DELETE FROM cart
                WHERE c_id = ? AND c_id = ?
            ");
            $deleteStmt->bind_param("ii", $cartId, $customerId);
        } else {
            $deleteStmt = $this->db->prepare("
                DELETE FROM cart
                WHERE c_id = ? AND c_id IS NULL AND ip_add = ?
            ");
            $deleteStmt->bind_param("is", $cartId, $guestIdentifier);
        }

        $deleteStmt->execute();

        if ($deleteStmt->affected_rows === 0) {
            throw new RuntimeException('Unable to remove cart item. Item not found or access denied.');
        }

        return true;
    }

    /**
     * Empty an entire cart (guest or customer)
     */
    public function emptyCart($customerId = null, $guestKey = null)
    {
        $guestIdentifier = $this->resolveGuestKey($guestKey);
        $customerId = $customerId !== null ? (int)$customerId : null;

        if ($customerId && $customerId > 0) {
            $stmt = $this->db->prepare("DELETE FROM cart WHERE c_id = ? OR (c_id IS NULL AND ip_add = ?)");
            $stmt->bind_param("is", $customerId, $guestIdentifier);
        } else {
            $stmt = $this->db->prepare("DELETE FROM cart WHERE c_id IS NULL AND ip_add = ?");
            $stmt->bind_param("s", $guestIdentifier);
        }

        $stmt->execute();
        return true;
    }

    /**
     * Fetch all cart items for rendering
     */
    public function getCartItems($customerId = null, $guestKey = null)
    {
        $guestIdentifier = $this->resolveGuestKey($guestKey);
        $customerId = $customerId !== null ? (int)$customerId : null;

        if ($customerId && $customerId > 0) {
            // Merge any outstanding guest items when user is logged in
            $this->mergeGuestCart($guestIdentifier, $customerId);

            $stmt = $this->db->prepare("
                SELECT c.c_id, c.p_id AS product_id, c.qty,
                       p.product_title, p.product_price, p.product_image, p.product_desc
                FROM cart c
                INNER JOIN products p ON c.p_id = p.product_id
                WHERE c.c_id = ?
                ORDER BY c.updated_at DESC
            ");
            $stmt->bind_param("i", $customerId);
        } else {
            $stmt = $this->db->prepare("
                SELECT c.c_id, c.p_id AS product_id, c.qty,
                       p.product_title, p.product_price, p.product_image, p.product_desc
                FROM cart c
                INNER JOIN products p ON c.p_id = p.product_id
                WHERE c.c_id IS NULL AND c.ip_add = ?
                ORDER BY c.updated_at DESC
            ");
            $stmt->bind_param("s", $guestIdentifier);
        }

        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $summary = $this->summarizeCart($items);
        return [
            'items' => $this->formatCartItems($items),
            'summary' => $summary
        ];
    }

    /**
     * Count total items (quantity) in cart
     */
    public function countCartItems($customerId = null, $guestKey = null)
    {
        $guestIdentifier = $this->resolveGuestKey($guestKey);
        $customerId = $customerId !== null ? (int)$customerId : null;

        if ($customerId && $customerId > 0) {
            $stmt = $this->db->prepare("SELECT SUM(qty) AS total_qty FROM cart WHERE c_id = ?");
            $stmt->bind_param("i", $customerId);
        } else {
            $stmt = $this->db->prepare("SELECT SUM(qty) AS total_qty FROM cart WHERE c_id IS NULL AND ip_add = ?");
            $stmt->bind_param("s", $guestIdentifier);
        }

        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int)($result['total_qty'] ?? 0);
    }

    /**
     * Helper: summarise cart totals
     */
    private function summarizeCart(array $items)
    {
        $totalItems = 0;
        $totalUnique = count($items);
        $subtotal = 0.0;

        foreach ($items as $item) {
            $qty = (int)($item['qty'] ?? 0);
            $price = (float)($item['product_price'] ?? 0);
            $totalItems += $qty;
            $subtotal += $qty * $price;
        }

        return [
            'total_unique_items' => $totalUnique,
            'total_items' => $totalItems,
            'subtotal' => round($subtotal, 2)
        ];
    }

    /**
     * Helper: format cart items with computed subtotal
     */
    private function formatCartItems(array $items)
    {
        return array_map(function ($item) {
            $qty = (int)$item['qty'];
            $price = (float)$item['product_price'];
            $subtotal = round($qty * $price, 2);

            return [
                'c_id' => (int)$item['c_id'],
                'product_id' => (int)$item['product_id'],
                'qty' => $qty,
                'product_title' => $item['product_title'],
                'product_price' => $price,
                'product_image' => $item['product_image'],
                'product_desc' => $item['product_desc'],
                'line_total' => $subtotal
            ];
        }, $items);
    }
}

