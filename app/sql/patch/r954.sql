ALTER TABLE admins ADD COLUMN modified_by bigint; -- kto modyfikowal ostanio
ALTER TABLE admins ADD COLUMN modified_at timestamp without time zone DEFAULT now(); -- kiedy ostanio modyfikowano
UPDATE admins SET modified_at = '2008-10-16';


-- DROP TABLE admins_history;

CREATE TABLE admins_history
(
  id bigserial NOT NULL,
  admin_id bigint NOT NULL,
  "login" character varying NOT NULL, -- login
  "name" character varying(255) NOT NULL, -- nazwa ekranowa - imie-ksywka-nazwisko albo nazwa bota itp.
  type_id smallint NOT NULL DEFAULT 1, -- typ administratora: lokalny, osiedlowy, centralny, bot
  phone character varying(50) NOT NULL DEFAULT ''::character varying, -- telefon prywatny
  gg character varying(20) NOT NULL DEFAULT ''::character varying, -- numer gadu-gadu
  jid character varying(100) NOT NULL DEFAULT ''::character varying, -- jabber id
  email character varying(100) NOT NULL, -- "oficjalny" email do administratora
  dormitory_id bigint, -- akademik, nie dotyczy botow i centralnych
  address character varying(255) NOT NULL DEFAULT ''::character varying, -- gdzie mieszka administrator
  active boolean NOT NULL DEFAULT true, -- czy konto jest aktywne?
  active_to timestamp without time zone,
  modified_by bigint,
  modified_at timestamp without time zone DEFAULT now(),
  CONSTRAINT admins_history_pkey PRIMARY KEY (id),
  CONSTRAINT admins_history_modified_by_fkey FOREIGN KEY (modified_by)
      REFERENCES admins (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT admins_history_admin_id_fkey FOREIGN KEY (admin_id)
      REFERENCES admins (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT
)
WITH (OIDS=FALSE);
COMMENT ON TABLE admins_history IS 'historia adminow';


-- Function: admin_update()

-- DROP FUNCTION admin_update();

CREATE OR REPLACE FUNCTION admin_update()
  RETURNS trigger AS
$BODY$BEGIN
if
	NEW."login"!=OLD."login" OR
	NEW."name"!=OLD."name" OR
	NEW.modified_by!=OLD.modified_by OR
	NEW.modified_at!=OLD.modified_at OR
	NEW.type_id!=OLD.type_id OR
	NEW.phone!=OLD.phone OR
	NEW.gg!=OLD.gg OR
	NEW.jid!=OLD.jid OR
	NEW.email!=OLD.email OR
	NEW.dormitory_id!=OLD.dormitory_id OR
	NEW.address!=OLD.address OR
	NEW.active!=OLD.active OR
	NEW.active_to!=OLD.active_to
then
	INSERT INTO admins_history (
		admin_id,
		"login",
		"name",
		modified_by,
		modified_at,
		type_id,
		phone,
		gg,
		jid,
		email,
		dormitory_id,
		address,
		active,
		active_to
	) VALUES (
		OLD.id,
		OLD."login",
		OLD."name",
		OLD.modified_by,
		OLD.modified_at,
		OLD.type_id,
		OLD.phone,
		OLD.gg,
		OLD.jid,
		OLD.email,
		OLD.dormitory_id,
		OLD.address,
		OLD.active,
		OLD.active_to
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION admin_update() IS 'archiwizacja informacji o adminie';


-- Trigger: admins_update on admins

-- DROP TRIGGER admins_update ON admins;

CREATE TRIGGER admins_update
  AFTER UPDATE
  ON admins
  FOR EACH ROW
  EXECUTE PROCEDURE admin_update();
COMMENT ON TRIGGER admins_update ON admins IS 'kopiuje dane do historii';
