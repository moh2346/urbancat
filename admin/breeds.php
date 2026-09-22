<?php
require __DIR__ . '/includes/auth.php';
admin_require();
require __DIR__ . '/layout.php';
if(isset($_GET['delete'])){
 if(!verify_csrf($_GET['csrf']??'')) flash('error','CSRF');
 else{ $pdo->prepare("DELETE FROM breeds WHERE id=?")->execute([(int)$_GET['delete']]); flash('success','Breed deleted'); }
 header('Location: '.base_url('admin/breeds.php')); exit;
}
admin_header('Breeds',$pdo);
$rows=$pdo->query("SELECT * FROM breeds ORDER BY name")->fetchAll();
?>
<div class="admin-card"><div style="display:flex;justify-content:space-between"><h3 style="margin:0">Breeds</h3><a href="<?= asset('admin/breed-edit.php') ?>" class="btn btn--primary btn--sm">Add breed</a></div>
<table class="table" style="margin-top:.8rem"><tr><th>Name</th><th>Slug</th><th>Actions</th></tr>
<?php foreach($rows as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['slug']) ?></td><td><a href="<?= asset('admin/breed-edit.php?id='.$r['id']) ?>">Edit</a> • <a href="<?= asset('admin/breeds.php?delete='.$r['id'].'&csrf='.urlencode(csrf_token())) ?>" onclick="return confirm('Delete breed and cats?')">Delete</a></td></tr><?php endforeach; ?>
</table></div>
<?php admin_footer(); ?>
