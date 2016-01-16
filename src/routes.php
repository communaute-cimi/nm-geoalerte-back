<?php
// Routes

$app->get('/[{name}]', function ($request, $response, $args) {

    $dsn = 'pgsql:host=localhost;dbname=postgres';
    $usr = 'postgres';
    $pwd = 'necmergitur';

    $pdo = new \Slim\PDO\Database($dsn, $usr, $pwd);

    $selectStatement = $pdo->select()
                           ->from('alert');

    $stmt = $selectStatement->execute();
    $data = $stmt->fetch();

    return $response
      ->withHeader('Content-type', 'application/json')
      ->withStatus(200)
      ->write(json_encode($data))
    ;
});
