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
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Expires" content="0">
	<title>AgroCare Farm - Fresh Agricultural Products</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<link href="css/index.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>
	<!-- Navigation -->
	<nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
		<div class="container">
			<a class="navbar-brand fw-bold" href="index.php">
				<i class="fas fa-seedling me-2"></i>AgroCare Farm
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav me-auto">
					<li class="nav-item">
						<a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i>Home</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="view/all_product.php"><i class="fas fa-apple-alt me-1"></i>All Products</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="view/product_search_result.php"><i class="fas fa-search me-1"></i>Search</a>
					</li>
				</ul>
				<ul class="navbar-nav">
					<li class="nav-item">
						<a class="nav-link" href="view/cart.php">
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
									<li><a class="dropdown-item" href="admin/category.php"><i class="fas fa-leaf me-2"></i>Manage Categories</a></li>
									<li><a class="dropdown-item" href="admin/brand.php"><i class="fas fa-tags me-2"></i>Manage Brands</a></li>
									<li><a class="dropdown-item" href="admin/product.php"><i class="fas fa-apple-alt me-2"></i>Manage Products</a></li>
									<li><hr class="dropdown-divider"></li>
								<?php endif; ?>
								<li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
							</ul>
						</li>
					<?php else: ?>
						<li class="nav-item">
							<a class="nav-link" href="login/register.php"><i class="fas fa-user-plus me-1"></i>Register</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="login/login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</nav>

	<!-- Hero Section -->
	<section class="hero-section">
		<div class="hero-overlay"></div>
		<div class="container">
			<div class="row align-items-center min-vh-100">
				<div class="col-lg-6">
					<div class="hero-content text-white">
						<h1 class="display-4 fw-bold mb-4">
							<i class="fas fa-seedling me-3"></i>Welcome to AgroCare Farm
						</h1>
						<p class="lead mb-4">Your trusted source for fresh, organic agricultural products. From farm to table, we bring you the finest crops, livestock, and agricultural supplies.</p>
						<div class="hero-buttons">
							<a href="view/all_product.php" class="btn btn-light btn-lg me-3">
								<i class="fas fa-apple-alt me-2"></i>Browse Products
							</a>
							<a href="login/register.php" class="btn btn-outline-light btn-lg">
								<i class="fas fa-user-plus me-2"></i>Join Us
							</a>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="hero-image text-center">
						<i class="fas fa-seedling hero-icon"></i>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Features Section -->
	<section class="features-section py-5">
		<div class="container">
			<div class="row text-center mb-5">
				<div class="col-12">
					<h2 class="display-5 fw-bold text-success mb-3">Why Choose AgroCare Farm?</h2>
					<p class="lead text-muted">We're committed to providing the highest quality agricultural products</p>
				</div>
			</div>
			<div class="row g-4">
				<div class="col-md-4">
					<div class="feature-card text-center p-4">
						<div class="feature-icon mb-3">
							<i class="fas fa-leaf"></i>
						</div>
						<h4 class="fw-bold text-success">Organic Products</h4>
						<p class="text-muted">100% organic and chemical-free agricultural products grown with care and sustainability.</p>
					</div>
				</div>
				<div class="col-md-4">
					<div class="feature-card text-center p-4">
						<div class="feature-icon mb-3">
							<i class="fas fa-truck"></i>
						</div>
						<h4 class="fw-bold text-success">Fresh Delivery</h4>
						<p class="text-muted">Direct from our farm to your doorstep, ensuring maximum freshness and quality.</p>
					</div>
				</div>
				<div class="col-md-4">
					<div class="feature-card text-center p-4">
						<div class="feature-icon mb-3">
							<i class="fas fa-heart"></i>
						</div>
						<h4 class="fw-bold text-success">Farm Fresh</h4>
						<p class="text-muted">Handpicked products from our local farms, supporting sustainable agriculture.</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Search Section -->
	<section class="search-section py-5 bg-light">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-8">
					<div class="search-card p-4">
						<h3 class="text-center mb-4 text-success">
							<i class="fas fa-search me-2"></i>Find Your Perfect Agricultural Products
						</h3>
						<form class="search-form" action="view/product_search_result.php" method="GET">
							<div class="row g-3">
								<div class="col-md-6">
									<input type="text" class="form-control form-control-lg" name="query" placeholder="Search products..." required>
								</div>
								<div class="col-md-3">
									<select class="form-select form-select-lg" name="category">
										<option value="">All Categories</option>
										<!-- Categories will be loaded via AJAX -->
									</select>
								</div>
								<div class="col-md-3">
									<button type="submit" class="btn btn-success btn-lg w-100">
										<i class="fas fa-search me-2"></i>Search
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Categories Section -->
	<section class="categories-section py-5">
		<div class="container">
			<div class="row text-center mb-5">
				<div class="col-12">
					<h2 class="display-5 fw-bold text-success mb-3">Our Product Categories</h2>
					<p class="lead text-muted">Explore our diverse range of agricultural products</p>
				</div>
			</div>
			<div class="row g-4" id="categoriesGrid">
				<!-- Categories will be loaded via AJAX -->
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="footer bg-dark text-white py-5">
		<div class="container">
			<div class="row">
				<div class="col-lg-4 mb-4">
					<h5 class="fw-bold mb-3">
						<i class="fas fa-seedling me-2"></i>AgroCare Farm
					</h5>
					<p class="text-light">Your trusted partner in sustainable agriculture. We bring you the freshest products from farm to table.</p>
				</div>
				<div class="col-lg-4 mb-4">
					<h6 class="fw-bold mb-3">Quick Links</h6>
					<ul class="list-unstyled">
						<li><a href="view/all_product.php" class="text-light text-decoration-none"><i class="fas fa-apple-alt me-2"></i>All Products</a></li>
						<li><a href="view/product_search_result.php" class="text-light text-decoration-none"><i class="fas fa-search me-2"></i>Search Products</a></li>
						<li><a href="login/register.php" class="text-light text-decoration-none"><i class="fas fa-user-plus me-2"></i>Register</a></li>
					</ul>
				</div>
				<div class="col-lg-4 mb-4">
					<h6 class="fw-bold mb-3">Contact Info</h6>
					<ul class="list-unstyled text-light">
						<li><i class="fas fa-map-marker-alt me-2"></i>123 Farm Road, Agriculture City</li>
						<li><i class="fas fa-phone me-2"></i>+1 (555) 123-4567</li>
						<li><i class="fas fa-envelope me-2"></i>info@agrocarefarm.com</li>
					</ul>
				</div>
			</div>
			<hr class="my-4">
			<div class="row">
				<div class="col-12 text-center">
					<p class="mb-0">&copy; 2024 AgroCare Farm. All rights reserved.</p>
				</div>
			</div>
		</div>
	</footer>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		window.APP_BASE_PATH = '<?php echo htmlspecialchars($appBasePath, ENT_QUOTES); ?>';
	</script>
	<script src="js/cart.js"></script>
	<script src="js/index.js?v=<?php echo time(); ?>"></script>
</body>
</html>