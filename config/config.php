<?php
// Read DB connection from environment with local XAMPP fallbacks
$dbHost = getenv('DB_HOST') !== false && getenv('DB_HOST') !== '' ? getenv('DB_HOST') : '127.0.0.1';
$dbPort = getenv('DB_PORT') !== false && getenv('DB_PORT') !== '' ? getenv('DB_PORT') : '3306';
$dbName = getenv('DB_NAME') !== false && getenv('DB_NAME') !== '' ? getenv('DB_NAME') : 'urban_cats';
$dbUser = getenv('DB_USER') !== false && getenv('DB_USER') !== '' ? getenv('DB_USER') : 'root';
$dbPass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
$dbSslCa = getenv('DB_SSL_CA') !== false && getenv('DB_SSL_CA') !== '' ? getenv('DB_SSL_CA') : null;

return [
    'db' => ['host'=>$dbHost,'port'=>$dbPort,'name'=>$dbName,'user'=>$dbUser,'pass'=>$dbPass,'charset'=>'utf8mb4','ssl_ca'=>$dbSslCa],
    'site' => ['name'=>'Urban Cats','base_url'=>'http://localhost/urbancat'],
    'security' => ['session_name'=>'urbancats_sess','csrf_token_name'=>'csrf_token'],
    'upload' => [
        'max_bytes'=>4*1024*1024,
        'allowed_mimes'=>['image/jpeg','image/png','image/webp'],
        'allowed_ext'=>['jpg','jpeg','png','webp'],
        'dir'=>__DIR__.'/../uploads/cats',
        'url_prefix'=>'uploads/cats',
        'thumb_w'=>600,
    ],
    'contact' => ['to_email'=>'urbankitty0@gmail.com','whatsapp_number'=>'2349122037945'],
    'paystack' => ['public_key'=>'pk_test_placeholder','secret_key'=>'sk_test_placeholder','webhook_secret'=>'whsec_placeholder','currency'=>'NGN'],
    'app' => ['env'=>'development'],
];
