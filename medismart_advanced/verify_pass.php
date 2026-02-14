<?php
$hash = '$2y$10$W3zVOzamiLX2xB.Yj.c6beZd9WtQLhIWGO/2PhBjn7aWalarKGKdq';
$password = 'admin@123';
if (password_verify($password, $hash)) {
    echo "Password matches!";
} else {
    echo "Password does not match.";
}
?>
