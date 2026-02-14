<?php
require_once __DIR__ . '/../config/auth.php'; require_admin();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

// Handle status update via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    header('Content-Type: application/json');
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF.']);
        exit;
    }
    $user_id = intval($_POST['user_id']);
    $status = $_POST['status'] === 'Active' ? 'Active' : 'Inactive';
    
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user status']);
    }
    $stmt->close();
    exit;
}

$users = $conn->query("SELECT id,full_name,email,email_verified,status,created_at FROM users ORDER BY id DESC");
?>
<h4>Users</h4>
<table class="table table-striped">
  <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Verified</th><th>Status</th><th>Joined</th></tr></thead>
  <tbody><?php while($u=$users->fetch_assoc()): ?><tr>
    <td><?php echo $u['id']; ?></td>
    <td><?php echo htmlspecialchars($u['full_name']); ?></td>
    <td><?php echo htmlspecialchars($u['email']); ?></td>
    <td><?php echo $u['email_verified']?'Yes':'No'; ?></td>
    <td>
      <select class="form-select form-select-sm status-select" data-user-id="<?php echo $u['id']; ?>" data-current-status="<?php echo $u['status']; ?>">
        <option value="Active" <?php echo $u['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
        <option value="Inactive" <?php echo $u['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
      </select>
    </td>
    <td><?php echo htmlspecialchars($u['created_at']); ?></td>
  </tr><?php endwhile; ?></tbody>
</table>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const statusSelects = document.querySelectorAll('.status-select');
  const csrf = '<?php echo htmlspecialchars(csrf_token()); ?>';
  
  statusSelects.forEach(select => {
    select.addEventListener('change', function() {
      const userId = this.getAttribute('data-user-id');
      const newStatus = this.value;
      const currentStatus = this.getAttribute('data-current-status');
      
      if (newStatus === currentStatus) {
        return;
      }
      
      if (confirm(`Are you sure you want to change this user's status to ${newStatus}?`)) {
        const formData = new FormData();
        formData.append('action', 'update_status');
        formData.append('user_id', userId);
        formData.append('status', newStatus);
        formData.append('csrf', csrf);
        
        fetch('', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            this.setAttribute('data-current-status', newStatus);
            alert('User status updated successfully!');
          } else {
            alert('Failed to update user status. Please try again.');
            this.value = currentStatus;
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred. Please try again.');
          this.value = currentStatus;
        });
      } else {
        this.value = currentStatus;
      }
    });
  });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
