CREATE OR REPLACE RULE update_active_off_computer AS
   ON UPDATE TO users
   WHERE old.active=true AND new.active=false
   DO 
UPDATE computers SET active=false, modified_by=new.modified_by, modified_at=new.modified_at where user_id=new.id and active=true;


ALTER TABLE users_history
   ADD COLUMN active boolean;

UPDATE users_history SET active=true;

ALTER TABLE users_history
   ALTER COLUMN active SET NOT NULL;

CREATE OR REPLACE FUNCTION user_update() RETURNS "trigger" AS
$BODY$BEGIN
if
	NEW.name!=OLD.name OR
	NEW.surname!=OLD.surname OR
	NEW.login!=OLD.login OR
	NEW.email!=OLD.email OR
	NEW.faculty_id!=OLD.faculty_id OR
	NEW.study_year_id!=OLD.study_year_id OR
	NEW.location_id!=OLD.location_id OR
	NEW.comment!=OLD.comment OR
	NEW.active!=OLD.active
then
	INSERT INTO users_history (
		user_id,
		name,
		surname,
		login,
		email,
		faculty_id,
		study_year_id,
		location_id,
		modified_by,
		modified_at,
		comment,
		active
	) VALUES (
		OLD.id,
		OLD.name,
		OLD.surname,
		OLD.login,
		OLD.email,
		OLD.faculty_id,
		OLD.study_year_id,
		OLD.location_id,
		OLD.modified_by,
		OLD.modified_at,
		OLD.comment,
		OLD.active
	);
end if;
return NEW;
END;$BODY$
LANGUAGE 'plpgsql' VOLATILE;

ALTER TABLE computers_history
   ADD COLUMN active boolean;

update computers_history set active=true;

ALTER TABLE computers_history
   ALTER COLUMN active SET NOT NULL;

CREATE OR REPLACE FUNCTION computer_update() RETURNS "trigger" AS
$BODY$BEGIN
if
	OLD.host!=NEW.host OR
	OLD.mac!=NEW.mac OR
	OLD.ipv4!=NEW.ipv4 OR
	OLD.user_id!=NEW.user_id OR
	OLD.location_id!=NEW.location_id OR
	OLD.avail_to!=NEW.avail_to OR
	OLD.avail_max_to!=NEW.avail_max_to OR
	OLD.comment!=NEW.comment OR
	OLD.can_admin!=NEW.can_admin OR
	OLD.active!=NEW.active
then
	INSERT INTO computers_history (
		computer_id,
		host,
		mac,
		ipv4,
		user_id,
		location_id,
		avail_to,
		avail_max_to,
		modified_by,
		modified_at,
		comment,
		can_admin,
		active
	) VALUES (
		OLD.id,
		OLD.host,
		OLD.mac,
		OLD.ipv4,
		OLD.user_id,
		OLD.location_id,
		OLD.avail_to,
		OLD.avail_max_to,
		OLD.modified_by,
		OLD.modified_at,
		OLD.comment,
		OLD.can_admin,
		OLD.active
	);
end if;
return NEW;
END;$BODY$
LANGUAGE 'plpgsql' VOLATILE;

CREATE OR REPLACE RULE update_active_off_computer AS
   ON UPDATE TO users
   WHERE old.active = true AND new.active = false
   DO 
UPDATE computers SET active = false, modified_by = new.modified_by, modified_at = new.modified_at, avail_to=new.modified_at
  WHERE computers.user_id = new.id AND computers.active = true;
