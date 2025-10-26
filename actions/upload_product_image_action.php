<?php
header('Content-Type: application/json');

try {
    // Check if file was uploaded
    if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode([
            'success' => false,
            'message' => 'No file uploaded or upload error occurred'
        ]);
        exit;
    }
    
    $file = $_FILES['product_image'];
    $user_id = (int)($_POST['user_id'] ?? 1); // Default to user 1 if not provided
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.'
        ]);
        exit;
    }
    
    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        echo json_encode([
            'success' => false,
            'message' => 'File size too large. Maximum size is 5MB.'
        ]);
        exit;
    }
    
    // Create upload directory structure
    $uploadDir = __DIR__ . '/../uploads/';
    $userDir = $uploadDir . 'u' . $user_id . '/';
    $productDir = $userDir . 'p' . $product_id . '/';
    
    // Create directories if they don't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    if (!is_dir($userDir)) {
        mkdir($userDir, 0755, true);
    }
    
    if (!is_dir($productDir)) {
        mkdir($productDir, 0755, true);
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'product_' . time() . '_' . uniqid() . '.' . $fileExtension;
    $filePath = $productDir . $fileName;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Return relative path from uploads directory
        $relativePath = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $fileName;
        
        echo json_encode([
            'success' => true,
            'message' => 'Image uploaded successfully',
            'file_path' => $relativePath,
            'file_name' => $fileName
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to move uploaded file'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error uploading image: ' . $e->getMessage()
    ]);
}
