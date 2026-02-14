<?php
require_once __DIR__ . '/../config/auth.php'; require_admin();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';
?>
<div class="row g-3">
  <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6>Manage Users</h6><a class="btn btn-sm btn-primary" href="<?php echo BASE_URL; ?>/admin/users.php">Open</a></div></div></div>
  <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6>Manage Products</h6><a class="btn btn-sm btn-primary" href="<?php echo BASE_URL; ?>/admin/products.php">Open</a></div></div></div>
  <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6>Orders</h6><a class="btn btn-sm btn-primary" href="<?php echo BASE_URL; ?>/admin/orders.php">Open</a></div></div></div>
  <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6>Prescriptions</h6><a class="btn btn-sm btn-primary" href="<?php echo BASE_URL; ?>/admin/prescriptions.php">Open</a></div></div></div>
  <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6>Support</h6><a class="btn btn-sm btn-primary" href="<?php echo BASE_URL; ?>/admin/tickets.php">Open</a></div></div></div>
  <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><h6>Analytics</h6><a class="btn btn-sm btn-primary" href="<?php echo BASE_URL; ?>/admin/analytics.php">Open</a></div></div></div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
