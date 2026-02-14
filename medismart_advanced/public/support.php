<?php
require_once __DIR__ . '/../config/auth.php'; require_user();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$uid=$_SESSION['user']['id'];
if ($_SERVER['REQUEST_METHOD']==='POST' && verify_csrf($_POST['csrf']??'')){
  $sub=trim($_POST['subject']??''); $msg=trim($_POST['message']??'');
  if ($sub && $msg){
    $stmt=$conn->prepare('INSERT INTO tickets(user_id,subject) VALUES (?,?)'); $stmt->bind_param('is',$uid,$sub); $stmt->execute();
    $tid=$stmt->insert_id; $stmt2=$conn->prepare('INSERT INTO messages(ticket_id,sender,body) VALUES (?, "USER", ?)'); $stmt2->bind_param('is',$tid,$msg); $stmt2->execute();
  }
}

$tickets=$conn->query("SELECT * FROM tickets WHERE user_id=$uid ORDER BY id DESC");
?>
<div class="row g-3">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-header">New Support Ticket</div>
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
          <div class="mb-2"><input class="form-control" name="subject" placeholder="Subject" required></div>
          <div class="mb-2"><textarea class="form-control" name="message" rows="4" placeholder="Describe your issue" required></textarea></div>
          <button class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="card shadow-sm">
      <div class="card-header">Your Tickets</div>
      <div class="card-body">
        <?php while($t=$tickets->fetch_assoc()): ?>
          <div class="mb-3 border p-2 rounded">
            <div class="d-flex justify-content-between"><strong>#<?php echo $t['id']; ?> <?php echo htmlspecialchars($t['subject']); ?></strong><span class="badge bg-secondary"><?php echo $t['status']; ?></span></div>
            <div class="mt-2">
              <?php $msgs=$conn->query("SELECT * FROM messages WHERE ticket_id={$t['id']} ORDER BY id ASC"); while($m=$msgs->fetch_assoc()): ?>
                <div class="small"><strong><?php echo $m['sender']; ?>:</strong> <?php echo htmlspecialchars($m['body']); ?> <span class="text-muted"><?php echo $m['created_at']; ?></span></div>
              <?php endwhile; ?>
            </div>
            <form class="mt-2" method="post" action="ticket_reply.php">
              <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
              <input type="hidden" name="ticket_id" value="<?php echo $t['id']; ?>">
              <div class="input-group"><input class="form-control" name="body" placeholder="Reply..."><button class="btn btn-outline-primary">Send</button></div>
            </form>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
