<?php
require __DIR__ . '/includes/auth.php';
admin_require();
require __DIR__ . '/layout.php';
if(isset($_POST['update_status']) && verify_csrf($_POST['csrf_token']??'')){
 $id=(int)$_POST['id']; $new=$_POST['status']??'pending';
 if($new==='paid'){flash('error','Paid must come via Paystack verification.');}
 elseif(in_array($new,['pending','failed','cancelled','abandoned','refunded'])){$pdo->prepare("UPDATE orders SET status=?, updated_at=NOW() WHERE id=?")->execute([$new,$id]); flash('success','Updated');}
 header('Location: '.base_url('admin/orders.php')); exit;
}
admin_header('Orders & Payments',$pdo);
$filter=$_GET['status']??''; $where='1=1'; $params=[];
if($filter && in_array($filter,['pending','paid','failed','cancelled','abandoned','refunded'])){$where.=' AND o.status=?'; $params[]=$filter;}
$sql="SELECT o.*, cust.full_name, cust.email, GROUP_CONCAT(c.name SEPARATOR ', ') as cat_names FROM orders o JOIN customers cust ON cust.id=o.customer_id LEFT JOIN order_items oi ON oi.order_id=o.id LEFT JOIN cats c ON c.id=oi.cat_id WHERE $where GROUP BY o.id ORDER BY o.created_at DESC LIMIT 100";
$st=$pdo->prepare($sql); $st->execute($params); $orders=$st->fetchAll();
?>
<div class="admin-card" style="margin-bottom:1rem">
<form method="get" style="display:flex; gap:.6rem; flex-wrap:wrap">
<label><select name="status"><option value="">All</option><?php foreach(['pending','paid','failed','cancelled','abandoned','refunded'] as $s):?><option value="<?= e($s) ?>" <?= $filter===$s?'selected':''?>><?= e(ucfirst($s)) ?></option><?php endforeach;?></select></label>
<button class="btn btn--ghost btn--sm">Filter</button><a href="<?= asset('admin/orders.php') ?>" class="btn btn--ghost btn--sm">Clear</a><span class="pill" style="margin-left:auto"><?= count($orders) ?> orders</span>
</form>
</div>
<div class="admin-card" style="overflow:auto">
<table class="table"><tr><th>Date</th><th>Order</th><th>Cats</th><th>Customer</th><th>Amount</th><th>Status</th><th>Actions</th></tr>
<?php foreach($orders as $o): ?>
<tr>
<td><?= e($o['created_at']) ?></td>
<td><strong><?= e($o['order_reference']) ?></strong><div class="muted" style="font-size:.78rem"><?= e($o['paystack_reference']) ?></div></td>
<td><?= e($o['cat_names']??'—') ?></td>
<td><?= e($o['full_name']) ?><div class="muted" style="font-size:.78rem"><?= e($o['email']) ?></div></td>
<td><?= e(format_price((float)$o['amount'])) ?><div class="muted" style="font-size:.78rem"><?= (int)$o['amount_kobo'] ?> kobo</div></td>
<td><span class="badge <?= $o['status']=='paid'?'badge--available':($o['status']=='failed'?'badge--sold':'badge--reserved') ?>"><?= e(ucfirst($o['status'])) ?></span></td>
<td>
<form method="post" style="display:flex; gap:.3rem"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$o['id'] ?>"><select name="status" style="font-size:.82rem; padding:.3rem"><option value="pending" <?= $o['status']=='pending'?'selected':''?>>pending</option><option value="paid" <?= $o['status']=='paid'?'selected':''?>>paid</option><option value="failed" <?= $o['status']=='failed'?'selected':''?>>failed</option><option value="cancelled" <?= $o['status']=='cancelled'?'selected':''?>>cancelled</option><option value="abandoned" <?= $o['status']=='abandoned'?'selected':''?>>abandoned</option><option value="refunded" <?= $o['status']=='refunded'?'selected':''?>>refunded</option></select><button name="update_status" class="btn btn--ghost btn--sm">Update</button></form>
<div style="font-size:.78rem; margin-top:.3rem"><a href="<?= asset('admin/order-view.php?id='.$o['id']) ?>" style="text-decoration:underline">View</a></div>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php if(!$orders): ?><p class="muted">No orders yet.</p><?php endif; ?>
</div>
<div class="admin-card" style="margin-top:1rem">
<h3>Payment records</h3>
<table class="table"><tr><th>Date</th><th>Order</th><th>Reference</th><th>Amount</th><th>Status</th></tr>
<?php $pays=$pdo->query("SELECT p.*, o.order_reference FROM payments p JOIN orders o ON o.id=p.order_id ORDER BY p.created_at DESC LIMIT 50")->fetchAll(); foreach($pays as $p): ?><tr><td><?= e($p['created_at']) ?></td><td><?= e($p['order_reference']) ?></td><td><?= e($p['paystack_reference']) ?></td><td><?= e(format_price((float)$p['amount'])) ?></td><td><?= e($p['status']) ?></td></tr><?php endforeach; ?>
</table>
</div>
<?php admin_footer(); ?>
