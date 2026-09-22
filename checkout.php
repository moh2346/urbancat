<?php
require __DIR__.'/includes/bootstrap.php';
require __DIR__.'/includes/functions_catalog.php';
require __DIR__.'/includes/paystack.php';
if(!customer_logged_in()){flash('error','Please login to checkout'); header('Location: '.base_url('login.php')); exit;}
$cid=get_cart_id($pdo);
$items=$pdo->prepare("SELECT c.*, b.name as breed_name FROM cart_items ci JOIN cats c ON c.id=ci.cat_id JOIN breeds b ON b.id=c.breed_id WHERE ci.cart_id=?"); $items->execute([$cid]); $list=$items->fetchAll();
if(!$list){flash('error','Cart empty'); header('Location: '.base_url('cart.php')); exit;}
// validate all still available and calculate total server-side
$total=0; $valid=true; foreach($list as $it){ if($it['availability']!='available' || !$it['is_active']) $valid=false; $price = $it['purchase_mode']=='deposit'? (float)($it['deposit_amount']?? $it['price']*0.3) : (float)$it['price']; $total+=$price; if($it['purchase_mode']=='enquiry') $valid=false; }
if(!$valid){flash('error','Some cats no longer available or enquiry-only. Update cart.'); header('Location: '.base_url('cart.php')); exit;}
$totalKobo=naira_to_kobo($total);
$cust=current_customer($pdo);
$errors=[]; if($_SERVER['REQUEST_METHOD']==='POST'){
 if(is_honeypot_filled()) $errors[]='Spam.'; if(!verify_csrf($_POST['csrf_token']??'')) $errors[]='Invalid session';
 $data=['address'=>sanitize_text($_POST['address']??'',255),'city'=>sanitize_text($_POST['city']??'',100),'state'=>sanitize_text($_POST['state']??'',100),'delivery_option'=>$_POST['delivery_option']??'delivery','message'=>sanitize_text($_POST['message']??'',2000),'consent'=>isset($_POST['consent'])?1:0];
 if($data['address']==='') $errors[]='Address required'; if($data['city']==='') $errors[]='City required'; if($data['state']==='') $errors[]='State required'; if(!in_array($data['delivery_option'],['collection','delivery'])) $errors[]='Delivery invalid'; if(!$data['consent']) $errors[]='Consent required';
 if(!$errors){
  try{
   $pdo->beginTransaction();
   // re-lock all cats
   foreach($list as $it){ $chk=$pdo->prepare("SELECT availability, is_active FROM cats WHERE id=? FOR UPDATE"); $chk->execute([$it['id']]); $f=$chk->fetch(); if(!$f || $f['availability']!='available' || !$f['is_active']){$pdo->rollBack(); $errors[]='Cat no longer available: '.e($it['name']); break;} }
   if(!$errors){
    $pdo->prepare("UPDATE customers SET address=?, city=?, state=? WHERE id=?")->execute([$data['address'],$data['city'],$data['state'],$cust['id']]);
    $oref=generate_reference('UC'); $pref=generate_reference('PS');
    $pdo->prepare("INSERT INTO orders (order_reference,customer_id,amount,amount_kobo,currency,status,paystack_reference,delivery_option,customer_message,consent,expires_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")->execute([$oref,$cust['id'],$total,$totalKobo,'NGN','pending',$pref,$data['delivery_option'],$data['message'],$data['consent'],date('Y-m-d H:i:s', strtotime('+48 hours'))]);
    $oid=(int)$pdo->lastInsertId();
    foreach($list as $it){ $price = $it['purchase_mode']=='deposit'? (float)($it['deposit_amount']?? $it['price']*0.3) : (float)$it['price']; $ptype=$it['purchase_mode']=='deposit'?'deposit':'full'; $pdo->prepare("INSERT INTO order_items (order_id,cat_id,price,payment_type) VALUES (?,?,?,?)")->execute([$oid,$it['id'],$price,$ptype]); }
    $pdo->commit();
    $psCfg=paystack_config($pdo); $cb=base_url('callback.php?reference='.urlencode($pref));
    $init=paystack_initialize($psCfg['secret_key'],['email'=>$cust['email'],'amount'=>$totalKobo,'reference'=>$pref,'callback_url'=>$cb,'metadata'=>['order_reference'=>$oref,'customer'=>$cust['full_name']]]);
    if(empty($init['status'])||!$init['status']){$pdo->prepare("UPDATE orders SET status='failed' WHERE id=?")->execute([$oid]); throw new RuntimeException($init['message']??'Paystack init failed');}
    $auth=$init['data']['authorization_url']??null; $ac=$init['data']['access_code']??null; if($ac) $pdo->prepare("UPDATE orders SET paystack_access_code=? WHERE id=?")->execute([$ac,$oid]); if(!$auth) throw new RuntimeException('No auth URL'); header('Location: '.$auth); exit;
   }
  }catch(Throwable $e){ if($pdo->inTransaction()) $pdo->rollBack(); $errors[]='Checkout failed: '.$e->getMessage(); }
 }
}
$pageTitle='Checkout — Urban Cats'; $psCfg=paystack_config($pdo);
?>
<!doctype html><html lang="en"><head><?php include __DIR__.'/includes/partials/head.php'; ?></head><body>
<?php include __DIR__.'/includes/partials/header.php'; ?>
<main id="main"><div class="container section"><div class="breadcrumbs"><a href="<?= asset('index.php') ?>">Home</a> / Checkout</div><h1 class="h2" style="font-family:var(--font-display)">Checkout</h1><p class="lead">Final scheduling confirmed by Urban Cats after payment.</p>
<?php if($errors): ?><div class="alert alert--error"><ul><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach;?></ul></div><?php endif;?>
<div class="detail-grid"><div>
<form method="post" class="form" novalidate><?= csrf_field().honeypot_field() ?>
<div class="card" style="padding:1.2rem"><h3>Delivery details</h3>
<div class="field"><label>Address *</label><input name="address" value="<?= e($_POST['address']?? $cust['address']??'') ?>" required></div>
<div class="form-grid"><div class="field"><label>City *</label><input name="city" value="<?= e($_POST['city']?? $cust['city']??'') ?>" required></div><div class="field"><label>State *</label><input name="state" value="<?= e($_POST['state']?? $cust['state']??'') ?>" required></div></div>
<div class="field"><label>Collection or Delivery *</label><select name="delivery_option" required><option value="delivery" <?= ($_POST['delivery_option']??'delivery')=='delivery'?'selected':'' ?>>Delivery</option><option value="collection" <?= ($_POST['delivery_option']??'')=='collection'?'selected':'' ?>>Collection in Gwarinpa</option></select></div>
<div class="field"><label>Message</label><textarea name="message"><?= e($_POST['message']??'') ?></textarea></div>
<label><input type="checkbox" name="consent" value="1" required> I agree to Terms *</label>
</div>
<button class="btn btn--primary btn--block" style="margin-top:1rem">Pay <?= e(format_price($total)) ?> via Paystack</button>
</form>
</div><div>
<div class="card" style="padding:1.2rem"><h3>Order summary</h3><?php foreach($list as $it): $p=$it['purchase_mode']=='deposit'? (float)($it['deposit_amount']?? $it['price']*0.3) : (float)$it['price']; ?><div style="display:flex; justify-content:space-between; padding:.4rem 0; border-bottom:1px solid var(--color-border)"><span><?= e($it['name']) ?> <span class="muted" style="font-size:.82rem">(<?= e($it['purchase_mode']) ?>)</span></span><strong><?= e(format_price($p)) ?></strong></div><?php endforeach; ?><div style="display:flex; justify-content:space-between; font-weight:700; margin-top:.6rem"><span>Total</span><span><?= e(format_price($total)) ?></span></div><p class="muted" style="font-size:.82rem; margin-top:.6rem">Test card: 4084084084084081. Amount verified server-side.</p></div>
</div></div></div></main>
<?php include __DIR__.'/includes/partials/footer.php'; ?><script type="module" src="<?= asset('assets/js/app.js') ?>"></script></body></html>
