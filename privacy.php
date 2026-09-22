<?php require __DIR__.'/includes/bootstrap.php'; $pageTitle='Privacy Policy — Urban Cats'; ?>
<!doctype html><html lang="en"><head><?php include __DIR__.'/includes/partials/head.php'; ?></head><body>
<?php include __DIR__.'/includes/partials/header.php'; ?>
<main id="main"><div class="container section" style="max-width:800px"><div class="breadcrumbs"><a href="<?= asset('index.php') ?>">Home</a> / Privacy</div><h1 class="h2" style="font-family:var(--font-display)">Privacy Policy</h1><p class="muted">Last updated 2026-09-20. Contact <?= e(setting($pdo,'contact_email','urbankitty0@gmail.com')) ?> for requests.</p><p class="muted">We collect enquiry, order, newsletter data to process placements. No sale of data. Retained as needed, then archived.</p></div></main>
<?php include __DIR__.'/includes/partials/footer.php'; ?><script type="module" src="<?= asset('assets/js/app.js') ?>"></script></body></html>
