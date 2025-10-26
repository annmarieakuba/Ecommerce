<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/brand_controller.php';

try {
    // Get POST data
    $brand_id = (int)($_POST['brand_id'] ?? 0);
    
    // Validate input
    if ($brand_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid brand ID'
        ]);
        exit;
    }
    
    // Prepare data for controller
    $kwargs = [
        'brand_id' => $brand_id
    ];
    
    $result = delete_brand_ctr($kwargs);
    
    if ($result === 'success') {
        echo json_encode([
            'success' => true,
            'message' => 'Brand deleted successfully'
        ]);
    } elseif ($result === 'in_use') {
        echo json_encode([
            'success' => false,
            'message' => 'Cannot delete brand: It is being used by products'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error deleting brand: ' . $result
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting brand: ' . $e->getMessage()
    ]);
}
