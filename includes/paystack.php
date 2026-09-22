<?php
function paystack_config(PDO $pdo):array{$cfg=require __DIR__.'/../config/config.php';$pub=setting($pdo,'paystack_public_key',$cfg['paystack']['public_key']);$sec=setting($pdo,'paystack_secret_key',$cfg['paystack']['secret_key']);$wh=setting($pdo,'paystack_webhook_secret',$cfg['paystack']['webhook_secret']);return ['public_key'=>$pub,'secret_key'=>$sec,'webhook_secret'=>$wh,'currency'=>$cfg['paystack']['currency']??'NGN'];}
function generate_reference(string $p='UC'):string{return $p.'_'.date('Ymd').'_'.bin2hex(random_bytes(6)).time();}
function naira_to_kobo(float|int $n):int{return (int)round((float)$n*100);}
function paystack_initialize(string $secret,array $data):array{
 if(str_contains($secret,'placeholder')){$cfg=require __DIR__.'/../config/config.php';if(($cfg['app']['env']??'development')!=='production'){return ['status'=>true,'message'=>'Simulated init','data'=>['authorization_url'=>$data['callback_url'].'&sim=1','access_code'=>'sim_access_'.$data['reference'],'reference'=>$data['reference']]];}}
 $ch=curl_init('https://api.paystack.co/transaction/initialize');
 curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>json_encode($data),CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$secret,'Content-Type: application/json','Cache-Control: no-cache'],CURLOPT_TIMEOUT=>30]);
 $res=curl_exec($ch);$err=curl_error($ch);curl_close($ch);if($err)throw new RuntimeException('Paystack init error: '.$err);$j=json_decode($res,true);if(!is_array($j))throw new RuntimeException('Invalid Paystack response');return $j;
}
function paystack_verify(string $secret,string $ref):array{
 if(str_contains($secret,'placeholder')){$cfg=require __DIR__.'/../config/config.php';if(($cfg['app']['env']??'development')!=='production'){return ['status'=>true,'message'=>'Simulated verify','data'=>['reference'=>$ref,'status'=>'success','gateway_response'=>'Simulated','currency'=>'NGN','amount'=>0,'paid_at'=>date('c')]];}}
 $ch=curl_init('https://api.paystack.co/transaction/verify/'.urlencode($ref));
 curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$secret,'Cache-Control: no-cache'],CURLOPT_TIMEOUT=>30]);
 $res=curl_exec($ch);$err=curl_error($ch);curl_close($ch);if($err)throw new RuntimeException('Verify error: '.$err);$j=json_decode($res,true);if(!is_array($j))throw new RuntimeException('Invalid verify');return $j;
}
function paystack_verify_webhook(string $secret,string $raw,?string $sig):bool{
 if(str_contains($secret,'placeholder')){$cfg=require __DIR__.'/../config/config.php';if(($cfg['app']['env']??'development')!=='production')return true;return false;}
 if(!$sig)return false;$h=hash_hmac('sha512',$raw,$secret);return hash_equals($h,$sig);
}
function finalize_order(PDO $pdo,int $orderId,string $payRef,int $amountKobo,string $currency,string $gw):bool{
 $pdo->beginTransaction();
 try{
  $st=$pdo->prepare("SELECT * FROM orders WHERE id=? FOR UPDATE");$st->execute([$orderId]);$order=$st->fetch();if(!$order){$pdo->rollBack();return false;}
  if($order['status']==='paid'){$pdo->commit();return true;}
  // lock order items cats
  $items=$pdo->prepare("SELECT * FROM order_items WHERE order_id=?");$items->execute([$orderId]);$its=$items->fetchAll();
  // check amount matches
  if((int)$order['amount_kobo']!==$amountKobo || $order['currency']!==$currency){$pdo->prepare("UPDATE orders SET status='failed',updated_at=NOW() WHERE id=?")->execute([$orderId]);$pdo->commit();return false;}
  // lock each cat
  foreach($its as $it){
   $cs=$pdo->prepare("SELECT * FROM cats WHERE id=? FOR UPDATE");$cs->execute([$it['cat_id']]);$cat=$cs->fetch();
   if(!$cat || $cat['availability']==='sold' || !$cat['is_active']){$pdo->prepare("UPDATE orders SET status='failed',updated_at=NOW() WHERE id=?")->execute([$orderId]);$pdo->commit();return false;}
  }
  $pdo->prepare("UPDATE orders SET status='paid',paystack_reference=?,paid_at=NOW(),updated_at=NOW() WHERE id=?")->execute([$payRef,$orderId]);
  $ex=$pdo->prepare("SELECT id FROM payments WHERE paystack_reference=?");$ex->execute([$payRef]);
  if(!$ex->fetch()){$pdo->prepare("INSERT INTO payments (order_id,paystack_reference,amount,amount_kobo,currency,status,gateway_response,paid_at,raw_response) VALUES (?,?,?,?,?,?,?,NOW(),?)")->execute([$orderId,$payRef,$order['amount'],$amountKobo,$currency,'success',$gw,json_encode(['reference'=>$payRef])]);}
  foreach($its as $it){
   $new=$it['payment_type']==='deposit'?'reserved':'sold';
   $pdo->prepare("UPDATE cats SET availability=? WHERE id=?")->execute([$new,$it['cat_id']]);
  }
  // clear cart for customer
  $pdo->prepare("DELETE FROM cart_items WHERE cart_id IN (SELECT id FROM carts WHERE customer_id=?)")->execute([$order['customer_id']]);
  $pdo->commit();return true;
 }catch(Throwable $e){$pdo->rollBack();error_log($e->getMessage());return false;}
}
