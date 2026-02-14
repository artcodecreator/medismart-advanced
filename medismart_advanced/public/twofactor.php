<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
if (!isset($_SESSION['pending_user'])) { header('Location: ' . BASE_URL . '/public/login.php'); exit; }

$error=null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!verify_csrf($_POST['csrf']??'')) $error='Invalid CSRF.';
  else {
    $code=trim($_POST['code']??'');
    $uid=$_SESSION['pending_user']['id'];
    $stmt=$conn->prepare('SELECT twofa_last_otp_hash, twofa_otp_expires, full_name FROM users WHERE id=?');
    $stmt->bind_param('i',$uid); $stmt->execute(); $u=$stmt->get_result()->fetch_assoc();
    if ($u && strtotime($u['twofa_otp_expires'])>=time() && password_verify($code, $u['twofa_last_otp_hash'])){
      $_SESSION['user']=['id'=>$uid,'name'=>$u['full_name']];
      unset($_SESSION['pending_user']);
      header('Location: ' . BASE_URL . '/public/browse.php'); exit;
    } else $error='Wrong or expired code.';
  }
}
?>
<div class="row"><div class="col-md-6 mx-auto">
  <div class="card shadow-sm">
    <div class="card-header">Two-Factor Authentication</div>
    <div class="card-body">
      <?php if($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
      <p class="text-muted">Enter the 6-digit code sent to your email. Check the <code>/mails</code> folder in project root in dev.</p>
      <form method="post">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <div class="mb-3"><input name="code" class="form-control" placeholder="123456" maxlength="6" required></div>
        <button class="btn btn-primary">Verify</button>
      </form>
    </div>
  </div>
</div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
