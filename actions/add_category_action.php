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

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Get and validate input data
$cat_name = trim($_POST['cat_name'] ?? '');

// Validate input
if (empty($cat_name)) {
    echo json_encode(['status' => 'error', 'message' => 'Category name is required']);
    exit;
}

if (strlen($cat_name) > 100) {
    echo json_encode(['status' => 'error', 'message' => 'Category name must be 100 characters or less']);
    exit;
}

try {
    $kwargs = [
        'cat_name' => $cat_name
    ];
    
    $result = add_category_ctr($kwargs);
    
    if (is_numeric($result)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Category added successfully',
            'category_id' => $result
        ]);
    } elseif ($result === 'exists') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Category name already exists'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add category: ' . $result
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to add category: ' . $e->getMessage()
    ]);
}
?>
