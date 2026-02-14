<?php
require_once __DIR__ . '/../config/auth.php'; require_user();
require_once __DIR__ . '/../config/db.php';
if ($_SERVER['REQUEST_METHOD']==='POST' && verify_csrf($_POST['csrf']??'')){
  $tid=(int)($_POST['ticket_id']??0); $body=trim($_POST['body']??'');
  if ($tid && $body){ $stmt=$conn->prepare('INSERT INTO messages(ticket_id,sender,body) VALUES (?, "USER", ?)'); $stmt->bind_param('is',$tid,$body); $stmt->execute(); }
}
header('Location: ' . BASE_URL . '/public/support.php'); exit;
