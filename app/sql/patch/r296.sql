CREATE OR REPLACE FUNCTION ipv4_counters() RETURNS "trigger" AS
$BODY$BEGIN
IF ('INSERT' = TG_OP) THEN
	IF (NEW.dormitory_id IS NOT NULL) THEN
		UPDATE dormitories
			SET computers_max = computers_max + 1
			WHERE id = NEW.dormitory_id;
	END IF;
ELSIF ('UPDATE' = TG_OP) THEN
	IF (NEW.dormitory_id<>OLD.dormitory_id) THEN
		IF (OLD.dormitory_id IS NOT NULL) THEN
			UPDATE dormitories
				SET computers_max = computers_max - 1
				WHERE id = OLD.dormitory_id;
		END IF;
		IF (NEW.dormitory_id IS NOT NULL) THEN
			UPDATE dormitories
				SET computers_max = computers_max + 1
				WHERE id = NEW.dormitory_id;
		END IF;
	END IF;
ELSIF ('DELETE' = TG_OP) THEN
	IF (OLD.dormitory_id IS NOT NULL) THEN
		UPDATE dormitories
			SET computers_max = computers_max - 1
			WHERE id = OLD.dormitory_id;
	END IF;
END IF;
RETURN NEW;
END;$BODY$
LANGUAGE 'plpgsql' VOLATILE;

CREATE OR REPLACE FUNCTION penalty_users() RETURNS "trigger" AS
$BODY$BEGIN
IF ('INSERT' = TG_OP) THEN
	IF NEW.type_id<>1 THEN	-- nie ostrzezenie
		UPDATE users
			SET banned = true, bans = bans + 1
			WHERE id = NEW.user_id;
	ELSE
		UPDATE users
			SET bans = bans + 1
			WHERE id = NEW.user_id;
	END IF;
ELSIF ('UPDATE' = TG_OP) THEN
	IF (OLD.active=true AND NEW.active = false) THEN
		UPDATE users
			SET banned = false
			WHERE users.id = old.user_id;
	END IF;
END IF;
RETURN NEW;
END;$BODY$
LANGUAGE 'plpgsql' VOLATILE;

CREATE OR REPLACE FUNCTION user_counters() RETURNS "trigger" AS
$BODY$
DECLARE
	change INT := 0; -- 1 = usun ze starego, 2 = dodaj do nowego, 3 = obie akcje
BEGIN
IF ('INSERT' = TG_OP) THEN
	IF (NEW.active) THEN
		change := 2;
	END IF;
ELSIF ('UPDATE' = TG_OP) THEN
	IF (OLD.location_id <> NEW.location_id) THEN
		change := 3;
	END IF;
	IF (OLD.active = false AND NEW.active = true) THEN
		change := 2;
	ELSIF (OLD.active = true AND NEW.active = false) THEN
		change := 1;
	ELSIF (OLD.active = false AND NEW.active = false) THEN
		change := 0;
	END IF;
ELSIF ('DELETE' = TG_OP) THEN
	IF (OLD.active) THEN
		UPDATE locations
			SET users_count = users_count - 1
			WHERE id = OLD.location_id;
	END IF;
END IF;
IF (1 = change OR 3 = change) THEN
	UPDATE locations
		SET users_count = users_count - 1
		WHERE id = OLD.location_id;
END IF;
IF (2 = change OR 3 = change) THEN
	UPDATE locations
		SET users_count = users_count + 1
		WHERE id = NEW.location_id;
END IF;
RETURN NEW;
END;$BODY$
LANGUAGE 'plpgsql' VOLATILE;
