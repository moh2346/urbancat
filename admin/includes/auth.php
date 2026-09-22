<?php
require __DIR__ . '/../../includes/bootstrap.php';
function admin_require(): void {
  if (empty($_SESSION['admin_id'])) { header('Location: '.base_url('admin/login.php')); exit; }
}
function admin_user(PDO $pdo): ?array {
  if(empty($_SESSION['admin_id'])) return null;
  $s=$pdo->prepare("SELECT id,name,email FROM admins WHERE id=?"); $s->execute([$_SESSION['admin_id']]); return $s->fetch() ?: null;
}
