<?php
require __DIR__.'/includes/bootstrap.php';
require __DIR__.'/includes/paystack.php';
$ref=trim($_GET['reference']?? $_GET['trxref']?? '');
if(!$ref){flash('error','Missing reference'); header('Location: '.base_url('payment-failed.php')); exit;}
if(isset($_GET['sim'])){
 $o=$pdo->prepare("SELECT * FROM orders WHERE paystack_reference=?"); $o->execute([$ref]); $ord=$o->fetch();
 if($ord && $ord['status']!='paid'){ finalize_order($pdo,(int)$ord['id'],$ref,(int)$ord['amount_kobo'],$ord['currency'],'Simulated'); }
 header('Location: '.base_url('payment-success.php?reference='.urlencode($ref))); exit;
}
try{
 $cfg=paystack_config($pdo);
 $ver=paystack_verify($cfg['secret_key'],$ref);
 if(empty($ver['status'])||!$ver['status']){ $pdo->prepare("UPDATE orders SET status='failed' WHERE paystack_reference=? AND status='pending'")->execute([$ref]); flash('error','Verification failed'); header('Location: '.base_url('payment-failed.php?reference='.urlencode($ref))); exit;}
 $d=$ver['data']??null; if(!$d || ($d['status']??'')!=='success'){ $pdo->prepare("UPDATE orders SET status='failed' WHERE paystack_reference=? AND status='pending'")->execute([$ref]); header('Location: '.base_url('payment-failed.php?reference='.urlencode($ref))); exit;}
 $currency=$d['currency']??'NGN'; $amount=(int)($d['amount']??0); $gw=$d['gateway_response']??'success';
 $st=$pdo->prepare("SELECT id FROM orders WHERE paystack_reference=?"); $st->execute([$ref]); $ord=$st->fetch(); if(!$ord){header('Location: '.base_url('payment-failed.php')); exit;}
 $ok=finalize_order($pdo,(int)$ord['id'],$ref,$amount,$currency,$gw);
 if($ok) header('Location: '.base_url('payment-success.php?reference='.urlencode($ref))); else {flash('error','Amount mismatch or cat sold'); header('Location: '.base_url('payment-failed.php?reference='.urlencode($ref)));}
 exit;
}catch(Throwable $e){ error_log($e->getMessage()); flash('error','Error verifying'); header('Location: '.base_url('payment-failed.php?reference='.urlencode($ref))); exit;}
