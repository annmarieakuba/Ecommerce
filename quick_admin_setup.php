<?php
/**
 * Quick Admin Setup - Direct Database Access
 * Run this script to quickly create or reset admin password
 */

require_once __DIR__ . '/settings/db_class.php';

// Configuration
$admin_email = 'admin@gmail.com';
$admin_password = 'admin123';
$admin_name = 'Admin User';

echo "<h2>Quick Admin Setup</h2>";

try {
    $db = new db_connection();
    
    if (!$db->db_connect()) {
        die(" Database connection failed");
    }
    
    echo " Database connected successfully<br><br>";
    
    // Check if admin exists
    $stmt = $db->db->prepare("SELECT customer_id, customer_name, customer_email FROM customer WHERE user_role = 1 LIMIT 1");
    $stmt->execute();
    $existing_admin = $stmt->get_result()->fetch_assoc();
    
    if ($existing_admin) {
        echo "<h3>Existing Admin Found:</h3>";
        echo "ID: {$existing_admin['customer_id']}<br>";
        echo "Name: {$existing_admin['customer_name']}<br>";
        echo "Email: {$existing_admin['customer_email']}<br><br>";
        
        // Reset password for existing admin
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        $stmt = $db->db->prepare("UPDATE customer SET customer_pass = ? WHERE customer_id = ?");
        $stmt->bind_param("si", $hashed_password, $existing_admin['customer_id']);
        
        if ($stmt->execute()) {
            echo "<strong>Password reset successfully!</strong><br>";
            echo "You can now login with:<br>";
            echo "Email: {$existing_admin['customer_email']}<br>";
            echo "Password: $admin_password<br>";
        } else {
            echo " Failed to reset password: " . $db->db->error;
        }
    } else {
        echo "<h3>No Admin Found - Creating New Admin</h3>";
        
        // Create new admin
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        $stmt = $db->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) VALUES (?, ?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("ssssss", $admin_name, $admin_email, $hashed_password, 'Unknown', 'Unknown', '000-000-0000');
        
        if ($stmt->execute()) {
            $new_admin_id = $db->db->insert_id;
            echo " <strong>New admin created successfully!</strong><br>";
            echo "Admin ID: $new_admin_id<br>";
            echo "You can now login with:<br>";
            echo "Email: $admin_email<br>";
            echo "Password: $admin_password<br>";
        } else {
            echo " Failed to create admin: " . $db->db->error;
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
