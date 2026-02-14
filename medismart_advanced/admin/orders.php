<?php
// admin/orders.php
require_once __DIR__ . '/../config/auth.php'; require_admin();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/Fraud.php';

if (isset($_GET['id'], $_GET['status'])){
  $id=(int)$_GET['id']; $st=$_GET['status'];
  $allowed=['APPROVED','REJECTED','SHIPPED','DELIVERED'];
  if (in_array($st, $allowed, true)) $conn->query("UPDATE orders SET status='$st' WHERE id=$id");
  header('Location: orders.php'); exit;
}

$orders=$conn->query("SELECT o.*, u.full_name FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC");

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function shipping_block($o){
  $parts = array_filter([
    $o['shipping_addr1'] ?? '',
    $o['shipping_addr2'] ?? '',
    $o['shipping_city'] ?? '',
    $o['shipping_postal'] ?? '',
  ], fn($x)=>trim((string)$x)!=='');
  $addr = $parts ? implode(', ', array_map('h', $parts)) : '—';
  $name = $o['shipping_name'] ? h($o['shipping_name']) : '—';
  $phone= $o['shipping_phone'] ? h($o['shipping_phone']) : '';
  return "<div class=\"small\"><div><strong>{$name}</strong></div><div>{$phone}</div><div>{$addr}</div></div>";
}
?>
<h4>Orders</h4>
<div class="table-responsive">
<table class="table table-striped table-hover align-middle">
  <thead>
    <tr>
      <th>ID</th>
      <th>User</th>
      <th>Total</th>
      <th>Status</th>
      <th>Payment</th>
      <th>Shipping</th>
      <th>Risk</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php while($o=$orders->fetch_assoc()): $risk = Fraud::score($conn, (int)$o['id']); ?>
      <tr>
        <td><?php echo (int)$o['id']; ?></td>
        <td><?php echo h($o['full_name']); ?></td>
        <td><?php echo number_format((float)$o['total'],2); ?></td>
        <td><?php echo h($o['status']); ?></td>
        <td><?php echo h($o['payment_method']); ?></td>
        <td><?php echo shipping_block($o); ?></td>
        <td>
          <span class="badge <?php echo $risk['risk']==='HIGH'?'bg-danger':($risk['risk']==='MEDIUM'?'bg-warning text-dark':'bg-success'); ?>">
            <?php echo $risk['risk']; ?> (<?php echo (int)$risk['score']; ?>)
          </span>
        </td>
        <td>
          <div class="btn-group btn-group-sm">
            <a class="btn btn-outline-success" href="?id=<?php echo (int)$o['id']; ?>&status=APPROVED">Approve</a>
            <a class="btn btn-outline-danger" href="?id=<?php echo (int)$o['id']; ?>&status=REJECTED">Reject</a>
            <a class="btn btn-outline-secondary" href="?id=<?php echo (int)$o['id']; ?>&status=SHIPPED">Ship</a>
            <a class="btn btn-outline-secondary" href="?id=<?php echo (int)$o['id']; ?>&status=DELIVERED">Deliver</a>
          </div>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
