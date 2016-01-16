<?php
//require_once 'vendor/autoload.php';
// Routes

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    /*$dsn = 'mysql:host=localhost;dbname=postgres;charset=utf8';
    $usr = 'postgres';
    $pwd = 'necmergitur';

    $pdo = new \Slim\PDO\Database($dsn, $usr, $pwd);

    // SELECT * FROM users WHERE id = ?
    $selectStatement = $pdo->select()
                           ->from('alert');;

    $stmt = $selectStatement->execute();
    $data = $stmt->fetch();*/

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
