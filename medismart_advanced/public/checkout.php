<?php
require_once __DIR__ . '/../config/auth.php'; require_user();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/FakePay.php';

$cart = $_SESSION['cart'] ?? [];
if (!$cart) { flash('Cart is empty','warning'); header('Location: cart.php'); exit; }

// Rx check
$ids = implode(',', array_map('intval', array_keys($cart)));
$rxRes = $conn->query("SELECT COUNT(*) c FROM products WHERE id IN ($ids) AND requires_prescription=1")->fetch_assoc();
$needsRx = ($rxRes['c'] ?? 0) > 0;
$hasRx = $conn->query("SELECT COUNT(*) c FROM prescriptions WHERE user_id={$_SESSION['user']['id']} AND status='APPROVED'")->fetch_assoc()['c'] ?? 0;

// Build lines
$total=0; $lines=[];
$res = $conn->query("SELECT id,name,price FROM products WHERE id IN ($ids)");
while($p=$res->fetch_assoc()){
  $qty=$cart[$p['id']];
  $lines[]=['id'=>$p['id'],'name'=>$p['name'],'qty'=>$qty,'price'=>$p['price']];
  $total += $qty*$p['price'];
}

// preserve form values on error
$F = [
  'ship_name'  => trim($_POST['ship_name']  ?? ''),
  'ship_phone' => trim($_POST['ship_phone'] ?? ''),
  'ship_addr1' => trim($_POST['ship_addr1'] ?? ''),
  'ship_addr2' => trim($_POST['ship_addr2'] ?? ''),
  'ship_city'  => trim($_POST['ship_city']  ?? ''),
  'ship_postal'=> trim($_POST['ship_postal']?? ''),
  'method'     => $_POST['method'] ?? 'COD',
  'card'       => $_POST['card'] ?? '',
  'exp'        => $_POST['exp'] ?? '',
  'cvc'        => $_POST['cvc'] ?? '',
];

$err=null; $done=false; $orderId=null;

