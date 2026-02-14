<?php
class Recommend {
  // Simple content-based recommender by category/brand/symptoms
  public static function suggest(mysqli $conn, int $userId, int $limit = 5) : array {
    $prefs = ['category'=>[], 'brand'=>[], 'sym'=>[]];
    $sql = "SELECT p.category, p.brand, p.symptoms FROM order_items oi
            JOIN orders o ON o.id=oi.order_id AND o.user_id=? AND o.status IN ('PLACED','APPROVED')
            JOIN products p ON p.id=oi.product_id";
    $stmt=$conn->prepare($sql); $stmt->bind_param('i',$userId); $stmt->execute(); $res=$stmt->get_result();
    while($r=$res->fetch_assoc()){
      $prefs['category'][$r['category']] = ($prefs['category'][$r['category']] ?? 0) + 1;
      $prefs['brand'][$r['brand']] = ($prefs['brand'][$r['brand']] ?? 0) + 1;
      foreach (explode(',', $r['symptoms']) as $s) {
        $s = trim($s); if ($s==='') continue;
        $prefs['sym'][$s] = ($prefs['sym'][$s] ?? 0) + 1;
      }
    }
    // Build query pref ordering
    $cat = array_keys($prefs['category']);
    $br  = array_keys($prefs['brand']);
    $sy  = array_keys($prefs['sym']);
    $q = "SELECT id,name,category,brand,price FROM products WHERE 1";
    if ($cat) {
      $safe = array_map([$conn,'real_escape_string'], $cat);
      $q .= " AND category IN ('" . implode("','", $safe) . "')";
    }
    if ($br) {
      $safe = array_map([$conn,'real_escape_string'], $br);
      $q .= " OR brand IN ('" . implode("','", $safe) . "')";
    }
    if ($sy) {
      $likeParts = [];
      foreach ($sy as $s) {
        $s = $conn->real_escape_string($s);
        $likeParts[] = "symptoms LIKE '%$s%'";
      }
      if ($likeParts) {
        $q .= " OR (" . implode(' OR ', $likeParts) . ")";
      }
    }
    $q .= " ORDER BY created_at DESC LIMIT " . intval(max(5,$limit));
    $items = [];
    $res = $conn->query($q);
    while($p=$res->fetch_assoc()) { $items[]=$p; }
    if (!$items) { // fallback: latest
      $res = $conn->query("SELECT id,name,category,brand,price FROM products ORDER BY id DESC LIMIT " . intval($limit));
      while($p=$res->fetch_assoc()) $items[]=$p;
    }
    return $items;
  }
}
?>
