<?php
// Run once: http://localhost/medismart_advanced/tools/reset_admin.php
require_once __DIR__ . '/../config/db.php';

$newHash = password_hash('Admin@123', PASSWORD_DEFAULT);

// Create admin if missing, else update the hash
$sql = "INSERT INTO admins (username,password_hash,role)
        VALUES ('admin', ?, 'SUPER')
        ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $newHash);
$stmt->execute();

header('Content-Type: text/plain; charset=utf-8');
echo "Admin password set to: Admin@123\n";