if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['csrf']??'')) {
    $err='Invalid CSRF.';
  } else {
    // minimal validation (why: avoid empty address on delivery)
    foreach (['ship_name','ship_phone','ship_addr1','ship_city','ship_postal'] as $k) {
      if ($F[$k]==='') { $err='Please fill all required shipping fields.'; break; }
    }
    if (!$err) {
      $method = $F['method'] ?: 'COD';
      $status = ($needsRx && !$hasRx) ? 'ON_HOLD' : 'PLACED';

      $stmt=$conn->prepare('INSERT INTO orders
        (user_id,total,status,payment_method,shipping_name,shipping_phone,shipping_addr1,shipping_addr2,shipping_city,shipping_postal)
        VALUES (?,?,?,?,?,?,?,?,?,?)');
      $stmt->bind_param(
        'idssssssss',
        $_SESSION['user']['id'], $total, $status, $method,
        $F['ship_name'], $F['ship_phone'], $F['ship_addr1'], $F['ship_addr2'], $F['ship_city'], $F['ship_postal']
      );
      $stmt->execute();
      $orderId = $stmt->insert_id;

      $stmt2=$conn->prepare('INSERT INTO order_items(order_id,product_id,quantity,unit_price) VALUES (?,?,?,?)');
      foreach($lines as $ln){
        $stmt2->bind_param('iiid',$orderId,$ln['id'],$ln['qty'],$ln['price']);
        $stmt2->execute();
      }

      if ($method==='CARD') {
        $r = FakePay::charge($conn, $orderId, $F['card'], $F['exp'], $F['cvc'], $total);
        if ($r['success']) { $conn->query("UPDATE orders SET status='PAID' WHERE id=$orderId"); }
        else { $err=$r['error']; }
      }

      if (!$err) { $done=true; $_SESSION['cart']=[]; }
    }
  }
}
?>
<div class="row">
  <div class="col-lg-7">
    <div class="card shadow-sm mb-3">
      <div class="card-header">Order Summary</div>
      <div class="card-body">
        <ul class="list-group">
          <?php foreach($lines as $ln): ?>
            <li class="list-group-item d-flex justify-content-between">
              <span><?php echo htmlspecialchars($ln['name']); ?> × <?php echo $ln['qty']; ?></span>
              <span>PKR <?php echo number_format($ln['qty']*$ln['price'],2); ?></span>
            </li>
          <?php endforeach; ?>
          <li class="list-group-item d-flex justify-content-between fw-bold">
            <span>Total</span><span>PKR <?php echo number_format($total,2); ?></span>
          </li>
        </ul>
        <?php if($needsRx && !$hasRx): ?>
          <div class="alert alert-warning mt-3">Prescription pending approval. Order will be placed On Hold.</div>
          <a class="btn btn-outline-secondary btn-sm" href="<?php echo BASE_URL; ?>/public/upload_prescription.php">Upload Prescription</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card shadow-sm mb-3">
      <div class="card-header">Shipping Address</div>
      <div class="card-body">
        <?php if($err): ?><div class="alert alert-danger mb-3"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
        <?php if(!$done): ?>
        <form method="post" id="checkoutForm">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">

          <div class="row g-2">
            <div class="col-md-6">
              <input class="form-control" name="ship_name" placeholder="Full name" value="<?php echo htmlspecialchars($F['ship_name']); ?>" required>
            </div>
            <div class="col-md-6">
              <input class="form-control" name="ship_phone" placeholder="Phone" value="<?php echo htmlspecialchars($F['ship_phone']); ?>" required>
            </div>
            <div class="col-12">
              <input class="form-control" name="ship_addr1" placeholder="Address line 1" value="<?php echo htmlspecialchars($F['ship_addr1']); ?>" required>
            </div>
            <div class="col-12">
              <input class="form-control" name="ship_addr2" placeholder="Address line 2 (optional)" value="<?php echo htmlspecialchars($F['ship_addr2']); ?>">
            </div>
            <div class="col-md-6">
              <input class="form-control" name="ship_city" placeholder="City" value="<?php echo htmlspecialchars($F['ship_city']); ?>" required>
            </div>
            <div class="col-md-6">
              <input class="form-control" name="ship_postal" placeholder="Postal code" value="<?php echo htmlspecialchars($F['ship_postal']); ?>" required>
            </div>
          </div>

          <hr class="my-3">

          <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select name="method" class="form-select" id="pmethod" onchange="document.querySelector('#cardBox').style.display=this.value==='CARD'?'block':'none'">
              <option value="COD"  <?php echo $F['method']==='COD'?'selected':''; ?>>Cash on Delivery</option>
              <option value="CARD" <?php echo $F['method']==='CARD'?'selected':''; ?>>Card (Test)</option>
            </select>
          </div>

          <div id="cardBox" style="display: <?php echo $F['method']==='CARD'?'block':'none'; ?>">
            <div class="mb-2"><input class="form-control" name="card" placeholder="Card Number (use 4242...4242)" value="<?php echo htmlspecialchars($F['card']); ?>"></div>
            <div class="mb-2"><input class="form-control" name="exp"  placeholder="MM/YY" value="<?php echo htmlspecialchars($F['exp']); ?>"></div>
            <div class="mb-2"><input class="form-control" name="cvc"  placeholder="CVC" value="<?php echo htmlspecialchars($F['cvc']); ?>"></div>
            <p class="small text-muted m-0">Demo only. Do not enter real card data.</p>
          </div>

          <button class="btn btn-primary w-100 mt-3">Pay &amp; Place Order</button>
        </form>
        <?php else: ?>
          <div class="alert alert-success">Order placed. ID #<?php echo $orderId; ?>.</div>
          <a class="btn btn-primary w-100" href="<?php echo BASE_URL; ?>/public/orders.php">View Orders</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
