<?php
require __DIR__.'/includes/bootstrap.php';
require __DIR__.'/includes/functions_catalog.php';
$pageTitle='Urban Cats — Beautiful cats, loving homes.';
$breeds=[]; $featured=[]; $faqs=[]; $testimonials=[]; $dbError=null;
try{
 $breeds=fetch_breeds($pdo);
}catch(Throwable $e){error_log('DB breeds failed: '.$e->getMessage()); $dbError='Our cat listings are temporarily unavailable. Please try again shortly.';}
try{
 $featured=$pdo->query("SELECT c.*, b.name as breed_name FROM cats c JOIN breeds b ON b.id=c.breed_id WHERE c.is_featured=1 AND c.is_active=1 ORDER BY c.created_at DESC LIMIT 6")->fetchAll();
 if(!$featured) $featured=$pdo->query("SELECT c.*, b.name as breed_name FROM cats c JOIN breeds b ON b.id=c.breed_id WHERE c.availability='available' ORDER BY c.created_at DESC LIMIT 6")->fetchAll();
}catch(Throwable $e){error_log('DB featured failed: '.$e->getMessage()); $featured=[]; $dbError=$dbError??'Our cat listings are temporarily unavailable.';}
try{$faqs=$pdo->query("SELECT * FROM faqs WHERE is_published=1 ORDER BY sort_order LIMIT 5")->fetchAll();}catch(Throwable $e){error_log('DB faqs failed: '.$e->getMessage()); $faqs=[];}
try{$testimonials=$pdo->query("SELECT * FROM testimonials WHERE is_published=1 ORDER BY id DESC LIMIT 3")->fetchAll();}catch(Throwable $e){error_log('DB testimonials failed: '.$e->getMessage()); $testimonials=[];}
$heroImg='assets/images/site/hero-cat.svg';
try{if($featured){ $t=primary_image($pdo,(int)$featured[0]['id']); if(is_file(__DIR__.'/'.$t)) $heroImg=$t; }}catch(Throwable $e){$heroImg='assets/images/site/hero-cat.svg';}
?>
<!doctype html><html lang="en" class="no-js"><head><?php include __DIR__.'/includes/partials/head.php'; ?></head><body>
<?php include __DIR__.'/includes/partials/header.php'; ?>
<main id="main">
<section class="hero">
  <div class="hero__content">
   <p class="hero__eyebrow">WELCOME TO URBAN CATS</p>
   <h1 class="hero__title"><span class="hero__title-text">Beautiful cats for loving homes.</span>
    <span class="hero__heart" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20 L4 10 C4 6 8 4 12 8 C16 4 20 6 20 10 L12 20 Z"/></svg></span>
   </h1>
   <p class="hero__description">Discover healthy, well-cared-for cats of different breeds, available from Urban Cats in Abuja.</p>
   <div class="hero__actions">
    <a href="<?= asset('cats.php') ?>" class="btn btn--primary">VIEW AVAILABLE CATS <svg class="icon" viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M5 12 H19"/><path d="M12 5 L19 12 L12 19"/></svg></a>
    <a href="<?= asset('breeds.php') ?>" class="btn btn--ghost">EXPLORE OUR BREEDS <svg class="icon" viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M5 12 H19"/><path d="M12 5 L19 12 L12 19"/></svg></a>
   </div>
  </div>
  <div class="hero__media"><img src="<?= asset('assets/images/hero/maine-coon-hero.webp') ?>" alt="Large Maine Coon cat resting comfortably in a warm home" width="1200" height="900" fetchpriority="high">
   <div class="hero-badge" aria-hidden="true">
    <div class="hero-badge__inner">
     <svg viewBox="0 0 100 100" width="88" height="88"><defs><path id="circle2" d="M50,50 m-37,0 a37,37 0 1,1 74,0 a37,37 0 1,1 -74,0"/></defs><text font-size="7.5" font-family="Manrope, sans-serif" font-weight="700" letter-spacing="1.2" fill="#25231f"><textPath href="#circle2">RAISED WITH CARE • URBAN CATS •</textPath></text></svg>
     <span style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%)"><svg class="hero-badge__paw" viewBox="0 0 24 24"><path d="M12 14 C10 14 8 12 8 10 C8 8 10 6 12 6 C14 6 16 8 16 10 C16 12 14 14 12 14 Z M7 12 C6 11 5 10 6 8 C7 6 9 7 9 9 C9 11 8 12 7 12 Z M17 12 C18 11 19 10 18 8 C17 6 15 7 15 9 C15 11 16 12 17 12 Z M9 16 C8 15 7 14 7 12 C8 10 10 11 11 13 C11 15 10 16 9 16 Z M15 16 C16 15 17 14 17 12 C16 10 14 11 13 13 C13 15 14 16 15 16 Z"/></svg></span>
    </div>
   </div>
  </div>
