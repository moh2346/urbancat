<?php
require __DIR__.'/includes/bootstrap.php';
require __DIR__.'/includes/functions_catalog.php';
$cid=get_cart_id($pdo);
if(isset($_GET['add'])){
 $slug=trim($_GET['add']); $cat=get_cat_by_slug($pdo,$slug);
 if($cat && $cat['is_active'] && $cat['availability']=='available'){
  try{$pdo->prepare("INSERT INTO cart_items (cart_id,cat_id) VALUES (?,?)")->execute([$cid,$cat['id']]); flash('success','Added to cart');}catch(PDOException $e){flash('error','Already in cart');}
 } else flash('error','Not available');
 header('Location: '.base_url('cart.php')); exit;
}
if(isset($_GET['remove'])){
 $id=(int)$_GET['remove']; $pdo->prepare("DELETE FROM cart_items WHERE id=? AND cart_id=?")->execute([$id,$cid]); header('Location: '.base_url('cart.php')); exit;
}
if(isset($_POST['clear'])){$pdo->prepare("DELETE FROM cart_items WHERE cart_id=?")->execute([$cid]); header('Location: '.base_url('cart.php')); exit;}
$items=$pdo->prepare("SELECT ci.id as ci_id, c.*, b.name as breed_name FROM cart_items ci JOIN cats c ON c.id=ci.cat_id JOIN breeds b ON b.id=c.breed_id WHERE ci.cart_id=?"); $items->execute([$cid]); $list=$items->fetchAll();
$total=0; foreach($list as $it){$total+=(float)($it['purchase_mode']=='deposit'? ($it['deposit_amount']?? $it['price']*0.3) : $it['price']);}
$pageTitle='Cart — Urban Cats';
?>
<!doctype html><html lang="en"><head><?php include __DIR__.'/includes/partials/head.php'; ?></head><body>
<?php include __DIR__.'/includes/partials/header.php'; ?>
<main id="main"><div class="container section"><div class="breadcrumbs"><a href="<?= asset('index.php') ?>">Home</a> / Cart</div><h1 class="h2" style="font-family:var(--font-display)">Cart</h1>
<?php include __DIR__.'/includes/partials/alerts.php'; ?>
<?php if(!$list): ?><p class="muted">Your cart is empty. <a href="<?= asset('cats.php') ?>" style="text-decoration:underline">Browse cats</a></p>
<?php else: ?>
<table class="table"><tr><th>Cat</th><th>Price</th><th></th></tr><?php foreach($list as $it): $pay=$it['purchase_mode']=='deposit'? (float)($it['deposit_amount']?? $it['price']*0.3) : (float)$it['price']; ?><tr><td><a href="<?= asset('cat.php?slug='.urlencode($it['slug'])) ?>" style="text-decoration:underline"><?= e($it['name']) ?></a> — <?= e($it['breed_name']) ?></td><td><?= e(format_price($pay)) ?> <?php if($it['purchase_mode']=='deposit') echo '<span class="muted" style="font-size:.78rem">deposit</span>'; ?></td><td><a href="<?= asset('cart.php?remove='.$it['ci_id']) ?>" style="color:#a00">Remove</a></td></tr><?php endforeach; ?></table>
<p style="text-align:right; font-weight:700; margin-top:.8rem">Total: <?= e(format_price($total)) ?></p>
<div style="display:flex; gap:.6rem; justify-content:flex-end; margin-top:1rem"><form method="post"><?= csrf_field() ?><button name="clear" class="btn btn--ghost">Clear</button></form><a href="<?= asset('checkout.php') ?>" class="btn btn--primary">Proceed to Checkout</a></div>
<?php endif; ?>
</div></main>
<?php include __DIR__.'/includes/partials/footer.php'; ?><script type="module" src="<?= asset('assets/js/app.js') ?>"></script></body></html>
