-- Table: penalties_history

-- DROP TABLE penalties_history;

CREATE TABLE penalties_history
(
  id bigserial NOT NULL,
  penalty_id bigint NOT NULL,
  end_at timestamp without time zone NOT NULL,
  "comment" text,
  modified_by bigint,
  reason text NOT NULL,
  modified_at timestamp without time zone,
  amnesty_after timestamp without time zone,
  CONSTRAINT penalties_history_pkey PRIMARY KEY (id),
  CONSTRAINT penalties_history_modified_by_fkey FOREIGN KEY (modified_by)
      REFERENCES admins (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT penalties_history_penalty_id_fkey FOREIGN KEY (penalty_id)
      REFERENCES penalties (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT
)
WITH (OIDS=FALSE);
ALTER TABLE penalties_history OWNER TO postgres;
COMMENT ON TABLE penalties_history IS 'historia kar nalozonych na uzytkownikow';


-- Function: penalty_update()

-- DROP FUNCTION penalty_update();

CREATE OR REPLACE FUNCTION penalty_update()
  RETURNS trigger AS
$BODY$BEGIN
if
	NEW.end_at!=OLD.end_at OR
	NEW."comment"!=OLD."comment" OR
	NEW.modified_by!=OLD.modified_by OR
	NEW.reason!=OLD.reason OR
	NEW.modified_at!=OLD.modified_at OR
	NEW.amnesty_after!=OLD.amnesty_after
then
	INSERT INTO penalties_history (
		penalty_id,
		end_at,
		comment,
		modified_by,
		reason,
		modified_at,
		amnesty_after
	) VALUES (
		OLD.id,
		OLD.end_at,
		OLD.comment,
		OLD.modified_by,
		OLD.reason,
		OLD.modified_at,
		OLD.amnesty_after
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE
  COST 100;
ALTER FUNCTION penalty_update() OWNER TO postgres;
COMMENT ON FUNCTION penalty_update() IS 'archiwizacja informacji o karze';


-- Trigger: penalties_update on penalties

-- DROP TRIGGER penalties_update ON penalties;

CREATE TRIGGER penalties_update
  AFTER UPDATE
  ON penalties
  FOR EACH ROW
  EXECUTE PROCEDURE penalty_update();
COMMENT ON TRIGGER penalties_update ON penalties IS 'kopiuje dane do historii';