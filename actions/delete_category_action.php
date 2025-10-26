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

// Validate input
if (empty($cat_id) || !is_numeric($cat_id)) {
    echo json_encode(['success' => false, 'message' => 'Valid category ID is required']);
    exit;
}

try {
    $kwargs = [
        'cat_id' => (int)$cat_id
    ];
    
    $result = delete_category_ctr($kwargs);
    
    if ($result === 'success') {
        echo json_encode([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    } elseif ($result === 'in_use') {
        echo json_encode([
            'success' => false,
            'message' => 'Cannot delete category. It is being used by products.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete category: ' . $result
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete category: ' . $e->getMessage()
    ]);
}
?>
