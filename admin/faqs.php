<?php
require __DIR__ . '/includes/auth.php';
admin_require();
require __DIR__ . '/layout.php';
if(isset($_GET['delete']) && verify_csrf($_GET['csrf']??'')){ $pdo->prepare("DELETE FROM faqs WHERE id=?")->execute([(int)$_GET['delete']]); header('Location: '.base_url('admin/faqs.php')); exit; }
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add'])){
 if(!verify_csrf($_POST['csrf_token']??'')) flash('error','CSRF');
 else{
  $pdo->prepare("INSERT INTO faqs (question,answer,sort_order) VALUES (?,?,?)")->execute([sanitize_text($_POST['question'],255),sanitize_text($_POST['answer'],5000),(int)$_POST['sort_order']]);
  flash('success','FAQ added');
  header('Location: '.base_url('admin/faqs.php')); exit;
 }
}
admin_header('FAQs',$pdo);
$rows=$pdo->query("SELECT * FROM faqs ORDER BY sort_order")->fetchAll();
?>
<div class="admin-card">
<table class="table"><tr><th>#</th><th>Question</th><th>Action</th></tr>
<?php foreach($rows as $r): ?><tr><td><?= (int)$r['sort_order'] ?></td><td><?= e($r['question']) ?></td><td><a href="<?= asset('admin/faqs.php?delete='.$r['id'].'&csrf='.urlencode(csrf_token())) ?>" onclick="return confirm('Delete?')">Delete</a></td></tr><?php endforeach; ?>
</table>
<form method="post" class="form" style="margin-top:1rem"><?= csrf_field() ?><div class="field"><label>Question</label><input name="question" required></div><div class="field"><label>Answer</label><textarea name="answer" required></textarea></div><div class="field"><label>Sort order</label><input type="number" name="sort_order" value="10"></div><button name="add" class="btn btn--primary">Add FAQ</button></form>
</div>
<?php admin_footer(); ?>
