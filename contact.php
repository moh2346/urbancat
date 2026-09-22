<?php
require __DIR__.'/includes/bootstrap.php';
$errors=[]; if($_SERVER['REQUEST_METHOD']==='POST'){
 if(is_honeypot_filled()) $errors[]='Spam detected.';
 if(!verify_csrf($_POST['csrf_token']??'')) $errors[]='Invalid session.';
 $data=['full_name'=>sanitize_text($_POST['full_name']??'',120),'email'=>trim($_POST['email']??''),'phone'=>sanitize_text($_POST['phone']??'',40),'message'=>sanitize_text($_POST['message']??'',2000),'consent'=>isset($_POST['consent'])?1:0];
 if($data['full_name']==='') $errors[]='Name required.'; if(!is_valid_email($data['email'])) $errors[]='Valid email required.'; if(mb_strlen($data['message'])<10) $errors[]='Message 10+ chars.'; if(!$data['consent']) $errors[]='Consent required.';
 $ip=$_SERVER['REMOTE_ADDR']??'0.0.0.0'; if(!check_rate_limit($pdo,$ip,5,600)) $errors[]='Too many requests.';
 if(!$errors){$pdo->prepare("INSERT INTO enquiries (type,full_name,email,phone,message,consent) VALUES ('general',?,?,?,?,?)")->execute([$data['full_name'],$data['email'],$data['phone'],$data['message'],$data['consent']]); flash('success','Message received. We will respond within 24–48h.'); header('Location: '.base_url('contact.php')); exit;}
}
$pageTitle='Contact — Urban Cats';
?>
<!doctype html><html lang="en"><head><?php include __DIR__.'/includes/partials/head.php'; ?></head><body>
<?php include __DIR__.'/includes/partials/header.php'; ?>
<main id="main"><div class="container section"><div class="breadcrumbs"><a href="<?= asset('index.php') ?>">Home</a> / Contact</div><div class="grid-2"><div><h1 class="h2" style="font-family:var(--font-display)">Contact</h1><p class="muted">Gwarinpa, Abuja • <?= e(setting($pdo,'contact_email','urbankitty0@gmail.com')) ?> • <?= e(setting($pdo,'contact_phone','09122037945')) ?></p>
<?php include __DIR__.'/includes/partials/alerts.php'; if($errors): ?><div class="alert alert--error"><ul><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach;?></ul></div><?php endif; ?>
<form method="post" class="form" novalidate><?= csrf_field().honeypot_field() ?><div class="field"><label>Full name *</label><input name="full_name" value="<?= e($_POST['full_name']??'') ?>" required></div><div class="field"><label>Email *</label><input name="email" type="email" value="<?= e($_POST['email']??'') ?>" required></div><div class="field"><label>Phone *</label><input name="phone" value="<?= e($_POST['phone']??'') ?>" required></div><div class="field"><label>Message *</label><textarea name="message" required><?= e($_POST['message']??'') ?></textarea></div><label><input type="checkbox" name="consent" value="1" required> I consent to processing *</label><button class="btn btn--primary">Send message</button></form></div><div><div class="card" style="padding:1.2rem"><h3>Visit</h3><p class="muted"><?= e(setting($pdo,'address','Gwarinpa, Abuja, Nigeria')) ?><br><?= e(setting($pdo,'opening_hours','Mon–Sat 9:00–18:00 WAT')) ?></p><a href="https://wa.me/<?= e(setting($pdo,'whatsapp_number','2349122037945')) ?>" class="btn btn--sage btn--block">Chat on WhatsApp</a></div></div></div></div></main>
<?php include __DIR__.'/includes/partials/footer.php'; ?><script type="module" src="<?= asset('assets/js/app.js') ?>"></script></body></html>
