<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Products - AgroCare Farm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/all_product.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">
                <i class="fas fa-seedling me-2"></i>AgroCare Farm
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php"><i class="fas fa-home me-1"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="all_product.php"><i class="fas fa-apple-alt me-1"></i>All Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_search_result.php"><i class="fas fa-search me-1"></i>Search</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['customer_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'User'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if (isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === 1): ?>
                                    <li><a class="dropdown-item" href="../admin/category.php"><i class="fas fa-leaf me-2"></i>Manage Categories</a></li>
                                    <li><a class="dropdown-item" href="../admin/brand.php"><i class="fas fa-tags me-2"></i>Manage Brands</a></li>
                                    <li><a class="dropdown-item" href="../admin/product.php"><i class="fas fa-apple-alt me-2"></i>Manage Products</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../login/register.php"><i class="fas fa-user-plus me-1"></i>Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../login/login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid" style="padding-top: 80px;">
        <div class="container">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="page-header text-center py-5">
                        <h1 class="display-4 fw-bold text-success mb-3">
                            <i class="fas fa-apple-alt me-3"></i>Our Agricultural Products
                        </h1>
                        <p class="lead text-muted">Discover our wide range of fresh, organic agricultural products</p>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="filter-card p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-success">
                                    <i class="fas fa-filter me-2"></i>Filter by Category
                                </label>
                                <select class="form-select" id="categoryFilter">
                                    <option value="">All Categories</option>
                                    <!-- Categories will be loaded via AJAX -->
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-success">
                                    <i class="fas fa-tags me-2"></i>Filter by Brand
                                </label>
                                <select class="form-select" id="brandFilter">
                                    <option value="">All Brands</option>
                                    <!-- Brands will be loaded via AJAX -->
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-success">
                                    <i class="fas fa-sort me-2"></i>Sort by
                                </label>
                                <select class="form-select" id="sortFilter">
                                    <option value="name">Product Name</option>
                                    <option value="price_low">Price: Low to High</option>
                                    <option value="price_high">Price: High to Low</option>
                                    <option value="category">Category</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row" id="productsGrid">
                <!-- Products will be loaded here via AJAX -->
            </div>

            <!-- Loading Indicator -->
            <div class="row" id="loadingIndicator" style="display: none;">
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading products...</p>
                </div>
            </div>

            <!-- No Products Message -->
            <div class="row" id="noProductsMessage" style="display: none;">
                <div class="col-12 text-center py-5">
                    <i class="fas fa-apple-alt fa-5x text-muted mb-4"></i>
                    <h3 class="text-muted">No Products Found</h3>
                    <p class="text-muted">Try adjusting your filters or check back later for new products.</p>
                    <a href="../index.php" class="btn btn-success">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </a>
                </div>
            </div>

            <!-- Pagination -->
            <div class="row mt-5">
                <div class="col-12">
                    <nav aria-label="Products pagination">
                        <ul class="pagination justify-content-center" id="pagination">
                            <!-- Pagination will be generated here -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalTitle">
                        <i class="fas fa-apple-alt me-2"></i>Product Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="productModalBody">
                    <!-- Product details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="addToCartBtn">
                        <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">
                        <i class="fas fa-seedling me-2"></i>
                        &copy; 2024 AgroCare Farm. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/all_product.js"></script>
</body>
</html>
