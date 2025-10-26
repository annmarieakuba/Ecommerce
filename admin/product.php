<?php
require_once __DIR__ . '/../settings/core.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: ../login/login.php');
    exit;
}

// Check if user is admin
if (!is_admin()) {
    header('Location: ../login/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AgroCare Farm Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/product.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-seedling"></i> AgroCare Farm Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../index.php">
                    <i class="fas fa-home"></i> Home
                </a>
                <a class="nav-link" href="category.php">
                    <i class="fas fa-leaf"></i> Categories
                </a>
                <a class="nav-link" href="brand.php">
                    <i class="fas fa-tags"></i> Brands
                </a>
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-apple-alt"></i> AgroCare Farm Products</h2>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus"></i> Add New AgroCare Farm Product
                    </button>
                </div>

                <!-- Products Table -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-apple-alt"></i> AgroCare Farm Agricultural Products</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="productsTable">
                                <thead class="table-success">
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Product Title</th>
                                        <th>Category</th>
                                        <th>Brand</th>
                                        <th>Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <!-- Products will be loaded here via AJAX -->
                                </tbody>
                            </table>
                        </div>
                        <div id="noProductsMessage" class="text-center text-muted py-4" style="display: none;">
                            <i class="fas fa-apple-alt fa-3x mb-3"></i>
                            <p>No AgroCare farm products found. Add your first agricultural product to get started!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Add New AgroCare Farm Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addProductForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addProductTitle" class="form-label">Product Title *</label>
                                    <input type="text" class="form-control" id="addProductTitle" name="product_title" required maxlength="200" placeholder="e.g., Fresh Organic Tomatoes">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="addProductPrice" class="form-label">Product Price *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="addProductPrice" name="product_price" required min="0.01" step="0.01" placeholder="0.00">
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="addProductCategory" class="form-label">Category *</label>
                                    <select class="form-select" id="addProductCategory" name="product_cat" required>
                                        <option value="">Select a category...</option>
                                        <!-- Categories will be loaded here via AJAX -->
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="addProductBrand" class="form-label">Brand *</label>
                                    <select class="form-select" id="addProductBrand" name="product_brand" required>
                                        <option value="">Select a brand...</option>
                                        <!-- Brands will be loaded here via AJAX -->
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addProductImage" class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="addProductImage" name="product_image" accept="image/*">
                                    <div class="form-text">Upload an image of your farm product (max 5MB)</div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="addProductKeywords" class="form-label">Keywords</label>
                                    <input type="text" class="form-control" id="addProductKeywords" name="product_keywords" maxlength="100" placeholder="e.g., organic, fresh, local">
                                    <div class="form-text">Separate keywords with commas</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="addProductDesc" class="form-label">Product Description</label>
                            <textarea class="form-control" id="addProductDesc" name="product_desc" rows="3" maxlength="500" placeholder="Describe your agricultural product..."></textarea>
                            <div class="form-text">Maximum 500 characters</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Add AgroCare Farm Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit AgroCare Farm Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editProductForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="editProductId" name="product_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductTitle" class="form-label">Product Title *</label>
                                    <input type="text" class="form-control" id="editProductTitle" name="product_title" required maxlength="200">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="editProductPrice" class="form-label">Product Price *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="editProductPrice" name="product_price" required min="0.01" step="0.01">
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="editProductCategory" class="form-label">Category *</label>
                                    <select class="form-select" id="editProductCategory" name="product_cat" required>
                                        <option value="">Select a category...</option>
                                        <!-- Categories will be loaded here via AJAX -->
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="editProductBrand" class="form-label">Brand *</label>
                                    <select class="form-select" id="editProductBrand" name="product_brand" required>
                                        <option value="">Select a brand...</option>
                                        <!-- Brands will be loaded here via AJAX -->
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductImage" class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="editProductImage" name="product_image" accept="image/*">
                                    <div class="form-text">Upload a new image (max 5MB)</div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="editProductKeywords" class="form-label">Keywords</label>
                                    <input type="text" class="form-control" id="editProductKeywords" name="product_keywords" maxlength="100">
                                    <div class="form-text">Separate keywords with commas</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editProductDesc" class="form-label">Product Description</label>
                            <textarea class="form-control" id="editProductDesc" name="product_desc" rows="3" maxlength="500"></textarea>
                            <div class="form-text">Maximum 500 characters</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Update AgroCare Farm Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer" class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/product.js"></script>
</body>
</html>
