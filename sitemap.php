<?php require __DIR__.'/includes/bootstrap.php'; require __DIR__.'/includes/functions_catalog.php'; header('Content-Type: application/xml; charset=utf-8'); $base=base_url(); echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url><loc><?= e($base.'/index.php') ?></loc><priority>1.0</priority></url>
<url><loc><?= e($base.'/cats.php') ?></loc><priority>0.9</priority></url>
<url><loc><?= e($base.'/breeds.php') ?></loc><priority>0.8</priority></url>
<?php foreach(fetch_breeds($pdo) as $b): ?><url><loc><?= e($base.'/breed.php?slug='.urlencode($b['slug'])) ?></loc></url><?php endforeach; ?>
<?php $cats=$pdo->query("SELECT slug FROM cats")->fetchAll(); foreach($cats as $c): ?><url><loc><?= e($base.'/cat.php?slug='.urlencode($c['slug'])) ?></loc></url><?php endforeach; ?>
<url><loc><?= e($base.'/about.php') ?></loc></url>
<url><loc><?= e($base.'/care-guide.php') ?></loc></url>
<url><loc><?= e($base.'/how-it-works.php') ?></loc></url>
<url><loc><?= e($base.'/delivery.php') ?></loc></url>
<url><loc><?= e($base.'/faq.php') ?></loc></url>
<url><loc><?= e($base.'/contact.php') ?></loc></url>
</urlset>
