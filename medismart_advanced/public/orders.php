<?php
// public/orders.php
require_once __DIR__ . '/../config/auth.php'; require_user();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$uid = $_SESSION['user']['id'];
$orders = $conn->query("SELECT * FROM orders WHERE user_id=$uid ORDER BY id DESC");

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function addr($o){
  $parts = array_filter([
    $o['shipping_addr1'] ?? '',
    $o['shipping_addr2'] ?? '',
    $o['shipping_city'] ?? '',
    $o['shipping_postal'] ?? ''
  ], fn($x)=>trim((string)$x) !== '');
  return implode(', ', array_map('h', $parts));
}
?>
<h4>Your Orders</h4>
<div class="table-responsive">
<table class="table table-striped table-hover align-middle">
  <thead>
    <tr>
      <th>ID</th>
      <th>Total</th>
      <th>Status</th>
      <th>Payment</th>
      <th>Placed</th>
      <th>Shipping</th>
    </tr>
  </thead>
  <tbody>
    <?php while($o = $orders->fetch_assoc()): ?>
      <tr>
        <td><?php echo (int)$o['id']; ?></td>
        <td><?php echo number_format((float)$o['total'], 2); ?></td>
        <td><?php echo h($o['status']); ?></td>
        <td><?php echo h($o['payment_method']); ?></td>
        <td><?php echo h($o['created_at']); ?></td>
        <td>
          <div class="small">
            <div><strong><?php echo h($o['shipping_name'] ?: '—'); ?></strong></div>
            <div><?php echo h($o['shipping_phone'] ?: ''); ?></div>
            <div><?php echo addr($o) ?: '—'; ?></div>
          </div>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
