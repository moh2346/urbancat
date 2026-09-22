<?php
require __DIR__.'/includes/bootstrap.php';
if(customer_logged_in()){header('Location: '.base_url('account.php')); exit;}
$err=''; if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!verify_csrf($_POST['csrf_token']??'')) $err='Invalid session.';
 elseif(is_honeypot_filled()) $err='Spam.';
 else {
  $name=sanitize_text($_POST['full_name']??'',120); $email=trim($_POST['email']??''); $pass=$_POST['password']??''; $phone=sanitize_text($_POST['phone']??'',40);
  if($name===''||!is_valid_email($email)||mb_strlen($pass)<8) $err='Name, valid email, password 8+ required.';
  else {
   try{$hash=password_hash($pass,PASSWORD_DEFAULT); $pdo->prepare("INSERT INTO customers (full_name,email,password_hash,phone) VALUES (?,?,?,?)")->execute([$name,$email,$hash,$phone]); $id=(int)$pdo->lastInsertId(); session_regenerate_id(true); $_SESSION['customer_id']=$id; header('Location: '.base_url('account.php')); exit;}catch(PDOException $e){ if(str_contains($e->getMessage(),'Duplicate')) $err='Email already registered.'; else $err='Registration failed.';}
  }
 }
}
$pageTitle='Register — Urban Cats';
?>
<!doctype html><html lang="en"><head><?php include __DIR__.'/includes/partials/head.php'; ?></head><body>
<?php include __DIR__.'/includes/partials/header.php'; ?>
<main id="main"><div class="container section" style="max-width:480px"><h1 class="h2" style="font-family:var(--font-display)">Create account</h1><?php if($err): ?><div class="alert alert--error"><?= e($err) ?></div><?php endif; ?><form method="post" class="form" novalidate><?= csrf_field().honeypot_field() ?><div class="field"><label>Full name *</label><input name="full_name" value="<?= e($_POST['full_name']??'') ?>" required></div><div class="field"><label>Email *</label><input type="email" name="email" value="<?= e($_POST['email']??'') ?>" required></div><div class="field"><label>Phone</label><input name="phone" value="<?= e($_POST['phone']??'') ?>"></div><div class="field"><label>Password * (8+)</label><input type="password" name="password" required></div><button class="btn btn--primary btn--block">Register</button></form><p class="muted" style="text-align:center; margin-top:.8rem">Have account? <a href="<?= asset('login.php') ?>" style="text-decoration:underline">Login</a></p></div></main>
<?php include __DIR__.'/includes/partials/footer.php'; ?><script type="module" src="<?= asset('assets/js/app.js') ?>"></script></body></html>
