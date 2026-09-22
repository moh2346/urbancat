<?php
require __DIR__.'/includes/bootstrap.php';
if(!customer_logged_in()){header('Location: '.base_url('login.php')); exit;}
$cust=current_customer($pdo);
if(isset($_GET['logout'])){unset($_SESSION['customer_id']); header('Location: '.base_url('index.php')); exit;}
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update'])){
 if(!verify_csrf($_POST['csrf_token']??'')) flash('error','Invalid session');
 else {
  $phone=sanitize_text($_POST['phone']??'',40); $addr=sanitize_text($_POST['address']??'',255); $city=sanitize_text($_POST['city']??'',100); $state=sanitize_text($_POST['state']??'',100);
  $pdo->prepare("UPDATE customers SET phone=?, address=?, city=?, state=? WHERE id=?")->execute([$phone,$addr,$city,$state,$cust['id']]); flash('success','Updated'); header('Location: '.base_url('account.php')); exit;
 }
}
$cust=current_customer($pdo);
$orders=$pdo->prepare("SELECT * FROM orders WHERE customer_id=? ORDER BY created_at DESC"); $orders->execute([$cust['id']]); $ords=$orders->fetchAll();
$pageTitle='My Account — Urban Cats';
?>
<!doctype html><html lang="en"><head><?php include __DIR__.'/includes/partials/head.php'; ?></head><body>
<?php include __DIR__.'/includes/partials/header.php'; ?>
<main id="main"><div class="container section"><h1 class="h2" style="font-family:var(--font-display)">My Account</h1><p class="muted">Welcome, <?= e($cust['full_name']) ?> — <a href="<?= asset('account.php?logout=1') ?>" style="text-decoration:underline">Logout</a></p>
<?php include __DIR__.'/includes/partials/alerts.php'; ?>
<div class="grid-2">
<div class="card" style="padding:1.2rem"><h3>Contact info</h3><form method="post" class="form"><?= csrf_field() ?><div class="field"><label>Phone</label><input name="phone" value="<?= e($cust['phone']??'') ?>"></div><div class="field"><label>Address</label><input name="address" value="<?= e($cust['address']??'') ?>"></div><div class="field"><label>City</label><input name="city" value="<?= e($cust['city']??'') ?>"></div><div class="field"><label>State</label><input name="state" value="<?= e($cust['state']??'') ?>"></div><button name="update" class="btn btn--primary">Save</button></form></div>
<div class="card" style="padding:1.2rem"><h3>Order history</h3><?php if(!$ords): ?><p class="muted">No orders yet.</p><?php else: ?><table class="table"><tr><th>Ref</th><th>Amount</th><th>Status</th></tr><?php foreach($ords as $o): ?><tr><td><?= e($o['order_reference']) ?></td><td><?= e(format_price((float)$o['amount'])) ?></td><td><?= e($o['status']) ?></td></tr><?php endforeach; ?></table><?php endif; ?><p style="margin-top:.6rem"><a href="<?= asset('favourites.php') ?>" style="text-decoration:underline">Favourites</a> • <a href="<?= asset('cart.php') ?>" style="text-decoration:underline">Cart</a></p></div>
</div></div></main>
<?php include __DIR__.'/includes/partials/footer.php'; ?><script type="module" src="<?= asset('assets/js/app.js') ?>"></script></body></html>
