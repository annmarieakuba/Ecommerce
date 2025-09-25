<?php
//start session
if (session_status() === PHP_SESSION_NONE) {
session_start();
}

//for header redirection
ob_start();

//funtion to check for login
function is_logged_in() {
return isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id']);
}

//function to check for role (admin, customer, etc)
function is_admin() {
return isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === 1;
}


?>
