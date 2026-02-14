<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
function csrf_token() { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function verify_csrf($t) { return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $t ?? ''); }
function require_user() { if (!isset($_SESSION['user'])) { header('Location: ' . BASE_URL . '/public/login.php'); exit; } }
function require_admin() { if (!isset($_SESSION['admin'])) { header('Location: ' . BASE_URL . '/admin/login.php'); exit; } }
function flash($msg, $type='success'){ $_SESSION['flash']=['msg'=>$msg,'type'=>$type]; }
?>
