<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Logout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logout-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="logout-card">
        <h2 class="mb-4">Quick Logout</h2>
        <p class="mb-4">Click the button below to logout and clear your session.</p>
        <a href="logout.php" class="btn btn-danger btn-lg">
            <i class="fa fa-sign-out-alt"></i> Logout Now
        </a>
        <hr class="my-4">
        <p class="text-muted">
            <small>After logging out, you can register new users without the "already logged in" error.</small>
        </p>
        <a href="index.php" class="btn btn-outline-secondary">Back to Home</a>
    </div>
</body>
</html>
