<?php
require_once __DIR__ . '/../config/db.php';
$token = $_GET['token'] ?? '';
$msg='Invalid or expired token.';
if ($token) {
  $stmt=$conn->prepare('UPDATE users SET email_verified=1, verify_token=NULL WHERE verify_token=?');
  $stmt->bind_param('s',$token); $stmt->execute();
  if ($stmt->affected_rows > 0) $msg='Email verified. You can login.';
}
?>
<!doctype html><meta charset="utf-8"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<div class="container py-5"><div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div>
<a class="btn btn-primary" href="<?php echo BASE_URL . '/public/login.php'; ?>">Login</a></div>
