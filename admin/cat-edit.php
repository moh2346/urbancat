<?php
require __DIR__ . '/includes/auth.php';
admin_require();
require __DIR__ . '/layout.php';
$config=require __DIR__ . '/../config/config.php';
$id=isset($_GET['id'])?(int)$_GET['id']:0;
$cat=null;
if($id){ $s=$pdo->prepare("SELECT * FROM cats WHERE id=?"); $s->execute([$id]); $cat=$s->fetch(); if(!$cat){ flash('error','Cat not found'); header('Location: '.base_url('admin/cats.php')); exit; } }

$breeds=$pdo->query("SELECT * FROM breeds ORDER BY name")->fetchAll();
$errors=[];

function handle_upload(array $file, array $cfg): ?string {
  if($file['error']===UPLOAD_ERR_NO_FILE) return null;
  if($file['error']!==UPLOAD_ERR_OK) throw new RuntimeException('Upload error');
  if($file['size'] > $cfg['upload']['max_bytes']) throw new RuntimeException('File too large (max 4MB)');
  $finfo=finfo_open(FILEINFO_MIME_TYPE); $mime=finfo_file($finfo,$file['tmp_name']); finfo_close($finfo);
  if(!in_array($mime,$cfg['upload']['allowed_mimes'])) throw new RuntimeException('Invalid file type: '.$mime);
  $ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
  if(!in_array($ext,$cfg['upload']['allowed_ext'])) throw new RuntimeException('Invalid extension');
  if(in_array($ext,['php','phtml','phar'])) throw new RuntimeException('Executable not allowed');
  $dir=$cfg['upload']['dir'];
  if(!is_dir($dir)) mkdir($dir,0755,true);
  $name=bin2hex(random_bytes(8)).'.'.$ext;
  $dest=$dir.'/'.$name;
  if(!move_uploaded_file($file['tmp_name'],$dest)) throw new RuntimeException('Move failed');
  return $cfg['upload']['url_prefix'].'/'.$name;
}

