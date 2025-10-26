<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/product_controller.php';

try {
    // Get POST data
    $product_id = (int)($_POST['product_id'] ?? 0);
    $product_title = trim($_POST['product_title'] ?? '');
    $product_price = floatval($_POST['product_price'] ?? 0);
    $product_cat = (int)($_POST['product_cat'] ?? 0);
    $product_brand = (int)($_POST['product_brand'] ?? 0);
    $product_desc = trim($_POST['product_desc'] ?? '');
    $product_image = trim($_POST['product_image'] ?? '');
    $product_keywords = trim($_POST['product_keywords'] ?? '');
    
    // Validate input
    if ($product_id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID'
        ]);
        exit;
    }
    
    if (empty($product_title)) {
        echo json_encode([
            'success' => false,
            'message' => 'Product title is required'
        ]);
        exit;
    }
    
    if ($product_price <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Product price must be greater than 0'
        ]);
        exit;
    }
    
    if ($product_cat <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Please select a valid category'
        ]);
        exit;
    }
    
    if ($product_brand <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Please select a valid brand'
        ]);
        exit;
    }
    
    // Prepare data for controller
    $kwargs = [
        'product_id' => $product_id,
        'product_title' => $product_title,
        'product_price' => $product_price,
        'product_cat' => $product_cat,
        'product_brand' => $product_brand,
        'product_desc' => $product_desc,
        'product_image' => $product_image,
        'product_keywords' => $product_keywords
    ];
    
    $result = edit_product_ctr($kwargs);
    
    if ($result === 'success') {
        echo json_encode([
            'success' => true,
            'message' => 'Product updated successfully'
        ]);
    } elseif ($result === 'missing_fields') {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill in all required fields'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error updating product: ' . $result
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating product: ' . $e->getMessage()
    ]);
}
