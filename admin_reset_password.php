<?php
/**
 * Admin Password Reset Utility
 * This script helps you reset admin passwords or create new admin users
 */

require_once __DIR__ . '/settings/core.php';
require_once __DIR__ . '/settings/db_class.php';

// Simple HTML interface
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Password Reset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 600px; margin-top: 50px; }
        .alert { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-key"></i> Admin Password Reset Utility</h4>
            </div>
            <div class="card-body">
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $action = $_POST['action'] ?? '';
                    $db = new db_connection();
                    
                    if ($action === 'list_admins') {
                        // List existing admin users
                        echo "<h5>Existing Admin Users:</h5>";
                        $result = $db->db_query("SELECT customer_id, customer_name, customer_email, user_role FROM customer WHERE user_role = 1");
                        if ($result) {
                            $admins = $db->db_fetch_all("SELECT customer_id, customer_name, customer_email, user_role FROM customer WHERE user_role = 1");
                            if ($admins) {
                                echo "<div class='table-responsive'><table class='table table-striped'>";
                                echo "<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr></thead><tbody>";
                                foreach ($admins as $admin) {
                                    echo "<tr><td>{$admin['customer_id']}</td><td>{$admin['customer_name']}</td><td>{$admin['customer_email']}</td><td>Admin</td></tr>";
                                }
                                echo "</tbody></table></div>";
                            } else {
                                echo "<p class='text-warning'>No admin users found.</p>";
                            }
                        }
                    } elseif ($action === 'reset_password') {
                        // Reset password for existing admin
                        $admin_id = (int)$_POST['admin_id'];
                        $new_password = $_POST['new_password'];
                        
                        if ($admin_id && $new_password) {
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            $stmt = $db->db->prepare("UPDATE customer SET customer_pass = ? WHERE customer_id = ? AND user_role = 1");
                            $stmt->bind_param("si", $hashed_password, $admin_id);
                            
                            if ($stmt->execute()) {
                                echo "<div class='alert alert-success'> Password reset successfully for admin ID: $admin_id</div>";
                                echo "<p><strong>New Password:</strong> $new_password</p>";
                            } else {
                                echo "<div class='alert alert-danger'> Failed to reset password</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'> Please provide admin ID and new password</div>";
                        }
                    } elseif ($action === 'create_admin') {
                        // Create new admin user
                        $name = $_POST['name'];
                        $email = $_POST['email'];
                        $password = $_POST['password'];
                        $country = $_POST['country'] ?? 'Unknown';
                        $city = $_POST['city'] ?? 'Unknown';
                        $contact = $_POST['contact'] ?? '000-000-0000';
                        
                        if ($name && $email && $password) {
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = $db->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) VALUES (?, ?, ?, ?, ?, ?, 1)");
                            $stmt->bind_param("ssssss", $name, $email, $hashed_password, $country, $city, $contact);
                            
                            if ($stmt->execute()) {
                                $new_admin_id = $db->db->insert_id;
                                echo "<div class='alert alert-success'> New admin created successfully!</div>";
                                echo "<p><strong>Admin ID:</strong> $new_admin_id</p>";
                                echo "<p><strong>Email:</strong> $email</p>";
                                echo "<p><strong>Password:</strong> $password</p>";
                            } else {
                                echo "<div class='alert alert-danger'> Failed to create admin: " . $db->db->error . "</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'> Please fill in all required fields</div>";
                        }
                    }
                }
                ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>1. List Existing Admins</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="list_admins">
                                    <button type="submit" class="btn btn-info">Show Admin Users</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>2. Reset Admin Password</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="reset_password">
                                    <div class="mb-3">
                                        <label class="form-label">Admin ID:</label>
                                        <input type="number" name="admin_id" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">New Password:</label>
                                        <input type="text" name="new_password" class="form-control" value="admin123" required>
                                    </div>
                                    <button type="submit" class="btn btn-warning">Reset Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>3. Create New Admin</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="create_admin">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Full Name *</label>
                                                <input type="text" name="name" class="form-control" value="Admin User" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email *</label>
                                                <input type="email" name="email" class="form-control" value="admin@gmail.com" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Password *</label>
                                                <input type="text" name="password" class="form-control" value="admin123" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Contact</label>
                                                <input type="text" name="contact" class="form-control" value="000-000-0000">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Country</label>
                                                <input type="text" name="country" class="form-control" value="Unknown">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">City</label>
                                                <input type="text" name="city" class="form-control" value="Unknown">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success">Create New Admin</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mt-4">
                    <h6><i class="fas fa-info-circle"></i> Instructions:</h6>
                    <ol>
                        <li>First, click "Show Admin Users" to see existing admins</li>
                        <li>If you have an existing admin, use "Reset Admin Password" with their ID</li>
                        <li>If no admin exists, use "Create New Admin" to create one</li>
                        <li>After creating/resetting, you can login with the provided credentials</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
