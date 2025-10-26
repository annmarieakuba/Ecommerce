<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/product_controller.php';

try {
    // Get POST data
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    // Validate input
    if ($product_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID'
        ]);
        exit;
    }
    
    // Prepare data for controller
    $kwargs = [
        'product_id' => $product_id
    ];
    
    $result = delete_product_ctr($kwargs);
    
    if ($result === 'success') {
        echo json_encode([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    } elseif ($result === 'in_cart') {
        echo json_encode([
            'success' => false,
            'message' => 'Cannot delete product: It is currently in shopping carts'
        ]);
    } elseif ($result === 'in_orders') {
        echo json_encode([
            'success' => false,
            'message' => 'Cannot delete product: It has been ordered by customers'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error deleting product: ' . $result
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting product: ' . $e->getMessage()
    ]);
}
