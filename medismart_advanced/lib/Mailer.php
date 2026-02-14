<?php
class Mailer {
  // Dev outbox writer for XAMPP
  public static function send($to, $subject, $body) {
    $dir = __DIR__ . '/../mails';
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $fname = $dir . '/' . preg_replace('/[^a-z0-9]+/i','_', $to . '_' . $subject) . '_' . time() . '.txt';
    file_put_contents($fname, "To: $to
Subject: $subject

$body");
    return true;
  }
}
?>
