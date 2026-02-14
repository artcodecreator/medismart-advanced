<?php
// public/profile.php  (UI/UX only)
require_once __DIR__ . '/../config/auth.php'; require_user();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$uid = $_SESSION['user']['id'];
$u = $conn->query("SELECT id,full_name,email,twofa_enabled,display_picture FROM users WHERE id=$uid")->fetch_assoc();
$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf($_POST['csrf'] ?? '')) { $msg = 'Invalid CSRF.'; }
  else {
    if (isset($_POST['toggle2fa'])) {
      $val = $u['twofa_enabled'] ? 0 : 1;
      $conn->query("UPDATE users SET twofa_enabled=$val WHERE id=$uid");
      $u['twofa_enabled'] = $val; $msg = '2FA setting updated.';
    }
  }
}

// Fallback avatar if none
$avatar = $u['display_picture'] ?: 'https://ui-avatars.com/api/?name='.urlencode($u['full_name']).'&background=10b981&color=fff&size=256';
?>
<style>
.profile-hero {
  position: relative;
  border-radius: 20px;
  padding: 2.2rem;
  background: linear-gradient(135deg, rgba(99,102,241,.75), rgba(6,182,212,.75));
  box-shadow: 0 10px 30px rgba(0,0,0,.15);
  overflow: hidden;
}
.profile-hero::after {
  content: "";
  position: absolute; inset: -40%;
  background: radial-gradient(400px 200px at 20% 10%, rgba(255,255,255,.25), transparent 60%),
              radial-gradient(400px 200px at 80% 110%, rgba(255,255,255,.18), transparent 60%);
  pointer-events: none;
}
.glass {
  background: rgba(255,255,255,.14);
  backdrop-filter: blur(10px) saturate(140%);
  -webkit-backdrop-filter: blur(10px) saturate(140%);
  border: 1px solid rgba(255,255,255,.35);
  border-radius: 18px;
  box-shadow: 0 10px 30px rgba(0,0,0,.12);
}
.avatar-xl {
  width: 140px; height: 140px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid rgba(255,255,255,.6);
  box-shadow: 0 8px 20px rgba(0,0,0,.18);
}
.kv {
  display: grid; grid-template-columns: 160px 1fr; gap: .5rem 1rem;
}
@media (max-width: 576px){ .kv { grid-template-columns: 1fr; } }
.badge-soft {
  background: linear-gradient(135deg, rgba(16,185,129,.18), rgba(6,182,212,.18));
  color: #064e3b;
  border: 1px solid rgba(16,185,129,.35);
}
.switch-wrap {
  display:flex; align-items:center; gap:.75rem;
  padding:.75rem; border-radius:12px;
  background: rgba(255,255,255,.35);
  border: 1px solid rgba(255,255,255,.45);
}
</style>

<div class="profile-hero mb-4 text-white">
  <div class="d-flex flex-wrap align-items-center gap-3">
    <img src="<?php echo htmlspecialchars($avatar); ?>" class="avatar-xl" alt="Profile picture">
    <div>
      <h2 class="h4 mb-1"><?php echo htmlspecialchars($u['full_name']); ?></h2>
      <div class="small opacity-90"><?php echo htmlspecialchars($u['email']); ?></div>
      <span class="badge badge-soft mt-2"><?php echo $u['twofa_enabled'] ? '2FA Enabled' : '2FA Disabled'; ?></span>
    </div>
  </div>
</div>

<?php if ($msg): ?>
  <div class="alert alert-info glass"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<div class="row g-4">
  <!-- Left: Avatar and quick actions -->
  <div class="col-lg-4">
    <div class="glass p-3 h-100">
      <div class="text-center">
        <img src="<?php echo htmlspecialchars($avatar); ?>" class="avatar-xl mb-3" alt="Profile picture">
        <div class="text-muted small">Your display image</div>
      </div>
      <hr>
      <div class="d-grid gap-2">
        <form method="post">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
          <button class="btn btn-primary">
            <?php echo $u['twofa_enabled'] ? 'Disable 2FA' : 'Enable 2FA'; ?>
          </button>
          <input type="hidden" name="toggle2fa" value="1">
        </form>
        <a href="<?php echo BASE_URL; ?>/public/upload_prescription.php" class="btn btn-outline-primary">Upload Prescription</a>
        <a href="<?php echo BASE_URL; ?>/public/orders.php" class="btn btn-outline-primary">View Orders</a>
      </div>
    </div>
  </div>

  <!-- Right: Profile details -->
  <div class="col-lg-8">
    <div class="glass p-3">
      <div class="card-header glass mb-3">Profile</div>
      <div class="kv">
        <div class="text-muted">Full Name</div>
        <div><strong><?php echo htmlspecialchars($u['full_name']); ?></strong></div>

        <div class="text-muted">Email</div>
        <div><?php echo htmlspecialchars($u['email']); ?></div>

        <div class="text-muted">Two-Factor Auth</div>
        <div>
          <span class="badge <?php echo $u['twofa_enabled'] ? 'bg-success' : 'bg-secondary'; ?>">
            <?php echo $u['twofa_enabled'] ? 'Enabled' : 'Disabled'; ?>
          </span>
        </div>
      </div>

      <hr>
      <div class="switch-wrap">
        <form method="post" class="d-flex align-items-center gap-3 m-0">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
          <div class="form-check form-switch m-0">
            <input class="form-check-input" type="checkbox" role="switch" id="twofaSwitch"
                   name="toggle2fa" value="1" <?php echo $u['twofa_enabled'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="twofaSwitch">Toggle 2FA</label>
          </div>
          <button class="btn btn-sm btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
