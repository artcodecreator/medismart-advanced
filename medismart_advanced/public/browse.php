<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$q = trim($_GET['q'] ?? '');
$cat = trim($_GET['category'] ?? '');
$brand = trim($_GET['brand'] ?? '');
$sym = trim($_GET['symptom'] ?? '');
$sort = $_GET['sort'] ?? 'name';

$sql = "SELECT id,name,category,brand,image_path,price,requires_prescription FROM products WHERE 1";
$params = [];
$types  = '';

if ($q)    { $sql .= " AND name LIKE ?";                                $params[] = '%'.$q.'%'; $types .= 's'; }
if ($cat)  { $sql .= " AND category=?";                                 $params[] = $cat;       $types .= 's'; }
if ($brand){ $sql .= " AND brand=?";                                    $params[] = $brand;     $types .= 's'; }
if ($sym)  { $sql .= " AND FIND_IN_SET(?, REPLACE(symptoms,' ',''))";   $params[] = $sym;       $types .= 's'; }

$allowed_sort = ['name','price','category','brand'];
if (!in_array($sort, $allowed_sort, true)) $sort = 'name';
$sql .= " ORDER BY $sort ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    // bind_param requires references; array spread alone won't work on PHP 7
    $vals = array_values($params);
    $refs = [];
    foreach ($vals as $i => $v) { $refs[$i] = &$vals[$i]; } // why: bind_param needs refs
    array_unshift($refs, $types);
    call_user_func_array([$stmt, 'bind_param'], $refs);
}
$stmt->execute();
$res    = $stmt->get_result();

$cats   = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
$brands = $conn->query("SELECT DISTINCT brand FROM products ORDER BY brand");

?>
<div class="card shadow-sm mb-3"><div class="card-body">
  <form class="row g-2">
    <div class="col-md-3"><input class="form-control" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Search"></div>
    <div class="col-md-2"><select class="form-select" name="category"><option value="">All Categories</option><?php while($r=$cats->fetch_assoc()): ?><option <?php if($cat===$r['category']) echo 'selected'; ?>><?php echo htmlspecialchars($r['category']); ?></option><?php endwhile; ?></select></div>
    <div class="col-md-2"><select class="form-select" name="brand"><option value="">All Brands</option><?php while($b=$brands->fetch_assoc()): ?><option <?php if($brand===$b['brand']) echo 'selected'; ?>><?php echo htmlspecialchars($b['brand']); ?></option><?php endwhile; ?></select></div>
    <div class="col-md-2"><input class="form-control" name="symptom" value="<?php echo htmlspecialchars($sym); ?>" placeholder="Symptom e.g. fever"></div>
    <div class="col-md-2"><select class="form-select" name="sort"><?php foreach(['name','price','category','brand'] as $s){ echo '<option'.($sort===$s?' selected':'').'>'.$s.'</option>'; } ?></select></div>
    <div class="col-md-1 d-grid"><button class="btn btn-primary">Filter</button></div>
  </form>
</div></div>

<div class="row g-3">
<?php while($p=$res->fetch_assoc()): ?>
  <div class="col-md-4">
    <div class="card h-100 shadow-sm">
      <?php
        // choose image: product image or category fallback
        $img = $p['image_path'];
        if (!$img) {
          $cat = $p['category'];
          $base = BASE_URL . '/public/assets/med/';
          if (in_array($cat, ['Pain Reliever','Fever Relief','Headache'])) $img = $base . 'pills_pain.svg';
          elseif ($cat === 'Antibiotic') $img = $base . 'antibiotic_capsules.svg';
          elseif ($cat === 'Allergy Relief') $img = $base . 'allergy_bottle.svg';
          elseif ($cat === 'Electrolyte') $img = $base . 'ors_pack.svg';
          else $img = $base . 'hero_medicine.svg';
        }
      ?>
      <img src="<?php echo htmlspecialchars($img); ?>" class="card-img-top" alt="Product image" style="height:160px;object-fit:cover">
      <div class="card-body">
        <h5 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h5>
        <div class="text-muted small"><?php echo htmlspecialchars($p['brand']); ?> • <?php echo htmlspecialchars($p['category']); ?></div>
        <div class="mt-2 fw-bold">PKR <?php echo number_format($p['price'],2); ?></div>
        <?php if ($p['requires_prescription']): ?><span class="badge bg-warning text-dark">Prescription Required</span><?php endif; ?>
      </div>
      <div class="card-footer bg-white border-0"><a class="btn btn-sm btn-primary" href="<?php echo BASE_URL; ?>/public/cart.php?add=<?php echo $p['id']; ?>">Add to Cart</a></div>
    </div>
  </div>
<?php endwhile; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
