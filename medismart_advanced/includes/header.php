<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/config.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MediSmart Online Pharmacy</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<link href="<?php echo BASE_URL; ?>/includes/style.css?v=1.0" rel="stylesheet">
</head>


<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="<?php echo BASE_URL; ?>/public/index.php">MediSmart</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/public/browse.php">Shop</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/public/cart.php">Cart</a></li>
        <?php if(isset($_SESSION['user'])): ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/public/orders.php">Orders</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/public/support.php">Support</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/public/profile.php">Profile</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/public/logout.php">Logout</a></li>
        <?php elseif(isset($_SESSION['admin'])): ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/admin/dashboard.php">Admin</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/admin/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/public/register.php">Register</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/public/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/admin/login.php">Admin</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container py-4">
<?php if(isset($_SESSION['flash'])): ?>
  <div class="alert alert-<?php echo $_SESSION['flash']['type']; ?>"><?php echo htmlspecialchars($_SESSION['flash']['msg']); unset($_SESSION['flash']); ?></div>
<?php endif; ?>
