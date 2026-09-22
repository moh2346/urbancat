<?php
$current=basename($_SERVER['SCRIPT_NAME']);
function nav_active($f){global $current;return $current===$f?' is-active':'';}
$cartCnt=function_exists('cart_count')?cart_count($pdo):0;
$favCnt=function_exists('fav_count')?fav_count($pdo):0;
?>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header" id="siteHeader">
 <div class="header__inner">
  <a href="<?= asset('index.php') ?>" class="brand" aria-label="Urban Cats home"><img src="<?= asset('assets/images/brand/logo-horizontal.svg') ?>" alt="Urban Cats" width="170" height="40" class="brand__logo"></a>
  <nav class="nav" aria-label="Primary">
   <a href="<?= asset('index.php') ?>" class="nav__link<?= nav_active('index.php') ?>">Home</a>
   <a href="<?= asset('cats.php') ?>" class="nav__link<?= nav_active('cats.php') ?>">Available Cats</a>
   <a href="<?= asset('breeds.php') ?>" class="nav__link<?= nav_active('breeds.php') ?>">Breeds</a>
   <a href="<?= asset('care-guide.php') ?>" class="nav__link<?= nav_active('care-guide.php') ?>">Care Guide</a>
   <a href="<?= asset('about.php') ?>" class="nav__link<?= nav_active('about.php') ?>">About</a>
   <a href="<?= asset('delivery.php') ?>" class="nav__link<?= nav_active('delivery.php') ?>">Delivery</a>
   <a href="<?= asset('contact.php') ?>" class="nav__link<?= nav_active('contact.php') ?>">Contact</a>
  </nav>
  <div class="header__utils">
   <button class="icon-btn hide-mobile" aria-label="Search" onclick="location.href='<?= asset('cats.php') ?>'"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M16 16 L20 20"/></svg></button>
   <a href="<?= asset(customer_logged_in()?'account.php':'login.php') ?>" class="icon-btn hide-mobile" aria-label="Account"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20 C4 14 8 12 12 12 S20 14 20 20"/></svg></a>
   <a href="<?= asset('favourites.php') ?>" class="icon-btn hide-mobile" aria-label="Favourites"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M12 20 L4 12 C2 10 2 6 6 4 C9 2 13 4 12 8 C11 4 15 2 18 4 C22 6 22 10 20 12 Z"/></svg><span style="font-size:.7rem; margin-left:2px"><?= (int)$favCnt ?></span></a>
   <a href="<?= asset('cart.php') ?>" class="icon-btn" aria-label="Cart"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8"><path d="M6 6 H4"/><path d="M6 6 L8 14 H18 L20 6 H6"/><circle cx="9" cy="19" r="2"/><circle cx="17" cy="19" r="2"/></svg><span style="font-size:.7rem"><?=(int)$cartCnt?></span></a>
   <button class="burger" id="burger" aria-expanded="false" aria-controls="mobileNav" aria-label="Open menu"><span></span><span></span><span></span></button>
  </div>
 </div>
 <nav class="mobile-nav" id="mobileNav" hidden>
  <button style="align-self:flex-end; border:none; background:none; font-size:1.4rem" id="closeNav" aria-label="Close menu">✕</button>
  <a href="<?= asset('index.php') ?>">Home</a>
  <a href="<?= asset('cats.php') ?>">Available Cats</a>
  <a href="<?= asset('breeds.php') ?>">Breeds</a>
  <a href="<?= asset('care-guide.php') ?>">Care Guide</a>
  <a href="<?= asset('about.php') ?>">About</a>
  <a href="<?= asset('how-it-works.php') ?>">How It Works</a>
  <a href="<?= asset('delivery.php') ?>">Delivery</a>
  <a href="<?= asset('faq.php') ?>">FAQ</a>
  <a href="<?= asset('contact.php') ?>">Contact</a>
  <a href="<?= asset('account.php') ?>">My Account</a>
  <a href="<?= asset('cart.php') ?>" class="btn btn--primary" style="margin-top:.6rem">View Cart (<?= (int)$cartCnt ?>)</a>
 </nav>
</header>
