<?php
/**
 * Test script for category functionality
 * This script tests the category CRUD operations
 */

require_once __DIR__ . '/settings/core.php';
require_once __DIR__ . '/controllers/category_controller.php';

echo "<h2>Category Management Test</h2>";

// Test 1: Add a category
echo "<h3>Test 1: Adding a category</h3>";
$testCategory = "Test Category " . date('Y-m-d H:i:s');
$result = add_category_ctr(['cat_name' => $testCategory]);

if (is_numeric($result)) {
    echo " Category added successfully with ID: $result<br>";
    $testCategoryId = $result;
} else {
    echo " Failed to add category: $result<br>";
    exit;
}

// Test 2: Get all categories
echo "<h3>Test 2: Fetching all categories</h3>";
$categories = get_categories_ctr();
if ($categories !== false) {
    echo " Found " . count($categories) . " categories<br>";
    foreach ($categories as $category) {
        echo "- ID: {$category['cat_id']}, Name: {$category['cat_name']}<br>";
    }
} else {
    echo " Failed to fetch categories<br>";
}

// Test 3: Update category
echo "<h3>Test 3: Updating category</h3>";
$newName = "Updated Test Category " . date('Y-m-d H:i:s');
$result = edit_category_ctr(['cat_id' => $testCategoryId, 'cat_name' => $newName]);

if ($result === 'success') {
    echo " Category updated successfully<br>";
} else {
    echo " Failed to update category: $result<br>";
}

// Test 4: Get single category
echo "<h3>Test 4: Fetching single category</h3>";
$category = get_category_ctr($testCategoryId);
if ($category !== false) {
    echo " Category found: ID: {$category['cat_id']}, Name: {$category['cat_name']}<br>";
} else {
    echo "Failed to fetch single category<br>";
}

// Test 5: Delete category
echo "<h3>Test 5: Deleting category</h3>";
$result = delete_category_ctr(['cat_id' => $testCategoryId]);

if ($result === 'success') {
    echo " Category deleted successfully<br>";
} else {
    echo " Failed to delete category: $result<br>";
}

// Test 6: Verify deletion
echo "<h3>Test 6: Verifying deletion</h3>";
$category = get_category_ctr($testCategoryId);
if ($category === false) {
    echo " Category successfully deleted (not found)<br>";
} else {
    echo " Category still exists after deletion<br>";
}

echo "<h3>All tests completed!</h3>";
?>
