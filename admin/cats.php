<?php
require __DIR__ . '/includes/auth.php';
admin_require();
require __DIR__ . '/layout.php';

// Delete
if(isset($_GET['delete'])){
  if(!verify_csrf($_GET['csrf']??'')) flash('error','Invalid CSRF');
  else{
    $id=(int)$_GET['delete'];
    // delete images files
    $imgs=$pdo->prepare("SELECT image_path FROM cat_images WHERE cat_id=?"); $imgs->execute([$id]); foreach($imgs->fetchAll() as $im){
      $path=__DIR__.'/../'.$im['image_path'];
      if(is_file($path) && str_contains($path, 'uploads/')) @unlink($path);
    }
    $pdo->prepare("DELETE FROM cats WHERE id=?")->execute([$id]);
    flash('success','Cat deleted.');
  }
  header('Location: '.base_url('admin/cats.php')); exit;
}
admin_header('Cats',$pdo);
$cats=$pdo->query("SELECT c.*, b.name as breed_name FROM cats c JOIN breeds b ON b.id=c.breed_id ORDER BY c.created_at DESC")->fetchAll();
?>
<div class="admin-card">
<div style="display:flex;justify-content:space-between;align-items:center"><h3 style="margin:0">All cats</h3><a href="<?= asset('admin/cat-edit.php') ?>" class="btn btn--primary btn--sm">Add cat</a></div>
<table class="table" style="margin-top:.8rem">
<tr><th>Name</th><th>Breed</th><th>Price</th><th>Mode</th><th>Status</th><th>Active</th><th>Featured</th><th>Actions</th></tr>
<?php foreach($cats as $c): ?>
<tr>
<td><?= e($c['name']) ?></td><td><?= e($c['breed_name']) ?></td><td><?= e(format_price((int)$c['price'])) ?><?php if($c['purchase_mode']==='deposit'): ?><br><span class="muted" style="font-size:.76rem"><?= e(format_price((int)($c['deposit_amount'] ?? 0))) ?> deposit</span><?php endif; ?></td><td><?= e($c['purchase_mode']) ?></td><td><?= e($c['availability']) ?></td><td><?= $c['is_active']?'Yes':'No' ?></td><td><?= $c['is_featured']?'Yes':'No' ?></td>
<td><a href="<?= asset('admin/cat-edit.php?id='.$c['id']) ?>">Edit</a> • <a href="<?= asset('admin/cats.php?delete='.$c['id'].'&csrf='.urlencode(csrf_token())) ?>" onclick="return confirm('Delete?')">Delete</a></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<?php admin_footer(); ?>
