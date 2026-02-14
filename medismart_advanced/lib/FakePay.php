<?php
class FakePay {
  // Demo gateway: accepts 4242424242424242 only
  public static function charge(mysqli $conn, int $orderId, string $card, string $exp, string $cvc, float $amount) : array {
    $ok = preg_replace('/\D/','',$card) === '4242424242424242';
    $stmt=$conn->prepare("INSERT INTO payment_attempts (order_id, amount, success) VALUES (?,?,?)");
    $succ = $ok ? 1 : 0;
    $stmt->bind_param('idi',$orderId, $amount, $succ); $stmt->execute();
    if ($ok) return ['success'=>true,'txn_id'=>'FAKE-' . bin2hex(random_bytes(4))];
    return ['success'=>false,'error'=>'Card declined in test gateway'];
  }
}
?>
