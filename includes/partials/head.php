<?php
$pageTitle=$pageTitle??'Urban Cats — Exceptional companions, thoughtfully placed';
$pageDesc=$pageDesc??'Premium Ragdoll, Maine Coon, British Shorthair and Persian cats from Gwarinpa, Abuja. Vet-checked, responsibly placed.';
$ogImage=$ogImage??asset('assets/images/site/hero-cat.svg');
$canonical=$pageCanonical??canonical_url();
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDesc) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($pageDesc) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta name="twitter:card" content="summary_large_image">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('assets/css/style.css?v=20250921testimonials') ?>">
<link rel="icon" href="<?= asset('assets/images/brand/favicon.svg') ?>" type="image/svg+xml">
<meta name="theme-color" content="#25231f">
<script>document.documentElement.classList.remove('no-js');document.documentElement.classList.add('js')</script>
