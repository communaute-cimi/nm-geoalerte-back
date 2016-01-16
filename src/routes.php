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
  $buffer = 4000;

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
      ST_AsGeoJSON(alert.geom)
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
  // Encode geometry to store in database
  $source = isset($vars->properties->source) ? $vars->properties->source : "";

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
