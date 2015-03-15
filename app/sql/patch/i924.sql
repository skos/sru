CREATE TABLE fw_exception_applications (
id bigserial NOT NULL,
user_id bigint NOT NULL,
comment text NOT NULL,
skos_opinion boolean,
skos_comment text,
skos_opinion_at timestamp with time zone,
skos_opinion_by bigint,
sspg_opinion boolean,
sspg_comment text,
sspg_opinion_at timestamp with time zone,
valid_to timestamp with time zone,

CONSTRAINT fw_exception_applications_pkey PRIMARY KEY (id),
CONSTRAINT fw_exception_applications_user_id_fkey FOREIGN KEY (user_id)
REFERENCES users (id)
ON UPDATE CASCADE ON DELETE CASCADE,
CONSTRAINT fw_exception_applications_opinion_by_fkey FOREIGN KEY (skos_opinion_by)
REFERENCES admins (id)
ON UPDATE CASCADE ON DELETE RESTRICT
);
CREATE INDEX fw_exception_applications_valid_to_idx ON fw_exception_applications(valid_to);
COMMENT ON COLUMN fw_exception_applications.sspg_opinion IS 'opinia SSPG';
COMMENT ON COLUMN fw_exception_applications.skos_opinion IS 'opinia SKOS';
COMMENT ON COLUMN fw_exception_applications.valid_to IS 'waznosc wniosku (i stworzonych wyjatkow)';
COMMENT ON TABLE fw_exception_applications IS 'wnioski o wyjatki w firewallu';

CREATE TABLE fw_exceptions (
id bigserial NOT NULL,
computer_id bigint NOT NULL,
port int NOT NULL,
comment text,
active boolean NOT NULL,
fw_exception_application_id bigint,
modified_by bigint,
modified_at timestamp with time zone NOT NULL DEFAULT now(),

CONSTRAINT fw_exceptions_pkey PRIMARY KEY (id),
CONSTRAINT fw_exceptions_computer_id_fkey FOREIGN KEY (computer_id)
REFERENCES computers (id)
ON UPDATE CASCADE ON DELETE CASCADE,
CONSTRAINT fw_exceptions_modified_by_fkey FOREIGN KEY (modified_by)
REFERENCES admins (id)
ON UPDATE CASCADE ON DELETE RESTRICT
);
COMMENT ON TABLE fw_exceptions IS 'wyjatki w firewallu';

CREATE UNIQUE INDEX fw_exceptions_computer_port_key
  ON fw_exceptions
  USING btree
  (computer_id, port, active)
  WHERE active = true;

CREATE TABLE fw_exceptions_history (
id bigserial NOT NULL,
fw_exception_id bigint NOT NULL,
comment text,
active boolean NOT NULL,
modified_by bigint,
modified_at timestamp with time zone NOT NULL DEFAULT now(),

CONSTRAINT fw_exceptions_history_pkey PRIMARY KEY (id),
CONSTRAINT fw_exceptions_history_fw_exception_id_fkey FOREIGN KEY (fw_exception_id)
REFERENCES fw_exceptions (id)
ON UPDATE CASCADE ON DELETE CASCADE,
CONSTRAINT fw_exceptions_history_modified_by_fkey FOREIGN KEY (modified_by)
REFERENCES admins (id)
ON UPDATE CASCADE ON DELETE RESTRICT
);
COMMENT ON TABLE fw_exceptions_history IS 'historia wyjatkow w firewallu';

CREATE OR REPLACE FUNCTION fw_exception_update()
  RETURNS trigger AS
$BODY$BEGIN
if
	NEW.comment!=OLD.comment OR
	(OLD.comment IS NOT NULL AND NEW.comment IS NULL) OR
	(OLD.comment IS NULL AND NEW.comment IS NOT NULL) OR
	NEW.active!=OLD.active
then
	INSERT INTO fw_exceptions_history (
		fw_exception_id,
		modified_by,
		modified_at,
		active,
		comment
	) VALUES (
		OLD.id,
		OLD.modified_by,
		OLD.modified_at,
		OLD.active,
		OLD.comment
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION fw_exception_update() IS 'archiwizacja wyjatku w firewallu';

CREATE TRIGGER fw_exceptions_update
  AFTER UPDATE
  ON fw_exceptions
  FOR EACH ROW
  EXECUTE PROCEDURE fw_exception_update();
COMMENT ON TRIGGER fw_exceptions_update ON fw_exceptions IS 'kopiuje dane do historii';