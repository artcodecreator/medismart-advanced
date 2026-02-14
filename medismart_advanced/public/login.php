<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

$error=null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!verify_csrf($_POST['csrf']??'')) $error='Invalid CSRF.';
  else {
    $email=trim($_POST['email']??''); $pass=$_POST['password']??'';
    $stmt=$conn->prepare('SELECT id, full_name, password_hash, email_verified, twofa_enabled, status FROM users WHERE email=?');
    $stmt->bind_param('s',$email); $stmt->execute(); $res=$stmt->get_result();
    if ($u=$res->fetch_assoc()) {
      $ok=password_verify($pass,$u['password_hash']);
      $conn->query("INSERT INTO login_events(user_id, ip, user_agent, success) VALUES ({$u['id']}, '{$conn->real_escape_string($_SERVER['REMOTE_ADDR']??'')}', '{$conn->real_escape_string($_SERVER['HTTP_USER_AGENT']??'')}', ".($ok?1:0).")");
      if ($ok && $u['email_verified'] && ($u['status'] ?? 'Active') === 'Active') {
        if ($u['twofa_enabled']) {
          $otp=rand(100000,999999);
          $hash=password_hash($otp, PASSWORD_DEFAULT);
          $expires = date('Y-m-d H:i:s', time()+300);
          $stmt=$conn->prepare('UPDATE users SET twofa_last_otp_hash=?, twofa_otp_expires=? WHERE id=?');
          $stmt->bind_param('ssi',$hash, $expires, $u['id']); $stmt->execute();
          // write OTP to mails folder
          require_once __DIR__ . '/../lib/Mailer.php';
          Mailer::send($email, 'Your MediSmart 2FA code', 'Code: ' . $otp . ' (valid 5 minutes)');
          $_SESSION['pending_user']=['id'=>$u['id'],'name'=>$u['full_name']];
          header('Location: ' . BASE_URL . '/public/twofactor.php'); exit;
        } else {
          $_SESSION['user']=['id'=>$u['id'],'name'=>$u['full_name']];
          header('Location: ' . BASE_URL . '/public/browse.php'); exit;
        }
      } else if ($ok && ($u['status'] ?? 'Active') !== 'Active') {
        $error = 'Your account is inactive. Please contact support.';
      } else $error='Invalid credentials or email not verified.';
    } else $error='Invalid credentials.';
  }
}
?>
<div class="row"><div class="col-md-6 mx-auto">
  <div class="card shadow-sm">
    <div class="card-header">User Login</div>
    <div class="card-body">
      <?php if($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
        <div class="d-flex gap-2"><button class="btn btn-primary">Login</button><button class="btn btn-secondary" type="reset">Clear All</button></div>
      </form>
    </div>
  </div>
</div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
