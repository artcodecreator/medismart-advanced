<?php
require_once __DIR__ . '/../config/auth.php'; require_user();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$msg=null; $err=null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!verify_csrf($_POST['csrf'] ?? '')) { $err='Invalid CSRF.'; }
  else if (isset($_FILES['file']) && $_FILES['file']['error']===UPLOAD_ERR_OK) {
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','application/pdf'=>'pdf'];
    $type = $_FILES['file']['type'];
    if (!isset($allowed[$type])) { $err='Only JPG/PNG/PDF allowed.'; }
    else {
      $ext=$allowed[$type]; $name=bin2hex(random_bytes(8)).".$ext";
      $dir=__DIR__ . "/assets/uploads"; if (!is_dir($dir)) { mkdir($dir,0777,true); }
      $dest="$dir/$name"; move_uploaded_file($_FILES['file']['tmp_name'],$dest);
      $path=BASE_URL . "/public/assets/uploads/$name";
      $stmt=$conn->prepare("INSERT INTO prescriptions (user_id,file_path) VALUES (?,?)");
      $stmt->bind_param("is", $_SESSION['user']['id'], $path); $stmt->execute();
      $msg="Uploaded. Pending verification.";
    }
  } else { $err='File required.'; }
}
?>
<div class="row"><div class="col-md-6 mx-auto">
  <div class="card shadow-sm">
    <div class="card-header">Upload Prescription</div>
    <div class="card-body">
      <?php if($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
      <?php if($err): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <div class="mb-3"><input type="file" name="file" class="form-control" required></div>
        <button class="btn btn-primary">Submit</button>
      </form>
    </div>
  </div>
</div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
