<?php
require_once __DIR__ . '/../config/auth.php'; require_admin();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['ticket_id'], $_POST['body']) && verify_csrf($_POST['csrf']??'')){
  $tid=(int)$_POST['ticket_id']; $body=trim($_POST['body']);
  if ($tid && $body){ $stmt=$conn->prepare('INSERT INTO messages(ticket_id,sender,body) VALUES (?, "ADMIN", ?)'); $stmt->bind_param('is',$tid,$body); $stmt->execute(); }
}

$tickets=$conn->query("SELECT t.*, u.full_name FROM tickets t JOIN users u ON u.id=t.user_id ORDER BY t.id DESC");
?>
<div class="card shadow-sm"><div class="card-header">Support Tickets</div><div class="card-body">
<?php while($t=$tickets->fetch_assoc()): ?>
  <div class="mb-3 border p-2 rounded">
    <div class="d-flex justify-content-between"><strong>#<?php echo $t['id']; ?> <?php echo htmlspecialchars($t['subject']); ?></strong><span><?php echo htmlspecialchars($t['full_name']); ?></span></div>
    <div class="mt-2">
      <?php $msgs=$conn->query("SELECT * FROM messages WHERE ticket_id={$t['id']} ORDER BY id ASC"); while($m=$msgs->fetch_assoc()): ?>
        <div class="small"><strong><?php echo $m['sender']; ?>:</strong> <?php echo htmlspecialchars($m['body']); ?> <span class="text-muted"><?php echo $m['created_at']; ?></span></div>
      <?php endwhile; ?>
    </div>
    <form class="mt-2" method="post">
      <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
      <input type="hidden" name="ticket_id" value="<?php echo $t['id']; ?>">
      <div class="input-group"><input class="form-control" name="body" placeholder="Reply..."><button class="btn btn-outline-primary">Send</button></div>
    </form>
  </div>
<?php endwhile; ?>
</div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
