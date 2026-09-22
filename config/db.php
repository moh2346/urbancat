<?php
$config = require __DIR__ . '/config.php';
$db=$config['db'];
$dsn=sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',$db['host'],$db['port'] ?? '3306',$db['name'],$db['charset']);
$options=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false];
// When DB_SSL_CA is provided and the file exists, enforce TLS verification
$sslCa = $db['ssl_ca'] ?? null;
if (!$sslCa || !is_file($sslCa)) {
    $envCa = getenv('DB_SSL_CA');
    if ($envCa !== false && $envCa !== '' && is_file($envCa)) $sslCa = $envCa;
}
if ($sslCa && is_file($sslCa)) {
    $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
}
try{$pdo=new PDO($dsn,$db['user'],$db['pass'],$options);}catch(PDOException $e){error_log('Database connection failed: ' . $e->getMessage());http_response_code(500);echo 'Database connection failed.';exit;}
