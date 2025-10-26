<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/product_controller.php';

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'get_all':
            $products = view_all_products_ctr();
            if ($products !== false) {
                echo json_encode([
                    'success' => true,
                    'data' => $products,
                    'message' => 'Products fetched successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'data' => [],
                    'message' => 'No products found'
                ]);
            }
            break;
            
        case 'get_single':
            $product_id = (int)($_GET['id'] ?? 0);
            if ($product_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid product ID'
                ]);
                break;
            }
            
            $product = view_single_product_ctr($product_id);
            if ($product !== false) {
                echo json_encode([
                    'success' => true,
                    'data' => $product,
                    'message' => 'Product fetched successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Product not found'
                ]);
            }
            break;
            
        case 'search':
            $query = trim($_GET['query'] ?? '');
            if (empty($query)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Search query is required'
                ]);
                break;
            }
            
            $products = search_products_ctr($query);
            if ($products !== false) {
                echo json_encode([
                    'success' => true,
                    'data' => $products,
                    'message' => 'Search results found'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'data' => [],
                    'message' => 'No products found matching your search'
                ]);
            }
            break;
            
        case 'filter_category':
            $cat_id = (int)($_GET['cat_id'] ?? 0);
            if ($cat_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid category ID'
                ]);
                break;
            }
            
            $products = filter_products_by_category_ctr($cat_id);
            if ($products !== false) {
                echo json_encode([
                    'success' => true,
                    'data' => $products,
                    'message' => 'Products filtered by category'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'data' => [],
                    'message' => 'No products found in this category'
                ]);
            }
            break;
            
        case 'filter_brand':
            $brand_id = (int)($_GET['brand_id'] ?? 0);
            if ($brand_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid brand ID'
                ]);
                break;
            }
            
            $products = filter_products_by_brand_ctr($brand_id);
            if ($products !== false) {
                echo json_encode([
                    'success' => true,
                    'data' => $products,
                    'message' => 'Products filtered by brand'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'data' => [],
                    'message' => 'No products found for this brand'
                ]);
            }
            break;
            
        case 'filter_category_brand':
            $cat_id = (int)($_GET['cat_id'] ?? 0);
            $brand_id = (int)($_GET['brand_id'] ?? 0);
            
            if ($cat_id <= 0 || $brand_id <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid category or brand ID'
                ]);
                break;
            }
            
            $products = filter_products_by_category_and_brand_ctr($cat_id, $brand_id);
            if ($products !== false) {
                echo json_encode([
                    'success' => true,
                    'data' => $products,
                    'message' => 'Products filtered by category and brand'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'data' => [],
                    'message' => 'No products found for this category and brand combination'
                ]);
            }
            break;
            
        case 'get_paginated':
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 10);
            $offset = ($page - 1) * $limit;
            
            $products = get_products_paginated_ctr($offset, $limit);
            $total = get_products_count_ctr();
            
            if ($products !== false) {
                echo json_encode([
                    'success' => true,
                    'data' => $products,
                    'pagination' => [
                        'current_page' => $page,
                        'per_page' => $limit,
                        'total' => $total,
                        'total_pages' => ceil($total / $limit)
                    ],
                    'message' => 'Products fetched successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'data' => [],
                    'pagination' => [
                        'current_page' => $page,
                        'per_page' => $limit,
                        'total' => 0,
                        'total_pages' => 0
                    ],
                    'message' => 'No products found'
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action specified'
            ]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
