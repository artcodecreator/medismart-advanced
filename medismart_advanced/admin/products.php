<?php
require_once __DIR__ . '/../config/auth.php'; require_admin();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (!verify_csrf($_POST['csrf']??'')) die('CSRF');
  $id=(int)($_POST['id']??0);
  $name=trim($_POST['name']??''); $cat=trim($_POST['category']??''); $brand=trim($_POST['brand']??'');
  $sym=trim($_POST['symptoms']??''); $rx=isset($_POST['rx'])?1:0; $price=(float)($_POST['price']??0); $stock=(int)($_POST['stock']??0);
  // handle image upload (optional)
  $imgPath=null;
  if (isset($_FILES['image']) && $_FILES['image']['error']!==UPLOAD_ERR_NO_FILE) {
    $allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/svg+xml'=>'svg'];
    $type=$_FILES['image']['type'];
    if (isset($allowed[$type])) {
      $ext=$allowed[$type]; $namef=bin2hex(random_bytes(8)).".$ext";
      $dir=__DIR__ . '/../public/assets/uploads/products'; if (!is_dir($dir)) mkdir($dir,0777,true);
      $dest="$dir/$namef"; move_uploaded_file($_FILES['image']['tmp_name'],$dest);
      $imgPath=BASE_URL . "/public/assets/uploads/products/$namef";
    }
  }
  if ($id){
    if ($imgPath){
      $stmt=$conn->prepare('UPDATE products SET name=?,category=?,brand=?,image_path=?,symptoms=?,requires_prescription=?,price=?,stock=? WHERE id=?');
      $stmt->bind_param('sssssidii',$name,$cat,$brand,$imgPath,$sym,$rx,$price,$stock,$id);
    } else {
      $stmt=$conn->prepare('UPDATE products SET name=?,category=?,brand=?,symptoms=?,requires_prescription=?,price=?,stock=? WHERE id=?');
      $stmt->bind_param('ssssidii',$name,$cat,$brand,$sym,$rx,$price,$stock,$id);
    }
    $stmt->execute();
  } else {
    if ($imgPath){
      $stmt=$conn->prepare('INSERT INTO products(name,category,brand,image_path,symptoms,requires_prescription,price,stock) VALUES (?,?,?,?,?,?,?,?)');
      $stmt->bind_param('sssssidi',$name,$cat,$brand,$imgPath,$sym,$rx,$price,$stock);
    } else {
      $stmt=$conn->prepare('INSERT INTO products(name,category,brand,symptoms,requires_prescription,price,stock) VALUES (?,?,?,?,?,?,?)');
      $stmt->bind_param('ssssidi',$name,$cat,$brand,$sym,$rx,$price,$stock);
    }
    $stmt->execute();
  }
  header('Location: products.php'); exit;
}
if (isset($_GET['delete'])){ $id=(int)$_GET['delete']; $conn->query("DELETE FROM products WHERE id=$id"); header('Location: products.php'); exit; }

$products=$conn->query('SELECT * FROM products ORDER BY id DESC');
?>
<div class="row">
  <div class="col-md-5">
    <div class="card shadow-sm mb-3">
      <div class="card-header">Add / Edit Product</div>
      <div class="card-body">
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
          <input type="hidden" name="id" value="">
          <div class="mb-2"><input class="form-control" name="name" placeholder="Name" required></div>
          <div class="mb-2"><input class="form-control" name="category" placeholder="Category" required></div>
          <div class="mb-2"><input class="form-control" name="brand" placeholder="Brand" required></div>
          <div class="mb-2"><label class="form-label">Image</label><input class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.svg"></div>
          <div class="mb-2"><input class="form-control" name="symptoms" placeholder="Symptoms CSV e.g. fever,headache"></div>
          <div class="mb-2"><label class="form-check"><input class="form-check-input" type="checkbox" name="rx"> Prescription Required</label></div>
          <div class="mb-2"><input class="form-control" type="number" step="0.01" name="price" placeholder="Price" required></div>
          <div class="mb-2"><input class="form-control" type="number" name="stock" placeholder="Stock" required></div>
          <button class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="card shadow-sm">
      <div class="card-header">Products</div>
      <div class="card-body p-0">
        <table class="table table-striped mb-0">
          <thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Category</th><th>Brand</th><th>Price</th><th>Stock</th><th>Rx</th><th></th></tr></thead>
          <tbody><?php while($p=$products->fetch_assoc()): ?><tr>
            <td><?php echo $p['id']; ?></td>
            <td><?php $img=$p['image_path'] ?: (BASE_URL . '/public/assets/med/hero_medicine.svg'); ?><img src="<?php echo htmlspecialchars($img); ?>" alt="thumb" style="width:48px;height:48px;object-fit:cover;border-radius:6px"></td>
            <td><?php echo htmlspecialchars($p['name']); ?></td>
            <td><?php echo htmlspecialchars($p['category']); ?></td>
            <td><?php echo htmlspecialchars($p['brand']); ?></td>
            <td><?php echo number_format($p['price'],2); ?></td>
            <td><?php echo $p['stock']; ?></td>
            <td><?php echo $p['requires_prescription']?'Yes':'No'; ?></td>
            <td>
              <a class="btn btn-sm btn-danger" href="?delete=<?php echo $p['id']; ?>" onclick="return confirm('Delete?')">Delete</a>
            </td>
          </tr><?php endwhile; ?></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
