<?php
require_once __DIR__ . '/../config/auth.php'; require_admin();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$data = $conn->query("SELECT DATE(created_at) d, SUM(total) s FROM orders WHERE status IN ('PAID','SHIPPED','DELIVERED','APPROVED') GROUP BY DATE(created_at) ORDER BY d ASC");
$labels=[]; $values=[]; while($r=$data->fetch_assoc()){ $labels[]=$r['d']; $values[]=(float)$r['s']; }

$top = $conn->query("SELECT p.name, SUM(oi.quantity) qty FROM order_items oi JOIN products p ON p.id=oi.product_id GROUP BY p.id ORDER BY qty DESC LIMIT 5");
$topLabels=[]; $topVals=[]; while($r=$top->fetch_assoc()){ $topLabels[]=$r['name']; $topVals[]=(int)$r['qty']; }
?>
<div class="row g-3">
  <div class="col-md-6"><div class="card shadow-sm"><div class="card-header">Revenue Over Time</div><div class="card-body"><canvas id="rev"></canvas></div></div></div>
  <div class="col-md-6"><div class="card shadow-sm"><div class="card-header">Top 5 Products</div><div class="card-body"><canvas id="top"></canvas></div></div></div>
</div>
<script>
new Chart(document.getElementById('rev'), {type:'line', data:{labels: <?php echo json_encode($labels); ?>, datasets:[{label:'Revenue', data: <?php echo json_encode($values); ?>}]}});
new Chart(document.getElementById('top'), {type:'bar', data:{labels: <?php echo json_encode($topLabels); ?>, datasets:[{label:'Qty', data: <?php echo json_encode($topVals); ?>}]}});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
