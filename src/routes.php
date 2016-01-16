<?php
// Routes

/**
 * Get all alerts
 */
$app->get('/alerts', function ($request, $response, $args) {
    $stmt = $this->database->query("SELECT message, long_message, category, url, ST_AsGeoJSON(geom) as geom FROM alert");
    $alerts = $stmt->fetchAll(PDO::FETCH_OBJ);

    if(!$alerts) {
      $alerts = [];
    }
    return $response
      ->withHeader('Content-type', 'application/json')
      ->withStatus(200)
      ->write(json_encode($alerts))
    ;
});


/**
 * Create an alert
 */
$app->post('/alerts', function ($request, $response, $args) {
  $vars = json_decode($request->getBody(), false);

  $message = isset($vars->properties->message) ? $vars->properties->message : "";
  $longMessage = isset($vars->properties->long_message) ? $vars->properties->long_message : "";
  $category = isset($vars->properties->category) ? $vars->properties->category : "";
  $url = isset($vars->properties->url) ? $vars->properties->url : "";
  $geom = isset($vars->geometry) ? json_encode($vars->geometry) : NULL;

  $stmt = $this->database->prepare("INSERT INTO alert(message, long_message, category, url, geom) VALUES (:message, :long_message, :category, :url, ST_SetSRID(ST_GeomFromGeoJSON(:geom), 4326))");
  $stmt->bindParam(':message', $message);
  $stmt->bindParam(':long_message', $longMessage);
  $stmt->bindParam(':category', $category);
  $stmt->bindParam(':url', $url);
  $stmt->bindParam(':geom', $geom);
  $r = $stmt->execute();

  return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200)
    ->write(json_encode($r))
  ;
});

/**
 * Retrieve alerts from a point
 */
/*$app->get('/alerts/findByPoint', function ($request, $response, $args) {
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
});*/

/**
 * Retrieve alerts from a path
 */
/*$app->get('/alerts/findByPath', function ($request, $response, $args) {
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
});*/
