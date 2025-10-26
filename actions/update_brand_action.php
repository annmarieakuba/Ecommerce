<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/brand_controller.php';

try {
    // Get POST data
    $brand_id = (int)($_POST['brand_id'] ?? 0);
    $brand_name = trim($_POST['brand_name'] ?? '');
    $cat_id = (int)($_POST['cat_id'] ?? 0);
    
    // Validate input
    if ($brand_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid brand ID'
        ]);
        exit;
    }
    
    if (empty($brand_name)) {
        echo json_encode([
            'success' => false,
            'message' => 'Brand name is required'
        ]);
        exit;
    }
    
    if ($cat_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Please select a valid category'
        ]);
        exit;
    }
    
    // Prepare data for controller
    $kwargs = [
        'brand_id' => $brand_id,
        'brand_name' => $brand_name,
        'cat_id' => $cat_id
    ];
    
    $result = edit_brand_ctr($kwargs);
    
    if ($result === 'success') {
        echo json_encode([
            'success' => true,
            'message' => 'Brand updated successfully'
        ]);
    } elseif ($result === 'exists') {
        echo json_encode([
            'success' => false,
            'message' => 'Brand name already exists in this category'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error updating brand: ' . $result
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating brand: ' . $e->getMessage()
    ]);
}
