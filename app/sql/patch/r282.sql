DROP RULE delete_counter ON computers;
DROP RULE insert_counter ON computers;
DROP RULE udpate_active_off ON computers;
DROP RULE udpate_active_on ON computers;
DROP RULE udpate_counter_dec ON computers;
DROP RULE udpate_counter_inc ON computers;
DROP RULE insert_banned_on ON computers_bans;
DROP RULE insert_counter ON computers_bans;
DROP RULE update_banned_off ON computers_bans;
DROP RULE udpate_computers_counter ON locations;
DROP RULE udpate_users_counter ON locations;
DROP RULE insert_banned_on ON penalties;
DROP RULE insert_counter ON penalties;
DROP RULE update_computers_bans_off ON penalties;
DROP RULE update_banned_off ON penalties;
DROP RULE delete_counter ON users;
DROP RULE insert_counter ON users;
DROP RULE update_active_off ON users;
DROP RULE update_active_off_computer ON users;
DROP RULE update_active_on ON users;
DROP RULE update_counter_dec ON users;
DROP RULE update_counter_inc ON users;

update locations
	set	users_max =1
	where dormitory_id=13;

update locations
	set	users_count = x.count
	from (
		select	location_id,
			count(id) as count
			from users
			where active
			group by location_id
		) as x
	where x.location_id=id;
update locations
	set	computers_count = x.count
	from (
		select	location_id,
			count(id) as count
			from computers
			where active
			group by location_id
		) as x
	where x.location_id=id;
update dormitories
	set	users_max=x.max,
		users_count=x.count,
		computers_count=x.computers
	from (
		select	dormitory_id,
			sum(users_count) as count,
			sum(users_max) as max,
			sum(computers_count) as computers
			from locations
			group by dormitory_id
		) as x
	where id=x.dormitory_id;
update dormitories
	set	computers_max=x.count
	from (
		select	dormitory_id,
			count(ip)
			from ipv4s
			group by dormitory_id
		) as x
	where	id=x.dormitory_id
		and dormitory_id is not null;

CREATE OR REPLACE FUNCTION computer_counters()
  RETURNS "trigger" AS
$BODY$
DECLARE
	change INT := 0; -- 2 = dodaj w nowym, 1 = usun w starym, 3 = usun w starym i dodaj w nowym
BEGIN
IF ('INSERT' = TG_OP AND NEW.active) THEN
	change := 2;
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
ELSIF ('DELETE' = TG_OP AND OLD.active) THEN
	change := 1;
END IF;
IF (1 = change OR 3 = change) THEN
	UPDATE locations
		SET computers_count = computers_count - 1
		WHERE id = OLD.location_id;
END IF;
IF (2 = change OR 3 = change) THEN
	UPDATE locations
		SET computers_count = computers_count + 1
		WHERE id = NEW.location_id;
END IF;
RETURN NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION computer_counters() OWNER TO hrynek;
COMMENT ON FUNCTION computer_counters() IS 'modyfikuje liczniki liczace komputery';

CREATE TRIGGER computers_counters AFTER INSERT OR UPDATE OR DELETE
   ON computers FOR EACH ROW
   EXECUTE PROCEDURE public.computer_counters();

CREATE OR REPLACE FUNCTION computer_ban_computers() RETURNS trigger AS
$BODY$BEGIN
IF ('INSERT' = TG_OP) THEN
	UPDATE computers
		SET banned = true, bans = bans + 1
		WHERE id = NEW.computer_id;
ELSIF ('UPDATE' = TG_OP AND OLD.active = true AND NEW.active = false AND
(SELECT count(id) AS count FROM computers_bans WHERE active AND computer_id = OLD.computer_id) < 1) THEN
	UPDATE computers
		SET banned = false
		WHERE id = OLD.computer_id;
END IF;
RETURN NEW;
END;$BODY$
LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION computer_ban_computers() IS 'modyfikuje komputery, ktorych dotyczy kara';

CREATE TRIGGER computer_ban_computers AFTER INSERT OR UPDATE OR DELETE
   ON computers_bans FOR EACH ROW
   EXECUTE PROCEDURE public.computer_ban_computers();

CREATE OR REPLACE FUNCTION location_counters() RETURNS trigger AS
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
END IF;
RETURN NEW;
END;$BODY$
LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION location_counters() IS 'modyfikuje liczniki uzytkownikow i komputerow';

CREATE TRIGGER locations_counters AFTER INSERT OR UPDATE OR DELETE
   ON locations FOR EACH ROW
   EXECUTE PROCEDURE public.location_counters();

