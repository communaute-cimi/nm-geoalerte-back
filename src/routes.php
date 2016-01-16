<?php
// Routes

/**
 * Get all alerts
 */
$app->get('/alerts', function ($request, $response, $args) {
    $select = $this->database
      ->select()
      ->from('alert')
      ->execute()
    ;

    $alerts = $select->fetchAll();

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

  $vars = $request->getParsedBody();

  $message = isset($vars['message']) ? $vars['message'] : "";
  $longMessage = isset($vars['long_message']) ? $vars['long_message'] : "";
  $category = isset($vars['category']) ? $vars['category'] : "";
  $url = isset($vars['url']) ? $vars['url'] : "";
  $geom = isset($vars['geom']) ? $vars['geom'] : NULL;

  /* // Ne fonctionne pas ...
  $id = $this->database
    ->insert(array(
      'message',
      'long_message',
      'category',
      'url',
      'geom'
    ))
    ->into('alert')
    ->values(array(
      $message,
      $longMessage,
      $category,
      $url,
      NULL
    ))
    ->execute();*/
  $stmt = $this->database->prepare("INSERT INTO alert (id, message, long_message, category, url, geom) VALUES (DEFAULT, :message, :long_message, :category, :url, :geom)");
  $stmt->bindParam(':message', $message);
  $stmt->bindParam(':long_message', $longMessage);
  $stmt->bindParam(':category', $category);
  $stmt->bindParam(':url', $url);
  $stmt->bindParam(':geom', $geom);
  $stmt->execute();

  return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200)
    ->write(json_encode([$vars, $stmt]))
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
