<?php
$contactEmail = setting($pdo,'contact_email','urbankitty0@gmail.com');
$contactPhone = setting($pdo,'contact_phone','09122037945');
$contactPhoneIntl = '+234 912 203 7945';
$waIntl = setting($pdo,'whatsapp_number','2349122037945');
$addr = setting($pdo,'address','Abuja, Nigeria');
// Normalize address to remove Gwarinpa if present in DB
if(str_contains($addr,'Gwarinpa')) $addr = 'Abuja, Nigeria';
$ig = trim(setting($pdo,'instagram_url',''));
$fb = trim(setting($pdo,'facebook_url',''));
$tk = trim(setting($pdo,'tiktok_url',''));
function isRealSocial($url){
 $url=trim($url);
 if($url==='' || $url==='#') return false;
 $generic=['https://instagram.com','https://instagram.com/','https://facebook.com','https://facebook.com/','https://tiktok.com','https://tiktok.com/','http://instagram.com','http://facebook.com','http://tiktok.com'];
 if(in_array($url,$generic,true)) return false;
 if(!filter_var($url,FILTER_VALIDATE_URL)) return false;
 return true;
}
$hasSocial = isRealSocial($ig) || isRealSocial($fb) || isRealSocial($tk);
?>
<footer class="site-footer">
 <div class="site-footer__inner">
  <!-- CTA -->
  <div class="footer-cta">
   <div class="footer-cta__text">
    <p class="footer-cta__eyebrow">WELCOME THEM HOME</p>
    <h2 class="footer-cta__title">Ready to meet your perfect companion?</h2>
    <p class="footer-cta__copy">Explore our available cats or speak with us directly about finding the right match for your home.</p>
   </div>
   <div class="footer-cta__actions">
    <a href="<?= asset('cats.php') ?>" class="btn btn--primary">VIEW AVAILABLE CATS</a>
    <a href="https://wa.me/2349122037945" target="_blank" rel="noopener noreferrer" class="btn btn--ghost btn--light">CHAT ON WHATSAPP</a>
   </div>
  </div>

  <!-- Main -->
  <div class="site-footer__main">
   <div class="site-footer__brand">
    <a href="<?= asset('index.php') ?>" aria-label="Urban Cats home"><img src="<?= asset('assets/images/brand/logo-horizontal.svg') ?>" alt="Urban Cats" width="170" height="40" loading="lazy"></a>
    <p class="site-footer__tagline">Exceptional companions, thoughtfully cared for.</p>
    <p class="site-footer__desc">Discover well-cared-for Ragdoll, Maine Coon, Persian and British Shorthair cats available in Abuja.</p>
    <?php if($hasSocial): ?>
    <div class="site-footer__social" aria-label="Social links">
     <?php if(isRealSocial($ig)): ?><a href="<?= e($ig) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1.2"/></svg></a><?php endif; ?>
     <?php if(isRealSocial($fb)): ?><a href="<?= e($fb) ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M14 8 H11 V12 H14 V16 H11 V20 H7 V12 H5 V8 H7 V6 C7 4 8 3 11 3 H14 V8 Z"/></svg></a><?php endif; ?>
     <?php if(isRealSocial($tk)): ?><a href="<?= e($tk) ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M14 3 V13 C14 16 12 18 8 18 C5 18 3 16 3 13 C3 10 5 8 8 8 C8 8 9 8 9 8 V12 C9 12 8 12 8 13 C8 14 9 15 11 15 C13 15 14 14 14 11 V3 Z"/></svg></a><?php endif; ?>
    </div>
    <?php endif; ?>
   </div>

   <div>
    <h3 class="site-footer__heading">EXPLORE</h3>
    <ul class="site-footer__links">
     <li><a href="<?= asset('index.php') ?>">Home</a></li>
     <li><a href="<?= asset('cats.php') ?>">Available Cats</a></li>
     <li><a href="<?= asset('breeds.php') ?>">Breeds</a></li>
     <li><a href="<?= asset('about.php') ?>">About Urban Cats</a></li>
     <li><a href="<?= asset('how-it-works.php') ?>">How Buying Works</a></li>
     <li><a href="<?= asset('care-guide.php') ?>">Care Guide</a></li>
    </ul>
   </div>

   <div>
    <h3 class="site-footer__heading">HELP</h3>
    <ul class="site-footer__links">
     <li><a href="<?= asset('contact.php') ?>">Contact</a></li>
     <li><a href="<?= asset('faq.php') ?>">Frequently Asked Questions</a></li>
     <li><a href="<?= asset('delivery.php') ?>">Delivery</a></li>
     <li><a href="<?= asset('enquiry.php') ?>">Enquiry or Reservation</a></li>
     <li><a href="<?= asset('faq.php') ?>">Payment Information</a></li>
    </ul>
   </div>

   <div>
    <h3 class="site-footer__heading">LEGAL</h3>
    <ul class="site-footer__links">
     <li><a href="<?= asset('privacy.php') ?>">Privacy Policy</a></li>
     <li><a href="<?= asset('terms.php') ?>">Terms and Conditions</a></li>
     <li><a href="<?= asset('refund.php') ?>">Refund Policy</a></li>
    </ul>
   </div>

   <div>
    <h3 class="site-footer__heading">CONTACT</h3>
    <ul class="site-footer__links" style="gap:10px">
     <li style="display:flex; gap:8px; align-items:center"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4 6 H20 V18 H4 Z"/><path d="M4 6 L12 12 L20 6"/></svg><a href="mailto:<?= e($contactEmail) ?>"><?= e($contactEmail) ?></a></li>
     <li style="display:flex; gap:8px; align-items:center"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M22 16.9 V20 C22 21 21 22 20 22 C11 22 4 15 4 6 C4 5 5 4 6 4 H9 C10 4 11 5 11 6 L11.5 11 L8 14.5 C9.5 17.5 13 20 16 21 L19 17.5 L20 16.9 Z"/></svg><a href="tel:+2349122037945"><?= e($contactPhoneIntl) ?></a></li>
     <li style="display:flex; gap:8px; align-items:center"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 21 C12 21 5 13 5 9 C5 5 8 3 12 3 C16 3 19 5 19 9 C19 13 12 21 12 21 Z"/><circle cx="12" cy="9" r="2.5"/></svg><span><?= e($addr) ?></span></li>
     <li><a href="https://wa.me/2349122037945" target="_blank" rel="noopener noreferrer" style="display:flex; gap:8px; align-items:center"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M8 12 C8 8 11 5 15 6 L15 6 C17 6 19 8 19 12 C19 15 17 19 12 21 C7 19 3 15 3 12 C3 10 4 8 6 7"/><path d="M9 12 L11 14 L15 10"/></svg>Chat on WhatsApp</a></li>
    </ul>
   </div>
  </div>

  <!-- Bottom -->
  <div class="site-footer__bottom">
   <p>© <?= date('Y') ?> Urban Cats. All rights reserved.</p>
   <div class="site-footer__legal">
    <a href="<?= asset('privacy.php') ?>">Privacy</a>
    <a href="<?= asset('terms.php') ?>">Terms</a>
    <a href="<?= asset('refund.php') ?>">Refund Policy</a>
   </div>
  </div>
 </div>
</footer>
<button class="back-to-top" id="backToTop" aria-label="Back to top">↑</button>
