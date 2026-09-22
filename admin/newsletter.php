<?php
require __DIR__ . '/includes/auth.php';
admin_require();
require __DIR__ . '/layout.php';
if(isset($_GET['delete']) && verify_csrf($_GET['csrf']??'')){ $pdo->prepare("DELETE FROM newsletter_subscribers WHERE id=?")->execute([(int)$_GET['delete']]); header('Location: '.base_url('admin/newsletter.php')); exit; }
admin_header('Newsletter',$pdo);
$rows=$pdo->query("SELECT * FROM newsletter_subscribers ORDER BY created_at DESC")->fetchAll();
?>
<div class="admin-card">
<table class="table"><tr><th>Email</th><th>Subscribed</th><th>Action</th></tr>
<?php foreach($rows as $r): ?><tr><td><?= e($r['email']) ?></td><td><?= e($r['created_at']) ?></td><td><a href="<?= asset('admin/newsletter.php?delete='.$r['id'].'&csrf='.urlencode(csrf_token())) ?>" onclick="return confirm('Delete?')">Delete</a></td></tr><?php endforeach; ?>
</table>
</div>
<?php admin_footer(); ?>
