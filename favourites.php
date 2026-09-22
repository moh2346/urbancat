<?php
require __DIR__.'/includes/bootstrap.php';
require __DIR__.'/includes/functions_catalog.php';
if(customer_logged_in()){
 $cust=current_customer($pdo);
 $list=$pdo->prepare("SELECT c.*, b.name as breed_name FROM favourites f JOIN cats c ON c.id=f.cat_id JOIN breeds b ON b.id=c.breed_id WHERE f.customer_id=?"); $list->execute([$cust['id']]); $cats=$list->fetchAll();
} else $cats=[];
$pageTitle='Favourites — Urban Cats';
?>
<!doctype html><html lang="en"><head><?php include __DIR__.'/includes/partials/head.php'; ?></head><body>
<?php include __DIR__.'/includes/partials/header.php'; ?>
<main id="main"><div class="container section"><div class="breadcrumbs"><a href="<?= asset('index.php') ?>">Home</a> / Favourites</div><h1 class="h2" style="font-family:var(--font-display)">Favourites</h1>
<?php if(customer_logged_in() && $cats): ?><div class="grid-3"><?php foreach($cats as $c): $img=primary_image($pdo,(int)$c['id']);?><article class="card"><a href="<?= asset('cat.php?slug='.urlencode($c['slug'])) ?>" class="card__media"><img src="<?= e($img) ?>" width="400" height="320"></a><div class="card__body"><h3><?= e($c['name']) ?></h3><p class="muted"><?= e($c['breed_name']) ?> • <?= e(format_price((float)$c['price'])) ?></p><a href="<?= asset('cat.php?slug='.urlencode($c['slug'])) ?>" class="btn btn--primary btn--sm">View Details</a></div></article><?php endforeach;?></div>
<?php elseif(!customer_logged_in()): ?><p class="muted">Favourites sync to your account when logged in. Using localStorage fallback — manage via heart buttons. <a href="<?= asset('login.php') ?>" style="text-decoration:underline">Login</a></p><div id="localFavs" class="grid-3"></div><script>const favs=JSON.parse(localStorage.getItem('uc_favs')||'[]'); document.getElementById('localFavs').innerHTML=favs.length? favs.map(s=>`<div class="card" style="padding:1rem">Local: ${s} — <a href="cat.php?slug=${s}">View</a></div>`).join('') : '<p class="muted">No local favourites</p>';</script>
<?php else: ?><p class="muted">No favourites yet. <a href="<?= asset('cats.php') ?>" style="text-decoration:underline">Browse cats</a></p><?php endif; ?>
</div></main>
<?php include __DIR__.'/includes/partials/footer.php'; ?><script type="module" src="<?= asset('assets/js/app.js') ?>"></script></body></html>
