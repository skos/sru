-- Sequence: services_type_id_seq

-- DROP SEQUENCE services_type_id_seq;

CREATE SEQUENCE services_type_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 4
  CACHE 1;

-- Sequence: services_history_id_seq

-- DROP SEQUENCE services_history_id_seq;

CREATE SEQUENCE services_history_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

-- Sequence: services_id_seq

-- DROP SEQUENCE services_id_seq;

CREATE SEQUENCE services_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

-- Table: services_type

-- DROP TABLE services_type;

CREATE TABLE services_type
(
  id bigserial NOT NULL,
  "name" character varying(255) NOT NULL, -- nazwa uslugi
  CONSTRAINT services_type_pkey PRIMARY KEY (id)
)
WITH (OIDS=FALSE);
COMMENT ON TABLE services_type IS 'dostepne uslugi';
COMMENT ON COLUMN services_type."name" IS 'nazwa uslugi';

COPY services_type (id, name) FROM stdin;
1	Konto shellowe
2	Konto pocztowe
3	Konto do bazy danych MySQL
4	Konto do bazy danych PostgreSQL
\.

-- Table: services

-- DROP TABLE services;

CREATE TABLE services
(
  id bigserial NOT NULL,
  created_at timestamp without time zone NOT NULL DEFAULT now(), -- czas utworzenia uslugi
  user_id bigint NOT NULL, -- id uzytkownika
  serv_type_id bigint NOT NULL, -- id typu/rodzaju uslugi
  active boolean DEFAULT false, -- stan uslugi, false-nieaktywna/czeka na aktywacje, true-aktywna, null-do usuniecia
  modified_by bigint,
  CONSTRAINT services_pkey PRIMARY KEY (id),
  CONSTRAINT services_serv_type_id_fkey FOREIGN KEY (serv_type_id)
      REFERENCES services_type (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT services_user_id_key UNIQUE (user_id, serv_type_id)
)
WITH (OIDS=FALSE);
COMMENT ON TABLE services IS 'uslugi uzytkownikow';
COMMENT ON COLUMN services.created_at IS 'czas utworzenia uslugi';
COMMENT ON COLUMN services.user_id IS 'id uzytkownika';
COMMENT ON COLUMN services.serv_type_id IS 'id typu/rodzaju uslugi';
COMMENT ON COLUMN services.active IS 'stan uslugi, false-nieaktywna/czeka na aktywacje, true-aktywna, null-do usuniecia';

-- Table: services_history

-- DROP TABLE services_history;

CREATE TABLE services_history
(
  id bigserial NOT NULL,
  modified_at timestamp without time zone NOT NULL DEFAULT now(), -- czas powstania tej wersji
  user_id bigint NOT NULL, -- id uzytkownika
  serv_id bigint NOT NULL, -- id uslugi
  serv_type_id bigint NOT NULL, -- id typu/rodzaju uslugi
  modified_by bigint, -- kto przydzielil usluge
  active smallint NOT NULL, -- stan uslugi
  CONSTRAINT services_history_pkey PRIMARY KEY (id),
  CONSTRAINT services_history_serv_type_id_fkey FOREIGN KEY (serv_type_id)
      REFERENCES services_type (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (OIDS=FALSE);
COMMENT ON TABLE services_history IS 'historia zmian w uslugach uzytkownika';
COMMENT ON COLUMN services_history.modified_at IS 'czas powstania tej wersji';
COMMENT ON COLUMN services_history.user_id IS 'id uzytkownika';
COMMENT ON COLUMN services_history.serv_id IS 'id uslugi';
COMMENT ON COLUMN services_history.serv_type_id IS 'id typu/rodzaju uslugi';
COMMENT ON COLUMN services_history.modified_by IS 'kto przydzielil usluge';
COMMENT ON COLUMN services_history.active IS 'stan uslugi';


-- Index: user_id

-- DROP INDEX user_id;

CREATE INDEX user_id
  ON services_history
  USING btree
  (user_id);

-- Function: user_service_create()

-- DROP FUNCTION user_service_create();

CREATE OR REPLACE FUNCTION user_service_create()
  RETURNS trigger AS
$BODY$BEGIN
	INSERT INTO services_history (
		user_id,
		serv_id,
		serv_type_id,
		modified_by,
		active
	) VALUES (
		NEW.user_id,
		NEW.id,
		NEW.serv_type_id,
		NEW.modified_by,
		'1'
	);
return NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

-- Function: user_service_update()

-- DROP FUNCTION user_service_update();

CREATE OR REPLACE FUNCTION user_service_update()
  RETURNS trigger AS
$BODY$DECLARE
	state INT; -- 2 = usluga aktywna, 3 = usluga czeka na deaktywacje, 4 = usluga usunieta

BEGIN
--IF
--	NEW.active <> OLD.active
--THEN
	IF (NEW.active = true) THEN
		state := 2;
	ELSIF (NEW.active is NULL) THEN
		state := 3;
	ELSE state := 4;
	END IF;
	INSERT INTO services_history (
		user_id,
		serv_id,
		serv_type_id,
		modified_by,
		active
	) VALUES (
		NEW.user_id,
		NEW.id,
		NEW.serv_type_id,
		NEW.modified_by,
		state
	);

	IF (state = 4) THEN 
		DELETE FROM services WHERE id = NEW.id;
	END IF;
--END IF;
RETURN NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

-- Trigger: user_service_create on services

-- DROP TRIGGER user_service_create ON services;

CREATE TRIGGER user_service_create
  AFTER INSERT
  ON services
  FOR EACH ROW
  EXECUTE PROCEDURE user_service_create();
COMMENT ON TRIGGER user_service_create ON services IS 'dodaje usluge w historii uslug';

-- Trigger: user_service_update on services

-- DROP TRIGGER user_service_update ON services;

CREATE TRIGGER user_service_update
  AFTER UPDATE
  ON services
  FOR EACH ROW
  EXECUTE PROCEDURE user_service_update();
COMMENT ON TRIGGER user_service_update ON services IS 'zapisuje zmiany w historii uslug';

-- View: services_history_view

-- DROP VIEW services_history_view;

CREATE OR REPLACE VIEW services_history_view AS 
 SELECT h.modified_at, h.user_id, h.active, t.name AS serv_name, a.id AS admin_id, a.name AS admin
   FROM services_history h
   LEFT JOIN services_type t ON t.id = h.serv_type_id
   LEFT JOIN admins a ON h.modified_by = a.id
  ORDER BY h.modified_at DESC;

COMMENT ON VIEW services_history_view IS 'widok historii uslug uzytkownika';
