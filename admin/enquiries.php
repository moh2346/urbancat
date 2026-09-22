<?php
require __DIR__ . '/includes/auth.php';
admin_require();
require __DIR__ . '/layout.php';
if(isset($_POST['update_status'])){
 if(!verify_csrf($_POST['csrf_token']??'')) flash('error','CSRF');
 else $pdo->prepare("UPDATE enquiries SET status=? WHERE id=?")->execute([$_POST['status'],(int)$_POST['id']]);
 header('Location: '.base_url('admin/enquiries.php')); exit;
}
if(isset($_GET['delete'])){
 if(verify_csrf($_GET['csrf']??'')) $pdo->prepare("DELETE FROM enquiries WHERE id=?")->execute([(int)$_GET['delete']]);
 header('Location: '.base_url('admin/enquiries.php')); exit;
}
admin_header('Enquiries',$pdo);
$rows=$pdo->query("SELECT e.*, c.name as cat_name FROM enquiries e LEFT JOIN cats c ON c.id=e.cat_id ORDER BY e.created_at DESC")->fetchAll();
?>
<div class="admin-card">
<table class="table"><tr><th>Date</th><th>Name</th><th>Type</th><th>Cat</th><th>Contact</th><th>Status</th><th>Actions</th></tr>
<?php foreach($rows as $r): ?>
<tr>
<td><?= e($r['created_at']) ?></td><td><?= e($r['full_name']) ?><div class="muted" style="font-size:.82rem"><?= e($r['email']) ?></div></td><td><?= e($r['type']) ?></td><td><?= e($r['cat_name']??'—') ?></td><td><?= e($r['phone']) ?></td><td><?= e($r['status']) ?></td>
<td>
<form method="post" style="display:flex;gap:.3rem"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><select name="status"><option value="new" <?= $r['status']==='new'?'selected':'' ?>>new</option><option value="contacted" <?= $r['status']==='contacted'?'selected':'' ?>>contacted</option><option value="closed" <?= $r['status']==='closed'?'selected':'' ?>>closed</option></select><button name="update_status" class="btn btn--ghost btn--sm">Update</button></form>
<a href="<?= asset('admin/enquiries.php?delete='.$r['id'].'&csrf='.urlencode(csrf_token())) ?>" onclick="return confirm('Delete?')">Delete</a>
<div style="max-width:220px;white-space:pre-wrap;font-size:.82rem" class="muted"><?= e(mb_substr($r['message'],0,120)) ?></div>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>
<?php admin_footer(); ?>
