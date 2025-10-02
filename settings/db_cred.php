<?php
//Database credentials

// Use server-provided environment variables when deployed,
// and fall back to local XAMPP defaults on localhost.

$envServer = getenv('DB_HOST') ?: ($_SERVER['DB_HOST'] ?? '');
$envUser   = getenv('DB_USER') ?: ($_SERVER['DB_USER'] ?? '');
$envPass   = getenv('DB_PASS') ?: ($_SERVER['DB_PASS'] ?? '');
$envName   = getenv('DB_NAME') ?: ($_SERVER['DB_NAME'] ?? '');

if ($envServer && $envUser && $envName) {
    define('SERVER', $envServer);
    define('USERNAME', $envUser);
    define('PASSWD', $envPass);
    define('DATABASE', $envName);
} else {
    // Local development defaults (XAMPP)
    define('SERVER', 'localhost');
    define('USERNAME', 'root');
    define('PASSWD', '');
    define('DATABASE', 'dbforlab');
}

?>