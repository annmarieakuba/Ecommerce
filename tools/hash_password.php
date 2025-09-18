<?php
// Simple CLI helper to print a password hash for use in SQL inserts
// Usage: php tools/hash_password.php "YourPassword123"

if (PHP_SAPI !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

$password = isset($argv[1]) ? $argv[1] : null;
if (!$password) {
    fwrite(STDERR, "Usage: php tools/hash_password.php \"YourPassword123\"\n");
    exit(1);
}

echo password_hash($password, PASSWORD_DEFAULT) . PHP_EOL;
