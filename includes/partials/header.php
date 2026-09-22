<?php
$current=basename($_SERVER['SCRIPT_NAME']);
function nav_active_attr($f){global $current;return $current===$f?' aria-current="page"':'';}
$cartCnt=function_exists('cart_count')?cart_count($pdo):0;
$favCnt=function_exists('fav_count')?fav_count($pdo):0;
?>
<a class="skip-link" href="#main">Skip to content</a>
<header class="uc-header" id="siteHeader">
    <div class="uc-header__container">
        <a class="uc-header__brand" href="<?= asset('index.php') ?>" aria-label="Urban Cats home">
            <img
                src="<?= asset('assets/images/branding/urban-cats-logo.svg') ?>"
                alt="Urban Cats"
                width="190"
                height="52"
                loading="eager"
            >
        </a>

        <nav class="uc-header__nav" aria-label="Main navigation">
            <a href="<?= asset('index.php') ?>"<?= nav_active_attr('index.php') ?>>Home</a>
            <a href="<?= asset('cats.php') ?>"<?= nav_active_attr('cats.php') ?>>Available Cats</a>
            <a href="<?= asset('breeds.php') ?>"<?= nav_active_attr('breeds.php') ?>>Breeds</a>
            <a href="<?= asset('care-guide.php') ?>"<?= nav_active_attr('care-guide.php') ?>>Care Guide</a>
            <a href="<?= asset('about.php') ?>"<?= nav_active_attr('about.php') ?>>About</a>
            <a href="<?= asset('delivery.php') ?>"<?= nav_active_attr('delivery.php') ?>>Delivery</a>
            <a href="<?= asset('contact.php') ?>"<?= nav_active_attr('contact.php') ?>>Contact</a>
        </nav>

        <div class="uc-header__actions">
            <button class="uc-header__action" type="button" aria-label="Search" onclick="location.href='<?= asset('cats.php') ?>'">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="11" cy="11" r="6.2"/><path d="M15.3 15.3 L19.1 19.1"/></svg>
            </button>
            <a href="<?= asset(customer_logged_in()?'account.php':'login.php') ?>" class="uc-header__action" aria-label="Account">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="8.2" r="3.6"/><path d="M5.2 18.8 C5.2 15.2 8.0 13.2 12 13.2 S18.8 15.2 18.8 18.8"/></svg>
            </a>
            <a href="<?= asset('favourites.php') ?>" class="uc-header__action" aria-label="Favourites">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M12 18.5 L5.1 11.1 C3.7 9.4 4.2 6.3 6.8 5.0 C9.0 3.9 11.6 5.0 12 7.2 C12.4 5.0 15.0 3.9 17.2 5.0 C19.8 6.3 20.3 9.4 18.9 11.1 L12 18.5 Z"/></svg>
                <?php if($favCnt>0): ?><span class="uc-header__badge"><?= (int)$favCnt ?></span><?php endif; ?>
            </a>
            <a href="<?= asset('cart.php') ?>" class="uc-header__action" aria-label="Cart">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M5.8 6.2 H3.4"/><path d="M5.8 6.2 L7.6 15.1 H17.2 L19.0 6.2 H5.8 Z"/><circle cx="9.2" cy="18.6" r="1.7"/><circle cx="16.6" cy="18.6" r="1.7"/></svg>
                <?php if($cartCnt>0): ?><span class="uc-header__badge"><?= (int)$cartCnt ?></span><?php endif; ?>
            </a>
        </div>

        <button
            class="uc-header__menu-button"
            id="ucMenuButton"
            type="button"
            aria-label="Open navigation menu"
            aria-expanded="false"
            aria-controls="mobile-navigation"
        >
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 7 H19"/><path d="M5 12 H19"/><path d="M5 17 H19"/></svg>
        </button>
    </div>

    <nav
        class="uc-mobile-nav"
        id="mobile-navigation"
        aria-label="Mobile navigation"
        hidden
    >
        <a href="<?= asset('index.php') ?>"<?= nav_active_attr('index.php') ?>>Home</a>
        <a href="<?= asset('cats.php') ?>"<?= nav_active_attr('cats.php') ?>>Available Cats</a>
        <a href="<?= asset('breeds.php') ?>"<?= nav_active_attr('breeds.php') ?>>Breeds</a>
        <a href="<?= asset('care-guide.php') ?>"<?= nav_active_attr('care-guide.php') ?>>Care Guide</a>
        <a href="<?= asset('about.php') ?>"<?= nav_active_attr('about.php') ?>>About</a>
        <a href="<?= asset('delivery.php') ?>"<?= nav_active_attr('delivery.php') ?>>Delivery</a>
        <a href="<?= asset('contact.php') ?>"<?= nav_active_attr('contact.php') ?>>Contact</a>
        <a href="<?= asset('faq.php') ?>"<?= nav_active_attr('faq.php') ?>>FAQ</a>
        <a href="<?= asset('enquiry.php') ?>"<?= nav_active_attr('enquiry.php') ?>>Enquiry or Reservation</a>
        <a href="<?= asset(customer_logged_in()?'account.php':'login.php') ?>"><?= customer_logged_in()?'My Account':'Log in' ?></a>
        <a href="<?= asset('cart.php') ?>" class="uc-mobile-nav__cta">View Cart<?php if($cartCnt>0): ?> (<?= (int)$cartCnt ?>)<?php endif; ?></a>
    </nav>
</header>
