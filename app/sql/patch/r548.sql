ALTER TABLE users ADD COLUMN referral_start timestamp without time zone;
ALTER TABLE users ADD COLUMN referral_end timestamp without time zone;
COMMENT ON COLUMN users.referral_start IS 'data poczatku skierowania';
COMMENT ON COLUMN users.referral_end IS 'data konca skierowania';

ALTER TABLE users_history ADD COLUMN referral_start timestamp without time zone;
ALTER TABLE users_history ADD COLUMN referral_end timestamp without time zone;
COMMENT ON COLUMN users_history.referral_start IS 'data poczatku skierowania';
COMMENT ON COLUMN users_history.referral_end IS 'data konca skierowania';

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
	NEW.referral_end!=OLD.referral_end
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
		referral_end
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
		OLD.referral_end
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION user_update() IS 'archiwizacja danych uzytkownika';