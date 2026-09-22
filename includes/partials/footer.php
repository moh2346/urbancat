<footer class="uc-footer">
    <div class="uc-footer__container">
        <div class="uc-footer__grid">
            <div class="uc-footer__brand">
                <a href="<?= asset('index.php') ?>" aria-label="Urban Cats home"><img src="<?= asset('assets/images/brand/logo-horizontal.svg') ?>" alt="Urban Cats" width="170" height="40" loading="lazy"></a>

                <p class="uc-footer__tagline">
                    Exceptional companions, thoughtfully cared for.
                </p>

                <p class="uc-footer__description">
                    Discover well-cared-for Ragdoll, Maine Coon, Persian and British Shorthair cats available in Abuja.
                </p>

                <a
                    class="uc-footer__whatsapp"
                    href="https://wa.me/2349122037945"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Chat on WhatsApp
                </a>
            </div>

            <nav class="uc-footer__column" aria-label="Explore">
                <h2 class="uc-footer__heading">Explore</h2>
                <ul class="uc-footer__links">
                    <li><a href="<?= asset('index.php') ?>">Home</a></li>
                    <li><a href="<?= asset('cats.php') ?>">Available Cats</a></li>
                    <li><a href="<?= asset('breeds.php') ?>">Breeds</a></li>
                    <li><a href="<?= asset('about.php') ?>">About Urban Cats</a></li>
                    <li><a href="<?= asset('care-guide.php') ?>">Care Guide</a></li>
                </ul>
            </nav>

            <nav class="uc-footer__column" aria-label="Help">
                <h2 class="uc-footer__heading">Help</h2>
                <ul class="uc-footer__links">
                    <li><a href="<?= asset('faq.php') ?>">Frequently Asked Questions</a></li>
                    <li><a href="<?= asset('contact.php') ?>">Contact</a></li>
                    <li><a href="<?= asset('delivery.php') ?>">Delivery</a></li>
                    <li><a href="<?= asset('enquiry.php') ?>">Enquiry or Reservation</a></li>
                    <li><a href="<?= asset('faq.php') ?>">Payment Information</a></li>
                </ul>
            </nav>

            <nav class="uc-footer__column" aria-label="Legal">
                <h2 class="uc-footer__heading">Legal</h2>
                <ul class="uc-footer__links">
                    <li><a href="<?= asset('privacy.php') ?>">Privacy Policy</a></li>
                    <li><a href="<?= asset('terms.php') ?>">Terms and Conditions</a></li>
                    <li><a href="<?= asset('refund.php') ?>">Refund Policy</a></li>
                </ul>
            </nav>

            <div class="uc-footer__column uc-footer__contact">
                <h2 class="uc-footer__heading">Contact</h2>

                <a href="mailto:urbankitty0@gmail.com">
                    urbankitty0@gmail.com
                </a>

                <a href="tel:+2349122037945">
                    +234 912 203 7945
                </a>

                <address>Abuja, Nigeria</address>

                <a
                    href="https://wa.me/2349122037945"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Chat on WhatsApp
                </a>
            </div>
        </div>

        <div class="uc-footer__bottom">
            <p>
                © <?= date('Y') ?> Urban Cats. All rights reserved.
            </p>

            <div class="uc-footer__bottom-links">
                <a href="<?= asset('privacy.php') ?>">Privacy</a>
                <a href="<?= asset('terms.php') ?>">Terms</a>
                <a href="<?= asset('refund.php') ?>">Refund Policy</a>
            </div>
        </div>
    </div>
</footer>
<button class="back-to-top" id="backToTop" aria-label="Back to top">↑</button>
