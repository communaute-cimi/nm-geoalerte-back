<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);


require_once 'vendor/autoload.php';
$dsn = 'mysql:host=localhost;dbname=postgres;charset=utf8';
$usr = 'postgres';
$pwd = 'necmergitur';

$pdo = new \Slim\PDO\Database($dsn, $usr, $pwd);

// SELECT * FROM users WHERE id = ?
$selectStatement = $pdo->select()
                       ->from('users')
                       ->where('id', '=', 1234);
$stmt = $selectStatement->execute();
$data = $stmt->fetch();
