<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/brand_controller.php';

try {
    $brands = get_brands_ctr();
    
    if ($brands !== false) {
        echo json_encode([
            'success' => true,
            'data' => $brands,
            'message' => 'Brands fetched successfully'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'data' => [],
            'message' => 'No brands found'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching brands: ' . $e->getMessage()
    ]);
}
