<?php require __DIR__.'/includes/bootstrap.php'; $pageTitle='Delivery — Urban Cats'; ?>
<!doctype html><html lang="en"><head><?php include __DIR__.'/includes/partials/head.php'; ?></head><body>
<?php include __DIR__.'/includes/partials/header.php'; ?>
<main id="main"><div class="container section"><div class="breadcrumbs"><a href="<?= asset('index.php') ?>">Home</a> / Delivery</div><h1 class="h2" style="font-family:var(--font-display)">Delivery Information</h1><p class="lead">Collection in Gwarinpa, Abuja or coordinated delivery after verified payment. Fees quoted separately.</p><p class="muted"><?= e(setting($pdo,'delivery_note','Delivery confirmed after payment.')) ?></p></div></main>
<?php include __DIR__.'/includes/partials/footer.php'; ?><script type="module" src="<?= asset('assets/js/app.js') ?>"></script></body></html>