</section>

<section class="trust-features">
 <div class="container">
  <div class="trust-features__panel">
   <div class="feature"><div class="feature__icon">✓</div><h3>Health Records</h3><p>Vet-check and vaccination information</p></div>
   <div class="feature"><div class="feature__icon">◈</div><h3>Secure Online Payment</h3><p>Protected Paystack checkout in NGN</p></div>
   <div class="feature"><div class="feature__icon">♥</div><h3>Breed Profiles</h3><p>Detailed information about each cat and breed</p></div>
   <div class="feature"><div class="feature__icon">☎</div><h3>Delivery Support</h3><p>Collection and delivery from Abuja</p></div>
  </div>
 </div>
</section>

 <section class="breeds-section">
  <div class="breeds-section__header">
   <p class="breeds-section__eyebrow">BROWSE OUR BREEDS</p>
   <h2 class="breeds-section__title">Find your perfect breed</h2>
   <p class="breeds-section__description">Meet four remarkable breeds, each with a distinctive personality, appearance and way of becoming part of your family.</p>
  </div>
  <div class="breeds-grid">
   <?php
   $breedDescriptions=[
    'british-shorthair'=>'Calm, affectionate and unmistakably charming, with a plush coat and gentle temperament.',
    'maine-coon'=>'Confident and sociable gentle giants, loved for their intelligence and impressive coats.',
    'persian'=>'Elegant, peaceful companions known for their luxurious coats and affectionate personalities.',
    'ragdoll'=>'Gentle, blue-eyed companions with relaxed temperaments and beautifully soft coats.'
   ];
   $breedOrder=['british-shorthair','maine-coon','persian','ragdoll'];
   $breedsBySlug=[]; foreach($breeds as $bb) $breedsBySlug[$bb['slug']]=$bb;
   $orderedBreeds=[]; foreach($breedOrder as $s) if(isset($breedsBySlug[$s])) $orderedBreeds[]=$breedsBySlug[$s];
   // fallback: if DB missing, use ordered
   if(count($orderedBreeds)!=4) $orderedBreeds=$breeds;
   if($dbError && !$breeds): ?><p class="muted" style="text-align:center"><?= e($dbError) ?></p><?php endif; ?>
   <?php foreach($orderedBreeds as $b):
     $slug=$b['slug']; $desc=$breedDescriptions[$slug] ?? $b['short_description'];
     $altMap=[
      'british-shorthair'=>'Plush British Shorthair cat with round face, blue-grey coat',
      'maine-coon'=>'Majestic Maine Coon cat with long fur and ear tufts',
      'persian'=>'Elegant long-haired Persian cat with luxurious coat',
      'ragdoll'=>'Beautiful blue-eyed Ragdoll cat with colour-point coat'
     ];
     $alt=$altMap[$slug] ?? $b['name'];
   ?>
   <article class="breed-card">
    <a class="breed-card__image-link" href="<?= asset('breed.php?slug='.urlencode($slug)) ?>">
     <img class="breed-card__image" src="<?= asset($b['image']) ?>" alt="<?= e($alt) ?>" width="640" height="800" loading="lazy">
    </a>
    <div class="breed-card__content">
     <h3 class="breed-card__title"><?= e($b['name']) ?></h3>
     <p class="breed-card__description"><?= e($desc) ?></p>
     <a class="breed-card__link" href="<?= asset('breed.php?slug='.urlencode($slug)) ?>">Explore breed <span aria-hidden="true">→</span></a>
    </div>
   </article>
   <?php endforeach; ?>
  </div>
 </section>

 <section class="available-cats-section">
  <div class="available-cats__header">
   <p class="available-cats__eyebrow">AVAILABLE NOW</p>
   <h2 class="available-cats__title">Meet our available cats</h2>
   <p class="available-cats__description">Meet our healthy, well-cared-for companions currently looking for loving homes.</p>
  </div>
  <?php
  // Ensure exactly 5 specified cats in order Luna, Milo, Bella, Oliver, Nala
  $desiredOrder=['Luna','Milo','Bella','Oliver','Nala'];
  $orderedFeatured=[];
  if($featured){
   $byName=[]; foreach($featured as $c) $byName[$c['name']]=$c;
   foreach($desiredOrder as $n) if(isset($byName[$n])) $orderedFeatured[]=$byName[$n];
   // include any remaining featured not in desired (should not happen) to fill to 5
   foreach($featured as $c) if(!in_array($c['name'],$desiredOrder)) $orderedFeatured[]=$c;
   $featured=array_slice($orderedFeatured,0,5);
  }
  if($dbError && !$featured): ?><p class="muted" style="text-align:center"><?= e($dbError) ?></p><?php endif; ?>
  <?php if(!$featured): ?><div style="text-align:center; padding:2rem; border:1px dashed var(--color-border); border-radius:12px; background:#fffdf9"><p class="muted">No cats currently available. Please check back soon or <a href="<?= asset('contact.php') ?>" style="text-decoration:underline">contact us</a>.</p></div>
  <?php else: ?>
  <div class="available-cats__grid">
   <?php foreach($featured as $cat):
    try{$img=primary_image($pdo,(int)$cat['id']); if(!$img || !is_file(__DIR__.'/'.$img)) $img='assets/images/cats/placeholder.svg';}catch(Throwable $e){$img='assets/images/cats/placeholder.svg';}
    $age=age_from_dob($cat['date_of_birth']);
   ?>
   <article class="available-cat-card">
    <div class="available-cat-card__media">
     <img src="<?= asset($img) ?>" alt="<?= e($cat['name']) ?>, a <?= e($cat['sex']) ?> <?= e($cat['breed_name']) ?> cat" width="600" height="750" loading="lazy">
     <span class="available-cat-card__status"><?= e(format_availability($cat['availability'])) ?></span>
     <button class="available-cat-card__favourite" type="button" aria-label="Add <?= e($cat['name']) ?> to favourites" data-fav="<?= e($cat['slug']) ?>">♡</button>
    </div>
    <div class="available-cat-card__content">
     <p class="available-cat-card__breed"><?= e($cat['breed_name']) ?></p>
     <h3 class="available-cat-card__name"><?= e($cat['name']) ?></h3>
     <p class="available-cat-card__details"><?= e(ucfirst($cat['sex'])) ?> · <?= e($age) ?></p>
     <div class="available-cat-card__footer">
      <strong class="available-cat-card__price"><?= e(format_price((float)$cat['price'])) ?></strong>
      <a href="<?= asset('cat.php?slug='.urlencode($cat['slug'])) ?>" class="available-cat-card__button">View Details</a>
     </div>
    </div>
   </article>
   <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <div style="text-align:center; margin-top:28px"><a href="<?= asset('cats.php') ?>" class="btn btn--ghost">View all cats</a></div>
 </section>

  <section class="why-urban-cats">
   <div class="why-urban-cats__intro">
    <p class="why-urban-cats__eyebrow">WHY CHOOSE URBAN CATS</p>
    <h2 class="why-urban-cats__title">Thoughtful care. Responsible placement.</h2>
    <p class="why-urban-cats__text">We keep our numbers small, maintain clear health records and support every customer before and after taking their cat home.</p>
   </div>
   <div class="why-urban-cats__benefits">
    <div class="why-benefit">
     <div class="why-benefit__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M9 12 L11 14 L15 9"/><path d="M12 3 L4 7 V12 C4 16 8 19 12 20 C16 19 20 16 20 12 V7 Z"/></svg></div>
     <div>
      <h3 class="why-benefit__title">Health documentation</h3>
      <p class="why-benefit__description">Receive available veterinary, vaccination and treatment records before completing your purchase.</p>
     </div>
    </div>
    <div class="why-benefit">
     <div class="why-benefit__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 9 L12 3 L21 9 V19 H3 V9 Z"/><path d="M9 19 V13 H15 V19"/></svg></div>
     <div>
      <h3 class="why-benefit__title">Responsible placement</h3>
      <p class="why-benefit__description">We help you select a cat whose breed, personality and care requirements suit your home.</p>
     </div>
    </div>
    <div class="why-benefit">
     <div class="why-benefit__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M21 11.5 C21 15 17 19 12 21 C7 19 3 15 3 11.5 C3 8 6 6 9 7 C11 8 12 9 12 9 C12 9 13 8 15 7 C18 6 21 8 21 11.5 Z"/><path d="M8 12 H16"/><path d="M8 16 H13"/></svg></div>
     <div>
      <h3 class="why-benefit__title">Ongoing support</h3>
      <p class="why-benefit__description">Get practical guidance on feeding, settling in, grooming and responsible long-term care.</p>
     </div>
    </div>
   </div>
  </section>

  <section class="buying-process">
   <div class="buying-process__header">
    <p class="buying-process__eyebrow">A SIMPLE PROCESS</p>
    <h2 class="buying-process__title">How buying works</h2>
    <p class="buying-process__description">From choosing your cat to welcoming them home, we make every stage clear and straightforward.</p>
   </div>
   <div class="buying-process__grid">
    <div class="buying-step">
     <div class="buying-step__top"><span class="buying-step__number">1</span><span class="buying-step__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="6"/><path d="M15 15 L19 19"/></svg></span></div>
     <h3 class="buying-step__title">Browse available cats</h3>
     <p class="buying-step__description">Explore current profiles, photographs, breeds, ages and availability.</p>
    </div>
    <div class="buying-step">
     <div class="buying-step__top"><span class="buying-step__number">2</span><span class="buying-step__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 20 L4 10 C4 6 8 4 12 8 C16 4 20 6 20 10 L12 20 Z"/></svg></span></div>
     <h3 class="buying-step__title">Choose your cat</h3>
     <p class="buying-step__description">Select the companion that best suits your home and lifestyle.</p>
    </div>
    <div class="buying-step">
     <div class="buying-step__top"><span class="buying-step__number">3</span><span class="buying-step__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 4 H20 V16 H8 L4 20 V4 Z"/><path d="M8 10 H16"/><path d="M8 13 H13"/></svg></span></div>
     <h3 class="buying-step__title">Submit an enquiry</h3>
     <p class="buying-step__description">Send your details and ask any questions before reserving your cat.</p>
    </div>
    <div class="buying-step">
     <div class="buying-step__top"><span class="buying-step__number">4</span><span class="buying-step__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 7 L12 11"/><circle cx="12" cy="14" r="1"/><path d="M7 11 H17 L16 18 H8 Z"/><path d="M8 11 C8 8 10 7 12 7 C14 7 16 8 16 11"/></svg></span></div>
     <h3 class="buying-step__title">Pay securely</h3>
     <p class="buying-step__description">Complete your approved reservation through our secure Paystack checkout.</p>
    </div>
    <div class="buying-step">
     <div class="buying-step__top"><span class="buying-step__number">5</span><span class="buying-step__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M9 12 L11 14 L15 10"/><circle cx="12" cy="12" r="8"/></svg></span></div>
     <h3 class="buying-step__title">Receive confirmation</h3>
     <p class="buying-step__description">We confirm payment and share the relevant collection and care information.</p>
    </div>
    <div class="buying-step">
     <div class="buying-step__top"><span class="buying-step__number">6</span><span class="buying-step__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 9 L12 3 L21 9 V19 H3 Z"/><path d="M9 19 V13 H15 V19"/><circle cx="18" cy="12" r="2"/></svg></span></div>
     <h3 class="buying-step__title">Collection or delivery</h3>
     <p class="buying-step__description">Collect your cat in Abuja or arrange an available delivery option.</p>
    </div>
   </div>
   <div class="buying-process__action">
    <a href="<?= asset('cats.php') ?>" class="btn btn--primary">VIEW AVAILABLE CATS</a>
   </div>
  </section>

 <section class="container section">
  <div class="newsletter-banner reveal">
   <div class="newsletter__content"><p class="kicker">Newsletter</p><h2 class="h2" style="font-size:1.6rem">Join Our Cat-Loving Community</h2><p class="muted" style="font-size:.9rem">New arrivals and care tips — a few emails a month.</p>
    <?php include __DIR__.'/includes/partials/alerts.php'; ?>
    <form action="<?= asset('newsletter.php') ?>" method="post" style="display:flex; gap:.5rem; margin-top:.8rem"><?= csrf_field() ?><input name="email" type="email" placeholder="Your email" required style="flex:1; border:1px solid var(--color-border); border-radius:999px; padding:.7rem 1rem"><button class="btn btn--primary">Subscribe</button></form>
   </div>
   <div class="newsletter__media"><img src="<?= asset('assets/images/site/newsletter-cat.svg') ?>" alt="Newsletter cat" loading="lazy"></div>
  </div>
 </section>

  <section class="testimonials-section">
   <div class="testimonials-section__header">
    <p class="testimonials-section__eyebrow">CUSTOMER STORIES</p>
    <h2 class="testimonials-section__title">Loved by cat families</h2>
    <p class="testimonials-section__description">Read about the experiences of families who have welcomed an Urban Cats companion into their homes.</p>
   </div>
   <?php
   // Display only approved testimonials (is_published=1 and is_demo=0); if none, show empty state per honesty requirement
   $approvedTestimonials = array_filter($testimonials, fn($t)=> !empty($t['is_published']) && empty($t['is_demo']));
   // Fallback: if DB still has demo but no approved, treat approved as empty to show empty state (do not publish demo as verified)
   if(empty($approvedTestimonials)){
    $hasApproved = false;
   } else {
    $hasApproved = true;
    $testimonialsToShow = array_slice($approvedTestimonials,0,3);
   }
   ?>
   <?php if(!$hasApproved): ?>
    <div style="text-align:center; padding:2.5rem; background:#fffdf9; border:1px dashed rgba(55,49,41,0.15); border-radius:16px; color:#69645a">
     <p style="margin:0; font-size:1rem">Customer stories will appear here soon.</p>
     <p style="margin:.4rem 0 0; font-size:.88rem">We are gathering approved stories from our wonderful families.</p>
    </div>
   <?php else: ?>
   <div class="testimonials-grid">
    <?php foreach($testimonialsToShow as $t):
     $initials = strtoupper(substr(trim($t['author_name']),0,1) . (strpos(trim($t['author_name']),' ')!==false ? substr(trim($t['author_name']), strpos(trim($t['author_name']),' ')+1,1) : ''));
     $initials = preg_replace('/[^A-Z]/','',$initials);
     if(empty($initials)) $initials='UC';
    ?>
    <article class="testimonial-card">
     <span class="testimonial-card__quote" aria-hidden="true">“</span>
     <blockquote class="testimonial-card__text"><?= e($t['content']) ?></blockquote>
     <footer class="testimonial-card__customer">
      <div class="testimonial-card__avatar" aria-hidden="true">
       <?php if(!empty($t['photo']) && is_file(__DIR__.'/'.$t['photo'])): ?><img src="<?= asset($t['photo']) ?>" alt="<?= e($t['author_name']) ?>"><?php else: ?><?= e($initials) ?><?php endif; ?>
      </div>
      <div>
       <cite class="testimonial-card__name"><?= e($t['author_name']) ?></cite>
       <p class="testimonial-card__location"><?= e($t['location']) ?></p>
      </div>
     </footer>
    </article>
    <?php endforeach; ?>
   </div>
   <?php endif; ?>
  </section>

  <section class="social-gallery">
   <div class="social-gallery__header">
    <p class="social-gallery__eyebrow">LIFE WITH URBAN CATS</p>
    <h2 class="social-gallery__title">From our cat gallery</h2>
    <p class="social-gallery__description">A closer look at our cats, their personalities and the moments that make them special.</p>
   </div>
   <div class="social-gallery__grid">
    <?php
    // Use genuine Urban Cats images if available, otherwise 6 distinct gallery images (not hotlinked)
    $galleryImages = [
     ['src'=>'assets/images/cats/luna-ragdoll.webp', 'alt'=>'Luna, a beautiful blue-eyed Ragdoll lounging comfortably'],
     ['src'=>'assets/images/cats/milo-maine-coon.webp', 'alt'=>'Milo, a majestic male Maine Coon with ear tufts'],
     ['src'=>'assets/images/cats/bella-maine-coon.webp', 'alt'=>'Bella, an elegant female Maine Coon in soft light'],
     ['src'=>'assets/images/cats/oliver-ragdoll.webp', 'alt'=>'Oliver, a handsome male blue-eyed Ragdoll'],
     ['src'=>'assets/images/cats/nala-ragdoll.webp', 'alt'=>'Nala, a soft attractive female Ragdoll kitten'],
     ['src'=>'assets/images/breeds/british-shorthair.webp', 'alt'=>'British Shorthair with plush coat, round face'],
    ];
    foreach($galleryImages as $idx=>$g):
     if(!is_file(__DIR__.'/'.$g['src'])) continue;
    ?>
    <div class="social-gallery__item" data-gallery-index="<?= $idx ?>" role="button" tabindex="0" aria-label="View gallery image <?= $idx+1 ?>">
     <img src="<?= asset($g['src']) ?>" alt="<?= e($g['alt']) ?>" width="400" height="400" loading="lazy">
    </div>
    <?php endforeach; ?>
   </div>
  </section>
  <div class="social-gallery__lightbox" id="galleryLightbox" aria-hidden="true" role="dialog" aria-label="Gallery lightbox">
   <button class="social-gallery__lightbox-close" id="lightboxClose" aria-label="Close">✕</button>
   <button class="social-gallery__lightbox-nav social-gallery__lightbox-prev" id="lightboxPrev" aria-label="Previous">‹</button>
   <img src="" alt="" id="lightboxImg">
   <button class="social-gallery__lightbox-nav social-gallery__lightbox-next" id="lightboxNext" aria-label="Next">›</button>
  </div>
</main>
<?php include __DIR__.'/includes/partials/footer.php'; ?>
<script type="module" src="<?= asset('assets/js/app.js') ?>"></script>
</body></html>
