<?php
require_once __DIR__ . '/config/db.php';

$username = 'admin';
$password = 'admin@123';
$role = 'SUPER';

// Hash the password
$password_hash = password_hash($password, PASSWORD_BCRYPT);

echo "Seeding admin user...\n";
echo "Username: $username\n";
echo "Password: $password\n";

// Prepare statement to check if admin exists
$stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Admin user already exists. Updating password...\n";
    $update_stmt = $conn->prepare("UPDATE admins SET password_hash = ? WHERE username = ?");
    $update_stmt->bind_param("ss", $password_hash, $username);
    if ($update_stmt->execute()) {
        echo "Admin password updated successfully.\n";
    } else {
        echo "Error updating admin password: " . $conn->error . "\n";
    }
    $update_stmt->close();
} else {
    echo "Creating new admin user...\n";
    $insert_stmt = $conn->prepare("INSERT INTO admins (username, password_hash, role) VALUES (?, ?, ?)");
    $insert_stmt->bind_param("sss", $username, $password_hash, $role);
    if ($insert_stmt->execute()) {
        echo "Admin user created successfully.\n";
    } else {
        echo "Error creating admin user: " . $conn->error . "\n";
    }
    $insert_stmt->close();
}

$stmt->close();
$conn->close();
?>
