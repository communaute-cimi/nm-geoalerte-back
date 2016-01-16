<?php
// Routes

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    $dsn = 'pgsql:host=localhost;dbname=postgres';
    $usr = 'postgres';
    $pwd = 'necmergitur';

    $pdo = new \Slim\PDO\Database($dsn, $usr, $pwd);

    $selectStatement = $pdo->select()
                           ->from('alert');

    $stmt = $selectStatement->execute();
    $data = $stmt->fetch();

    // Render index view
    return $this->renderer->render($response, 'index.phtml', ['args' => $args, 'db' => $data]);
});
