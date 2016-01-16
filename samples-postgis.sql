-- Supprimer et créer la table

DROP TABLE alert ;

CREATE TABLE alert
(
  id serial NOT NULL,
  message character varying,
  long_message character varying,
  url character varying,
  category character varying,
  geom geometry(Polygon,4326),
  CONSTRAINT pk_alert PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

-- Insérer un polygon (17eme arrondissement)

INSERT INTO alert (
	id, message, geom, long_message, url, category)
    VALUES (
	1, 
	'test', 
	ST_GeomFromText('Polygon ((2.27015621580967508 48.87822479667140385, 2.28484173129501222 48.9055561727135597, 2.28484173129501222 48.9055561727135597, 2.33991241436502762 48.90677996567067254, 2.33664896647939724 48.87740893470000003, 2.30564621156590688 48.86680272907170064, 2.27015621580967508 48.87822479667140385, 2.27015621580967508 48.87822479667140385))', 4326), 
	'Alerte long', 
	'www.prefpolice.fr', 'danger');

-- Jointure entre un point et les événements
WITH point AS (
    SELECT ST_GeometryFromText('POINT(2.30238 48.88679)', 4326) as geom
)
select ST_AsGeoJSON(alert.geom) as geojson
FROM point
INNER JOIN alert ON (ST_CONTAINS(alert.geom, point.geom))
where alert.id = 1

SELECT ST_GeomFromGeoJSON('{"type":"Point","coordinates":[-48.23456,20.12345]}');

ALTER TABLE alert
	ALTER COLUMN geom
	SET DATA TYPE geometry(Polygon,4326)
	USING ST_Transform(geom, 4326);
-- Reprojection
USING ST_Transform(geom, 2154);


-- Intersection KO
WITH buffer AS (
    SELECT ST_Buffer(ST_MakePoint(1.30238,48.88679)::geography, 4000) AS geom
), poly AS (
    SELECT ST_GeomFromText('Polygon ((2.27015621580967508 48.87822479667140385, 2.28484173129501222 48.9055561727135597, 2.28484173129501222 48.9055561727135597, 2.33991241436502762 48.90677996567067254, 2.33664896647939724 48.87740893470000003, 2.30564621156590688 48.86680272907170064, 2.27015621580967508 48.87822479667140385, 2.27015621580967508 48.87822479667140385))', 4326) AS geom
)
SELECT * FROM buffer 
	INNER JOIN poly ON (ST_intersects(buffer.geom, poly.geom))

-- Intersection OK

WITH buffer AS (
    SELECT ST_Buffer(ST_MakePoint(2.30238, 48.88679)::geography, 4000) AS geom
), poly AS (
    SELECT ST_GeomFromText('Polygon ((2.27015621580967508 48.87822479667140385, 2.28484173129501222 48.9055561727135597, 2.28484173129501222 48.9055561727135597, 2.33991241436502762 48.90677996567067254, 2.33664896647939724 48.87740893470000003, 2.30564621156590688 48.86680272907170064, 2.27015621580967508 48.87822479667140385, 2.27015621580967508 48.87822479667140385))', 4326) AS geom
)
SELECT * FROM buffer 
	INNER JOIN poly ON (ST_intersects(buffer.geom, poly.geom))


WITH buffer AS (
    SELECT ST_Buffer(ST_MakePoint(2.30238, 48.88679)::geography, 4000) AS geom
)
SELECT * FROM buffer 
	INNER JOIN alert ON (ST_intersects(buffer.geom, alert.geom))
