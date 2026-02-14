<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/auth.php';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['cart'])) $_SESSION['cart']=[];

if (isset($_GET['add'])) {
  $id=(int)$_GET['add']; $_SESSION['cart'][$id] = ($_SESSION['cart'][$id]??0) + 1;
  flash('Added to cart'); header('Location: ' . BASE_URL . '/public/cart.php'); exit;
}
if (isset($_GET['remove'])) { $id=(int)$_GET['remove']; unset($_SESSION['cart'][$id]); header('Location: cart.php'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST') {
  foreach($_POST['qty'] ?? [] as $pid=>$qty) { $_SESSION['cart'][(int)$pid] = max(0, min(99, (int)$qty)); if($_SESSION['cart'][(int)$pid]==0) unset($_SESSION['cart'][(int)$pid]); }
}

$items=[]; $total=0;
if ($_SESSION['cart']) {
  $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
  $res = $conn->query("SELECT id,name,price,requires_prescription FROM products WHERE id IN ($ids)");
  while($p=$res->fetch_assoc()) {
    $q = $_SESSION['cart'][$p['id']];
    $p['qty']=$q; $p['line']=$q*$p['price']; $items[]=$p; $total+=$p['line'];
  }
}
?>
<div class="card shadow-sm">
  <div class="card-header">Your Cart</div>
  <div class="card-body">
    <?php if(!$items): ?>
      <p>No items. <a href="<?php echo BASE_URL; ?>/public/browse.php">Shop now</a>.</p>
    <?php else: ?>
      <form method="post">
      <div class="table-responsive"><table class="table">
        <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Line</th><th></th></tr></thead>
        <tbody>
          <?php foreach($items as $it): ?>
            <tr>
              <td><?php echo htmlspecialchars($it['name']); ?> <?php if($it['requires_prescription']): ?><span class="badge bg-warning text-dark">Rx</span><?php endif; ?></td>
              <td style="max-width:100px"><input class="form-control" type="number" min="0" max="99" name="qty[<?php echo $it['id']; ?>]" value="<?php echo $it['qty']; ?>"></td>
              <td><?php echo number_format($it['price'],2); ?></td>
              <td><?php echo number_format($it['line'],2); ?></td>
              <td><a class="btn btn-sm btn-danger" href="?remove=<?php echo $it['id']; ?>">Remove</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table></div>
      <div class="d-flex justify-content-between align-items-center">
        <button class="btn btn-secondary">Update</button>
        <div class="fs-5">Total: PKR <strong><?php echo number_format($total,2); ?></strong></div>
      </div>
      </form>
      <hr>
      <a class="btn btn-primary" href="<?php echo BASE_URL; ?>/public/checkout.php">Proceed to Checkout</a>
    <?php endif; ?>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
