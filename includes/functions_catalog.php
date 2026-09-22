<?php
function fetch_breeds(PDO $pdo):array{return $pdo->query("SELECT * FROM breeds ORDER BY name")->fetchAll();}
function get_breed_by_slug(PDO $pdo,string $slug):?array{$s=$pdo->prepare("SELECT * FROM breeds WHERE slug=?");$s->execute([$slug]);$r=$s->fetch();return $r?:null;}
function get_cat_by_slug(PDO $pdo,string $slug):?array{$s=$pdo->prepare("SELECT c.*, b.name as breed_name, b.slug as breed_slug FROM cats c JOIN breeds b ON b.id=c.breed_id WHERE c.slug=?");$s->execute([$slug]);$r=$s->fetch();return $r?:null;}
function cat_images(PDO $pdo,int $id):array{$s=$pdo->prepare("SELECT * FROM cat_images WHERE cat_id=? ORDER BY is_primary DESC, sort_order ASC, id ASC");$s->execute([$id]);return $s->fetchAll();}
function primary_image(PDO $pdo,int $id):string{$imgs=cat_images($pdo,$id);if($imgs)return $imgs[0]['image_path'];return 'assets/images/cats/placeholder.svg';}
function format_availability(string $a):string{return match($a){'available'=>'Available','reserved'=>'Reserved','sold'=>'Sold','coming_soon'=>'Coming Soon',default=>$a};}
function availability_class(string $a):string{return 'badge--'.str_replace('_','-',$a);}
function get_cart_id(PDO $pdo):int{
 $sid=session_id();
 $cid=$_SESSION['customer_id']??null;
 if($cid){
   $st=$pdo->prepare("SELECT id FROM carts WHERE customer_id=?");$st->execute([$cid]);$r=$st->fetchColumn();if($r)return (int)$r;
   $pdo->prepare("INSERT INTO carts (customer_id,session_id) VALUES (?,?)")->execute([$cid,$sid]);return (int)$pdo->lastInsertId();
 }
 $st=$pdo->prepare("SELECT id FROM carts WHERE session_id=? AND customer_id IS NULL");$st->execute([$sid]);$r=$st->fetchColumn();if($r)return (int)$r;
 $pdo->prepare("INSERT INTO carts (session_id) VALUES (?)")->execute([$sid]);return (int)$pdo->lastInsertId();
}
function cart_count(PDO $pdo):int{$cid=get_cart_id($pdo);$st=$pdo->prepare("SELECT COUNT(*) FROM cart_items WHERE cart_id=?");$st->execute([$cid]);return (int)$st->fetchColumn();}
function fav_count(PDO $pdo):int{if(!customer_logged_in()){ $f=json_decode($_COOKIE['uc_favs']??'[]',true);return is_array($f)?count($f):0;} $cust=current_customer($pdo);if(!$cust)return 0;$st=$pdo->prepare("SELECT COUNT(*) FROM favourites WHERE customer_id=?");$st->execute([$cust['id']]);return (int)$st->fetchColumn();}
