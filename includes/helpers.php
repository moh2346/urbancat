<?php
function e(string $s): string {return htmlspecialchars($s, ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function base_url(string $p=''):string{static $b=null;if($b===null){$cfg=require __DIR__.'/../config/config.php';$b=rtrim($cfg['site']['base_url'],'/');if(empty($b)||$b==='http://localhost'){$s=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http';$h=$_SERVER['HTTP_HOST']??'localhost';$d=rtrim(dirname($_SERVER['SCRIPT_NAME']),'/');$b=$s.'://'.$h.$d;if(strpos($b,'/admin')!==false)$b=substr($b,0,strpos($b,'/admin'));} }return $b.($p?'/'.ltrim($p,'/'):'');}
function asset(string $p):string{return base_url($p);}
function setting(PDO $pdo,string $k,string $d=''):string{static $c=[];if(isset($c[$k]))return $c[$k];try{$s=$pdo->prepare('SELECT setting_value FROM site_settings WHERE setting_key=?');$s->execute([$k]);$v=$s->fetchColumn();$c[$k]=$v!==false?(string)$v:$d;return $c[$k];}catch(Throwable $e){return $d;}}
function csrf_token():string{if(session_status()!==PHP_SESSION_ACTIVE)session_start();if(empty($_SESSION['csrf_token']))$_SESSION['csrf_token']=bin2hex(random_bytes(32));return $_SESSION['csrf_token'];}
function csrf_field():string{return '<input type="hidden" name="csrf_token" value="'.e(csrf_token()).'">';}
function verify_csrf(?string $t):bool{if(session_status()!==PHP_SESSION_ACTIVE)session_start();return isset($_SESSION['csrf_token'])&&hash_equals($_SESSION['csrf_token'],(string)$t);}
function honeypot_field():string{return '<div style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden" aria-hidden="true"><label for="website">Leave empty</label><input type="text" name="website" id="website" tabindex="-1" autocomplete="off"></div>';}
function is_honeypot_filled():bool{return !empty($_POST['website']);}
function format_price(float $n):string{return '₦'.number_format($n,0);}
function format_date(string $d):string{return date('j M Y',strtotime($d));}
function age_from_dob(string $dob):string{$b=new DateTime($dob);$n=new DateTime();$df=$n->diff($b);if($df->y>0)return $df->y.' yr'.($df->y>1?'s':'').($df->m?' '.$df->m.' mo':'');if($df->m>0)return $df->m.' month'.($df->m>1?'s':'');return $df->d.' day'.($df->d!=1?'s':'');}
function slugify(string $s):string{$s=strtolower(trim($s));$s=preg_replace('/[^a-z0-9]+/','-',$s);return trim($s,'-')?:'item';}
function flash(string $k,?string $m=null){if($m!==null){$_SESSION['_flash'][$k]=$m;return;} $v=$_SESSION['_flash'][$k]??null;unset($_SESSION['_flash'][$k]);return $v;}
function sanitize_text(string $s,int $max=500):string{$s=trim(strip_tags($s));return mb_substr($s,0,$max);}
function is_valid_email(string $e):bool{return filter_var($e,FILTER_VALIDATE_EMAIL)!==false;}
function check_rate_limit(PDO $pdo,string $key,int $max=5,int $win=600):bool{try{$st=$pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address=? AND attempted_at > DATE_SUB(NOW(), INTERVAL ? SECOND) AND success=0");$st->execute([$key,$win]);return (int)$st->fetchColumn()<$max;}catch(Throwable $e){return true;}}
function log_attempt(PDO $pdo,string $ip,?string $email,int $success):void{try{$pdo->prepare("INSERT INTO login_attempts (ip_address,email,success) VALUES (?,?,?)")->execute([$ip,$email,$success]);}catch(Throwable $e){}}
function canonical_url():string{$s=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http';$h=$_SERVER['HTTP_HOST']??'localhost';$u=$_SERVER['REQUEST_URI']??'/';return $s.'://'.$h.$u;}
function customer_logged_in():bool{return !empty($_SESSION['customer_id']);}
function current_customer(PDO $pdo):?array{if(empty($_SESSION['customer_id']))return null;$st=$pdo->prepare("SELECT * FROM customers WHERE id=?");$st->execute([$_SESSION['customer_id']]);return $st->fetch()?:null;}
