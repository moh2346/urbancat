<?php
return [
    'db' => ['host'=>'127.0.0.1','name'=>'urban_cats','user'=>'root','pass'=>'','charset'=>'utf8mb4'],
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
