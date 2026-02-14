<?php
// Base URL for links (adjust if not using /medismart_advanced)
define('BASE_URL', '/medismart_advanced');

// DB config
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'medismart_advanced');
// Optional: customize when XAMPP uses a non-default port (e.g., 3307)
define('DB_PORT', 3306);
// Optional socket (leave null on Windows/XAMPP)
define('DB_SOCKET', null);

// Crypto key for token/enc (demo only)
define('APP_KEY', 'demo_secret_key_change_me');

if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
