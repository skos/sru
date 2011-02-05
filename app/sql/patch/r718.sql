ALTER TABLE users ADD COLUMN services_available boolean NOT NULL DEFAULT true;
ALTER TABLE users_history ADD COLUMN services_available boolean NOT NULL DEFAULT true;


-- Function: user_update()

-- DROP FUNCTION user_update();

CREATE OR REPLACE FUNCTION user_update()
  RETURNS trigger AS
$BODY$BEGIN
if
	NEW.name!=OLD.name OR
	NEW.surname!=OLD.surname OR
	NEW.login!=OLD.login OR
	NEW.email!=OLD.email OR
	(OLD.email IS NULL AND NEW.email IS NOT NULL) OR
	NEW.gg!=OLD.gg OR
	NEW.faculty_id!=OLD.faculty_id OR
	NEW.study_year_id!=OLD.study_year_id OR
	NEW.location_id!=OLD.location_id OR
	NEW.comment!=OLD.comment OR
	NEW.active!=OLD.active OR
	NEW.referral_start!=OLD.referral_start OR
	(OLD.referral_start IS NULL AND NEW.referral_start IS NOT NULL) OR
	(OLD.referral_start IS NOT NULL AND NEW.referral_start IS NULL) OR
	NEW.referral_end!=OLD.referral_end OR
	(OLD.referral_end IS NULL AND NEW.referral_end IS NOT NULL) OR
	(OLD.referral_end IS NOT NULL AND NEW.referral_end IS NULL) OR
	NEW.registry_no!=OLD.registry_no OR
	(OLD.registry_no IS NULL AND NEW.registry_no IS NOT NULL) OR
	(OLD.registry_no IS NOT NULL AND NEW.registry_no IS NULL) OR
	NEW.services_available!=OLD.services_available
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
		registry_no,
		services_available
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
		OLD.registry_no,
		OLD.services_available
	);
end if;
return NEW;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE;
COMMENT ON FUNCTION user_update() IS 'archiwizacja danych uzytkownika';
