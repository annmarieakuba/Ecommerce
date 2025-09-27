<?php
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/category_controller.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

// Check if user is admin
if (!is_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Access denied. Admin privileges required.']);
    exit;
}

try {
    $categories = get_categories_ctr();
    
    if ($categories !== false) {
        echo json_encode([
            'status' => 'success',
            'data' => $categories,
            'message' => 'Categories fetched successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'data' => [],
            'message' => 'No categories found'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch categories: ' . $e->getMessage()
    ]);
}
?>
