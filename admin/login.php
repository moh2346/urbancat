<?php
require __DIR__ . '/../includes/bootstrap.php';
if(!empty($_SESSION['admin_id'])){ header('Location: '.base_url('admin/index.php')); exit; }
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!verify_csrf($_POST['csrf_token'] ?? '')) $errors[]='Invalid session.';
  elseif(is_honeypot_filled()) $errors[]='Spam detected.';
  else{
    $email=trim($_POST['email']??''); $pass=$_POST['password']??'';
    $ip=$_SERVER['REMOTE_ADDR']??'0.0.0.0';
    if(!check_rate_limit($pdo,$ip,5,600)) $errors[]='Too many attempts. Try again in 10 minutes.';
    else{
      $s=$pdo->prepare("SELECT id,password_hash FROM admins WHERE email=?"); $s->execute([$email]); $admin=$s->fetch();
      if($admin && password_verify($pass,$admin['password_hash'])){
        // check if rehash needed
        if(password_needs_rehash($admin['password_hash'], PASSWORD_DEFAULT)){
          $pdo->prepare("UPDATE admins SET password_hash=? WHERE id=?")->execute([password_hash($pass,PASSWORD_DEFAULT),$admin['id']]);
        }
        session_regenerate_id(true);
        $_SESSION['admin_id']=$admin['id'];
        log_attempt($pdo,$ip,$email,1);
        header('Location: '.base_url('admin/index.php')); exit;
      } else {
        log_attempt($pdo,$ip,$email,0);
        $errors[]='Invalid email or password.';
      }
    }
  }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Login — Urban Cats</title><link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>"><style>body{background:var(--ivory);display:grid;place-items:center;min-height:100vh}.login{max-width:420px;width:92%;background:var(--white);padding:1.6rem;border-radius:18px;box-shadow:var(--shadow-card);border:1px solid rgba(28,27,25,.07)}</style></head><body>
<div class="login">
<h1 style="font-family:var(--font-display);margin:0 0 .6rem">Admin login</h1>
<?php if($errors): ?><div class="alert alert--error"><ul style="margin:0;padding-left:1.1rem"><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" class="form" novalidate>
<?= csrf_field().honeypot_field() ?>
<div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="<?= e($_POST['email']??'') ?>" required></div>
<div class="field"><label for="password">Password</label><input id="password" name="password" type="password" required></div>
<button class="btn btn--primary btn--block" type="submit">Sign in</button>
</form>
<p class="muted" style="font-size:.82rem;margin-top:1rem">First admin? Run <code>admin/setup.php</code> then delete it.</p>
<p class="muted" style="font-size:.82rem"><a href="<?= asset('index.php') ?>">← Back to site</a></p>
</div>
</body></html>
