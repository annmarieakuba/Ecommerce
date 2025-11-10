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
    <title>Checkout - AgroCare Farm</title>
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
                        <a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart me-1"></i>Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="checkout.php"><i class="fas fa-credit-card me-1"></i>Checkout</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
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
                        <h1 class="h3 fw-bold text-success mb-1"><i class="fas fa-credit-card me-2"></i>Checkout</h1>
                        <p class="text-muted mb-0">Simulate your payment and place your order.</p>
                    </div>
                    <a href="cart.php" class="btn btn-outline-success">
                        <i class="fas fa-arrow-left me-2"></i>Back to Cart
                    </a>
                </div>
            </div>
        </div>

        <?php if (!isset($_SESSION['customer_id'])): ?>
            <div class="alert alert-warning">
                <i class="fas fa-info-circle me-2"></i>Please log in to complete your checkout.
                <a href="../login/login.php" class="alert-link">Login now</a>
            </div>
        <?php endif; ?>

        <div id="checkoutFeedback"></div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h2 class="h5 mb-0 text-success"><i class="fas fa-box-open me-2"></i>Order Summary</h2>
                    </div>
                    <div class="card-body p-0">
                        <div id="checkoutLoadingState" class="justify-content-center align-items-center p-4" style="display: none;">
                            <div class="spinner-border text-success me-2" role="status"></div>
                            <span class="text-success">Preparing your order...</span>
                        </div>
                        <div id="checkoutItemsContainer" class="p-4"></div>
                    </div>
                </div>
                <div id="checkoutResult"></div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="h5 text-success mb-3"><i class="fas fa-receipt me-2"></i>Payment Details</h3>
                        <div class="mb-3">
                            <div class="text-muted small">Items</div>
                            <div class="fw-semibold"><span id="checkoutItemCount">0</span></div>
                        </div>
                        <div class="mb-4">
                            <div class="text-muted small">Subtotal</div>
                            <div class="h4 fw-bold text-success" id="checkoutSubtotal">$0.00</div>
                        </div>
                        <input type="hidden" id="checkoutCurrency" value="USD">
                        <input type="hidden" id="checkoutPaymentMethod" value="Simulated Modal Payment">
                        <button class="btn btn-success w-100 btn-lg" id="simulatePaymentBtn" <?php echo isset($_SESSION['customer_id']) ? '' : 'disabled'; ?>>
                            <i class="fas fa-money-check-alt me-2"></i>Simulate Payment
                        </button>
                        <?php if (!isset($_SESSION['customer_id'])): ?>
                            <small class="text-danger d-block mt-2">Login required to continue.</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> AgroCare Farm. All rights reserved.</p>
        </div>
    </footer>

    <div class="modal fade" id="paymentConfirmationModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel"><i class="fas fa-university me-2"></i>Simulated Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">This is a simulated payment. Confirming will finalize your order.</p>
                    <ul class="list-unstyled mb-0">
                        <li><i class="fas fa-check text-success me-2"></i>Payment method: <strong>Dummy Transfer</strong></li>
                        <li><i class="fas fa-check text-success me-2"></i>No real charges will be made</li>
                        <li><i class="fas fa-check text-success me-2"></i>Your cart items will move to orders</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmPaymentBtn">Yes, I have paid</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.APP_BASE_PATH = '<?php echo htmlspecialchars($appBasePath, ENT_QUOTES); ?>';
    </script>
    <script src="../js/cart.js"></script>
    <script src="../js/checkout.js"></script>
</body>
</html>

