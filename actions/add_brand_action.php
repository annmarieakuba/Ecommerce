<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/brand_controller.php';

try {
    // Get POST data
    $brand_name = trim($_POST['brand_name'] ?? '');
    $cat_id = (int)($_POST['cat_id'] ?? 0);
    
    // Validate input
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
        'brand_name' => $brand_name,
        'cat_id' => $cat_id
    ];
    
    $result = add_brand_ctr($kwargs);
    
    if (is_numeric($result)) {
        echo json_encode([
            'success' => true,
            'message' => 'Brand added successfully',
            'brand_id' => $result
        ]);
    } elseif ($result === 'exists') {
        echo json_encode([
            'success' => false,
            'message' => 'Brand name already exists in this category'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error adding brand: ' . $result
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error adding brand: ' . $e->getMessage()
    ]);
}
