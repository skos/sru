ALTER TABLE locations ADD COLUMN modified_by bigint; -- kto modyfikowal ostanio
ALTER TABLE locations ADD COLUMN modified_at timestamp without time zone DEFAULT now(); -- kiedy ostanio modyfikowano


-- DROP TABLE locations_history;

CREATE TABLE locations_history
(
  id bigserial NOT NULL,
  location_id bigint NOT NULL,
  "comment" text NOT NULL,
  modified_by bigint,
  modified_at timestamp without time zone DEFAULT now(),
  CONSTRAINT locations_history_pkey PRIMARY KEY (id),
  CONSTRAINT locations_history_modified_by_fkey FOREIGN KEY (modified_by)
      REFERENCES locations (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT locations_history_location_id_fkey FOREIGN KEY (location_id)
      REFERENCES locations (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT
)
WITH (OIDS=FALSE);
COMMENT ON TABLE locations_history IS 'historia lokacji';


-- Function: location_update()

-- DROP FUNCTION location_update();

CREATE OR REPLACE FUNCTION location_update()
  RETURNS trigger AS
$BODY$BEGIN
if
	NEW."comment"!=OLD."comment"
then
	INSERT INTO locations_history (
		location_id,
		"comment",
		modified_by,
		modified_at
	) VALUES (
		OLD.id,
		OLD."comment",
		OLD.modified_by,
		OLD.modified_at
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION location_update() IS 'archiwizacja informacji o lokacji';


-- Trigger: locations_update on locations

-- DROP TRIGGER locations_update ON locations;

CREATE TRIGGER locations_update
  AFTER UPDATE
  ON locations
  FOR EACH ROW
  EXECUTE PROCEDURE location_update();
COMMENT ON TRIGGER locations_update ON locations IS 'kopiuje dane do historii';
