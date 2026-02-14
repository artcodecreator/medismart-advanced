<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../lib/Mailer.php';

$errors=[]; $ok=false;
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!verify_csrf($_POST['csrf'] ?? '')) $errors[]='Invalid CSRF.';
  $full_name=trim($_POST['full_name']??''); $email=trim($_POST['email']??''); $pass=$_POST['password']??'';
  $cnic=preg_replace('/\D/','', $_POST['cnic']??''); $about=trim($_POST['about_me']??'');
  if (!preg_match('/^[A-Za-z]{3,12}$/',$full_name)) $errors[]='Full Name 3–12 letters only.';
  if (!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Invalid email.';
  if (!preg_match('/^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{8,}$/',$pass)) $errors[]='Weak password.';
  if (!preg_match('/^\d{13}$/',$cnic)) $errors[]='CNIC must be 13 digits.';
  $dp_path=null;
  if (isset($_FILES['display_picture']) && $_FILES['display_picture']['error']!==UPLOAD_ERR_NO_FILE) {
    $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/jpg'=>'jpg'];
    $type=$_FILES['display_picture']['type'];
    if (!isset($allowed[$type])) $errors[]='Display picture must be JPG/PNG.';
    if ($_FILES['display_picture']['size']>2*1024*1024) $errors[]='Image max 2MB.';
    if (!$errors) {
      $ext=$allowed[$type]; $name=bin2hex(random_bytes(8)).".$ext";
      $dir=__DIR__.'/assets/uploads'; if (!is_dir($dir)) mkdir($dir,0777,true);
      $dest="$dir/$name"; move_uploaded_file($_FILES['display_picture']['tmp_name'],$dest);
      $dp_path=BASE_URL . "/public/assets/uploads/$name";
    }
  }
  if (!$errors) {
    $hash=password_hash($pass,PASSWORD_DEFAULT);
    $token=bin2hex(random_bytes(16));
    $stmt=$conn->prepare("INSERT INTO users(full_name,email,password_hash,verify_token,cnic,about_me,display_picture) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param('sssssss',$full_name,$email,$hash,$token,$cnic,$about,$dp_path);
    if ($stmt->execute()) {
      $link = (isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:'http') . '://' . $_SERVER['HTTP_HOST'] . BASE_URL . "/public/verify.php?token=$token";
      Mailer::send($email, 'Verify your MediSmart account', "Click to verify: $link");
      $ok=true;
      flash('Registration successful. Check mails folder for verification link.');
      header('Location: ' . BASE_URL . '/public/login.php'); exit;
    } else { $errors[]='Email already exists.'; }
  }
}
?>
<div class="row">
  <div class="col-lg-8 mx-auto">
    <div class="card shadow-sm">
      <div class="card-header">User Registration</div>
      <div class="card-body">
        <?php if($errors): ?><div class="alert alert-danger"><?php echo implode('<br>',$errors); ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" novalidate>
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
          <div class="mb-3"><label class="form-label">Full Name</label><input name="full_name" class="form-control" required pattern="[A-Za-z]{3,12}"></div>
          <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Display Picture</label><input type="file" name="display_picture" class="form-control" accept=".jpg,.jpeg,.png"></div>
          <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required pattern="(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{8,}"></div>
          <div class="mb-3"><label class="form-label">CNIC</label><input name="cnic" class="form-control" required pattern="\d{13}" maxlength="13"></div>
          <div class="mb-3"><label class="form-label">About Me</label><textarea name="about_me" class="form-control" rows="3"></textarea></div>
          <div class="d-flex gap-2"><button class="btn btn-primary">Signup</button><button class="btn btn-secondary" type="reset">Clear All</button></div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
