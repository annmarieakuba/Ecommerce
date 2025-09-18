<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="css/index.css" rel="stylesheet">
</head>
<body>

	<div class="menu-tray">
		<span class="me-2">Menu:</span>
		<?php if (isset($_SESSION['customer_id'])): ?>
			<span class="me-2">Welcome, <?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'User'); ?>!</span>
			<a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
		<?php else: ?>
			<a href="login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
			<a href="login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
		<?php endif; ?>
	</div>

	<div class="container" style="padding-top:120px;">
		<div class="text-center">
			<h1>Welcome</h1>
			<?php if (isset($_SESSION['customer_id'])): ?>
				<p class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'User'); ?>!</p>
				<p class="text-muted">You are logged in as a <?php echo ($_SESSION['user_role'] == 1) ? 'Administrator' : 'Customer'; ?>.</p>
			<?php else: ?>
				<p class="text-muted">Use the menu in the top-right to Register or Login.</p>
			<?php endif; ?>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>