ALTER TABLE admins_history ADD COLUMN password_changed timestamp without time zone; -- kiedy zostało zmienione hasło - na potrzeby poprawności historii

-- Function: admin_update()

-- DROP FUNCTION admin_update();

CREATE OR REPLACE FUNCTION admin_update()
  RETURNS trigger AS
$BODY$BEGIN
if
	NEW.password!=OLD.password
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
		active_to,
		password_changed
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
		OLD.active_to,
		now()
	);
elsif
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
  LANGUAGE plpgsql VOLATILE;
COMMENT ON FUNCTION admin_update() IS 'archiwizacja informacji o adminie';