<?php
// C:\xampp\htdocs\medismart_advanced\admin\login.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $error = 'Invalid CSRF.';
    } else {
        $u = trim($_POST['username'] ?? '');
        $p = $_POST['password'] ?? '';

        $stmt = $conn->prepare('SELECT id, username, password_hash, role FROM admins WHERE username = ?');
        $stmt->bind_param('s', $u);
        $stmt->execute();
        $res = $stmt->get_result();
        $a = $res->fetch_assoc();

        if ($a && password_verify($p, $a['password_hash'])) {
            $_SESSION['admin'] = ['id' => $a['id'], 'username' => $a['username'], 'role' => $a['role']];
            header('Location: ' . BASE_URL . '/admin/dashboard.php');
            exit;
        }
        $error = 'Login failed.';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="row"><div class="col-md-6 mx-auto">
  <div class="card shadow-sm">
    <div class="card-header">Admin Login</div>
    <div class="card-body">
      <?php if($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <div class="mb-3"><label class="form-label">Username</label><input class="form-control" name="username" required></div>
        <div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
        <div class="d-flex gap-2"><button class="btn btn-primary">Login</button><button class="btn btn-secondary" type="reset">Clear All</button></div>
      </form>
    </div>
  </div>
</div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
