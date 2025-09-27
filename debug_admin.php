<?php

require_once __DIR__ . '/settings/db_class.php';

echo "<h2>Debug Admin Account</h2>";

try {
    $db = new db_connection();
    
    if (!$db->db_connect()) {
        die(" Database connection failed");
    }
    
    echo " Database connected successfully<br><br>";
    
    // Check all users in the database
    echo "<h3>All Users in Database:</h3>";
    $result = $db->db_query("SELECT customer_id, customer_name, customer_email, user_role FROM customer ORDER BY customer_id");
    if ($result) {
        $users = $db->db_fetch_all("SELECT customer_id, customer_name, customer_email, user_role FROM customer ORDER BY customer_id");
        if ($users) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<thead><tr style='background-color: #f0f0f0;'><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr></thead><tbody>";
            foreach ($users as $user) {
                $role_text = ($user['user_role'] == 1) ? 'Admin' : 'Customer';
                $row_color = ($user['user_role'] == 1) ? 'background-color: #e8f5e8;' : '';
                echo "<tr style='$row_color'>";
                echo "<td>{$user['customer_id']}</td>";
                echo "<td>{$user['customer_name']}</td>";
                echo "<td><strong>{$user['customer_email']}</strong></td>";
                echo "<td><strong>{$role_text}</strong></td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p> No users found in database!</p>";
        }
    }
    
    echo "<br><hr><br>";
    
    // Check specifically for admin users
    echo "<h3> Admin Users Only:</h3>";
    $result = $db->db_query("SELECT customer_id, customer_name, customer_email, user_role FROM customer WHERE user_role = 1");
    if ($result) {
        $admins = $db->db_fetch_all("SELECT customer_id, customer_name, customer_email, user_role FROM customer WHERE user_role = 1");
        if ($admins) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<thead><tr style='background-color: #e8f5e8;'><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr></thead><tbody>";
            foreach ($admins as $admin) {
                echo "<tr>";
                echo "<td>{$admin['customer_id']}</td>";
                echo "<td>{$admin['customer_name']}</td>";
                echo "<td><strong>{$admin['customer_email']}</strong></td>";
                echo "<td><strong>Admin</strong></td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p> No admin users found!</p>";
        }
    }
    
    echo "<br><hr><br>";
    
    // Let's create a guaranteed admin account
    echo "<h3>Creating Guaranteed Admin Account</h3>";
    
    $admin_email = 'admin@gmail.com';
    $admin_password = 'SecureAdmin2024!';
    $admin_name = 'Admin User';
    
    // First, delete any existing admin with this email
    $stmt = $db->db->prepare("DELETE FROM customer WHERE customer_email = ?");
    $stmt->bind_param("s", $admin_email);
    $stmt->execute();
    echo "Cleaned up any existing admin with email: $admin_email<br>";
    
    // Create new admin
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    $stmt = $db->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) VALUES (?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("ssssss", $admin_name, $admin_email, $hashed_password, 'Unknown', 'Unknown', '000-000-0000');
    
    if ($stmt->execute()) {
        $new_admin_id = $db->db->insert_id;
        echo " <strong>New admin created successfully!</strong><br>";
        echo "Admin ID: $new_admin_id<br>";
        echo "Name: $admin_name<br>";
        echo "Email: <strong>$admin_email</strong><br>";
        echo "Password: <strong>$admin_password</strong><br><br>";
        
        echo "<h3> Login Credentials:</h3>";
        echo "<div style='background-color: #e8f5e8; padding: 15px; border-radius: 5px; border: 2px solid #4CAF50;'>";
        echo "<strong>Email:</strong> $admin_email<br>";
        echo "<strong>Password:</strong> $admin_password<br>";
        echo "</div>";
        
    } else {
        echo " Failed to create admin: " . $db->db->error;
    }
    
    echo "<br><hr>";
    echo "<h3> Next Steps:</h3>";
    echo "1. Copy the credentials above<br>";
    echo "2. Go to <a href='login/login.php' target='_blank'>Login Page</a><br>";
    echo "3. Use the exact email and password shown<br>";
    echo "4. After successful login, you'll see the 'Category' link<br>";
    
} catch (Exception $e) {
    echo " Error: " . $e->getMessage();
}
?>
