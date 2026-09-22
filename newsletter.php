<?php
require __DIR__.'/includes/bootstrap.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: '.base_url('index.php')); exit;}
if(is_honeypot_filled()||!verify_csrf($_POST['csrf_token']??'')){flash('error','Invalid session'); header('Location: '.base_url('index.php')); exit;}
$email=trim($_POST['email']??''); if(!is_valid_email($email)){flash('error','Valid email required'); header('Location: '.base_url('index.php')); exit;}
try{$pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?) ON DUPLICATE KEY UPDATE unsubscribed_at=NULL")->execute([$email]); flash('success','Subscribed!');}catch(Throwable $e){flash('error','Already subscribed or error');}
header('Location: '.($_SERVER['HTTP_REFERER']?? base_url('index.php'))); exit;
