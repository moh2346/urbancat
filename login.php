<?php
require __DIR__.'/includes/bootstrap.php';
if(customer_logged_in()){header('Location: '.base_url('account.php')); exit;}
$err=''; if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!verify_csrf($_POST['csrf_token']??'')) $err='Invalid session.';
 elseif(is_honeypot_filled()) $err='Spam.';
 else {
  $email=trim($_POST['email']??''); $pass=$_POST['password']??''; $ip=$_SERVER['REMOTE_ADDR']??'0.0.0.0';
  if(!check_rate_limit($pdo,$ip,5,600)) $err='Too many attempts.';
  else {
   $st=$pdo->prepare("SELECT id,password_hash FROM customers WHERE email=?"); $st->execute([$email]); $u=$st->fetch();
   if($u && password_verify($pass,$u['password_hash'])){session_regenerate_id(true); $_SESSION['customer_id']=$u['id']; log_attempt($pdo,$ip,$email,1); header('Location: '.base_url('account.php')); exit;}
   else{log_attempt($pdo,$ip,$email,0); $err='Invalid email or password.';}
  }
 }
}
$pageTitle='Login — Urban Cats';
?>
<!doctype html><html lang="en"><head><?php include __DIR__.'/includes/partials/head.php'; ?></head><body>
<?php include __DIR__.'/includes/partials/header.php'; ?>
<main id="main"><div class="container section" style="max-width:480px"><h1 class="h2" style="font-family:var(--font-display)">Login</h1><?php if($err): ?><div class="alert alert--error"><?= e($err) ?></div><?php endif; ?><?php include __DIR__.'/includes/partials/alerts.php'; ?><form method="post" class="form" novalidate><?= csrf_field().honeypot_field() ?><div class="field"><label>Email</label><input type="email" name="email" value="<?= e($_POST['email']??'') ?>" required></div><div class="field"><label>Password</label><input type="password" name="password" required></div><button class="btn btn--primary btn--block">Login</button></form><p class="muted" style="text-align:center; margin-top:.8rem">No account? <a href="<?= asset('register.php') ?>" style="text-decoration:underline">Register</a></p></div></main>
<?php include __DIR__.'/includes/partials/footer.php'; ?><script type="module" src="<?= asset('assets/js/app.js') ?>"></script></body></html>
