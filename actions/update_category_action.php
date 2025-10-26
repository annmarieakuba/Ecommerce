<?php
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/category_controller.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if user is admin
if (!is_admin()) {
    echo json_encode(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get and validate input data
$cat_id = trim($_POST['cat_id'] ?? '');
$cat_name = trim($_POST['cat_name'] ?? '');

// Validate input
if (empty($cat_id) || !is_numeric($cat_id)) {
    echo json_encode(['success' => false, 'message' => 'Valid category ID is required']);
    exit;
}

if (empty($cat_name)) {
    echo json_encode(['success' => false, 'message' => 'Category name is required']);
    exit;
}

if (strlen($cat_name) > 100) {
    echo json_encode(['success' => false, 'message' => 'Category name must be 100 characters or less']);
    exit;
}

try {
    $kwargs = [
        'cat_id' => (int)$cat_id,
        'cat_name' => $cat_name
    ];
    
    $result = edit_category_ctr($kwargs);
    
    if ($result === 'success') {
        echo json_encode([
            'success' => true,
            'message' => 'Category updated successfully'
        ]);
    } elseif ($result === 'exists') {
        echo json_encode([
            'success' => false,
            'message' => 'Category name already exists'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update category: ' . $result
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update category: ' . $e->getMessage()
    ]);
}
?>
