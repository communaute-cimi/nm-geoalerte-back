<?php
// Routes

/**
 * Get all alerts
 */
$app->get('/v1/alerts', function ($request, $response, $args) {
  $stmt = $this->database->query("
    SELECT
      emetteur,
      dthr,
      message,
      long_message,
      category,
      url,
      ST_AsGeoJSON(geom) as geom
    FROM alert
  ");
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
 * Get alerts from a location
 */
$app->get('/v1/alerts/{lat}/{lng}', function ($request, $response, $args) {

  $lat = $args['lat'];
  $lng = $args['lng'];
  $buffer = 200;

  $stmt = $this->database->query("
    WITH buffer AS (
      SELECT ST_Buffer(ST_MakePoint($lat, $lng)::geography, $buffer) AS geom
    )
    SELECT
      emetteur,
      dthr,
      message,
      long_message,
      category,
      url,
      ST_AsGeoJSON(alert.geom) as geom
    FROM buffer
    INNER JOIN alert ON (ST_intersects(buffer.geom, alert.geom))
  ");
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
$app->post('/v1/alerts', function ($request, $response, $args) {
  // Parsing JSON input
  $vars = json_decode($request->getBody(), false);

  $message = isset($vars->properties->message) ? $vars->properties->message : "";
  $longMessage = isset($vars->properties->long_message) ? $vars->properties->long_message : "";
  $category = isset($vars->properties->category) ? $vars->properties->category : "";
  $url = isset($vars->properties->url) ? $vars->properties->url : "";
  $source = isset($vars->properties->source) ? $vars->properties->source : "";
  // Encode geometry to store in database
  $geom = isset($vars->geometry) ? json_encode($vars->geometry) : NULL;

  $stmt = $this->database->prepare("
    INSERT INTO alert(
      message,
      long_message,
      category,
      url,
      emetteur,
      geom
    ) VALUES (
      :message,
      :long_message,
      :category,
      :url,
      :emetteur,
      ST_SetSRID(ST_GeomFromGeoJSON(:geom), 4326)
    )
  ");
  $stmt->bindParam(':message', $message);
  $stmt->bindParam(':long_message', $longMessage);
  $stmt->bindParam(':category', $category);
  $stmt->bindParam(':url', $url);
  $stmt->bindParam(':emetteur', $source);
  $stmt->bindParam(':geom', $geom);
  $r = $stmt->execute();

  return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200)
    ->write(json_encode($r))
  ;
});

/**
 * Remove an alert by id
 */
$app->delete('/v1/alerts/{id}', function ($request, $response, $args) {
  $id = $args['id'];

  $stmt = $this->database->prepare("
    DELETE FROM alert
    WHERE id=:id
  ");
  $stmt->bindParam(':id', $id);
  $r = $stmt->execute();

  return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200)
    ->write(json_encode($stmt->rowCount() > 0))
  ;
});

/**
 * Update an alert by id
 */
$app->put('/v1/alerts/{id}', function ($request, $response, $args) {
  $id = $args['id'];
  $vars = json_decode($request->getBody(), false);

  $count = 0;
  $sql = "UPDATE alert SET ";
  // Parsing JSON input
  if(isset($vars->properties->message) && $count += 1) $sql .= "message='" . $vars->properties->message . "',";
  if(isset($vars->properties->long_message) && $count += 1) $sql .= "long_message='" . $vars->properties->long_message . "',";
  if(isset($vars->properties->category) && $count += 1) $sql .= "category='" . $vars->properties->category . "',";
  if(isset($vars->properties->url) && $count += 1) $sql .= "url='" . $vars->properties->url . "',";
  if(isset($vars->properties->source) && $count += 1) $sql .= "emetteur='" . $vars->properties->source . "',";
  // Encode geometry to store in database
  if(isset($vars->geometry) && $count += 1) $sql .= "geom=ST_SetSRID(ST_GeomFromGeoJSON('" . json_encode($vars->geometry) . "'), 4326),"; // "geom='" . json_encode($vars->geometry)

  if($count) {
    $sql = substr($sql, 0, -1);
    $sql .= " WHERE id=:id";
    $stmt = $this->database->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $r = $stmt->rowCount() > 0;
  } else {
    $r = false;
  }


  return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200)
    ->write(json_encode($r))
  ;
});