CREATE OR REPLACE FUNCTION penalty_users() RETURNS trigger AS
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
ELSIF ('UPDATE' = TG_OP AND OLD.active=true AND NEW.active = false) THEN
	UPDATE users
		SET banned = false
		WHERE users.id = old.user_id;
END IF;
RETURN NEW;
END;$BODY$
LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION penalty_users() IS 'modyfikuje dane uzytkownika';

CREATE OR REPLACE FUNCTION penalty_computers_bans() RETURNS trigger AS
$BODY$BEGIN
IF ('UPDATE' = TG_OP AND OLD.active = true AND NEW.active = false) THEN
	 UPDATE computers_bans
		SET active = false
		WHERE penalty_id = OLD.id;
END IF;
RETURN NEW;
END;$BODY$
LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION penalty_computers_bans() IS 'modyfikuje bany na komputery';

CREATE TRIGGER penalties_computers_bans AFTER INSERT OR UPDATE OR DELETE
   ON penalties FOR EACH ROW
   EXECUTE PROCEDURE public.penalty_computers_bans();
CREATE TRIGGER penalties_users AFTER INSERT OR UPDATE OR DELETE
   ON penalties FOR EACH ROW
   EXECUTE PROCEDURE public.penalty_users();

CREATE OR REPLACE FUNCTION user_counters() RETURNS trigger AS
$BODY$
DECLARE
	change INT := 0; -- 1 = usun ze starego, 2 = dodaj do nowego, 3 = obie akcje
BEGIN
IF ('INSERT' = TG_OP AND NEW.active) THEN
	change := 2;
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
ELSIF ('DELETE' = TG_OP AND OLD.active) THEN
	UPDATE locations
		SET users_count = users_count - 1
		WHERE id = OLD.location_id;
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
COMMENT ON FUNCTION user_counters() IS 'modyfikuje liczniki liczace uzytkownikow';

CREATE OR REPLACE FUNCTION user_computers() RETURNS trigger AS
$BODY$BEGIN
IF ('UPDATE' = TG_OP AND OLD.active=true AND NEW.active=false) THEN
	UPDATE computers
		SET	active = false,
			modified_by = new.modified_by,
			modified_at = new.modified_at,
			avail_to = new.modified_at
		WHERE user_id = NEW.id AND active = true;

END IF;
RETURN NEW;
END;$BODY$
LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION user_computers() IS 'zmienia dane komputerow';

CREATE TRIGGER users_counters AFTER INSERT OR UPDATE OR DELETE
   ON users FOR EACH ROW
   EXECUTE PROCEDURE public.user_counters();
CREATE TRIGGER users_computers AFTER INSERT OR UPDATE OR DELETE
   ON users FOR EACH ROW
   EXECUTE PROCEDURE public.user_computers();

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
ELSIF ('INSERT' = TG_OP AND NEW.users_max<>0) THEN
	UPDATE dormitories
		SET users_max = users_max + NEW.users_max
		WHERE id = NEW.dormitory_id;
ELSIF ('DELETE' = TG_OP AND OLD.users_max<>0) THEN
	UPDATE dormitories
		SET users_max = users_max - OLD.users_max
		WHERE id = OLD.dormitory_id;
END IF;
RETURN NEW;
END;$BODY$
LANGUAGE 'plpgsql' VOLATILE;

COMMENT ON FUNCTION user_update() IS 'archiwizacja danych uzytkownika';

COMMENT ON FUNCTION computer_update() IS 'archiwizacja danych komputera';

ALTER TABLE computers ALTER bans TYPE integer;

CREATE OR REPLACE FUNCTION ipv4_counters() RETURNS trigger AS
$BODY$BEGIN
IF ('INSERT' = TG_OP AND NEW.dormitory_id IS NOT NULL) THEN
	UPDATE dormitories
		SET computers_max = computers_max + 1
		WHERE id = NEW.dormitory_id;
ELSIF ('UPDATE' = TG_OP AND NEW.dormitory_id<>OLD.dormitory_id) THEN
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
ELSIF ('DELETE' = TG_OP AND OLD.dormitory_id IS NOT NULL) THEN
	UPDATE dormitories
		SET computers_max = computers_max - 1
		WHERE id = OLD.dormitory_id;
END IF;
RETURN NEW;
END;$BODY$
LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION ipv4_counters() IS 'modyfikuje liczniki ip-kow';

CREATE TRIGGER ipv4s_counters AFTER INSERT OR UPDATE OR DELETE
   ON ipv4s FOR EACH ROW
   EXECUTE PROCEDURE public.ipv4_counters();