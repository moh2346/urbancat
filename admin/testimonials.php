<?php
require __DIR__ . '/includes/auth.php';
admin_require();
require __DIR__ . '/layout.php';
if(isset($_GET['delete']) && verify_csrf($_GET['csrf']??'')){ $pdo->prepare("DELETE FROM testimonials WHERE id=?")->execute([(int)$_GET['delete']]); header('Location: '.base_url('admin/testimonials.php')); exit; }
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add'])){
 if(!verify_csrf($_POST['csrf_token']??'')) flash('error','CSRF');
 else $pdo->prepare("INSERT INTO testimonials (author_name,location,content,rating,is_demo) VALUES (?,?,?,?,?)")->execute([sanitize_text($_POST['author_name'],120),sanitize_text($_POST['location'],120),sanitize_text($_POST['content'],2000),(int)$_POST['rating'],isset($_POST['is_demo'])?1:0]);
 header('Location: '.base_url('admin/testimonials.php')); exit;
}
admin_header('Testimonials',$pdo);
$rows=$pdo->query("SELECT * FROM testimonials ORDER BY id DESC")->fetchAll();
?>
<div class="admin-card">
<table class="table"><tr><th>Author</th><th>Demo?</th><th>Action</th></tr>
<?php foreach($rows as $r): ?><tr><td><?= e($r['author_name']) ?> — <?= e($r['location']) ?></td><td><?= $r['is_demo']?'Yes':'No' ?></td><td><a href="<?= asset('admin/testimonials.php?delete='.$r['id'].'&csrf='.urlencode(csrf_token())) ?>" onclick="return confirm('Delete?')">Delete</a></td></tr><?php endforeach; ?>
</table>
<form method="post" class="form" style="margin-top:1rem"><?= csrf_field() ?><div class="field"><label>Author</label><input name="author_name" required></div><div class="field"><label>Location</label><input name="location"></div><div class="field"><label>Content</label><textarea name="content" required></textarea></div><div class="field"><label>Rating (1-5)</label><input type="number" name="rating" min="1" max="5" value="5"></div><label><input type="checkbox" name="is_demo" value="1" checked> Demo/sample content</label><button name="add" class="btn btn--primary">Add</button></form>
</div>
<?php admin_footer(); ?>
