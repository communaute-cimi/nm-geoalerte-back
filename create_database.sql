-- Créer une extension postgis
-- DROP TABLE alert;

CREATE TABLE alert
(
  id serial NOT NULL,
  message character varying,
  area geometry,
  long_message character varying,
  url character varying,
  category character varying,
  CONSTRAINT pk_alert PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
