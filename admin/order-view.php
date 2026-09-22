<?php
require __DIR__.'/includes/auth.php';
admin_require();
require __DIR__.'/layout.php';
$id=(int)($_GET['id']??0);
$st=$pdo->prepare("SELECT o.*, cust.* FROM orders o JOIN customers cust ON cust.id=o.customer_id WHERE o.id=?"); $st->execute([$id]); $o=$st->fetch();
if(!$o){flash('error','Order not found'); header('Location: '.base_url('admin/orders.php')); exit;}
$items=$pdo->prepare("SELECT oi.*, c.name as cat_name, c.slug as cat_slug, c.price as cat_price FROM order_items oi JOIN cats c ON c.id=oi.cat_id WHERE oi.order_id=?"); $items->execute([$id]); $its=$items->fetchAll();
admin_header('Order '.$o['order_reference'],$pdo);
?>
<div class="grid-2">
<div class="admin-card">
<h3>Order</h3>
<div style="display:grid; gap:.4rem; font-size:.9rem">
<div style="display:flex; justify-content:space-between"><span class="muted">Order ref</span><strong><?= e($o['order_reference']) ?></strong></div>
<div style="display:flex; justify-content:space-between"><span class="muted">Paystack ref</span><strong><?= e($o['paystack_reference']) ?></strong></div>
<div style="display:flex; justify-content:space-between"><span class="muted">Status</span><strong><?= e($o['status']) ?></strong></div>
<div style="display:flex; justify-content:space-between"><span class="muted">Amount</span><strong><?= e(format_price((float)$o['amount'])) ?> (<?= (int)$o['amount_kobo'] ?> kobo)</strong></div>
<div style="display:flex; justify-content:space-between"><span class="muted">Delivery</span><strong><?= e($o['delivery_option']) ?></strong></div>
<div style="display:flex; justify-content:space-between"><span class="muted">Created</span><strong><?= e($o['created_at']) ?></strong></div>
<?php if($o['paid_at']): ?><div style="display:flex; justify-content:space-between"><span class="muted">Paid at</span><strong><?= e($o['paid_at']) ?></strong></div><?php endif; ?>
</div>
<h3 style="margin-top:1rem">Items</h3>
<table class="table"><tr><th>Cat</th><th>Price</th><th>Type</th></tr><?php foreach($its as $it): ?><tr><td><a href="<?= asset('cat.php?slug='.urlencode($it['cat_slug'])) ?>" style="text-decoration:underline"><?= e($it['cat_name']) ?></a></td><td><?= e(format_price((float)$it['price'])) ?></td><td><?= e($it['payment_type']) ?></td></tr><?php endforeach; ?></table>
</div>
<div class="admin-card">
<h3>Customer</h3>
<div style="display:grid; gap:.4rem; font-size:.9rem">
<div style="display:flex; justify-content:space-between"><span class="muted">Name</span><strong><?= e($o['full_name']) ?></strong></div>
<div style="display:flex; justify-content:space-between"><span class="muted">Email</span><strong><?= e($o['email']) ?></strong></div>
<div style="display:flex; justify-content:space-between"><span class="muted">Phone</span><strong><?= e($o['phone']) ?></strong></div>
<div style="display:flex; justify-content:space-between"><span class="muted">Address</span><strong><?= e($o['address']) ?></strong></div>
<div style="display:flex; justify-content:space-between"><span class="muted">City</span><strong><?= e($o['city']) ?></strong></div>
<div style="display:flex; justify-content:space-between"><span class="muted">State</span><strong><?= e($o['state']) ?></strong></div>
</div>
<?php if($o['customer_message']): ?><p class="muted" style="margin-top:.6rem"><?= nl2br(e($o['customer_message'])) ?></p><?php endif; ?>
<h3 style="margin-top:1rem">Payments log</h3>
<table class="table"><?php $pays=$pdo->prepare("SELECT * FROM payments WHERE order_id=?"); $pays->execute([$id]); foreach($pays->fetchAll() as $p): ?><tr><td><?= e($p['created_at']) ?></td><td><?= e($p['paystack_reference']) ?></td><td><?= e($p['status']) ?></td><td><?= e($p['gateway_response']) ?></td></tr><?php endforeach; ?></table>
</div>
</div>
<div style="margin-top:1rem"><a href="<?= asset('admin/orders.php') ?>" class="btn btn--ghost">Back</a></div>
<?php admin_footer(); ?>
