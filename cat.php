<?php
require __DIR__.'/includes/bootstrap.php';
require __DIR__.'/includes/functions_catalog.php';
$slug=trim($_GET['slug']??''); if(!$slug){http_response_code(404); include __DIR__.'/404.php'; exit;}
$cat=get_cat_by_slug($pdo,$slug); if(!$cat){http_response_code(404); include __DIR__.'/404.php'; exit;}
$images=cat_images($pdo,(int)$cat['id']); if(!$images) $images=[['image_path'=>'assets/images/cats/placeholder.svg']];
$related=$pdo->prepare("SELECT c.*, b.name as breed_name FROM cats c JOIN breeds b ON b.id=c.breed_id WHERE c.breed_id=? AND c.id!=? AND c.is_active=1 ORDER BY c.created_at DESC LIMIT 3"); $related->execute([$cat['breed_id'],$cat['id']]); $relatedCats=$related->fetchAll();
$wa=setting($pdo,'whatsapp_number','2349122037945'); $waLink="https://wa.me/$wa?text=".urlencode("Hello Urban Cats, interested in ".$cat['name']." (".$cat['slug'].")");
$canBuy=$cat['is_active'] && $cat['availability']=='available' && $cat['purchase_mode']!='enquiry';
$dep=$cat['deposit_amount']? (float)$cat['deposit_amount'] : null;
$pageTitle=$cat['name'].' — '.$cat['breed_name'].' — Urban Cats';
$ogImage=asset($images[0]['image_path']);
?>
<!doctype html><html lang="en"><head><?php include __DIR__.'/includes/partials/head.php'; ?>
<script type="application/ld+json">{"@context":"https://schema.org","@type":"Product","name":"<?= e($cat['name']) ?>","category":"<?= e($cat['breed_name']) ?>","offers":{"@type":"Offer","price":"<?= (float)$cat['price'] ?>","priceCurrency":"NGN"}}</script>
</head><body>
<?php include __DIR__.'/includes/partials/header.php'; ?>
<main id="main"><div class="container section">
 <div class="breadcrumbs"><a href="<?= asset('index.php') ?>">Home</a> / <a href="<?= asset('cats.php') ?>">Cats</a> / <?= e($cat['name']) ?></div>
 <div class="detail-grid">
  <div>
   <div class="gallery"><div class="gallery__main"><img data-gallery-main src="<?= e($images[0]['image_path']) ?>" alt="<?= e($cat['name']) ?>" width="800" height="600"></div><div class="gallery__thumbs"><?php foreach($images as $i=>$im):?><img data-thumb src="<?= e($im['image_path']) ?>" alt="<?= e($cat['name']) ?> <?= $i+1 ?>" class="<?= $i===0?'is-active':'' ?>" width="200" height="140"><?php endforeach;?></div></div>
   <div class="card" style="padding:1.2rem; margin-top:1rem"><h2 style="font-family:var(--font-display)"><?= e($cat['name']) ?> — About</h2><p class="muted"><?= nl2br(e($cat['description'])) ?></p><h3>Care notes</h3><p class="muted"><?= nl2br(e($cat['care_notes'])) ?></p><p class="muted" style="font-size:.82rem; border-top:1px solid var(--color-border); padding-top:.7rem; margin-top:1rem">Health info reflects available records, not vet advice.</p></div>
  </div>
  <div>
   <div class="card" style="padding:1.2rem">
    <div style="display:flex; justify-content:space-between; align-items:center"><h1 style="font-family:var(--font-display); font-size:1.6rem; margin:0"><?= e($cat['name']) ?></h1><span class="badge <?= e(availability_class($cat['availability'])) ?>"><?= e(format_availability($cat['availability'])) ?></span></div>
    <p class="muted" style="margin:.2rem 0 .6rem"><a href="<?= asset('breed.php?slug='.urlencode($cat['breed_slug'])) ?>" style="text-decoration:underline; font-weight:600"><?= e($cat['breed_name']) ?></a> • <?= e(ucfirst($cat['sex'])) ?> • <?= e($cat['colour']) ?></p>
    <div style="display:grid; gap:.4rem; font-size:.9rem; border-top:1px solid var(--color-border); padding-top:.8rem">
     <div style="display:flex; justify-content:space-between"><span class="muted">Age</span><strong><?= e(age_from_dob($cat['date_of_birth'])) ?></strong></div>
     <div style="display:flex; justify-content:space-between"><span class="muted">Location</span><strong><?= e($cat['location']) ?></strong></div>
     <div style="display:flex; justify-content:space-between"><span class="muted">Price</span><strong><?= e(format_price((float)$cat['price'])) ?></strong></div>
     <?php if($dep):?><div style="display:flex; justify-content:space-between"><span class="muted">Deposit</span><strong><?= e(format_price((float)$dep)) ?></strong></div><?php endif;?>
     <div style="display:flex; justify-content:space-between"><span class="muted">Litter</span><strong><?= $cat['litter_trained']?'Yes':'No' ?></strong></div>
     <div style="display:flex; justify-content:space-between"><span class="muted">Vaccination</span><strong><?= e($cat['vaccination_status']) ?></strong></div>
    </div>
    <div style="display:grid; gap:.6rem; margin-top:1rem">
     <?php if($canBuy): ?><a href="<?= asset('cart.php?add='.urlencode($cat['slug'])) ?>" class="btn btn--primary btn--block"><?php if($cat['purchase_mode']=='deposit') echo 'Reserve — '.e(format_price((float)$dep)); else echo 'Buy — '.e(format_price((float)$cat['price'])); ?></a>
     <?php else: ?><div class="alert alert--error" style="margin:0; text-align:center"><?php if($cat['purchase_mode']=='enquiry') echo 'Enquiry only'; else echo 'Not available for purchase'; ?></div><?php endif;?>
     <a href="<?= asset('enquiry.php?cat='.urlencode($cat['slug'])) ?>" class="btn btn--ghost btn--block">Enquire</a>
     <a href="<?= e($waLink) ?>" target="_blank" class="btn btn--ghost btn--block">WhatsApp Enquiry</a>
     <button class="btn btn--ghost btn--block" data-fav="<?= e($cat['slug']) ?>">♡ Favourite</button>
    </div>
    <p class="muted" style="font-size:.78rem; text-align:center; margin-top:.6rem">WhatsApp <?= e(setting($pdo,'whatsapp_display','09122037945')) ?> • <?= e(setting($pdo,'contact_email','urbankitty0@gmail.com')) ?></p>
   </div>
   <?php if($relatedCats): ?><h3 style="margin:1rem 0 .5rem">Related cats</h3><?php foreach($relatedCats as $rc): $ri=primary_image($pdo,(int)$rc['id']);?><a href="<?= asset('cat.php?slug='.urlencode($rc['slug'])) ?>" class="card" style="flex-direction:row; padding:.5rem; gap:.6rem; margin-bottom:.5rem"><img src="<?= e($ri) ?>" width="80" height="60" style="border-radius:8px; object-fit:cover"><div><strong><?= e($rc['name']) ?></strong><div class="muted" style="font-size:.82rem"><?= e($rc['breed_name']) ?> • <?= e(format_price((float)$rc['price'])) ?></div></div></a><?php endforeach; endif;?>
  </div>
 </div>
</div></main>
<?php include __DIR__.'/includes/partials/footer.php'; ?><script type="module" src="<?= asset('assets/js/app.js') ?>"></script></body></html>
