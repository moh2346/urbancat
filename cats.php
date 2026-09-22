<?php
require __DIR__.'/includes/bootstrap.php';
require __DIR__.'/includes/functions_catalog.php';
$breeds=fetch_breeds($pdo);
$q=trim($_GET['q']??''); $breed=trim($_GET['breed']??''); $sex=trim($_GET['sex']??''); $age=trim($_GET['age']??''); $location=trim($_GET['location']??''); $avail=trim($_GET['availability']??''); $min=$_GET['min']??''; $max=$_GET['max']??''; $sort=$_GET['sort']??'newest'; $page=max(1,(int)($_GET['page']??1)); $per=9;
$where=["c.is_active=1"]; $params=[];
if($q!==''){$where[]="(c.name LIKE ? OR c.colour LIKE ?)"; $params[]="%$q%"; $params[]="%$q%";}
if($breed!=='' ){$where[]="b.slug=?"; $params[]=$breed;}
if(in_array($sex,['male','female'])){$where[]="c.sex=?"; $params[]=$sex;}
if(in_array($avail,['available','reserved','sold','coming_soon'])){$where[]="c.availability=?"; $params[]=$avail;}
if($location!==''){$where[]="c.location LIKE ?"; $params[]="%$location%";}
if($min!==''&&is_numeric($min)){$where[]="c.price>=?"; $params[]=$min;}
if($max!==''&&is_numeric($max)){$where[]="c.price<=?"; $params[]=$max;}
if($age==='kitten')$where[]="c.date_of_birth > DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
elseif($age==='young')$where[]="c.date_of_birth BETWEEN DATE_SUB(CURDATE(), INTERVAL 24 MONTH) AND DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
elseif($age==='adult')$where[]="c.date_of_birth <= DATE_SUB(CURDATE(), INTERVAL 24 MONTH)";
$ws=implode(' AND ',$where);
$order=match($sort){'price_asc'=>'c.price ASC','price_desc'=>'c.price DESC','name'=>'c.name ASC',default=>'c.created_at DESC'};
$cnt=$pdo->prepare("SELECT COUNT(*) FROM cats c JOIN breeds b ON b.id=c.breed_id WHERE $ws");$cnt->execute($params);$total=(int)$cnt->fetchColumn();
$pages=max(1,(int)ceil($total/$per));$page=min($page,$pages);$off=($page-1)*$per;
$sql="SELECT c.*, b.name as breed_name FROM cats c JOIN breeds b ON b.id=c.breed_id WHERE $ws ORDER BY $order LIMIT $per OFFSET $off";
$st=$pdo->prepare($sql);$st->execute($params);$cats=$st->fetchAll();
function qs($o=[]){$p=$_GET;foreach($o as $k=>$v){if($v===''||$v===null)unset($p[$k]);else $p[$k]=$v;}return http_build_query($p);}
$pageTitle='Available Cats — Urban Cats';
?>
<!doctype html><html lang="en"><head><?php include __DIR__.'/includes/partials/head.php'; ?></head><body>
<?php include __DIR__.'/includes/partials/header.php'; ?>
<main id="main"><div class="container section">
 <div class="breadcrumbs"><a href="<?= asset('index.php') ?>">Home</a> / Available Cats</div>
 <h1 class="h2" style="font-family:var(--font-display)">Available Cats</h1>
 <form method="get" class="filters" style="margin:1rem 0">
  <div class="filters__grid">
   <div class="field"><label>Search</label><input name="q" value="<?= e($q) ?>" placeholder="Name, colour"></div>
   <div class="field"><label>Breed</label><select name="breed"><option value="">All</option><?php foreach($breeds as $b):?><option value="<?= e($b['slug']) ?>" <?= $breed===$b['slug']?'selected':'' ?>><?= e($b['name']) ?></option><?php endforeach;?></select></div>
   <div class="field"><label>Sex</label><select name="sex"><option value="">Any</option><option value="male" <?= $sex==='male'?'selected':''?>>Male</option><option value="female" <?= $sex==='female'?'selected':''?>>Female</option></select></div>
   <div class="field"><label>Age</label><select name="age"><option value="">Any</option><option value="kitten" <?= $age==='kitten'?'selected':''?>>Kitten</option><option value="young" <?= $age==='young'?'selected':''?>>Young</option><option value="adult" <?= $age==='adult'?'selected':''?>>Adult</option></select></div>
   <div class="field"><label>Availability</label><select name="availability"><option value="">All</option><option value="available" <?= $avail==='available'?'selected':''?>>Available</option><option value="reserved" <?= $avail==='reserved'?'selected':''?>>Reserved</option><option value="sold" <?= $avail==='sold'?'selected':''?>>Sold</option></select></div>
   <div class="field"><label>Min price</label><input type="number" name="min" value="<?= e($min) ?>"></div>
   <div class="field"><label>Max price</label><input type="number" name="max" value="<?= e($max) ?>"></div>
   <div class="field"><label>Sort</label><select name="sort"><option value="newest" <?= $sort==='newest'?'selected':''?>>Newest</option><option value="price_asc" <?= $sort==='price_asc'?'selected':''?>>Price low</option><option value="price_desc" <?= $sort==='price_desc'?'selected':''?>>Price high</option><option value="name" <?= $sort==='name'?'selected':''?>>Name</option></select></div>
  </div>
  <div style="display:flex; gap:.5rem; margin-top:.7rem"><button class="btn btn--primary">Apply</button><a href="<?= asset('cats.php') ?>" class="btn btn--ghost">Clear</a></div>
 </form>
 <p class="muted"><?= $total ?> results — page <?= $page ?> of <?= $pages ?></p>
 <?php if(!$cats): ?><div style="padding:2rem; text-align:center; border:1px dashed var(--color-border); border-radius:12px">No cats match. <a href="<?= asset('cats.php') ?>" style="text-decoration:underline">Clear filters</a></div>
 <?php else: ?>
 <div class="grid-3">
  <?php foreach($cats as $c): $img=primary_image($pdo,(int)$c['id']); ?>
  <article class="card"><a href="<?= asset('cat.php?slug='.urlencode($c['slug'])) ?>" class="card__media"><img src="<?= e($img) ?>" alt="<?= e($c['name']) ?>" width="400" height="320" loading="lazy"></a><div class="card__body"><div class="card__meta"><?= e($c['breed_name']) ?> • <?= e(ucfirst($c['sex'])) ?> • <?= e(age_from_dob($c['date_of_birth'])) ?></div><h3 style="font-family:var(--font-display)"><?= e($c['name']) ?></h3><p class="muted" style="font-size:.84rem; margin:0"><?= e($c['location']) ?> • <span class="badge <?= e(availability_class($c['availability'])) ?>"><?= e(format_availability($c['availability'])) ?></span></p><p class="price"><?= e(format_price((float)$c['price'])) ?></p><div class="card__actions"><a href="<?= asset('cat.php?slug='.urlencode($c['slug'])) ?>" class="btn btn--primary btn--sm" style="flex:1">View Details</a><button class="btn-fav" data-fav="<?= e($c['slug']) ?>">♡</button></div></div></article>
  <?php endforeach; ?>
 </div>
 <div class="pagination"><?php for($i=1;$i<=$pages;$i++): if($i==$page): ?><span class="is-active"><?= $i ?></span><?php else: ?><a href="?<?= e(qs(['page'=>$i])) ?>"><?= $i ?></a><?php endif; endfor; ?></div>
 <?php endif; ?>
</div></main>
<?php include __DIR__.'/includes/partials/footer.php'; ?><script type="module" src="<?= asset('assets/js/app.js') ?>"></script></body></html>
