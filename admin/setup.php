<?php
// One-time setup to create first admin. DELETE after use.
require __DIR__ . '/../includes/bootstrap.php';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!verify_csrf($_POST['csrf_token'] ?? '')) $msg='Invalid CSRF';
  else{
    $name=sanitize_text($_POST['name']??'Admin',120);
    $email=trim($_POST['email']??'');
    $pass=$_POST['password']??'';
    if(!is_valid_email($email)) $msg='Valid email required';
    elseif(mb_strlen($pass)<8) $msg='Password at least 8 chars';
    else{
      $cnt=$pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
      if($cnt>0) $msg='Admin already exists. Delete this file if you need another admin via direct DB.';
      else{
        $hash=password_hash($pass, PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO admins (name,email,password_hash) VALUES (?,?,?)")->execute([$name,$email,$hash]);
        $msg='Admin created: '.h($email).' — DELETE this file now (admin/setup.php) for security.';
      }
    }
  }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Setup Admin — Urban Cats</title><link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>"><style>body{background:var(--ivory);display:grid;place-items:center;min-height:100vh}.box{max-width:460px;width:92%;background:var(--white);padding:1.6rem;border-radius:18px;box-shadow:var(--shadow-card)}</style></head><body>
<div class="box">
<h1 style="font-family:var(--font-display)">Create first admin</h1>
<?php if($msg): ?><div class="alert <?= str_contains($msg,'created')?'alert--success':'alert--error' ?>"><?= e($msg) ?></div><?php endif; ?>
<form method="post" class="form">
<?= csrf_field() ?>
<div class="field"><label>Name</label><input name="name" value="<?= e($_POST['name']??'') ?>" required></div>
<div class="field"><label>Email</label><input name="email" type="email" value="<?= e($_POST['email']??'') ?>" required></div>
<div class="field"><label>Password (min 8)</label><input name="password" type="password" required></div>
<button class="btn btn--primary btn--block" type="submit">Create admin</button>
</form>
<p class="muted" style="font-size:.82rem">After success, <strong>delete</strong> <code>admin/setup.php</code>.</p>
</div></body></html>
