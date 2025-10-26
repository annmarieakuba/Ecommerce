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
    <title>AgroCare Farm Brand Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/brand.css" rel="stylesheet">
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
                    <h2><i class="fas fa-tags"></i> AgroCare Farm Brands</h2>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                        <i class="fas fa-plus"></i> Add New AgroCare Farm Brand
                    </button>
                </div>

                <!-- Brands Table -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-tags"></i> AgroCare Farm Brands by Category</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="brandsTable">
                                <thead class="table-success">
                                    <tr>
                                        <th>ID</th>
                                        <th>Brand Name</th>
                                        <th>Category</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="brandsTableBody">
                                    <!-- Brands will be loaded here via AJAX -->
                                </tbody>
                            </table>
                        </div>
                        <div id="noBrandsMessage" class="text-center text-muted py-4" style="display: none;">
                            <i class="fas fa-tags fa-3x mb-3"></i>
                            <p>No AgroCare farm brands found. Add your first farm brand to get started!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Brand Modal -->
    <div class="modal fade" id="addBrandModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Add New AgroCare Farm Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addBrandForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="addBrandName" class="form-label">Brand Name *</label>
                            <input type="text" class="form-control" id="addBrandName" name="brand_name" required maxlength="100" placeholder="e.g., Organic Seeds, Premium Feed">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="addBrandCategory" class="form-label">Category *</label>
                            <select class="form-select" id="addBrandCategory" name="cat_id" required>
                                <option value="">Select a category...</option>
                                <!-- Categories will be loaded here via AJAX -->
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Add AgroCare Farm Brand
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Brand Modal -->
    <div class="modal fade" id="editBrandModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit AgroCare Farm Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editBrandForm">
                    <div class="modal-body">
                        <input type="hidden" id="editBrandId" name="brand_id">
                        <div class="mb-3">
                            <label for="editBrandName" class="form-label">Brand Name *</label>
                            <input type="text" class="form-control" id="editBrandName" name="brand_name" required maxlength="100">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="editBrandCategory" class="form-label">Category *</label>
                            <select class="form-select" id="editBrandCategory" name="cat_id" required>
                                <option value="">Select a category...</option>
                                <!-- Categories will be loaded here via AJAX -->
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Update AgroCare Farm Brand
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteBrandModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this AgroCare farm brand?</p>
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> This action cannot be undone. If this brand is being used by products, the deletion will be prevented.
                    </div>
                    <input type="hidden" id="deleteBrandId">
                    <p><strong>Brand:</strong> <span id="deleteBrandName"></span></p>
                    <p><strong>Category:</strong> <span id="deleteBrandCategory"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash"></i> Delete AgroCare Farm Brand
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer" class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/brand.js"></script>
</body>
</html>
