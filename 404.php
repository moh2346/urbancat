<?php http_response_code(404); require __DIR__.'/includes/bootstrap.php'; $pageTitle='Not Found — Urban Cats'; ?>
<!doctype html><html lang="en"><head><?php include __DIR__.'/includes/partials/head.php'; ?></head><body>
<?php include __DIR__.'/includes/partials/header.php'; ?>
<main id="main"><div class="container section" style="text-align:center; max-width:600px"><h1 class="h2" style="font-family:var(--font-display)">Page not found</h1><p class="muted">The page you seek does not exist.</p><a href="<?= asset('index.php') ?>" class="btn btn--primary">Go Home</a></div></main>
<?php include __DIR__.'/includes/partials/footer.php'; ?><script type="module" src="<?= asset('assets/js/app.js') ?>"></script></body></html>