if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!verify_csrf($_POST['csrf_token'] ?? '')) $errors[]='Invalid CSRF';
  else{
    $data=[
      'breed_id'=>(int)($_POST['breed_id']??0),
      'name'=>sanitize_text($_POST['name']??'',80),
      'slug'=>slugify($_POST['slug']?? $_POST['name']??''),
      'sex'=>$_POST['sex']??'male',
      'date_of_birth'=>$_POST['date_of_birth']??'',
      'colour'=>sanitize_text($_POST['colour']??'',80),
      'location'=>sanitize_text($_POST['location']??'',120),
      'price'=>(int)($_POST['price']??0),
      'availability'=>$_POST['availability']??'available',
      'is_featured'=>isset($_POST['is_featured'])?1:0,
      'personality'=>sanitize_text($_POST['personality']??'',255),
      'vaccination_status'=>sanitize_text($_POST['vaccination_status']??'',255),
      'health_notes'=>sanitize_text($_POST['health_notes']??'',255),
      'litter_trained'=>isset($_POST['litter_trained'])?1:0,
      'parent_info'=>sanitize_text($_POST['parent_info']??'',255),
      'description'=>sanitize_text($_POST['description']??'',5000),
      'care_notes'=>sanitize_text($_POST['care_notes']??'',5000),
      'purchase_mode'=>$_POST['purchase_mode']??'full',
      'deposit_amount'=>$_POST['deposit_amount']!=='' ? (float)$_POST['deposit_amount'] : null,
      'is_active'=>isset($_POST['is_active'])?1:0,
    ];
    // validate
    if($data['name']==='') $errors[]='Name required';
    if(!$data['breed_id']) $errors[]='Breed required';
    if(!in_array($data['sex'],['male','female'])) $errors[]='Sex invalid';
    if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$data['date_of_birth'])) $errors[]='Date of birth required (YYYY-MM-DD)';
    if($data['price']<=0) $errors[]='Price required';
    if(!in_array($data['availability'],['available','reserved','sold'])) $errors[]='Availability invalid';
    if(!in_array($data['purchase_mode'],['full','deposit','enquiry'])) $errors[]='Purchase mode invalid';
    if($data['purchase_mode']==='deposit'){
      if($data['deposit_amount']===null || $data['deposit_amount']<=0) $errors[]='Deposit amount required for deposit mode';
      elseif($data['deposit_amount'] >= $data['price']) $errors[]='Deposit must be less than full price';
    }
    if(!$errors){
      try{
        if($cat){
          $pdo->prepare("UPDATE cats SET breed_id=?,name=?,slug=?,sex=?,date_of_birth=?,colour=?,location=?,price=?,availability=?,is_featured=?,personality=?,vaccination_status=?,health_notes=?,litter_trained=?,parent_info=?,description=?,care_notes=?,purchase_mode=?,deposit_amount=?,is_active=? WHERE id=?")
          ->execute([$data['breed_id'],$data['name'],$data['slug'],$data['sex'],$data['date_of_birth'],$data['colour'],$data['location'],$data['price'],$data['availability'],$data['is_featured'],$data['personality'],$data['vaccination_status'],$data['health_notes'],$data['litter_trained'],$data['parent_info'],$data['description'],$data['care_notes'],$data['purchase_mode'],$data['deposit_amount'],$data['is_active'],$id]);
          $catId=$id;
        } else {
          $pdo->prepare("INSERT INTO cats (breed_id,name,slug,sex,date_of_birth,colour,location,price,availability,is_featured,personality,vaccination_status,health_notes,litter_trained,parent_info,description,care_notes,purchase_mode,deposit_amount,is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
          ->execute([$data['breed_id'],$data['name'],$data['slug'],$data['sex'],$data['date_of_birth'],$data['colour'],$data['location'],$data['price'],$data['availability'],$data['is_featured'],$data['personality'],$data['vaccination_status'],$data['health_notes'],$data['litter_trained'],$data['parent_info'],$data['description'],$data['care_notes'],$data['purchase_mode'],$data['deposit_amount'],$data['is_active']]);
          $catId=(int)$pdo->lastInsertId();
        }
        // handle images
        if(!empty($_FILES['images']['name'][0])){
          foreach($_FILES['images']['name'] as $idx=>$name){
            if(empty($name)) continue;
            $file=['name'=>$_FILES['images']['name'][$idx],'type'=>$_FILES['images']['type'][$idx],'tmp_name'=>$_FILES['images']['tmp_name'][$idx],'error'=>$_FILES['images']['error'][$idx],'size'=>$_FILES['images']['size'][$idx]];
            $path=handle_upload($file,$config);
            if($path){
              $isPrimary = ($pdo->query("SELECT COUNT(*) FROM cat_images WHERE cat_id=$catId")->fetchColumn()==0) ? 1 : 0;
              $pdo->prepare("INSERT INTO cat_images (cat_id,image_path,is_primary,sort_order) VALUES (?,?,?,?)")->execute([$catId,$path,$isPrimary,$idx]);
            }
          }
        }
        // handle set primary via post
        if(isset($_POST['primary_image'])){
          $pdo->prepare("UPDATE cat_images SET is_primary=0 WHERE cat_id=?")->execute([$catId]);
          $pdo->prepare("UPDATE cat_images SET is_primary=1 WHERE id=? AND cat_id=?")->execute([(int)$_POST['primary_image'],$catId]);
        }
        // handle delete image
        if(isset($_POST['delete_image'])){
          $imgId=(int)$_POST['delete_image'];
          $s=$pdo->prepare("SELECT image_path FROM cat_images WHERE id=? AND cat_id=?"); $s->execute([$imgId,$catId]); $row=$s->fetch();
          if($row){
            $p=__DIR__.'/../'.$row['image_path'];
            if(is_file($p) && str_contains($p,'uploads/')) @unlink($p);
            $pdo->prepare("DELETE FROM cat_images WHERE id=?")->execute([$imgId]);
          }
        }
        flash('success','Cat saved.');
        header('Location: '.base_url('admin/cat-edit.php?id='.$catId)); exit;
      }catch(Throwable $e){ $errors[]='Save failed: '. $e->getMessage(); }
    }
  }
}

