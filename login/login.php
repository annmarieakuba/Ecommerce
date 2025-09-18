<?php
session_start();

// If already logged in, redirect to home
if (isset($_SESSION['customer_id'])) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11"></link>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Georgia', serif;
            background: linear-gradient(135deg, #2c3e50, #34495e, #3498db, #2980b9);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
            z-index: 1;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }
        
        .login-container::after {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #3498db, #2980b9, #34495e, #2c3e50);
            border-radius: 30px;
            z-index: -1;
        }
        
        .form-header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 50px 40px 40px;
            text-align: center;
            position: relative;
        }
        
        .form-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
            opacity: 0.3;
        }
        
        .form-header h1 {
            font-size: 38px;
            font-weight: 300;
            color: white;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
            letter-spacing: 2px;
        }
        
        .form-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 18px;
            margin-bottom: 0;
            position: relative;
            z-index: 1;
            font-style: italic;
        }
        
        .form-body {
            padding: 0 40px 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-control::placeholder {
            color: #a0aec0;
        }
        
        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 18px;
            z-index: 2;
        }
        
        .form-control:focus + .input-icon {
            color: #667eea;
        }
        
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 20px;
            border-radius: 25px;
            font-size: 18px;
            font-weight: 400;
            cursor: pointer;
            transition: all 0.4s ease;
            margin-top: 20px;
            position: relative;
            overflow: hidden;
            text-transform: lowercase;
            letter-spacing: 1px;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transition: all 0.6s ease;
            transform: translate(-50%, -50%);
        }
        
        .btn-login:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(52, 152, 219, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .form-footer {
            text-align: center;
            padding: 20px 40px 40px;
            background: #f8f9fa;
        }
        
        .form-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .form-footer a:hover {
            color: #764ba2;
        }
        
        .error {
            color: #e53e3e;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }
        
        .loader {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
            display: none;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #a0aec0;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            z-index: 2;
        }
        
        .password-toggle:hover {
            color: #667eea;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .form-header, .form-body, .form-footer {
                padding: 30px 25px;
            }
            
            .form-header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="form-header">
            <h1>Welcome back!</h1>
            <p>Sign in to your account</p>
                    </div>
        <div class="form-body">
            <form id="login-form">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
                    <i class="fas fa-envelope input-icon"></i>
                    <span id="emailError" class="error"></span>
                            </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                    <i class="fas fa-lock input-icon"></i>
                    <button type="button" class="password-toggle" onclick="togglePassword()">Show</button>
                    <span id="passwordError" class="error"></span>
                            </div>
                
                <div class="form-group" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; font-size: 14px; color: #4a5568; cursor: pointer;">
                        <input type="checkbox" style="margin-right: 8px;" id="remember">
                        Remember me
                    </label>
                    <a href="#" style="color: #667eea; text-decoration: none; font-size: 14px;">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn-login">Sign in</button>
                <div class="loader" id="loader"></div>
            </form>
            </div>
        <div class="form-footer">
            Don't have an account? <a href="register.php">Sign up here</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/login.js"></script>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = 'Hide';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = 'Show';
            }
        }
    </script>
</body>
</html>