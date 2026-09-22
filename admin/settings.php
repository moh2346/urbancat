<?php
require __DIR__ . '/includes/auth.php';
admin_require();
require __DIR__ . '/layout.php';
if($_SERVER['REQUEST_METHOD']==='POST' && verify_csrf($_POST['csrf_token']??'')){
 foreach(['contact_email','contact_phone','whatsapp_number','whatsapp_display','address','opening_hours','announcement_text','base_url','paystack_public_key','paystack_secret_key','paystack_webhook_secret'] as $k){
  if(isset($_POST[$k])) $pdo->prepare("INSERT INTO site_settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?")->execute([$k,$_POST[$k],$_POST[$k]]);
 }
 flash('success','Settings saved'); header('Location: '.base_url('admin/settings.php')); exit;
}
admin_header('Settings',$pdo);
function s($k){ global $pdo; return e(setting($pdo,$k,'')); }
?>
<div class="admin-card">
<form method="post" class="form"><?= csrf_field() ?>
<div class="field"><label>Contact email</label><input name="contact_email" value="<?= s('contact_email') ?>"></div>
<div class="field"><label>Contact phone</label><input name="contact_phone" value="<?= s('contact_phone') ?>"></div>
<div class="form-grid"><div class="field"><label>WhatsApp number (digits)</label><input name="whatsapp_number" value="<?= s('whatsapp_number') ?>"></div><div class="field"><label>WhatsApp display</label><input name="whatsapp_display" value="<?= s('whatsapp_display') ?>"></div></div>
<div class="field"><label>Address</label><input name="address" value="<?= s('address') ?>"></div>
<div class="field"><label>Opening hours</label><input name="opening_hours" value="<?= s('opening_hours') ?>"></div>
<div class="field"><label>Announcement text</label><input name="announcement_text" value="<?= s('announcement_text') ?>"></div>
<div class="field"><label>Base URL</label><input name="base_url" value="<?= s('base_url') ?>"></div>
<h3 style="margin-top:1rem">Paystack</h3>
<p class="muted" style="font-size:.86rem">Set test keys first. Switching to live requires HTTPS and real webhook secret.</p>
<div class="field"><label>Paystack public key</label><input name="paystack_public_key" value="<?= s('paystack_public_key') ?>"></div>
<div class="field"><label>Paystack secret key</label><input type="password" name="paystack_secret_key" value="<?= s('paystack_secret_key') ?>"></div>
<div class="field"><label>Paystack webhook secret</label><input type="password" name="paystack_webhook_secret" value="<?= s('paystack_webhook_secret') ?>"></div>
<div style="padding:.8rem; background:var(--color-ivory); border:1px solid var(--color-border); border-radius:10px; font-size:.84rem">
<strong>Webhook URL:</strong> <?= e(base_url('payment/webhook.php')) ?><br>
<strong>Callback URL:</strong> <?= e(base_url('callback.php')) ?><br>
<span class="muted">Status: <?= e(setting($pdo,'paystack_secret_key','')==='sk_test_placeholder'?'Simulated (placeholder keys)':'Keys configured') ?></span>
</div>
<button class="btn btn--primary" type="submit">Save settings</button>
</form>
</div>
<?php admin_footer(); ?>