admin_header($cat?'Edit Cat':'Add Cat',$pdo);
$val = fn($k,$d='') => e($_POST[$k] ?? ($cat[$k] ?? $d));
?>
<?php if($errors): ?><div class="alert alert--error"><ul style="margin:0;padding-left:1.1rem"><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="admin-card">
<form method="post" enctype="multipart/form-data" class="form">
<?= csrf_field() ?>
<div class="form-grid">
<div class="field"><label>Name *</label><input name="name" value="<?= $val('name') ?>" required></div>
<div class="field"><label>Slug (auto if empty)</label><input name="slug" value="<?= $val('slug') ?>" placeholder="milo-ragdoll-male"></div>
</div>
<div class="form-grid">
<div class="field"><label>Breed *</label><select name="breed_id" required><option value="">Select</option><?php foreach($breeds as $b): ?><option value="<?= e($b['id']) ?>" <?= ((int)($cat['breed_id']??$_POST['breed_id']??0)==(int)$b['id'])?'selected':'' ?>><?= e($b['name']) ?></option><?php endforeach; ?></select></div>
<div class="field"><label>Sex</label><select name="sex"><option value="male" <?= $val('sex','male')==='male'?'selected':'' ?>>Male</option><option value="female" <?= $val('sex')==='female'?'selected':'' ?>>Female</option></select></div>
</div>
<div class="form-grid">
<div class="field"><label>Date of birth *</label><input type="date" name="date_of_birth" value="<?= $val('date_of_birth') ?>" required></div>
<div class="field"><label>Colour</label><input name="colour" value="<?= $val('colour') ?>"></div>
</div>
<div class="form-grid">
<div class="field"><label>Location</label><input name="location" value="<?= $val('location') ?>" placeholder="Abuja, FCT"></div>
<div class="field"><label>Price (₦)</label><input type="number" name="price" value="<?= $val('price') ?>" required></div>
</div>
<div class="form-grid">
<div class="field"><label>Availability</label><select name="availability"><option value="available" <?= $val('availability')==='available'?'selected':'' ?>>Available</option><option value="reserved" <?= $val('availability')==='reserved'?'selected':'' ?>>Reserved</option><option value="sold" <?= $val('availability')==='sold'?'selected':'' ?>>Sold</option></select></div>
<div class="field"><label>Featured?</label><label><input type="checkbox" name="is_featured" value="1" <?= ($cat['is_featured']??0)?'checked':'' ?>> Yes</label><label style="margin-left:1rem"><input type="checkbox" name="litter_trained" value="1" <?= ($cat['litter_trained']??1)?'checked':'' ?>> Litter trained</label></div>
</div>
<div class="field"><label>Personality</label><input name="personality" value="<?= $val('personality') ?>"></div>
<div class="field"><label>Vaccination status</label><input name="vaccination_status" value="<?= $val('vaccination_status') ?>"></div>
<div class="field"><label>Health notes</label><input name="health_notes" value="<?= $val('health_notes') ?>"></div>
<div class="form-grid">
<div class="field"><label>Purchase mode *</label><select name="purchase_mode" required><option value="full" <?= $val('purchase_mode','full')==='full'?'selected':'' ?>>Full payment</option><option value="deposit" <?= $val('purchase_mode')==='deposit'?'selected':'' ?>>Reservation deposit</option><option value="enquiry" <?= $val('purchase_mode')==='enquiry'?'selected':'' ?>>Enquiry only</option></select></div>
<div class="field"><label>Deposit amount (₦) — if deposit mode</label><input type="number" step="0.01" name="deposit_amount" value="<?= $val('deposit_amount') ?>" placeholder="150000"></div>
</div>
<div class="field"><label><input type="checkbox" name="is_active" value="1" <?= ($cat['is_active']??1)?'checked':'' ?>> Active (visible for purchase)</label></div>
<div class="field"><label>Parent info</label><input name="parent_info" value="<?= $val('parent_info') ?>"></div>
<div class="field"><label>Description *</label><textarea name="description" required><?= $val('description') ?></textarea></div>
<div class="field"><label>Care notes</label><textarea name="care_notes"><?= $val('care_notes') ?></textarea></div>
<div class="field"><label>Upload images (jpg/png/webp, max 4MB each)</label><input type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp" data-image-preview data-preview-target="#preview"><div id="preview" style="display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.5rem"></div></div>
<button class="btn btn--primary" type="submit">Save cat</button> <a href="<?= asset('admin/cats.php') ?>" class="btn btn--ghost">Back</a>
</form>
<?php if($cat): $imgs=$pdo->prepare("SELECT * FROM cat_images WHERE cat_id=? ORDER BY is_primary DESC, id"); $imgs->execute([$cat['id']]); $list=$imgs->fetchAll(); if($list): ?>
<h3 style="margin-top:1.2rem">Existing images</h3>
<div style="display:flex;gap:.7rem;flex-wrap:wrap">
<?php foreach($list as $im): ?>
<div style="border:1px solid rgba(28,27,25,.12);border-radius:10px;padding:.4rem;text-align:center">
<img src="<?= e($im['image_path']) ?>" width="120" height="90" style="object-fit:cover;border-radius:8px">
<div style="font-size:.82rem;margin-top:.3rem"><?= $im['is_primary']?'Primary':'' ?></div>
<form method="post" style="display:flex;gap:.3rem;justify-content:center;margin-top:.3rem">
<?= csrf_field() ?>
<input type="hidden" name="primary_image" value="<?= (int)$im['id'] ?>"><button class="btn btn--ghost btn--sm" style="padding:.3rem .6rem">Make primary</button>
</form>
<form method="post"><input type="hidden" name="delete_image" value="<?= (int)$im['id'] ?>"><?= csrf_field() ?><button class="btn btn--ghost btn--sm" onclick="return confirm('Delete image?')">Delete</button></form>
</div>
<?php endforeach; ?>
</div>
<?php endif; endif; ?>
</div>
<?php admin_footer(); ?>
