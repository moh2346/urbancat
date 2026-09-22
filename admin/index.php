<?php
require __DIR__ . '/includes/auth.php';
admin_require();
require __DIR__ . '/layout.php';
$stats=[
  'cats'=>$pdo->query("SELECT COUNT(*) FROM cats")->fetchColumn(),
  'available'=>$pdo->query("SELECT COUNT(*) FROM cats WHERE availability='available'")->fetchColumn(),
  'enquiries'=>$pdo->query("SELECT COUNT(*) FROM enquiries WHERE status='new'")->fetchColumn(),
  'subs'=>$pdo->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE unsubscribed_at IS NULL")->fetchColumn(),
];
admin_header('Dashboard',$pdo);
?>
<div class="grid-4">
<div class="admin-card"><strong><?= (int)$stats['cats'] ?></strong><div class="muted">Total cats</div></div>
<div class="admin-card"><strong><?= (int)$stats['available'] ?></strong><div class="muted">Available</div></div>
<div class="admin-card"><strong><?= (int)$stats['enquiries'] ?></strong><div class="muted">New enquiries</div></div>
<div class="admin-card"><strong><?= (int)$stats['subs'] ?></strong><div class="muted">Subscribers</div></div>
</div>
<div class="admin-card" style="margin-top:1rem">
<h3>Recent enquiries</h3>
<table class="table">
<tr><th>Name</th><th>Type</th><th>Cat</th><th>Date</th></tr>
<?php $rows=$pdo->query("SELECT e.*, c.name as cat_name FROM enquiries e LEFT JOIN cats c ON c.id=e.cat_id ORDER BY e.created_at DESC LIMIT 5")->fetchAll();
foreach($rows as $r): ?><tr><td><?= e($r['full_name']) ?></td><td><?= e($r['type']) ?></td><td><?= e($r['cat_name']??'—') ?></td><td><?= e($r['created_at']) ?></td></tr><?php endforeach; ?>
</table>
</div>
<?php admin_footer(); ?>
