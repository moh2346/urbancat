<?php
$config=require __DIR__.'/../config/config.php';
$secure=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off');
session_name($config['security']['session_name']);
if(session_status()!==PHP_SESSION_ACTIVE){
 session_set_cookie_params(['lifetime'=>0,'path'=>'/','domain'=>'','secure'=>$secure,'httponly'=>true,'samesite'=>'Lax']);
 session_start();
 if(empty($_SESSION['initiated'])){session_regenerate_id(true);$_SESSION['initiated']=time();}
}
require __DIR__.'/helpers.php';
require __DIR__.'/../config/db.php';
try{$siteName=setting($pdo,'site_name','Urban Cats');}catch(Throwable $e){$siteName='Urban Cats';}
