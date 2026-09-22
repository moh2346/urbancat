<?php
// Shared admin layout helpers — call admin_header(title)
function admin_header(string $title, PDO $pdo): void {
  $user = admin_user($pdo);
  echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).' — Admin</title><link rel="stylesheet" href="'.asset('assets/css/style.css').'"><style>
  body{background:var(--ivory)}.admin{max-width:1320px;margin:0 auto;padding:18px}.admin-nav{background:var(--charcoal);color:var(--cream);padding:.7rem 1rem;border-radius:14px;display:flex;gap:.7rem;align-items:center;flex-wrap:wrap}.admin-nav a{color:rgba(247,242,232,.84);font-size:.9rem;padding:.35rem .6rem;border-radius:999px}.admin-nav a.is-active,.admin-nav a:hover{background:rgba(255,255,255,.12);color:var(--cream)}.admin-card{background:var(--white);border:1px solid rgba(28,27,25,.07);border-radius:16px;padding:1rem;box-shadow:var(--shadow-soft)}
  </style></head><body><div class="admin">';
  echo '<nav class="admin-nav"><strong>Urban Cats Admin</strong><span style="opacity:.6">|</span>';
  $links=['index.php'=>'Dashboard','cats.php'=>'Cats','breeds.php'=>'Breeds','orders.php'=>'Orders','enquiries.php'=>'Enquiries','newsletter.php'=>'Newsletter','faqs.php'=>'FAQs','testimonials.php'=>'Testimonials','settings.php'=>'Settings'];
  $cur=basename($_SERVER['SCRIPT_NAME']);
  foreach($links as $f=>$l){ echo '<a href="'.asset('admin/'.$f).'" class="'.($cur===$f?'is-active':'').'">'.e($l).'</a>'; }
  echo '<span style="margin-left:auto;font-size:.86rem">'.e($user['name']??'').' ('.e($user['email']??'').') <a href="'.asset('admin/logout.php').'" style="color:#fff;text-decoration:underline">Logout</a></span>';
  echo '</nav>';
  echo '<h1 style="font-family:var(--font-display);margin:1rem 0 .6rem">'.e($title).'</h1>';
  $suc=flash('success'); $err=flash('error');
  if($suc) echo '<div class="alert alert--success">'.e($suc).'</div>';
  if($err) echo '<div class="alert alert--error">'.e($err).'</div>';
}
function admin_footer(): void { echo '</div><script type="module" src="'.asset('assets/js/app.js').'"></script></body></html>'; }
