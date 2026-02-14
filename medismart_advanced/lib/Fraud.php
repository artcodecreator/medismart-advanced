<?php
class Fraud {
  public static function score(mysqli $conn, int $orderId) : array {
    $score = 0; $reasons = [];
    $o = $conn->query("SELECT o.*, TIMESTAMPDIFF(HOUR,u.created_at,NOW()) as acct_age_hours FROM orders o JOIN users u ON u.id=o.user_id WHERE o.id=$orderId")->fetch_assoc();
    if (!$o) return ['score'=>0,'reasons'=>[]];

    if ($o['total'] >= 5000) { $score += 30; $reasons[]='High order value'; }
    if ($o['acct_age_hours'] < 24) { $score += 20; $reasons[]='New account'; }
    $orderId = intval($orderId);
    $fails = $conn->query("SELECT COUNT(*) c FROM payment_attempts WHERE order_id=$orderId AND success=0")->fetch_assoc()['c'] ?? 0;
    if ($fails >= 2) { $score += 25; $reasons[]='Multiple failed payments'; }
    $pres = $conn->query("SELECT COUNT(*) c FROM prescriptions WHERE user_id={$o['user_id']} AND status='APPROVED'")->fetch_assoc()['c'] ?? 0;
    if ($pres == 0 && $conn->query("SELECT COUNT(*) c FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=$orderId AND p.requires_prescription=1")->fetch_assoc()['c'] > 0) {
      $score += 25; $reasons[]='Prescription required but not approved';
    }
    // Cap at 100
    if ($score > 100) $score = 100;
    $risk = $score >= 60 ? 'HIGH' : ($score >= 30 ? 'MEDIUM' : 'LOW');
    return ['score'=>$score, 'risk'=>$risk, 'reasons'=>$reasons];
  }
}
?>
