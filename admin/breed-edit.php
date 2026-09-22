<?php
require __DIR__ . '/includes/auth.php';
admin_require();
require __DIR__ . '/layout.php';
$id=isset($_GET['id'])?(int)$_GET['id']:0;
$row=null; if($id){ $s=$pdo->prepare("SELECT * FROM breeds WHERE id=?"); $s->execute([$id]); $row=$s->fetch(); }
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!verify_csrf($_POST['csrf_token']??'')) $errors[]='CSRF';
 else{
  $data=[
   'slug'=>slugify($_POST['slug']?? $_POST['name']??''),
   'name'=>sanitize_text($_POST['name']??'',80),
   'short_description'=>sanitize_text($_POST['short_description']??'',255),
   'description'=>sanitize_text($_POST['description']??'',5000),
   'temperament'=>sanitize_text($_POST['temperament']??'',255),
   'grooming'=>sanitize_text($_POST['grooming']??'',255),
   'activity_level'=>sanitize_text($_POST['activity_level']??'',100),
   'size_info'=>sanitize_text($_POST['size_info']??'',150),
   'family_compatibility'=>sanitize_text($_POST['family_compatibility']??'',255),
   'care_notes'=>sanitize_text($_POST['care_notes']??'',5000),
  ];
  if($data['name']==='') $errors[]='Name required';
  if(!$errors){
   if($row) $pdo->prepare("UPDATE breeds SET slug=?,name=?,short_description=?,description=?,temperament=?,grooming=?,activity_level=?,size_info=?,family_compatibility=?,care_notes=? WHERE id=?")->execute([$data['slug'],$data['name'],$data['short_description'],$data['description'],$data['temperament'],$data['grooming'],$data['activity_level'],$data['size_info'],$data['family_compatibility'],$data['care_notes'],$id]);
   else $pdo->prepare("INSERT INTO breeds (slug,name,short_description,description,temperament,grooming,activity_level,size_info,family_compatibility,care_notes) VALUES (?,?,?,?,?,?,?,?,?,?)")->execute([$data['slug'],$data['name'],$data['short_description'],$data['description'],$data['temperament'],$data['grooming'],$data['activity_level'],$data['size_info'],$data['family_compatibility'],$data['care_notes']]);
   flash('success','Breed saved'); header('Location: '.base_url('admin/breeds.php')); exit;
  }
 }
}
admin_header($row?'Edit Breed':'Add Breed',$pdo);
$val=fn($k,$d='')=>e($_POST[$k]??($row[$k]??$d));
if($errors): ?><div class="alert alert--error"><ul style="margin:0;padding-left:1.1rem"><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="admin-card">
<form method="post" class="form"><?= csrf_field() ?>
<div class="form-grid"><div class="field"><label>Name</label><input name="name" value="<?= $val('name') ?>" required></div><div class="field"><label>Slug</label><input name="slug" value="<?= $val('slug') ?>"></div></div>
<div class="field"><label>Short description</label><input name="short_description" value="<?= $val('short_description') ?>"></div>
<div class="field"><label>Description</label><textarea name="description"><?= $val('description') ?></textarea></div>
<div class="field"><label>Temperament</label><input name="temperament" value="<?= $val('temperament') ?>"></div>
<div class="field"><label>Grooming</label><input name="grooming" value="<?= $val('grooming') ?>"></div>
<div class="field"><label>Activity level</label><input name="activity_level" value="<?= $val('activity_level') ?>"></div>
<div class="field"><label>Size info</label><input name="size_info" value="<?= $val('size_info') ?>"></div>
<div class="field"><label>Family compatibility</label><input name="family_compatibility" value="<?= $val('family_compatibility') ?>"></div>
<div class="field"><label>Care notes</label><textarea name="care_notes"><?= $val('care_notes') ?></textarea></div>
<button class="btn btn--primary" type="submit">Save</button> <a href="<?= asset('admin/breeds.php') ?>" class="btn btn--ghost">Back</a>
</form></div>
<?php admin_footer(); ?>
