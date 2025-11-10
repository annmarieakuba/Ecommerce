<?php
session_start();

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if (substr($baseDir, -5) === '/view') {
    $baseDir = substr($baseDir, 0, -5);
}
$appBasePath = ($baseDir === '' || $baseDir === '.') ? '/' : $baseDir . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Cart - AgroCare Farm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
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
                        <a class="nav-link" href="all_product.php"><i class="fas fa-apple-alt me-1"></i>All Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cart.php"><i class="fas fa-shopping-cart me-1"></i>Cart</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="cart.php">
                            <i class="fas fa-shopping-cart me-1"></i>Cart
                            <span class="badge bg-light text-success ms-1" data-cart-count style="display: none;">0</span>
                        </a>
                    </li>
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

    <main class="container" style="padding-top: 90px; padding-bottom: 40px;">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 fw-bold text-success mb-1"><i class="fas fa-shopping-basket me-2"></i>Your Cart</h1>
                        <p class="text-muted mb-0">Review and manage the items before you checkout.</p>
                    </div>
                    <a href="all_product.php" class="btn btn-outline-success">
                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
        </div>

        <div id="cartFeedback"></div>

        <div class="card shadow-sm mb-4">
            <div class="card-body p-0">
                <div id="cartLoadingState" class="justify-content-center align-items-center p-4" style="display: none;">
                    <div class="spinner-border text-success me-2" role="status"></div>
                    <span class="text-success">Loading cart...</span>
                </div>
                <div id="cartItemsContainer" class="p-4"></div>
            </div>
            <div class="card-footer bg-light">
                <div class="row align-items-center g-3">
                    <div class="col-md-4">
                        <div class="text-muted small mb-1">Items in Cart</div>
                        <div class="fw-semibold"><span id="cartItemCount">0</span> (<span id="cartUniqueCount">0</span> unique)</div>
                    </div>
                    <div class="col-md-4 text-md-center">
                        <button class="btn btn-outline-danger" id="emptyCartBtn">
                            <i class="fas fa-trash-alt me-2"></i>Empty Cart
                        </button>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="text-muted small mb-1">Subtotal</div>
                        <div class="h5 fw-bold text-success mb-3" id="cartSubtotal">$0.00</div>
                        <a href="checkout.php" class="btn btn-success" id="proceedToCheckoutBtn">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">
                <i class="fas fa-seedling me-2"></i>
                &copy; <?php echo date('Y'); ?> AgroCare Farm. All rights reserved.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.APP_BASE_PATH = '<?php echo htmlspecialchars($appBasePath, ENT_QUOTES); ?>';
    </script>
    <script src="../js/cart.js"></script>
</body>
</html>

