<?php
/**
 * Update Admin Email Script
 * This script updates the existing admin's email from admin@example.com to admin@gmail.com
 */

require_once __DIR__ . '/settings/db_class.php';

echo "<h2>Update Admin Email</h2>";

try {
    $db = new db_connection();
    
    if (!$db->db_connect()) {
        die(" Database connection failed");
    }
    
    echo "Database connected successfully<br><br>";
    
    // First, let's see what admin users exist
    echo "<h3>Current Admin Users:</h3>";
    $result = $db->db_query("SELECT customer_id, customer_name, customer_email, user_role FROM customer WHERE user_role = 1");
    if ($result) {
        $admins = $db->db_fetch_all("SELECT customer_id, customer_name, customer_email, user_role FROM customer WHERE user_role = 1");
        if ($admins) {
            echo "<div class='table-responsive'><table class='table table-striped' border='1' style='border-collapse: collapse;'>";
            echo "<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr></thead><tbody>";
            foreach ($admins as $admin) {
                echo "<tr><td>{$admin['customer_id']}</td><td>{$admin['customer_name']}</td><td>{$admin['customer_email']}</td><td>Admin</td></tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "<p>No admin users found.</p>";
        }
    }
    
    echo "<br><hr><br>";
    
    // Update admin email from admin@example.com to admin@gmail.com
    $old_email = 'admin@example.com';
    $new_email = 'admin@gmail.com';
    
    // Check if admin with old email exists
    $stmt = $db->db->prepare("SELECT customer_id, customer_name FROM customer WHERE customer_email = ? AND user_role = 1");
    $stmt->bind_param("s", $old_email);
    $stmt->execute();
    $existing_admin = $stmt->get_result()->fetch_assoc();
    
    if ($existing_admin) {
        echo "<h3>Found admin with old email: {$old_email}</h3>";
        echo "Admin ID: {$existing_admin['customer_id']}<br>";
        echo "Admin Name: {$existing_admin['customer_name']}<br><br>";
        
        // Update the email
        $stmt = $db->db->prepare("UPDATE customer SET customer_email = ? WHERE customer_email = ? AND user_role = 1");
        $stmt->bind_param("ss", $new_email, $old_email);
        
        if ($stmt->execute()) {
            echo " <strong>Email updated successfully!</strong><br>";
            echo "Old email: $old_email<br>";
            echo "New email: $new_email<br><br>";
            
            // Also reset password to admin123
            $new_password = 'admin123';
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->db->prepare("UPDATE customer SET customer_pass = ? WHERE customer_email = ? AND user_role = 1");
            $stmt->bind_param("ss", $hashed_password, $new_email);
            
            if ($stmt->execute()) {
                echo " <strong>Password also reset to: $new_password</strong><br><br>";
            }
            
            echo "<h3>🎉 You can now login with:</h3>";
            echo "<strong>Email:</strong> $new_email<br>";
            echo "<strong>Password:</strong> $new_password<br><br>";
            
        } else {
            echo " Failed to update email: " . $db->db->error;
        }
    } else {
        echo "<h3>No admin found with email: $old_email</h3>";
        echo "Let's check if there's any admin with the new email already...<br>";
        
        $stmt = $db->db->prepare("SELECT customer_id, customer_name FROM customer WHERE customer_email = ? AND user_role = 1");
        $stmt->bind_param("s", $new_email);
        $stmt->execute();
        $admin_with_new_email = $stmt->get_result()->fetch_assoc();
        
        if ($admin_with_new_email) {
            echo " Found admin with new email: $new_email<br>";
            echo "Admin ID: {$admin_with_new_email['customer_id']}<br>";
            echo "Admin Name: {$admin_with_new_email['customer_name']}<br><br>";
            
            // Reset password for this admin
            $new_password = 'admin123';
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->db->prepare("UPDATE customer SET customer_pass = ? WHERE customer_email = ? AND user_role = 1");
            $stmt->bind_param("ss", $hashed_password, $new_email);
            
            if ($stmt->execute()) {
                echo " <strong>Password reset successfully!</strong><br><br>";
                echo "<h3>🎉 You can login with:</h3>";
                echo "<strong>Email:</strong> $new_email<br>";
                echo "<strong>Password:</strong> $new_password<br><br>";
            }
        } else {
            echo "No admin found with either email. You may need to create a new admin.<br>";
            echo "Use the <a href='quick_admin_setup.php'>Quick Admin Setup</a> script to create one.";
        }
    }
    
    echo "<br><hr>";
    echo "<h3>Next Steps:</h3>";
    echo "1. Go to <a href='login/login.php'>Login Page</a><br>";
    echo "2. Use the credentials shown above<br>";
    echo "3. After login, you'll see the 'Category' link in the menu<br>";
    echo "4. Click 'Category' to access the admin panel<br>";
    
} catch (Exception $e) {
    echo " Error: " . $e->getMessage();
}
?>

