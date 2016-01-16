-- Créer une extension postgis
-- DROP TABLE alert;
-- Supprimer et créer la table

DROP TABLE IF EXISTS alert ;

CREATE TABLE alert
(
  id serial NOT NULL,
  message character varying,
  long_message character varying,
  url character varying,
  category character varying,
  emetteur character varying,
  dthr timestamp without time zone,
  geom geometry(Polygon,4326),
  
  CONSTRAINT pk_alert PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

-- Insérer un polygon (17eme arrondissement)

INSERT INTO alert (
	message, long_message, url, category, emetteur, dthr, geom)
    VALUES (
	'test',
	'Alerte long',
	'www.prefpolice.fr', 
	'danger',
	'Préfecture de Police',
	now(),
	ST_GeomFromText('Polygon ((2.27015621580967508 48.87822479667140385, 2.28484173129501222 48.9055561727135597, 2.28484173129501222 48.9055561727135597, 2.33991241436502762 48.90677996567067254, 2.33664896647939724 48.87740893470000003, 2.30564621156590688 48.86680272907170064, 2.27015621580967508 48.87822479667140385, 2.27015621580967508 48.87822479667140385))', 4326)
	);
