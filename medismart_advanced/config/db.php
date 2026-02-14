<?php
require_once __DIR__ . '/config.php';

// Resolve port/socket with safe defaults
$port = defined('DB_PORT') ? DB_PORT : (int)(ini_get('mysqli.default_port') ?: 3306);
$socket = defined('DB_SOCKET') ? DB_SOCKET : ini_get('mysqli.default_socket');

// First attempt: configured host
$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, null, $port, $socket);
if ($conn->connect_errno) {
  // Fallback: try 127.0.0.1 (sometimes differs from localhost resolution)
  $conn = @new mysqli('127.0.0.1', DB_USER, DB_PASS, null, $port, $socket);
}

if ($conn->connect_errno) {
  $hint = "Please start MySQL/MariaDB in XAMPP, confirm port $port, and retry.";
  die('DB connection failed: ' . $conn->connect_error . "\n" . $hint);
}

// Ensure database exists, then select and set charset
$conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db(DB_NAME);
$conn->set_charset('utf8mb4');

// Auto-initialize schema on first run if core tables are missing
$needInit = false;
$res = $conn->query("SHOW TABLES LIKE 'users'");
if (!$res || $res->num_rows === 0) { $needInit = true; }

if ($needInit) {
  $schemaPath = realpath(__DIR__ . '/../sql/schema.sql');
  if ($schemaPath && is_readable($schemaPath)) {
    $sql = file_get_contents($schemaPath);
    if ($sql) {
      // Execute all statements; ignore errors from already-existing objects
      $conn->multi_query($sql);
      // flush remaining results
      while ($conn->more_results() && $conn->next_result()) { /* drain */ }
    }
  }
}

// Lightweight migrations for existing installs
// Ensure users.status column exists (added for account activation control)
try {
  $col = $conn->query("SHOW COLUMNS FROM users LIKE 'status'");
  if ($col && $col->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN status ENUM('Active','Inactive') DEFAULT 'Active' AFTER email_verified");
  }
} catch (Throwable $e) { /* ignore if table not yet present */ }

// Ensure products.image_path exists for product images
try {
  $col = $conn->query("SHOW COLUMNS FROM products LIKE 'image_path'");
  if ($col && $col->num_rows === 0) {
    $conn->query("ALTER TABLE products ADD COLUMN image_path VARCHAR(255) NULL AFTER brand");
    // Set demo images based on category
    $base = $conn->real_escape_string((defined('BASE_URL')?BASE_URL:'/medismart_advanced'));
    $conn->query("UPDATE products SET image_path=CONCAT('$base','/public/assets/med/pills_pain.svg') WHERE category IN ('Pain Reliever','Fever Relief','Headache')");
    $conn->query("UPDATE products SET image_path=CONCAT('$base','/public/assets/med/antibiotic_capsules.svg') WHERE category='Antibiotic'");
    $conn->query("UPDATE products SET image_path=CONCAT('$base','/public/assets/med/allergy_bottle.svg') WHERE category='Allergy Relief'");
    $conn->query("UPDATE products SET image_path=CONCAT('$base','/public/assets/med/ors_pack.svg') WHERE category='Electrolyte'");
  }
} catch (Throwable $e) { /* ignore */ }
?>
