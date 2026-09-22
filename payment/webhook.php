<?php
// Paystack webhook — verify signature, idempotent
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/paystack.php';

$raw = file_get_contents('php://input');
$sig = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? null;
$cfg = paystack_config($pdo);

if(!paystack_verify_webhook($cfg['webhook_secret'], $raw, $sig)){
  http_response_code(401);
  echo 'Invalid signature';
  exit;
}
$payload = json_decode($raw, true);
$event = $payload['event'] ?? '';
$data = $payload['data'] ?? null;
if(!$data || !isset($data['reference'])){
  http_response_code(200);
  echo 'No reference';
  exit;
}
$reference = $data['reference'];
$status = $data['status'] ?? '';
$amount = (int)($data['amount'] ?? 0);
$currency = $data['currency'] ?? 'NGN';
$gatewayResponse = $data['gateway_response'] ?? $event;

// find order
$stmt = $pdo->prepare("SELECT id, status FROM orders WHERE paystack_reference=?");
$stmt->execute([$reference]);
$order = $stmt->fetch();
if(!$order){
  http_response_code(200);
  echo 'Order not found';
  exit;
}
if($event === 'charge.success' && $status==='success'){
  finalize_order($pdo, (int)$order['id'], $reference, $amount, $currency, $gatewayResponse);
  // also log payment if not already
  $exists = $pdo->prepare("SELECT id FROM payments WHERE paystack_reference=?");
  $exists->execute([$reference]);
  if(!$exists->fetch()){
    $pdo->prepare("INSERT INTO payments (order_id,paystack_reference,amount,amount_kobo,currency,status,gateway_response,paid_at,raw_response) VALUES (?,?,?,?,?,?,?,NOW(),?)")
      ->execute([$order['id'],$reference,$amount/100,$amount,$currency,'success',$gatewayResponse,$raw]);
  }
} else if(in_array($event, ['charge.failed','charge.cancelled'])){
  $pdo->prepare("UPDATE orders SET status='failed' WHERE paystack_reference=? AND status='pending'")->execute([$reference]);
}

http_response_code(200);
echo 'Webhook processed';
