<?php
require_once __DIR__ . '/../config/auth.php'; require_admin();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

if (isset($_GET['id'], $_GET['action'])) {
  $id=(int)$_GET['id'];
  $action=$_GET['action']==='approve'?'APPROVED':'REJECTED';
  $stmt=$conn->prepare("UPDATE prescriptions SET status=? WHERE id=?");
  $stmt->bind_param("si",$action,$id); $stmt->execute();
  header("Location: prescriptions.php"); exit;
}

$pres = $conn->query("SELECT p.id, u.full_name, p.file_path, p.status, p.created_at FROM prescriptions p JOIN users u ON u.id=p.user_id ORDER BY p.id DESC");
?>
<h4>Prescription Verification</h4>
<table class="table table-striped">
  <thead><tr><th>ID</th><th>User</th><th>File</th><th>Status</th><th>Actions</th></tr></thead>
  <tbody>
    <?php while($r=$pres->fetch_assoc()): ?>
      <tr>
        <td><?php echo $r['id']; ?></td>
        <td><?php echo htmlspecialchars($r['full_name']); ?></td>
        <td><a href="<?php echo htmlspecialchars($r['file_path']); ?>" target="_blank">View</a></td>
        <td><?php echo htmlspecialchars($r['status']); ?></td>
        <td>
          <a class="btn btn-sm btn-success" href="?id=<?php echo $r['id']; ?>&action=approve">Approve</a>
          <a class="btn btn-sm btn-danger" href="?id=<?php echo $r['id']; ?>&action=reject">Reject</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
