<?php
// Routes

/**
 * Get all alerts
 */
$app->get('/alerts/get', function ($request, $response, $args) {
    $statement = $this->database
      ->select()
      ->from('alert')
      ->execute()
    ;

    $data = $statement->fetch();

    return $response
      ->withHeader('Content-type', 'application/json')
      ->withStatus(200)
      ->write(json_encode($data))
    ;
});

/**
 * Retrieve alerts from a point
 */
$app->get('/alerts/findByPoint', function ($request, $response, $args) {
    $lat = $args['lat'];
    $lon = $args['lon'];

    // TODO UPDATE QUERY TO FILTER WITH LAT & LON
    $statement = $this->database
      ->select()
      ->from('alert')
      ->execute()
    ;

    $data = $statement->fetch();

    return $response
      ->withHeader('Content-type', 'application/json')
      ->withStatus(200)
      ->write(json_encode($data))
    ;
});

/**
 * Retrieve alerts from a path
 */
$app->get('/alerts/findByPath', function ($request, $response, $args) {
    $start_lat = $args['start_lat'];
    $start_lon = $args['start_lon'];
    $dest_lat = $args['dest_lat'];
    $dest_lon = $args['dest_lon'];

    // TODO UPDATE QUERY TO FILTER WITH PATH COORDINATES
    $statement = $this->database
      ->select()
      ->from('alert')
      ->execute()
    ;

    $data = $statement->fetch();

    return $response
      ->withHeader('Content-type', 'application/json')
      ->withStatus(200)
      ->write(json_encode($data))
    ;
});
