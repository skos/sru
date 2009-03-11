CREATE OR REPLACE FUNCTION location_counters() RETURNS "trigger" AS
$BODY$BEGIN
IF ('UPDATE' = TG_OP) THEN
	IF (OLD.computers_count <> NEW.computers_count) THEN
		UPDATE dormitories
			SET computers_count = computers_count + NEW.computers_count - OLD.computers_count
			WHERE id = NEW.dormitory_id;
	END IF;
	IF (OLD.users_count <> NEW.users_count) THEN
		UPDATE dormitories
			SET users_count = users_count + NEW.users_count - OLD.users_count
			WHERE id = NEW.dormitory_id;
	END IF;
	IF (OLD.users_max <> NEW.users_max) THEN
		UPDATE dormitories
			SET users_max = users_max + NEW.users_max - OLD.users_max
			WHERE id = NEW.dormitory_id;
	END IF;
	IF (OLD.dormitory_id <> NEW.dormitory_id) THEN
		UPDATE dormitories
			SET users_max = users_max - NEW.users_max -- new.users_max, bo nieco wyzej juz zmodyfikowalismy users_max dla danego akademika
			WHERE id = OLD.dormitory_id;
		UPDATE dormitories
			SET users_max = users_max + NEW.users_max
			WHERE id = NEW.dormitory_id;
	END IF;
ELSIF ('INSERT' = TG_OP) THEN
	IF (NEW.users_max<>0) THEN
		UPDATE dormitories
			SET users_max = users_max + NEW.users_max
			WHERE id = NEW.dormitory_id;
	END IF;
ELSIF ('DELETE' = TG_OP) THEN
	IF (OLD.users_max<>0) THEN
		UPDATE dormitories
			SET users_max = users_max - OLD.users_max
			WHERE id = OLD.dormitory_id;
	END IF;
END IF;
RETURN NEW;
END;$BODY$
LANGUAGE 'plpgsql' VOLATILE;
