ALTER TABLE users ADD COLUMN registry_no integer;
ALTER TABLE users ADD COLUMN update_needed boolean NOT NULL DEFAULT true;
COMMENT ON COLUMN users.registry_no IS 'nr indeksu';
COMMENT ON COLUMN users.update_needed IS 'dane wymagaja uaktualnienia?';

ALTER TABLE users ALTER COLUMN email DROP NOT NULL;
ALTER TABLE users_history ALTER COLUMN email DROP NOT NULL;

ALTER TABLE users_history ADD COLUMN registry_no integer;
COMMENT ON COLUMN users_history.registry_no IS 'nr indeksu';

CREATE UNIQUE INDEX users_registry_no_key
  ON users
  USING btree
  (registry_no);

CREATE OR REPLACE FUNCTION user_update()
  RETURNS trigger AS
$BODY$BEGIN
if
	NEW.name!=OLD.name OR
	NEW.surname!=OLD.surname OR
	NEW.login!=OLD.login OR
	NEW.email!=OLD.email OR
	NEW.gg!=OLD.gg OR
	NEW.faculty_id!=OLD.faculty_id OR
	NEW.study_year_id!=OLD.study_year_id OR
	NEW.location_id!=OLD.location_id OR
	NEW.comment!=OLD.comment OR
	NEW.active!=OLD.active OR
	NEW.referral_start!=OLD.referral_start OR
	NEW.referral_end!=OLD.referral_end OR
	NEW.registry_no!=OLD.registry_no
then
	INSERT INTO users_history (
		user_id,
		name,
		surname,
		login,
		email,
		gg,
		faculty_id,
		study_year_id,
		location_id,
		modified_by,
		modified_at,
		comment,
		active,
		referral_start,
		referral_end,
		registry_no
	) VALUES (
		OLD.id,
		OLD.name,
		OLD.surname,
		OLD.login,
		OLD.email,
		OLD.gg,
		OLD.faculty_id,
		OLD.study_year_id,
		OLD.location_id,
		OLD.modified_by,
		OLD.modified_at,
		OLD.comment,
		OLD.active,
		OLD.referral_start,
		OLD.referral_end,
		OLD.registry_no
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION user_update() IS 'archiwizacja danych uzytkownika';