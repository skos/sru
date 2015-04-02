--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- TOC entry 408 (class 2612 OID 17301)
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: -
--

CREATE PROCEDURAL LANGUAGE plpgsql;


SET search_path = public, pg_catalog;

--
-- TOC entry 19 (class 1255 OID 17302)
-- Dependencies: 408 3
-- Name: computer_add(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION computer_add() RETURNS trigger
    LANGUAGE plpgsql
    AS $$DECLARE
	penalties_cursor CURSOR FOR
		SELECT id FROM penalties p WHERE p.user_id = NEW.user_id AND active = true AND type_id = 3;
	penalty penalties%ROWTYPE;
	computer_ban computers_bans%ROWTYPE;

BEGIN
IF NEW.banned = true THEN
	RETURN NEW;
END IF;
IF ('INSERT' = TG_OP OR ('UPDATE' = TG_OP AND NEW.active = true)) THEN
	OPEN penalties_cursor;
	LOOP
		FETCH penalties_cursor INTO penalty;
		EXIT WHEN NOT FOUND;
		SELECT id INTO computer_ban FROM computers_bans WHERE computer_id = NEW.id AND penalty_id = penalty.id;
		IF NOT FOUND THEN
			INSERT INTO computers_bans(computer_id, penalty_id, active) 
				VALUES (NEW.id, penalty.id, true);
		END IF;
	END LOOP;
	CLOSE penalties_cursor;
END IF;
RETURN NEW;
END;$$;


--
-- TOC entry 2158 (class 0 OID 0)
-- Dependencies: 19
-- Name: FUNCTION computer_add(); Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON FUNCTION computer_add() IS 'naklada kare na nowy komputer, jesli uzytkownik jest zbanowany';


--
-- TOC entry 20 (class 1255 OID 17303)
-- Dependencies: 3 408
-- Name: computer_ban_computers(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION computer_ban_computers() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
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
END;$$;


--
-- TOC entry 2159 (class 0 OID 0)
-- Dependencies: 20
-- Name: FUNCTION computer_ban_computers(); Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON FUNCTION computer_ban_computers() IS 'modyfikuje komputery, ktorych dotyczy kara';


--
-- TOC entry 21 (class 1255 OID 17304)
-- Dependencies: 408 3
-- Name: computer_counters(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION computer_counters() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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
END;$$;


--
-- TOC entry 2160 (class 0 OID 0)
-- Dependencies: 21
-- Name: FUNCTION computer_counters(); Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON FUNCTION computer_counters() IS 'modyfikuje liczniki liczace komputery';


--
-- TOC entry 33 (class 1255 OID 17305)
-- Dependencies: 3 408
-- Name: computer_update(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION computer_update() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
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
	OLD.active!=NEW.active OR
	OLD.type_id!=NEW.type_id
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
		active,
		type_id
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
		OLD.active,
		OLD.type_id
	);
end if;
return NEW;
END;$$;


--
-- TOC entry 2161 (class 0 OID 0)
-- Dependencies: 33
-- Name: FUNCTION computer_update(); Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON FUNCTION computer_update() IS 'archiwizacja danych komputera';


--
-- TOC entry 22 (class 1255 OID 17306)
-- Dependencies: 408 3
-- Name: ipv4_counters(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION ipv4_counters() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
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
END;$$;


--
-- TOC entry 2162 (class 0 OID 0)
-- Dependencies: 22
-- Name: FUNCTION ipv4_counters(); Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON FUNCTION ipv4_counters() IS 'modyfikuje liczniki ip-kow';


--
-- TOC entry 24 (class 1255 OID 17307)
-- Dependencies: 3 408
-- Name: location_counters(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION location_counters() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
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
END;$$;


--
-- TOC entry 2163 (class 0 OID 0)
-- Dependencies: 24
-- Name: FUNCTION location_counters(); Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON FUNCTION location_counters() IS 'modyfikuje liczniki uzytkownikow i komputerow';


--
-- TOC entry 25 (class 1255 OID 17308)
-- Dependencies: 408 3
-- Name: penalty_computers_bans(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION penalty_computers_bans() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
IF ('UPDATE' = TG_OP) THEN
IF (OLD.active = true AND NEW.active = false) THEN
	 UPDATE computers_bans
		SET active = false
		WHERE penalty_id = OLD.id;
END IF;
END IF;
RETURN NEW;
END;$$;


--
-- TOC entry 2164 (class 0 OID 0)
-- Dependencies: 25
-- Name: FUNCTION penalty_computers_bans(); Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON FUNCTION penalty_computers_bans() IS 'modyfikuje bany na komputery';


--
-- TOC entry 26 (class 1255 OID 17309)
-- Dependencies: 3 408
-- Name: penalty_update(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION penalty_update() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
if
	NEW.end_at!=OLD.end_at OR
	NEW."comment"!=OLD."comment" OR
	NEW.modified_by!=OLD.modified_by OR
	NEW.reason!=OLD.reason OR
	NEW.modified_at!=OLD.modified_at OR
	NEW.amnesty_after!=OLD.amnesty_after
then
	INSERT INTO penalties_history (
		penalty_id,
		end_at,
		comment,
		modified_by,
		reason,
		modified_at,
		amnesty_after
	) VALUES (
		OLD.id,
		OLD.end_at,
		OLD.comment,
		OLD.modified_by,
		OLD.reason,
		OLD.modified_at,
		OLD.amnesty_after
	);
end if;
return NEW;
END;$$;


--
-- TOC entry 2165 (class 0 OID 0)
-- Dependencies: 26
-- Name: FUNCTION penalty_update(); Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON FUNCTION penalty_update() IS 'archiwizacja informacji o karze';


--
-- TOC entry 27 (class 1255 OID 17310)
-- Dependencies: 408 3
-- Name: penalty_users(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION penalty_users() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
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
	IF (OLD.active=true AND NEW.active = false AND (SELECT COUNT(*) from computers where banned='true' and user_id = old.user_id) = 0) THEN
		UPDATE users
			SET banned = false
			WHERE users.id = old.user_id;
	END IF;
END IF;
RETURN NEW;
END;$$;


--
-- TOC entry 2166 (class 0 OID 0)
-- Dependencies: 27
-- Name: FUNCTION penalty_users(); Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON FUNCTION penalty_users() IS 'modyfikuje dane uzytkownika';


--
-- TOC entry 28 (class 1255 OID 17311)
-- Dependencies: 408 3
-- Name: remove_bans(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION remove_bans() RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
        updated INT;
BEGIN
        UPDATE penalties SET active = 'false' WHERE active = 'true' and end_at < now();

        GET DIAGNOSTICS updated = ROW_COUNT;
        RETURN updated;
END;
$$;


--
-- TOC entry 29 (class 1255 OID 17312)
-- Dependencies: 3 408
-- Name: user_computers(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION user_computers() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
IF ('UPDATE' = TG_OP) THEN
IF (OLD.active=true AND NEW.active=false) THEN
	UPDATE computers
		SET	active = false,
			can_admin = false,
			modified_by = new.modified_by,
			modified_at = new.modified_at,
			avail_to = new.modified_at
		WHERE user_id = NEW.id AND active = true;

END IF;
END IF;
RETURN NEW;
END;$$;


--
-- TOC entry 2167 (class 0 OID 0)
-- Dependencies: 29
-- Name: FUNCTION user_computers(); Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON FUNCTION user_computers() IS 'zmienia dane komputerow';


--
-- TOC entry 30 (class 1255 OID 17313)
-- Dependencies: 3 408
-- Name: user_counters(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION user_counters() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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
END;$$;


--
-- TOC entry 2168 (class 0 OID 0)
-- Dependencies: 30
-- Name: FUNCTION user_counters(); Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON FUNCTION user_counters() IS 'modyfikuje liczniki liczace uzytkownikow';


--
-- TOC entry 31 (class 1255 OID 17314)
-- Dependencies: 408 3
-- Name: user_service_create(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION user_service_create() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
	INSERT INTO services_history (
		user_id,
		serv_id,
		serv_type_id,
		modified_by,
		active
	) VALUES (
		NEW.user_id,
		NEW.id,
		NEW.serv_type_id,
		NEW.modified_by,
		'1'
	);
return NEW;
END;$$;


--
-- TOC entry 32 (class 1255 OID 17315)
-- Dependencies: 3 408
-- Name: user_service_update(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION user_service_update() RETURNS trigger
    LANGUAGE plpgsql
    AS $$DECLARE
	state INT; -- 2 = usluga aktywna, 3 = usluga czeka na deaktywacje, 4 = usluga usunieta

BEGIN
IF (NEW.active = true) THEN
	state := 2;
ELSIF (NEW.active is NULL) THEN
	state := 3;
ELSE state := 4;
END IF;
INSERT INTO services_history (
	user_id,
	serv_id,
	serv_type_id,
	modified_by,
	active
) VALUES (
	NEW.user_id,
	NEW.id,
	NEW.serv_type_id,
	NEW.modified_by,
	state
);

IF (state = 4) THEN 
	DELETE FROM services WHERE id = NEW.id;
END IF;
RETURN NEW;
END;$$;


--
-- TOC entry 23 (class 1255 OID 17402)
-- Dependencies: 3 408
-- Name: user_services(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION user_services() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
IF ('UPDATE' = TG_OP) THEN
IF (OLD.active=true AND NEW.active=false) THEN
	UPDATE services
		SET	active = null,
			modified_by = new.modified_by
		WHERE user_id = NEW.id AND active = true;
	UPDATE services
		SET	active = false,
			modified_by = new.modified_by
		WHERE user_id = NEW.id AND active = false;

END IF;
END IF;
RETURN NEW;
END;$$;


--
-- TOC entry 2169 (class 0 OID 0)
-- Dependencies: 23
-- Name: FUNCTION user_services(); Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON FUNCTION user_services() IS 'zmienia dane uslug';


--
-- TOC entry 34 (class 1255 OID 17316)
-- Dependencies: 408 3
-- Name: user_update(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION user_update() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
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
$$;


--
-- TOC entry 2170 (class 0 OID 0)
-- Dependencies: 34
-- Name: FUNCTION user_update(); Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON FUNCTION user_update() IS 'archiwizacja danych uzytkownika';


--
-- TOC entry 1595 (class 1259 OID 16860)
-- Dependencies: 3
-- Name: admins_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE admins_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 1596 (class 1259 OID 16862)
-- Dependencies: 1922 1923 1924 1925 1926 1927 1928 1929 3
-- Name: admins; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE admins (
    id bigint DEFAULT nextval('admins_id_seq'::regclass) NOT NULL,
    login character varying NOT NULL,
    password character(32) NOT NULL,
    last_login_at timestamp without time zone,
    last_login_ip inet,
    name character varying(255) NOT NULL,
    type_id smallint DEFAULT 1 NOT NULL,
    phone character varying(50) DEFAULT ''::character varying NOT NULL,
    gg character varying(20) DEFAULT ''::character varying NOT NULL,
    jid character varying(100) DEFAULT ''::character varying NOT NULL,
    email character varying(100) NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    dormitory_id bigint,
    address character varying(255) DEFAULT ''::character varying NOT NULL,
    active boolean DEFAULT true NOT NULL
);


--
-- TOC entry 2171 (class 0 OID 0)
-- Dependencies: 1596
-- Name: TABLE admins; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE admins IS 'administratorzy';


--
-- TOC entry 2172 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN admins.login; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.login IS 'login';


--
-- TOC entry 2173 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN admins.password; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.password IS 'haslo zakodowane md5';


--
-- TOC entry 2174 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN admins.last_login_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.last_login_at IS 'czas ostatniego logowania';


--
-- TOC entry 2175 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN admins.last_login_ip; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.last_login_ip IS 'ip, z ktorego ostatnio sie logowal';


--
-- TOC entry 2176 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN admins.name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.name IS 'nazwa ekranowa - imie-ksywka-nazwisko albo nazwa bota itp.';


--
-- TOC entry 2177 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN admins.type_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.type_id IS 'typ administratora: lokalny, osiedlowy, centralny, bot';


--
-- TOC entry 2178 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN admins.phone; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.phone IS 'telefon prywatny';


--
-- TOC entry 2179 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN admins.gg; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.gg IS 'numer gadu-gadu';


--
-- TOC entry 2180 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN admins.jid; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.jid IS 'jabber id';


--
-- TOC entry 2181 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN admins.email; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.email IS '"oficjalny" email do administratora';


--
-- TOC entry 2182 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN admins.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.created_at IS 'czas utworzenia konta';


--
-- TOC entry 2183 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN admins.dormitory_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.dormitory_id IS 'akademik, nie dotyczy botow i centralnych';


--
-- TOC entry 2184 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN admins.address; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.address IS 'gdzie mieszka administrator';


--
-- TOC entry 2185 (class 0 OID 0)
-- Dependencies: 1596
-- Name: COLUMN admins.active; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.active IS 'czy konto jest aktywne?';


--
-- TOC entry 1642 (class 1259 OID 17456)
-- Dependencies: 3
-- Name: admins_dormitories; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE admins_dormitories (
    id bigint NOT NULL,
    admin bigint,
    dormitory bigint
);


--
-- TOC entry 2186 (class 0 OID 0)
-- Dependencies: 1642
-- Name: TABLE admins_dormitories; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE admins_dormitories IS 'Przypisania adminów do wielu akademików';


--
-- TOC entry 1640 (class 1259 OID 17452)
-- Dependencies: 3
-- Name: admins_dormitories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE admins_dormitories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1641 (class 1259 OID 17454)
-- Dependencies: 3 1642
-- Name: admins_dormitories_id_seq1; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE admins_dormitories_id_seq1
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2187 (class 0 OID 0)
-- Dependencies: 1641
-- Name: admins_dormitories_id_seq1; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE admins_dormitories_id_seq1 OWNED BY admins_dormitories.id;


--
-- TOC entry 1597 (class 1259 OID 16876)
-- Dependencies: 3
-- Name: bans_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE bans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1598 (class 1259 OID 16878)
-- Dependencies: 3
-- Name: computers_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE computers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1599 (class 1259 OID 16880)
-- Dependencies: 1930 1931 1932 1933 1934 1935 1936 1937 3
-- Name: computers; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE computers (
    id bigint DEFAULT nextval('computers_id_seq'::regclass) NOT NULL,
    host character varying(50) NOT NULL,
    mac macaddr NOT NULL,
    ipv4 inet NOT NULL,
    user_id bigint,
    location_id bigint,
    avail_to timestamp without time zone NOT NULL,
    avail_max_to timestamp without time zone NOT NULL,
    modified_by bigint,
    modified_at timestamp without time zone DEFAULT now() NOT NULL,
    comment pg_catalog.text DEFAULT ''::pg_catalog.text NOT NULL,
    active boolean DEFAULT true NOT NULL,
    type_id smallint DEFAULT 1 NOT NULL,
    bans integer DEFAULT 0 NOT NULL,
    can_admin boolean DEFAULT false NOT NULL,
    banned boolean DEFAULT false NOT NULL
);


--
-- TOC entry 2188 (class 0 OID 0)
-- Dependencies: 1599
-- Name: TABLE computers; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE computers IS 'komputery';


--
-- TOC entry 2189 (class 0 OID 0)
-- Dependencies: 1599
-- Name: COLUMN computers.host; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.host IS 'nazwa hosta';


--
-- TOC entry 2190 (class 0 OID 0)
-- Dependencies: 1599
-- Name: COLUMN computers.mac; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.mac IS 'adres mac karty sieciowej';


--
-- TOC entry 2191 (class 0 OID 0)
-- Dependencies: 1599
-- Name: COLUMN computers.ipv4; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.ipv4 IS 'adres ip';


--
-- TOC entry 2192 (class 0 OID 0)
-- Dependencies: 1599
-- Name: COLUMN computers.user_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.user_id IS 'uzytkownik, do ktorego nalezy ten komputer';


--
-- TOC entry 2193 (class 0 OID 0)
-- Dependencies: 1599
-- Name: COLUMN computers.location_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.location_id IS 'pokoj';


--
-- TOC entry 2194 (class 0 OID 0)
-- Dependencies: 1599
-- Name: COLUMN computers.avail_to; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.avail_to IS 'do kiedy jest wazna rejestracja';


--
-- TOC entry 2195 (class 0 OID 0)
-- Dependencies: 1599
-- Name: COLUMN computers.avail_max_to; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.avail_max_to IS 'do kiedy mozna sobie przedluzyc rejestracje';


--
-- TOC entry 2196 (class 0 OID 0)
-- Dependencies: 1599
-- Name: COLUMN computers.modified_by; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.modified_by IS 'kto wprowadzil te dane';


--
-- TOC entry 2197 (class 0 OID 0)
-- Dependencies: 1599
-- Name: COLUMN computers.modified_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.modified_at IS 'czas powstania tej wersji';


--
-- TOC entry 2198 (class 0 OID 0)
-- Dependencies: 1599
-- Name: COLUMN computers.comment; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.comment IS 'komentarz';


--
-- TOC entry 2199 (class 0 OID 0)
-- Dependencies: 1599
-- Name: COLUMN computers.active; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.active IS 'czy komputer ma wazna rejestracje';


--
-- TOC entry 2200 (class 0 OID 0)
-- Dependencies: 1599
-- Name: COLUMN computers.type_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.type_id IS 'typ komputera: student, administracja, organizacja, serwer itd.';


--
-- TOC entry 2201 (class 0 OID 0)
-- Dependencies: 1599
-- Name: COLUMN computers.bans; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.bans IS 'licznik banow';


--
-- TOC entry 2202 (class 0 OID 0)
-- Dependencies: 1599
-- Name: COLUMN computers.can_admin; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.can_admin IS 'komputer nalezy do administratora';


--
-- TOC entry 2203 (class 0 OID 0)
-- Dependencies: 1599
-- Name: COLUMN computers.banned; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.banned IS 'czy komputer jest aktualnie zabanowany?';


--
-- TOC entry 1638 (class 1259 OID 17424)
-- Dependencies: 3
-- Name: computers_aliases_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE computers_aliases_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1639 (class 1259 OID 17427)
-- Dependencies: 1995 1996 3
-- Name: computers_aliases; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE computers_aliases (
    id bigint DEFAULT nextval('computers_aliases_id_seq'::regclass) NOT NULL,
    computer_id bigint NOT NULL,
    host character varying(50) NOT NULL,
    is_cname boolean DEFAULT true NOT NULL
);


--
-- TOC entry 2204 (class 0 OID 0)
-- Dependencies: 1639
-- Name: COLUMN computers_aliases.computer_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_aliases.computer_id IS 'ktory komputer';


--
-- TOC entry 2205 (class 0 OID 0)
-- Dependencies: 1639
-- Name: COLUMN computers_aliases.host; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_aliases.host IS 'alias';


--
-- TOC entry 2206 (class 0 OID 0)
-- Dependencies: 1639
-- Name: COLUMN computers_aliases.is_cname; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_aliases.is_cname IS 'czy wpis CNAME czy A';


--
-- TOC entry 1600 (class 1259 OID 16894)
-- Dependencies: 3
-- Name: computers_ban_id; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE computers_ban_id
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1601 (class 1259 OID 16896)
-- Dependencies: 1938 1939 3
-- Name: computers_bans; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE computers_bans (
    id bigint DEFAULT nextval('computers_ban_id'::regclass) NOT NULL,
    computer_id bigint NOT NULL,
    penalty_id bigint NOT NULL,
    active boolean DEFAULT true NOT NULL
);


--
-- TOC entry 2207 (class 0 OID 0)
-- Dependencies: 1601
-- Name: TABLE computers_bans; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE computers_bans IS 'zbanowane komputery';


--
-- TOC entry 2208 (class 0 OID 0)
-- Dependencies: 1601
-- Name: COLUMN computers_bans.computer_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_bans.computer_id IS 'ktory komputer';


--
-- TOC entry 2209 (class 0 OID 0)
-- Dependencies: 1601
-- Name: COLUMN computers_bans.penalty_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_bans.penalty_id IS 'ktora kara';


--
-- TOC entry 1602 (class 1259 OID 16901)
-- Dependencies: 3
-- Name: computers_history_computer_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE computers_history_computer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1603 (class 1259 OID 16903)
-- Dependencies: 3
-- Name: computers_history_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE computers_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1604 (class 1259 OID 16905)
-- Dependencies: 1940 1941 1942 1943 3
-- Name: computers_history; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE computers_history (
    computer_id bigint DEFAULT nextval('computers_history_computer_id_seq'::regclass) NOT NULL,
    host character varying(50) NOT NULL,
    mac macaddr NOT NULL,
    ipv4 inet NOT NULL,
    user_id bigint,
    location_id bigint,
    avail_to timestamp without time zone NOT NULL,
    modified_by bigint,
    modified_at timestamp without time zone DEFAULT now() NOT NULL,
    comment pg_catalog.text NOT NULL,
    can_admin boolean DEFAULT false NOT NULL,
    id bigint DEFAULT nextval('computers_history_id_seq'::regclass) NOT NULL,
    avail_max_to timestamp without time zone NOT NULL,
    active boolean NOT NULL,
    type_id smallint
);


--
-- TOC entry 2210 (class 0 OID 0)
-- Dependencies: 1604
-- Name: TABLE computers_history; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE computers_history IS 'historia zmian danych komputerow';


--
-- TOC entry 2211 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN computers_history.host; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.host IS 'nazwa hosta';


--
-- TOC entry 2212 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN computers_history.mac; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.mac IS 'adres mac karty sieciowej';


--
-- TOC entry 2213 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN computers_history.ipv4; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.ipv4 IS 'adres ip';


--
-- TOC entry 2214 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN computers_history.user_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.user_id IS 'uzytkownik, do ktorego nalezy ten komputer';


--
-- TOC entry 2215 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN computers_history.location_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.location_id IS 'pokoj';


--
-- TOC entry 2216 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN computers_history.avail_to; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.avail_to IS 'do kiedy jest wazna rejestracja';


--
-- TOC entry 2217 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN computers_history.modified_by; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.modified_by IS 'kto wprowadzil te dane';


--
-- TOC entry 2218 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN computers_history.modified_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.modified_at IS 'czas powstania tej wersji';


--
-- TOC entry 2219 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN computers_history.comment; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.comment IS 'komentarz';


--
-- TOC entry 2220 (class 0 OID 0)
-- Dependencies: 1604
-- Name: COLUMN computers_history.can_admin; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.can_admin IS 'komputer nalezy do administratora';


--
-- TOC entry 1605 (class 1259 OID 16915)
-- Dependencies: 3
-- Name: dormitories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE dormitories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1606 (class 1259 OID 16917)
-- Dependencies: 1944 1945 1946 1947 1948 3
-- Name: dormitories; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE dormitories (
    id bigint DEFAULT nextval('dormitories_id_seq'::regclass) NOT NULL,
    name character varying(255) NOT NULL,
    alias character varying(10) NOT NULL,
    users_count integer DEFAULT 0 NOT NULL,
    computers_count integer DEFAULT 0 NOT NULL,
    users_max integer DEFAULT 0 NOT NULL,
    computers_max integer DEFAULT 0 NOT NULL,
    name_en character varying(255)
);


--
-- TOC entry 2221 (class 0 OID 0)
-- Dependencies: 1606
-- Name: TABLE dormitories; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE dormitories IS 'akademiki';


--
-- TOC entry 2222 (class 0 OID 0)
-- Dependencies: 1606
-- Name: COLUMN dormitories.name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN dormitories.name IS 'pelna nazwa';


--
-- TOC entry 2223 (class 0 OID 0)
-- Dependencies: 1606
-- Name: COLUMN dormitories.alias; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN dormitories.alias IS 'skrot, uzywany do budowy url-i';


--
-- TOC entry 2224 (class 0 OID 0)
-- Dependencies: 1606
-- Name: COLUMN dormitories.users_count; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN dormitories.users_count IS 'ilosc zarejestrowanych uzytkownikow';


--
-- TOC entry 2225 (class 0 OID 0)
-- Dependencies: 1606
-- Name: COLUMN dormitories.computers_count; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN dormitories.computers_count IS 'ilosc zarejestrowanych komputerow';


--
-- TOC entry 1607 (class 1259 OID 16925)
-- Dependencies: 3
-- Name: faulties_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE faulties_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1608 (class 1259 OID 16927)
-- Dependencies: 1949 1950 1951 3
-- Name: faculties; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE faculties (
    id bigint DEFAULT nextval('faulties_id_seq'::regclass) NOT NULL,
    name character varying(255) NOT NULL,
    alias character varying(10) NOT NULL,
    users_count integer DEFAULT 0 NOT NULL,
    computers_count integer DEFAULT 0 NOT NULL,
    name_en character varying(255)
);


--
-- TOC entry 2226 (class 0 OID 0)
-- Dependencies: 1608
-- Name: TABLE faculties; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE faculties IS 'wydzialy';


--
-- TOC entry 2227 (class 0 OID 0)
-- Dependencies: 1608
-- Name: COLUMN faculties.name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN faculties.name IS 'nazwa wydzialu';


--
-- TOC entry 2228 (class 0 OID 0)
-- Dependencies: 1608
-- Name: COLUMN faculties.alias; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN faculties.alias IS 'skrot nazwy, uzywany do budowy url-i';


--
-- TOC entry 2229 (class 0 OID 0)
-- Dependencies: 1608
-- Name: COLUMN faculties.users_count; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN faculties.users_count IS 'ilosc zarejestrowanych uzytkownikow';


--
-- TOC entry 2230 (class 0 OID 0)
-- Dependencies: 1608
-- Name: COLUMN faculties.computers_count; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN faculties.computers_count IS 'ilosc zarejestrowanych komputerow';


--
-- TOC entry 1609 (class 1259 OID 16933)
-- Dependencies: 3
-- Name: ipv4s; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ipv4s (
    ip inet NOT NULL,
    dormitory_id bigint
);


--
-- TOC entry 2231 (class 0 OID 0)
-- Dependencies: 1609
-- Name: TABLE ipv4s; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE ipv4s IS 'dostepne adresy ip';


--
-- TOC entry 2232 (class 0 OID 0)
-- Dependencies: 1609
-- Name: COLUMN ipv4s.ip; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN ipv4s.ip IS 'adres ip';


--
-- TOC entry 2233 (class 0 OID 0)
-- Dependencies: 1609
-- Name: COLUMN ipv4s.dormitory_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN ipv4s.dormitory_id IS 'akademik';


--
-- TOC entry 1643 (class 1259 OID 17772)
-- Dependencies: 1998 1999 2000 3
-- Name: lanstats; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE lanstats (
    "time" timestamp without time zone DEFAULT now() NOT NULL,
    ip inet NOT NULL,
    bytes bigint DEFAULT 0 NOT NULL,
    packets bigint DEFAULT 0 NOT NULL
);


--
-- TOC entry 1610 (class 1259 OID 16939)
-- Dependencies: 3
-- Name: locations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE locations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1611 (class 1259 OID 16941)
-- Dependencies: 1952 1953 1954 1955 3
-- Name: locations; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE locations (
    id bigint DEFAULT nextval('locations_id_seq'::regclass) NOT NULL,
    alias character varying(10) NOT NULL,
    comment pg_catalog.text DEFAULT ''::pg_catalog.text NOT NULL,
    users_count integer DEFAULT 0 NOT NULL,
    computers_count integer DEFAULT 0 NOT NULL,
    dormitory_id bigint NOT NULL,
    users_max smallint
);


--
-- TOC entry 2235 (class 0 OID 0)
-- Dependencies: 1611
-- Name: TABLE locations; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE locations IS 'pokoje';


--
-- TOC entry 2236 (class 0 OID 0)
-- Dependencies: 1611
-- Name: COLUMN locations.alias; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN locations.alias IS 'unikalna nazwa pokoju, uzywana do budowy url-i';


--
-- TOC entry 2237 (class 0 OID 0)
-- Dependencies: 1611
-- Name: COLUMN locations.comment; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN locations.comment IS 'komentarz do pokoju';


--
-- TOC entry 2238 (class 0 OID 0)
-- Dependencies: 1611
-- Name: COLUMN locations.users_count; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN locations.users_count IS 'ilosc zarejestrowanych uzytkownikow';


--
-- TOC entry 2239 (class 0 OID 0)
-- Dependencies: 1611
-- Name: COLUMN locations.computers_count; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN locations.computers_count IS 'ilosc zarejestrowanych komputerow';


--
-- TOC entry 2240 (class 0 OID 0)
-- Dependencies: 1611
-- Name: COLUMN locations.dormitory_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN locations.dormitory_id IS 'akademik, w ktorym znajduje sie pokoj';


--
-- TOC entry 2241 (class 0 OID 0)
-- Dependencies: 1611
-- Name: COLUMN locations.users_max; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN locations.users_max IS 'maksymalna ilosc osob w pokoju';


--
-- TOC entry 1612 (class 1259 OID 16951)
-- Dependencies: 1956 1957 1958 1959 1960 3
-- Name: penalties; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE penalties (
    id bigint DEFAULT nextval('bans_id_seq'::regclass) NOT NULL,
    created_by bigint NOT NULL,
    user_id bigint,
    type_id smallint DEFAULT 1 NOT NULL,
    start_at timestamp without time zone DEFAULT now() NOT NULL,
    end_at timestamp without time zone NOT NULL,
    comment pg_catalog.text,
    modified_by bigint,
    reason pg_catalog.text NOT NULL,
    modified_at timestamp without time zone,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    amnesty_at timestamp without time zone,
    amnesty_after timestamp without time zone,
    amnesty_by bigint,
    active boolean DEFAULT true NOT NULL,
    template_id smallint
);


--
-- TOC entry 2242 (class 0 OID 0)
-- Dependencies: 1612
-- Name: TABLE penalties; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE penalties IS 'kary nalozone na uzytkownikow';


--
-- TOC entry 2243 (class 0 OID 0)
-- Dependencies: 1612
-- Name: COLUMN penalties.created_by; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalties.created_by IS 'tworca kary';


--
-- TOC entry 2244 (class 0 OID 0)
-- Dependencies: 1612
-- Name: COLUMN penalties.user_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalties.user_id IS 'ukarany uzytkownik';


--
-- TOC entry 2245 (class 0 OID 0)
-- Dependencies: 1612
-- Name: COLUMN penalties.type_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalties.type_id IS 'typ kary: ostrzezenie, wszystko, komputer itp';


--
-- TOC entry 2246 (class 0 OID 0)
-- Dependencies: 1612
-- Name: COLUMN penalties.start_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalties.start_at IS 'od kiedy kara obowiazuje';


--
-- TOC entry 2247 (class 0 OID 0)
-- Dependencies: 1612
-- Name: COLUMN penalties.end_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalties.end_at IS 'do kiedy kara obowiazuje';


--
-- TOC entry 2248 (class 0 OID 0)
-- Dependencies: 1612
-- Name: COLUMN penalties.comment; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalties.comment IS 'komentarze administratorow';


--
-- TOC entry 2249 (class 0 OID 0)
-- Dependencies: 1612
-- Name: COLUMN penalties.modified_by; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalties.modified_by IS 'kto modyfikowal ostanio';


--
-- TOC entry 2250 (class 0 OID 0)
-- Dependencies: 1612
-- Name: COLUMN penalties.reason; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalties.reason IS 'powod(dla uzytkownika)';


--
-- TOC entry 2251 (class 0 OID 0)
-- Dependencies: 1612
-- Name: COLUMN penalties.modified_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalties.modified_at IS 'kiedy ostanio modyfikowano';


--
-- TOC entry 2252 (class 0 OID 0)
-- Dependencies: 1612
-- Name: COLUMN penalties.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalties.created_at IS 'kiedy utworzono kare';


--
-- TOC entry 2253 (class 0 OID 0)
-- Dependencies: 1612
-- Name: COLUMN penalties.amnesty_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalties.amnesty_at IS 'kiedy udzielono amnesti';


--
-- TOC entry 2254 (class 0 OID 0)
-- Dependencies: 1612
-- Name: COLUMN penalties.amnesty_after; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalties.amnesty_after IS 'od kiedy dopuszcza sie mozliwosc amnesti';


--
-- TOC entry 2255 (class 0 OID 0)
-- Dependencies: 1612
-- Name: COLUMN penalties.amnesty_by; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalties.amnesty_by IS 'kto udzielil amnesti';


--
-- TOC entry 2256 (class 0 OID 0)
-- Dependencies: 1612
-- Name: COLUMN penalties.template_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalties.template_id IS 'id szablonu, na podstawie ktorego zostala utworzona kara';


--
-- TOC entry 1613 (class 1259 OID 16962)
-- Dependencies: 3
-- Name: penalties_history; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE penalties_history (
    id bigint NOT NULL,
    penalty_id bigint NOT NULL,
    end_at timestamp without time zone NOT NULL,
    comment pg_catalog.text,
    modified_by bigint,
    reason pg_catalog.text NOT NULL,
    modified_at timestamp without time zone,
    amnesty_after timestamp without time zone
);


--
-- TOC entry 2257 (class 0 OID 0)
-- Dependencies: 1613
-- Name: TABLE penalties_history; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE penalties_history IS 'historia kar nalozonych na uzytkownikow';


--
-- TOC entry 1614 (class 1259 OID 16968)
-- Dependencies: 1613 3
-- Name: penalties_history_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE penalties_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2258 (class 0 OID 0)
-- Dependencies: 1614
-- Name: penalties_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE penalties_history_id_seq OWNED BY penalties_history.id;


--
-- TOC entry 1615 (class 1259 OID 16970)
-- Dependencies: 3
-- Name: penalty_templates_id; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE penalty_templates_id
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1616 (class 1259 OID 16972)
-- Dependencies: 1962 1963 1964 1965 1966 3
-- Name: penalty_templates; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE penalty_templates (
    id bigint DEFAULT nextval('penalty_templates_id'::regclass) NOT NULL,
    title character varying(100) NOT NULL,
    description pg_catalog.text,
    penalty_type_id smallint NOT NULL,
    duration integer NOT NULL,
    amnesty_after integer DEFAULT 0 NOT NULL,
    reason pg_catalog.text DEFAULT ''::pg_catalog.text NOT NULL,
    reason_en pg_catalog.text DEFAULT ''::pg_catalog.text NOT NULL,
    active boolean DEFAULT true NOT NULL
);


--
-- TOC entry 2259 (class 0 OID 0)
-- Dependencies: 1616
-- Name: TABLE penalty_templates; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE penalty_templates IS 'szablony kar';


--
-- TOC entry 2260 (class 0 OID 0)
-- Dependencies: 1616
-- Name: COLUMN penalty_templates.title; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalty_templates.title IS 'tytul';


--
-- TOC entry 2261 (class 0 OID 0)
-- Dependencies: 1616
-- Name: COLUMN penalty_templates.description; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalty_templates.description IS 'opis dla administratora';


--
-- TOC entry 2262 (class 0 OID 0)
-- Dependencies: 1616
-- Name: COLUMN penalty_templates.penalty_type_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalty_templates.penalty_type_id IS 'typ kary: ostrzezenie, wszystko, komputer it';


--
-- TOC entry 2263 (class 0 OID 0)
-- Dependencies: 1616
-- Name: COLUMN penalty_templates.duration; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalty_templates.duration IS 'czas trwania kary';


--
-- TOC entry 2264 (class 0 OID 0)
-- Dependencies: 1616
-- Name: COLUMN penalty_templates.amnesty_after; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN penalty_templates.amnesty_after IS 'czas po ktorym mozna udzielic amnesti';


--
-- TOC entry 1617 (class 1259 OID 16981)
-- Dependencies: 1967 1968 3
-- Name: services; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE services (
    id bigint NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    user_id bigint NOT NULL,
    serv_type_id bigint NOT NULL,
    active boolean DEFAULT false,
    modified_by bigint
);


--
-- TOC entry 2265 (class 0 OID 0)
-- Dependencies: 1617
-- Name: TABLE services; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE services IS 'uslugi uzytkownikow';


--
-- TOC entry 2266 (class 0 OID 0)
-- Dependencies: 1617
-- Name: COLUMN services.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN services.created_at IS 'czas utworzenia uslugi';


--
-- TOC entry 2267 (class 0 OID 0)
-- Dependencies: 1617
-- Name: COLUMN services.user_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN services.user_id IS 'id uzytkownika';


--
-- TOC entry 2268 (class 0 OID 0)
-- Dependencies: 1617
-- Name: COLUMN services.serv_type_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN services.serv_type_id IS 'id typu/rodzaju uslugi';


--
-- TOC entry 2269 (class 0 OID 0)
-- Dependencies: 1617
-- Name: COLUMN services.active; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN services.active IS 'stan uslugi, false-nieaktywna/czeka na aktywacje, true-aktywna, null-do usuniecia';


--
-- TOC entry 1618 (class 1259 OID 16986)
-- Dependencies: 1970 3
-- Name: services_history; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE services_history (
    id bigint NOT NULL,
    modified_at timestamp without time zone DEFAULT now() NOT NULL,
    user_id bigint NOT NULL,
    serv_id bigint,
    serv_type_id bigint NOT NULL,
    modified_by bigint,
    active smallint NOT NULL
);


--
-- TOC entry 2270 (class 0 OID 0)
-- Dependencies: 1618
-- Name: TABLE services_history; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE services_history IS 'historia zmian w uslugach uzytkownika';


--
-- TOC entry 2271 (class 0 OID 0)
-- Dependencies: 1618
-- Name: COLUMN services_history.modified_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN services_history.modified_at IS 'czas powstania tej wersji';


--
-- TOC entry 2272 (class 0 OID 0)
-- Dependencies: 1618
-- Name: COLUMN services_history.user_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN services_history.user_id IS 'id uzytkownika';


--
-- TOC entry 2273 (class 0 OID 0)
-- Dependencies: 1618
-- Name: COLUMN services_history.serv_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN services_history.serv_id IS 'id uslugi';


--
-- TOC entry 2274 (class 0 OID 0)
-- Dependencies: 1618
-- Name: COLUMN services_history.serv_type_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN services_history.serv_type_id IS 'id typu/rodzaju uslugi';


--
-- TOC entry 2275 (class 0 OID 0)
-- Dependencies: 1618
-- Name: COLUMN services_history.modified_by; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN services_history.modified_by IS 'kto przydzielil usluge';


--
-- TOC entry 2276 (class 0 OID 0)
-- Dependencies: 1618
-- Name: COLUMN services_history.active; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN services_history.active IS 'stan uslugi';


--
-- TOC entry 1619 (class 1259 OID 16990)
-- Dependencies: 1618 3
-- Name: services_history_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE services_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2277 (class 0 OID 0)
-- Dependencies: 1619
-- Name: services_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE services_history_id_seq OWNED BY services_history.id;


--
-- TOC entry 1620 (class 1259 OID 16992)
-- Dependencies: 3
-- Name: services_type; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE services_type (
    id bigint NOT NULL,
    name character varying(255) NOT NULL
);


--
-- TOC entry 2278 (class 0 OID 0)
-- Dependencies: 1620
-- Name: TABLE services_type; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE services_type IS 'dostepne uslugi';


--
-- TOC entry 2279 (class 0 OID 0)
-- Dependencies: 1620
-- Name: COLUMN services_type.name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN services_type.name IS 'nazwa uslugi';


--
-- TOC entry 1621 (class 1259 OID 16995)
-- Dependencies: 1730 3
-- Name: services_history_view; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW services_history_view AS
    SELECT h.modified_at, h.user_id, h.active, t.name AS serv_name, a.id AS admin_id, a.name AS admin FROM ((services_history h LEFT JOIN services_type t ON ((t.id = h.serv_type_id))) LEFT JOIN admins a ON ((h.modified_by = a.id))) ORDER BY h.modified_at DESC;


--
-- TOC entry 2280 (class 0 OID 0)
-- Dependencies: 1621
-- Name: VIEW services_history_view; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON VIEW services_history_view IS 'widok historii uslug uzytkownika';


--
-- TOC entry 1622 (class 1259 OID 16999)
-- Dependencies: 3 1617
-- Name: services_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE services_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2281 (class 0 OID 0)
-- Dependencies: 1622
-- Name: services_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE services_id_seq OWNED BY services.id;


--
-- TOC entry 1623 (class 1259 OID 17001)
-- Dependencies: 3 1620
-- Name: services_type_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE services_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2282 (class 0 OID 0)
-- Dependencies: 1623
-- Name: services_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE services_type_id_seq OWNED BY services_type.id;


--
-- TOC entry 1632 (class 1259 OID 17336)
-- Dependencies: 3
-- Name: switches_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE switches_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1633 (class 1259 OID 17338)
-- Dependencies: 1991 3
-- Name: switches; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE switches (
    id bigint DEFAULT nextval('switches_id_seq'::regclass) NOT NULL,
    model bigint NOT NULL,
    serial_no character varying(32) NOT NULL,
    localization character varying(128),
    comment pg_catalog.text,
    dormitory bigint NOT NULL,
    inventory_no character varying(32),
    received date,
    inoperational boolean NOT NULL,
    hierarchy_no integer,
    ipv4 inet
);


--
-- TOC entry 2283 (class 0 OID 0)
-- Dependencies: 1633
-- Name: COLUMN switches.localization; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN switches.localization IS 'umiejscowanie switcha';


--
-- TOC entry 2284 (class 0 OID 0)
-- Dependencies: 1633
-- Name: COLUMN switches.inventory_no; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN switches.inventory_no IS 'numer inwentarzowy';


--
-- TOC entry 2285 (class 0 OID 0)
-- Dependencies: 1633
-- Name: COLUMN switches.received; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN switches.received IS 'data dodania na stan';


--
-- TOC entry 2286 (class 0 OID 0)
-- Dependencies: 1633
-- Name: COLUMN switches.inoperational; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN switches.inoperational IS 'czy sprawny';


--
-- TOC entry 2287 (class 0 OID 0)
-- Dependencies: 1633
-- Name: COLUMN switches.hierarchy_no; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN switches.hierarchy_no IS 'nr w hierarchi DSu';


--
-- TOC entry 1634 (class 1259 OID 17345)
-- Dependencies: 3
-- Name: switches_type_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE switches_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1635 (class 1259 OID 17347)
-- Dependencies: 1992 3
-- Name: switches_model; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE switches_model (
    id bigint DEFAULT nextval('switches_type_id_seq'::regclass) NOT NULL,
    model_name character varying(32) NOT NULL,
    model_no character varying(8) NOT NULL,
    ports_no integer NOT NULL
);


--
-- TOC entry 2288 (class 0 OID 0)
-- Dependencies: 1635
-- Name: COLUMN switches_model.model_name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN switches_model.model_name IS 'opisowa nazwa modelu';


--
-- TOC entry 2289 (class 0 OID 0)
-- Dependencies: 1635
-- Name: COLUMN switches_model.model_no; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN switches_model.model_no IS 'kod modelu wg producenta';


--
-- TOC entry 2290 (class 0 OID 0)
-- Dependencies: 1635
-- Name: COLUMN switches_model.ports_no; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN switches_model.ports_no IS 'liczba portow';


--
-- TOC entry 1636 (class 1259 OID 17351)
-- Dependencies: 3
-- Name: switches_port_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE switches_port_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1637 (class 1259 OID 17353)
-- Dependencies: 1993 1994 3
-- Name: switches_port; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE switches_port (
    id bigint DEFAULT nextval('switches_port_id_seq'::regclass) NOT NULL,
    switch bigint NOT NULL,
    location bigint,
    ordinal_no integer NOT NULL,
    comment character varying(255),
    connected_switch bigint,
    is_admin boolean DEFAULT false NOT NULL
);


--
-- TOC entry 2291 (class 0 OID 0)
-- Dependencies: 1637
-- Name: COLUMN switches_port.location; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN switches_port.location IS 'lokalizacja podlaczona do portu';


--
-- TOC entry 2292 (class 0 OID 0)
-- Dependencies: 1637
-- Name: COLUMN switches_port.ordinal_no; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN switches_port.ordinal_no IS 'nr portu na switchu';


--
-- TOC entry 2293 (class 0 OID 0)
-- Dependencies: 1637
-- Name: COLUMN switches_port.connected_switch; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN switches_port.connected_switch IS 'podlaczony do portu switch';


--
-- TOC entry 2294 (class 0 OID 0)
-- Dependencies: 1637
-- Name: COLUMN switches_port.is_admin; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN switches_port.is_admin IS 'czy port admina';


--
-- TOC entry 1624 (class 1259 OID 17003)
-- Dependencies: 3
-- Name: text_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE text_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1625 (class 1259 OID 17005)
-- Dependencies: 1973 1974 3
-- Name: text; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE text (
    id bigint DEFAULT nextval('text_id_seq'::regclass) NOT NULL,
    alias pg_catalog.text NOT NULL,
    title pg_catalog.text NOT NULL,
    content pg_catalog.text NOT NULL,
    modified_at timestamp without time zone DEFAULT now() NOT NULL,
    modified_by bigint
);


--
-- TOC entry 2295 (class 0 OID 0)
-- Dependencies: 1625
-- Name: TABLE text; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE text IS 'statyczne strony tekstowe';


--
-- TOC entry 2296 (class 0 OID 0)
-- Dependencies: 1625
-- Name: COLUMN text.alias; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN text.alias IS '"url"';


--
-- TOC entry 2297 (class 0 OID 0)
-- Dependencies: 1625
-- Name: COLUMN text.title; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN text.title IS 'tytul';


--
-- TOC entry 2298 (class 0 OID 0)
-- Dependencies: 1625
-- Name: COLUMN text.content; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN text.content IS 'tresc glowna';


--
-- TOC entry 2299 (class 0 OID 0)
-- Dependencies: 1625
-- Name: COLUMN text.modified_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN text.modified_at IS 'data ostatniej modyfikacji';


--
-- TOC entry 2300 (class 0 OID 0)
-- Dependencies: 1625
-- Name: COLUMN text.modified_by; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN text.modified_by IS 'kto dokonal modyfikacji';


--
-- TOC entry 1626 (class 1259 OID 17013)
-- Dependencies: 3
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1627 (class 1259 OID 17015)
-- Dependencies: 1975 1976 1977 1978 1979 1980 1981 1982 1983 1984 3
-- Name: users; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE users (
    id bigint DEFAULT nextval('users_id_seq'::regclass) NOT NULL,
    login character varying NOT NULL,
    password character(32) NOT NULL,
    surname character varying(100) NOT NULL,
    email character varying(100),
    faculty_id bigint,
    study_year_id smallint,
    location_id bigint NOT NULL,
    bans smallint DEFAULT 0 NOT NULL,
    modified_by bigint,
    modified_at timestamp without time zone DEFAULT now() NOT NULL,
    comment pg_catalog.text DEFAULT ''::pg_catalog.text NOT NULL,
    name character varying(100) NOT NULL,
    active boolean DEFAULT true NOT NULL,
    banned boolean DEFAULT false NOT NULL,
    gg pg_catalog.text NOT NULL,
    last_login_at timestamp without time zone,
    last_login_ip inet,
    lang character(2) DEFAULT 'pl'::bpchar,
    referral_start timestamp without time zone,
    referral_end timestamp without time zone,
    registry_no integer,
    update_needed boolean DEFAULT true NOT NULL,
    change_password_needed boolean DEFAULT false NOT NULL,
    services_available boolean DEFAULT true NOT NULL
);


--
-- TOC entry 2301 (class 0 OID 0)
-- Dependencies: 1627
-- Name: TABLE users; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE users IS 'uzytkownicy sieci';


--
-- TOC entry 2302 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.login; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.login IS 'login';


--
-- TOC entry 2303 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.password; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.password IS 'haslo zakodowane md5';


--
-- TOC entry 2304 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.surname; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.surname IS 'nazwisko';


--
-- TOC entry 2305 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.email; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.email IS 'email';


--
-- TOC entry 2306 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.faculty_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.faculty_id IS 'wydzial ,jezeli dotyczy';


--
-- TOC entry 2307 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.study_year_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.study_year_id IS 'identyfikator roku studiow, jezeli dotyczy';


--
-- TOC entry 2308 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.location_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.location_id IS 'miejsce zamieszkania';


--
-- TOC entry 2309 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.bans; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.bans IS 'ilosc otrzymanych banow';


--
-- TOC entry 2310 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.modified_by; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.modified_by IS 'kto wprowadzil te dane';


--
-- TOC entry 2311 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.modified_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.modified_at IS 'czas powstania tej wersji';


--
-- TOC entry 2312 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.comment; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.comment IS 'komentarze dotyczace uzytkownika';


--
-- TOC entry 2313 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.name IS 'imie';


--
-- TOC entry 2314 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.active; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.active IS 'czy uzytkownik moze logowac sie do systemu?';


--
-- TOC entry 2315 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.banned; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.banned IS 'czy uzytkownik jest w tej chwili zabanowany?';


--
-- TOC entry 2316 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.referral_start; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.referral_start IS 'data poczatku skierowania';


--
-- TOC entry 2317 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.referral_end; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.referral_end IS 'data konca skierowania';


--
-- TOC entry 2318 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.registry_no; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.registry_no IS 'nr indeksu';


--
-- TOC entry 2319 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.update_needed; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.update_needed IS 'dane wymagaja uaktualnienia?';


--
-- TOC entry 2320 (class 0 OID 0)
-- Dependencies: 1627
-- Name: COLUMN users.change_password_needed; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.change_password_needed IS 'haslo wymaga zmiany?';


--
-- TOC entry 1628 (class 1259 OID 17027)
-- Dependencies: 3
-- Name: users_history_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE users_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1629 (class 1259 OID 17029)
-- Dependencies: 1985 1986 1987 3
-- Name: users_history; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE users_history (
    user_id bigint NOT NULL,
    name character varying(50) NOT NULL,
    surname character varying(100) NOT NULL,
    email character varying(100),
    faculty_id bigint,
    study_year_id smallint,
    location_id bigint NOT NULL,
    modified_by bigint,
    modified_at timestamp without time zone NOT NULL,
    comment pg_catalog.text NOT NULL,
    id bigint DEFAULT nextval('users_history_id_seq'::regclass) NOT NULL,
    login character varying NOT NULL,
    active boolean NOT NULL,
    gg pg_catalog.text DEFAULT ''::pg_catalog.text NOT NULL,
    referral_start timestamp without time zone,
    referral_end timestamp without time zone,
    registry_no integer,
    services_available boolean DEFAULT true NOT NULL
);


--
-- TOC entry 2321 (class 0 OID 0)
-- Dependencies: 1629
-- Name: TABLE users_history; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE users_history IS 'historia zmian danych uzytkownikow';


--
-- TOC entry 2322 (class 0 OID 0)
-- Dependencies: 1629
-- Name: COLUMN users_history.user_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.user_id IS 'id uzytkownika';


--
-- TOC entry 2323 (class 0 OID 0)
-- Dependencies: 1629
-- Name: COLUMN users_history.name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.name IS 'imie';


--
-- TOC entry 2324 (class 0 OID 0)
-- Dependencies: 1629
-- Name: COLUMN users_history.surname; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.surname IS 'nazwisko';


--
-- TOC entry 2325 (class 0 OID 0)
-- Dependencies: 1629
-- Name: COLUMN users_history.email; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.email IS 'email';


--
-- TOC entry 2326 (class 0 OID 0)
-- Dependencies: 1629
-- Name: COLUMN users_history.faculty_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.faculty_id IS 'wydzial';


--
-- TOC entry 2327 (class 0 OID 0)
-- Dependencies: 1629
-- Name: COLUMN users_history.study_year_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.study_year_id IS 'identyfikator roku studiow';


--
-- TOC entry 2328 (class 0 OID 0)
-- Dependencies: 1629
-- Name: COLUMN users_history.location_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.location_id IS 'miejsce zamieszkania';


--
-- TOC entry 2329 (class 0 OID 0)
-- Dependencies: 1629
-- Name: COLUMN users_history.modified_by; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.modified_by IS 'kto wprowadzil te dane';


--
-- TOC entry 2330 (class 0 OID 0)
-- Dependencies: 1629
-- Name: COLUMN users_history.modified_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.modified_at IS 'czas powstania tej wersji';


--
-- TOC entry 2331 (class 0 OID 0)
-- Dependencies: 1629
-- Name: COLUMN users_history.comment; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.comment IS 'komentarz';


--
-- TOC entry 2332 (class 0 OID 0)
-- Dependencies: 1629
-- Name: COLUMN users_history.login; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.login IS 'login';


--
-- TOC entry 2333 (class 0 OID 0)
-- Dependencies: 1629
-- Name: COLUMN users_history.gg; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.gg IS 'gadu-gadu';


--
-- TOC entry 2334 (class 0 OID 0)
-- Dependencies: 1629
-- Name: COLUMN users_history.referral_start; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.referral_start IS 'data poczatku skierowania';


--
-- TOC entry 2335 (class 0 OID 0)
-- Dependencies: 1629
-- Name: COLUMN users_history.referral_end; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.referral_end IS 'data konca skierowania';


--
-- TOC entry 2336 (class 0 OID 0)
-- Dependencies: 1629
-- Name: COLUMN users_history.registry_no; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.registry_no IS 'nr indeksu';


--
-- TOC entry 1630 (class 1259 OID 17043)
-- Dependencies: 3
-- Name: users_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE users_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1631 (class 1259 OID 17045)
-- Dependencies: 1988 1989 1990 3
-- Name: users_tokens; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE users_tokens (
    id integer DEFAULT nextval('users_tokens_id_seq'::regclass) NOT NULL,
    user_id integer NOT NULL,
    token pg_catalog.text NOT NULL,
    valid_to timestamp without time zone DEFAULT (now() + '7 days'::interval) NOT NULL,
    type smallint DEFAULT 0 NOT NULL
);


--
-- TOC entry 2337 (class 0 OID 0)
-- Dependencies: 1631
-- Name: COLUMN users_tokens.valid_to; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_tokens.valid_to IS 'do kiedy token jest wazny';


--
-- TOC entry 2338 (class 0 OID 0)
-- Dependencies: 1631
-- Name: COLUMN users_tokens.type; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_tokens.type IS 'do czego moze byc ten token wykorzystany
0 - aktywacja konta';


--
-- TOC entry 1997 (class 2604 OID 17459)
-- Dependencies: 1641 1642 1642
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE admins_dormitories ALTER COLUMN id SET DEFAULT nextval('admins_dormitories_id_seq1'::regclass);


--
-- TOC entry 1961 (class 2604 OID 17317)
-- Dependencies: 1614 1613
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE penalties_history ALTER COLUMN id SET DEFAULT nextval('penalties_history_id_seq'::regclass);


--
-- TOC entry 1969 (class 2604 OID 17318)
-- Dependencies: 1622 1617
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE services ALTER COLUMN id SET DEFAULT nextval('services_id_seq'::regclass);


--
-- TOC entry 1971 (class 2604 OID 17319)
-- Dependencies: 1619 1618
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE services_history ALTER COLUMN id SET DEFAULT nextval('services_history_id_seq'::regclass);


--
-- TOC entry 1972 (class 2604 OID 17320)
-- Dependencies: 1623 1620
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE services_type ALTER COLUMN id SET DEFAULT nextval('services_type_id_seq'::regclass);


--
-- TOC entry 2092 (class 2606 OID 17463)
-- Dependencies: 1642 1642 1642
-- Name: admins_dormitories_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY admins_dormitories
    ADD CONSTRAINT admins_dormitories_key UNIQUE (admin, dormitory);


--
-- TOC entry 2094 (class 2606 OID 17461)
-- Dependencies: 1642 1642
-- Name: admins_dormitories_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY admins_dormitories
    ADD CONSTRAINT admins_dormitories_pkey PRIMARY KEY (id);


--
-- TOC entry 2002 (class 2606 OID 17067)
-- Dependencies: 1596 1596 1596
-- Name: admins_login_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY admins
    ADD CONSTRAINT admins_login_key UNIQUE (login, active);


--
-- TOC entry 2004 (class 2606 OID 17069)
-- Dependencies: 1596 1596
-- Name: admins_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY admins
    ADD CONSTRAINT admins_pkey PRIMARY KEY (id);


--
-- TOC entry 2087 (class 2606 OID 17434)
-- Dependencies: 1639 1639
-- Name: computers_aliases_host_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY computers_aliases
    ADD CONSTRAINT computers_aliases_host_key UNIQUE (host);


--
-- TOC entry 2089 (class 2606 OID 17432)
-- Dependencies: 1639 1639
-- Name: computers_aliases_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY computers_aliases
    ADD CONSTRAINT computers_aliases_pkey PRIMARY KEY (id);


--
-- TOC entry 2011 (class 2606 OID 17071)
-- Dependencies: 1601 1601
-- Name: computers_bans_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY computers_bans
    ADD CONSTRAINT computers_bans_pkey PRIMARY KEY (id);


--
-- TOC entry 2016 (class 2606 OID 17073)
-- Dependencies: 1604 1604
-- Name: computers_history_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY computers_history
    ADD CONSTRAINT computers_history_pkey PRIMARY KEY (id);


--
-- TOC entry 2009 (class 2606 OID 17075)
-- Dependencies: 1599 1599
-- Name: computers_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY computers
    ADD CONSTRAINT computers_pkey PRIMARY KEY (id);


--
-- TOC entry 2018 (class 2606 OID 17077)
-- Dependencies: 1606 1606
-- Name: dormitories_alias_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY dormitories
    ADD CONSTRAINT dormitories_alias_key UNIQUE (alias);


--
-- TOC entry 2020 (class 2606 OID 17079)
-- Dependencies: 1606 1606
-- Name: dormitories_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY dormitories
    ADD CONSTRAINT dormitories_pkey PRIMARY KEY (id);


--
-- TOC entry 2022 (class 2606 OID 17081)
-- Dependencies: 1608 1608
-- Name: faulties_alias_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY faculties
    ADD CONSTRAINT faulties_alias_key UNIQUE (alias);


--
-- TOC entry 2024 (class 2606 OID 17083)
-- Dependencies: 1608 1608
-- Name: faulties_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY faculties
    ADD CONSTRAINT faulties_pkey PRIMARY KEY (id);


--
-- TOC entry 2026 (class 2606 OID 17085)
-- Dependencies: 1609 1609
-- Name: ipv4s_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ipv4s
    ADD CONSTRAINT ipv4s_pkey PRIMARY KEY (ip);


--
-- TOC entry 2028 (class 2606 OID 17087)
-- Dependencies: 1611 1611 1611
-- Name: locations_alias_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY locations
    ADD CONSTRAINT locations_alias_key UNIQUE (alias, dormitory_id);


--
-- TOC entry 2030 (class 2606 OID 17089)
-- Dependencies: 1611 1611
-- Name: locations_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY locations
    ADD CONSTRAINT locations_pkey PRIMARY KEY (id);


--
-- TOC entry 2038 (class 2606 OID 17091)
-- Dependencies: 1613 1613
-- Name: penalties_history_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY penalties_history
    ADD CONSTRAINT penalties_history_pkey PRIMARY KEY (id);


--
-- TOC entry 2036 (class 2606 OID 17093)
-- Dependencies: 1612 1612
-- Name: penalties_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY penalties
    ADD CONSTRAINT penalties_pkey PRIMARY KEY (id);


--
-- TOC entry 2040 (class 2606 OID 17095)
-- Dependencies: 1616 1616
-- Name: penalty_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY penalty_templates
    ADD CONSTRAINT penalty_templates_pkey PRIMARY KEY (id);


--
-- TOC entry 2042 (class 2606 OID 17097)
-- Dependencies: 1616 1616
-- Name: penalty_templates_title_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY penalty_templates
    ADD CONSTRAINT penalty_templates_title_key UNIQUE (title);


--
-- TOC entry 2048 (class 2606 OID 17099)
-- Dependencies: 1618 1618
-- Name: services_history_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY services_history
    ADD CONSTRAINT services_history_pkey PRIMARY KEY (id);


--
-- TOC entry 2044 (class 2606 OID 17101)
-- Dependencies: 1617 1617
-- Name: services_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY services
    ADD CONSTRAINT services_pkey PRIMARY KEY (id);


--
-- TOC entry 2051 (class 2606 OID 17103)
-- Dependencies: 1620 1620
-- Name: services_type_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY services_type
    ADD CONSTRAINT services_type_pkey PRIMARY KEY (id);


--
-- TOC entry 2046 (class 2606 OID 17105)
-- Dependencies: 1617 1617 1617
-- Name: services_user_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY services
    ADD CONSTRAINT services_user_id_key UNIQUE (user_id, serv_type_id);


--
-- TOC entry 2069 (class 2606 OID 17359)
-- Dependencies: 1633 1633
-- Name: switches_inventory_no_unique; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY switches
    ADD CONSTRAINT switches_inventory_no_unique UNIQUE (inventory_no);


--
-- TOC entry 2071 (class 2606 OID 17361)
-- Dependencies: 1633 1633
-- Name: switches_ipv4_unique; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY switches
    ADD CONSTRAINT switches_ipv4_unique UNIQUE (ipv4);


--
-- TOC entry 2077 (class 2606 OID 17363)
-- Dependencies: 1635 1635
-- Name: switches_model_model_no_unique; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY switches_model
    ADD CONSTRAINT switches_model_model_no_unique UNIQUE (model_no);


--
-- TOC entry 2079 (class 2606 OID 17365)
-- Dependencies: 1635 1635
-- Name: switches_model_name_unique; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY switches_model
    ADD CONSTRAINT switches_model_name_unique UNIQUE (model_name);


--
-- TOC entry 2081 (class 2606 OID 17367)
-- Dependencies: 1635 1635
-- Name: switches_model_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY switches_model
    ADD CONSTRAINT switches_model_pkey PRIMARY KEY (id);


--
-- TOC entry 2073 (class 2606 OID 17369)
-- Dependencies: 1633 1633
-- Name: switches_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY switches
    ADD CONSTRAINT switches_pkey PRIMARY KEY (id);


--
-- TOC entry 2085 (class 2606 OID 17371)
-- Dependencies: 1637 1637
-- Name: switches_port_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY switches_port
    ADD CONSTRAINT switches_port_pkey PRIMARY KEY (id);


--
-- TOC entry 2075 (class 2606 OID 17373)
-- Dependencies: 1633 1633
-- Name: switches_serial_no_unique; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY switches
    ADD CONSTRAINT switches_serial_no_unique UNIQUE (serial_no);


--
-- TOC entry 2053 (class 2606 OID 17107)
-- Dependencies: 1625 1625
-- Name: text_alias_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY text
    ADD CONSTRAINT text_alias_key UNIQUE (alias);


--
-- TOC entry 2055 (class 2606 OID 17109)
-- Dependencies: 1625 1625
-- Name: text_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY text
    ADD CONSTRAINT text_pkey PRIMARY KEY (id);


--
-- TOC entry 2063 (class 2606 OID 17111)
-- Dependencies: 1629 1629
-- Name: users_history_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY users_history
    ADD CONSTRAINT users_history_pkey PRIMARY KEY (id);


--
-- TOC entry 2057 (class 2606 OID 17113)
-- Dependencies: 1627 1627
-- Name: users_login_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_login_key UNIQUE (login);


--
-- TOC entry 2059 (class 2606 OID 17117)
-- Dependencies: 1627 1627
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 2065 (class 2606 OID 17119)
-- Dependencies: 1631 1631
-- Name: users_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY users_tokens
    ADD CONSTRAINT users_tokens_pkey PRIMARY KEY (id);


--
-- TOC entry 2014 (class 1259 OID 17423)
-- Dependencies: 1604
-- Name: computers_history_ipv4_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX computers_history_ipv4_idx ON computers_history USING btree (ipv4);


--
-- TOC entry 2005 (class 1259 OID 17120)
-- Dependencies: 1599 1599 1599
-- Name: computers_host_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX computers_host_key ON computers USING btree (host, active) WHERE (active = true);


--
-- TOC entry 2006 (class 1259 OID 17441)
-- Dependencies: 1599 1599 1599
-- Name: computers_ipv4_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX computers_ipv4_key ON computers USING btree (ipv4, active) WHERE (active = true);


--
-- TOC entry 2007 (class 1259 OID 17122)
-- Dependencies: 1599 1599 1599 1599
-- Name: computers_mac_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX computers_mac_key ON computers USING btree (mac, active) WHERE ((active = true) AND (type_id <> 4));


--
-- TOC entry 2090 (class 1259 OID 17440)
-- Dependencies: 1639
-- Name: fki_computers_aliases_fkey; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fki_computers_aliases_fkey ON computers_aliases USING btree (computer_id);


--
-- TOC entry 2012 (class 1259 OID 17123)
-- Dependencies: 1601
-- Name: fki_computers_bans_computer_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fki_computers_bans_computer_id ON computers_bans USING btree (computer_id);


--
-- TOC entry 2013 (class 1259 OID 17124)
-- Dependencies: 1601
-- Name: fki_computers_bans_penalty_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fki_computers_bans_penalty_id ON computers_bans USING btree (penalty_id);


--
-- TOC entry 2031 (class 1259 OID 17125)
-- Dependencies: 1612
-- Name: fki_penalties_amnesty_by; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fki_penalties_amnesty_by ON penalties USING btree (amnesty_by);


--
-- TOC entry 2032 (class 1259 OID 17126)
-- Dependencies: 1612
-- Name: fki_penalties_created_by; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fki_penalties_created_by ON penalties USING btree (created_by);


--
-- TOC entry 2033 (class 1259 OID 17127)
-- Dependencies: 1612
-- Name: fki_penalties_modified_by; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fki_penalties_modified_by ON penalties USING btree (modified_by);


--
-- TOC entry 2034 (class 1259 OID 17128)
-- Dependencies: 1612
-- Name: fki_penalties_user_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fki_penalties_user_id ON penalties USING btree (user_id);


--
-- TOC entry 2066 (class 1259 OID 17374)
-- Dependencies: 1633
-- Name: fki_switches_model_fkey; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fki_switches_model_fkey ON switches USING btree (model);


--
-- TOC entry 2082 (class 1259 OID 17375)
-- Dependencies: 1637
-- Name: fki_switches_port_connected_switch_fkey; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX fki_switches_port_connected_switch_fkey ON switches_port USING btree (connected_switch);


--
-- TOC entry 2083 (class 1259 OID 17580)
-- Dependencies: 1637
-- Name: idx_switches_port_ordinal_no; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX idx_switches_port_ordinal_no ON switches_port USING btree (ordinal_no);


--
-- TOC entry 2067 (class 1259 OID 17579)
-- Dependencies: 1633
-- Name: idx_switches_serial_no; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX idx_switches_serial_no ON switches USING btree (serial_no);


--
-- TOC entry 2049 (class 1259 OID 17129)
-- Dependencies: 1618
-- Name: user_id; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX user_id ON services_history USING btree (user_id);


--
-- TOC entry 2060 (class 1259 OID 17485)
-- Dependencies: 1627
-- Name: users_registry_no_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX users_registry_no_key ON users USING btree (registry_no);


--
-- TOC entry 2061 (class 1259 OID 17130)
-- Dependencies: 1627
-- Name: users_surname_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX users_surname_key ON users USING btree (surname);


--
-- TOC entry 2138 (class 2620 OID 17322)
-- Dependencies: 19 1599
-- Name: computer_add; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER computer_add
    AFTER INSERT OR UPDATE ON computers
    FOR EACH ROW
    EXECUTE PROCEDURE computer_add();


--
-- TOC entry 2141 (class 2620 OID 17323)
-- Dependencies: 20 1601
-- Name: computer_ban_computers; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER computer_ban_computers
    AFTER INSERT OR DELETE OR UPDATE ON computers_bans
    FOR EACH ROW
    EXECUTE PROCEDURE computer_ban_computers();


--
-- TOC entry 2139 (class 2620 OID 17324)
-- Dependencies: 1599 21
-- Name: computers_counters; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER computers_counters
    AFTER INSERT OR DELETE OR UPDATE ON computers
    FOR EACH ROW
    EXECUTE PROCEDURE computer_counters();


--
-- TOC entry 2140 (class 2620 OID 17325)
-- Dependencies: 1599 33
-- Name: computers_update; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER computers_update
    AFTER UPDATE ON computers
    FOR EACH ROW
    EXECUTE PROCEDURE computer_update();


--
-- TOC entry 2339 (class 0 OID 0)
-- Dependencies: 2140
-- Name: TRIGGER computers_update ON computers; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TRIGGER computers_update ON computers IS 'zapisuje historie zmian';


--
-- TOC entry 2142 (class 2620 OID 17326)
-- Dependencies: 1609 22
-- Name: ipv4s_counters; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER ipv4s_counters
    AFTER INSERT OR DELETE OR UPDATE ON ipv4s
    FOR EACH ROW
    EXECUTE PROCEDURE ipv4_counters();


--
-- TOC entry 2143 (class 2620 OID 17327)
-- Dependencies: 24 1611
-- Name: locations_counters; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER locations_counters
    AFTER INSERT OR DELETE OR UPDATE ON locations
    FOR EACH ROW
    EXECUTE PROCEDURE location_counters();


--
-- TOC entry 2144 (class 2620 OID 17328)
-- Dependencies: 1612 25
-- Name: penalties_computers_bans; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER penalties_computers_bans
    AFTER INSERT OR DELETE OR UPDATE ON penalties
    FOR EACH ROW
    EXECUTE PROCEDURE penalty_computers_bans();


--
-- TOC entry 2145 (class 2620 OID 17329)
-- Dependencies: 1612 26
-- Name: penalties_update; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER penalties_update
    AFTER UPDATE ON penalties
    FOR EACH ROW
    EXECUTE PROCEDURE penalty_update();


--
-- TOC entry 2340 (class 0 OID 0)
-- Dependencies: 2145
-- Name: TRIGGER penalties_update ON penalties; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TRIGGER penalties_update ON penalties IS 'kopiuje dane do historii';


--
-- TOC entry 2146 (class 2620 OID 17330)
-- Dependencies: 27 1612
-- Name: penalties_users; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER penalties_users
    AFTER INSERT OR DELETE OR UPDATE ON penalties
    FOR EACH ROW
    EXECUTE PROCEDURE penalty_users();


--
-- TOC entry 2147 (class 2620 OID 17331)
-- Dependencies: 31 1617
-- Name: user_service_create; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER user_service_create
    AFTER INSERT ON services
    FOR EACH ROW
    EXECUTE PROCEDURE user_service_create();


--
-- TOC entry 2341 (class 0 OID 0)
-- Dependencies: 2147
-- Name: TRIGGER user_service_create ON services; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TRIGGER user_service_create ON services IS 'dodaje usluge w historii uslug';


--
-- TOC entry 2148 (class 2620 OID 17332)
-- Dependencies: 32 1617
-- Name: user_service_update; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER user_service_update
    AFTER UPDATE ON services
    FOR EACH ROW
    EXECUTE PROCEDURE user_service_update();


--
-- TOC entry 2342 (class 0 OID 0)
-- Dependencies: 2148
-- Name: TRIGGER user_service_update ON services; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TRIGGER user_service_update ON services IS 'zapisuje zmiany w historii uslug';


--
-- TOC entry 2149 (class 2620 OID 17333)
-- Dependencies: 1627 29
-- Name: users_computers; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER users_computers
    AFTER INSERT OR DELETE OR UPDATE ON users
    FOR EACH ROW
    EXECUTE PROCEDURE user_computers();


--
-- TOC entry 2150 (class 2620 OID 17334)
-- Dependencies: 30 1627
-- Name: users_counters; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER users_counters
    AFTER INSERT OR DELETE OR UPDATE ON users
    FOR EACH ROW
    EXECUTE PROCEDURE user_counters();


--
-- TOC entry 2152 (class 2620 OID 17403)
-- Dependencies: 1627 23
-- Name: users_services; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER users_services
    AFTER INSERT OR DELETE OR UPDATE ON users
    FOR EACH ROW
    EXECUTE PROCEDURE user_services();


--
-- TOC entry 2151 (class 2620 OID 17335)
-- Dependencies: 34 1627
-- Name: users_update; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER users_update
    AFTER UPDATE ON users
    FOR EACH ROW
    EXECUTE PROCEDURE user_update();


--
-- TOC entry 2343 (class 0 OID 0)
-- Dependencies: 2151
-- Name: TRIGGER users_update ON users; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TRIGGER users_update ON users IS 'kopiuje dane do historii';


--
-- TOC entry 2135 (class 2606 OID 17464)
-- Dependencies: 2003 1642 1596
-- Name: admins_dormitories_admin_id; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY admins_dormitories
    ADD CONSTRAINT admins_dormitories_admin_id FOREIGN KEY (admin) REFERENCES admins(id);


--
-- TOC entry 2136 (class 2606 OID 17469)
-- Dependencies: 1606 1642 2019
-- Name: admins_dormitories_dormitory_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY admins_dormitories
    ADD CONSTRAINT admins_dormitories_dormitory_id_fkey FOREIGN KEY (dormitory) REFERENCES dormitories(id);


--
-- TOC entry 2095 (class 2606 OID 17132)
-- Dependencies: 2019 1596 1606
-- Name: admins_dormitory_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY admins
    ADD CONSTRAINT admins_dormitory_id_fkey FOREIGN KEY (dormitory_id) REFERENCES dormitories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2134 (class 2606 OID 17435)
-- Dependencies: 1639 2008 1599
-- Name: computers_aliases_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers_aliases
    ADD CONSTRAINT computers_aliases_fkey FOREIGN KEY (computer_id) REFERENCES computers(id) ON DELETE CASCADE;


--
-- TOC entry 2100 (class 2606 OID 17137)
-- Dependencies: 1601 2008 1599
-- Name: computers_bans_computer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers_bans
    ADD CONSTRAINT computers_bans_computer_id_fkey FOREIGN KEY (computer_id) REFERENCES computers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2101 (class 2606 OID 17142)
-- Dependencies: 1612 1601 2035
-- Name: computers_bans_penalty_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers_bans
    ADD CONSTRAINT computers_bans_penalty_id_fkey FOREIGN KEY (penalty_id) REFERENCES penalties(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2102 (class 2606 OID 17147)
-- Dependencies: 2008 1599 1604
-- Name: computers_history_computer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers_history
    ADD CONSTRAINT computers_history_computer_id_fkey FOREIGN KEY (computer_id) REFERENCES computers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2103 (class 2606 OID 17152)
-- Dependencies: 2029 1604 1611
-- Name: computers_history_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers_history
    ADD CONSTRAINT computers_history_location_id_fkey FOREIGN KEY (location_id) REFERENCES locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2104 (class 2606 OID 17157)
-- Dependencies: 1596 1604 2003
-- Name: computers_history_modified_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers_history
    ADD CONSTRAINT computers_history_modified_by_fkey FOREIGN KEY (modified_by) REFERENCES admins(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2105 (class 2606 OID 17162)
-- Dependencies: 1604 2058 1627
-- Name: computers_history_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers_history
    ADD CONSTRAINT computers_history_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2096 (class 2606 OID 17167)
-- Dependencies: 2025 1609 1599
-- Name: computers_ipv4_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers
    ADD CONSTRAINT computers_ipv4_fkey FOREIGN KEY (ipv4) REFERENCES ipv4s(ip) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2097 (class 2606 OID 17172)
-- Dependencies: 2029 1599 1611
-- Name: computers_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers
    ADD CONSTRAINT computers_location_id_fkey FOREIGN KEY (location_id) REFERENCES locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2098 (class 2606 OID 17177)
-- Dependencies: 1599 2003 1596
-- Name: computers_modified_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers
    ADD CONSTRAINT computers_modified_by_fkey FOREIGN KEY (modified_by) REFERENCES admins(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- TOC entry 2099 (class 2606 OID 17182)
-- Dependencies: 1627 1599 2058
-- Name: computers_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers
    ADD CONSTRAINT computers_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2106 (class 2606 OID 17187)
-- Dependencies: 2019 1606 1609
-- Name: ipv4s_dormitory_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ipv4s
    ADD CONSTRAINT ipv4s_dormitory_id_fkey FOREIGN KEY (dormitory_id) REFERENCES dormitories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2137 (class 2606 OID 17787)
-- Dependencies: 1609 1643 2025
-- Name: lanstats_ipv4_ip_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY lanstats
    ADD CONSTRAINT lanstats_ipv4_ip_fk FOREIGN KEY (ip) REFERENCES ipv4s(ip);


--
-- TOC entry 2107 (class 2606 OID 17192)
-- Dependencies: 1606 1611 2019
-- Name: locations_dormitory_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY locations
    ADD CONSTRAINT locations_dormitory_id_fkey FOREIGN KEY (dormitory_id) REFERENCES dormitories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2108 (class 2606 OID 17197)
-- Dependencies: 1596 2003 1612
-- Name: penalties_amnesty_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY penalties
    ADD CONSTRAINT penalties_amnesty_by_fkey FOREIGN KEY (amnesty_by) REFERENCES admins(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2109 (class 2606 OID 17202)
-- Dependencies: 1596 1612 2003
-- Name: penalties_created_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY penalties
    ADD CONSTRAINT penalties_created_by_fkey FOREIGN KEY (created_by) REFERENCES admins(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2113 (class 2606 OID 17207)
-- Dependencies: 1596 1613 2003
-- Name: penalties_history_modified_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY penalties_history
    ADD CONSTRAINT penalties_history_modified_by_fkey FOREIGN KEY (modified_by) REFERENCES admins(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2114 (class 2606 OID 17212)
-- Dependencies: 1613 1612 2035
-- Name: penalties_history_penalty_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY penalties_history
    ADD CONSTRAINT penalties_history_penalty_id_fkey FOREIGN KEY (penalty_id) REFERENCES penalties(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2110 (class 2606 OID 17217)
-- Dependencies: 1596 2003 1612
-- Name: penalties_modified_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY penalties
    ADD CONSTRAINT penalties_modified_by_fkey FOREIGN KEY (modified_by) REFERENCES admins(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2112 (class 2606 OID 17792)
-- Dependencies: 1612 1616 2039
-- Name: penalties_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY penalties
    ADD CONSTRAINT penalties_template_id_fkey FOREIGN KEY (template_id) REFERENCES penalty_templates(id) ON DELETE SET NULL;


--
-- TOC entry 2111 (class 2606 OID 17222)
-- Dependencies: 1612 1627 2058
-- Name: penalties_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY penalties
    ADD CONSTRAINT penalties_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2117 (class 2606 OID 17227)
-- Dependencies: 1618 2003 1596
-- Name: services_history_modified_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY services_history
    ADD CONSTRAINT services_history_modified_by_fkey FOREIGN KEY (modified_by) REFERENCES admins(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2118 (class 2606 OID 17232)
-- Dependencies: 1617 1618 2043
-- Name: services_history_serv_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY services_history
    ADD CONSTRAINT services_history_serv_id_fkey FOREIGN KEY (serv_id) REFERENCES services(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2119 (class 2606 OID 17237)
-- Dependencies: 1620 2050 1618
-- Name: services_history_serv_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY services_history
    ADD CONSTRAINT services_history_serv_type_id_fkey FOREIGN KEY (serv_type_id) REFERENCES services_type(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2120 (class 2606 OID 17242)
-- Dependencies: 1618 1627 2058
-- Name: services_history_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY services_history
    ADD CONSTRAINT services_history_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2115 (class 2606 OID 17247)
-- Dependencies: 2050 1617 1620
-- Name: services_serv_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY services
    ADD CONSTRAINT services_serv_type_id_fkey FOREIGN KEY (serv_type_id) REFERENCES services_type(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2116 (class 2606 OID 17252)
-- Dependencies: 1617 2058 1627
-- Name: services_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY services
    ADD CONSTRAINT services_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2129 (class 2606 OID 17376)
-- Dependencies: 2019 1633 1606
-- Name: switches_dormitories_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY switches
    ADD CONSTRAINT switches_dormitories_fkey FOREIGN KEY (dormitory) REFERENCES dormitories(id);


--
-- TOC entry 2130 (class 2606 OID 17381)
-- Dependencies: 2080 1635 1633
-- Name: switches_model_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY switches
    ADD CONSTRAINT switches_model_fkey FOREIGN KEY (model) REFERENCES switches_model(id);


--
-- TOC entry 2131 (class 2606 OID 17386)
-- Dependencies: 2072 1637 1633
-- Name: switches_port_connected_switch_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY switches_port
    ADD CONSTRAINT switches_port_connected_switch_fkey FOREIGN KEY (connected_switch) REFERENCES switches(id);


--
-- TOC entry 2132 (class 2606 OID 17391)
-- Dependencies: 2029 1611 1637
-- Name: switches_port_location_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY switches_port
    ADD CONSTRAINT switches_port_location_fkey FOREIGN KEY (location) REFERENCES locations(id);


--
-- TOC entry 2133 (class 2606 OID 17396)
-- Dependencies: 1637 1633 2072
-- Name: switches_port_switch_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY switches_port
    ADD CONSTRAINT switches_port_switch_fkey FOREIGN KEY (switch) REFERENCES switches(id);


--
-- TOC entry 2121 (class 2606 OID 17257)
-- Dependencies: 1608 2023 1627
-- Name: users_faculty_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_faculty_id_fkey FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2124 (class 2606 OID 17262)
-- Dependencies: 1608 1629 2023
-- Name: users_history_faculty_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users_history
    ADD CONSTRAINT users_history_faculty_id_fkey FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2125 (class 2606 OID 17267)
-- Dependencies: 1611 2029 1629
-- Name: users_history_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users_history
    ADD CONSTRAINT users_history_location_id_fkey FOREIGN KEY (location_id) REFERENCES locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2126 (class 2606 OID 17272)
-- Dependencies: 2003 1629 1596
-- Name: users_history_modified_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users_history
    ADD CONSTRAINT users_history_modified_by_fkey FOREIGN KEY (modified_by) REFERENCES admins(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2127 (class 2606 OID 17277)
-- Dependencies: 2058 1629 1627
-- Name: users_history_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users_history
    ADD CONSTRAINT users_history_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2122 (class 2606 OID 17282)
-- Dependencies: 1611 2029 1627
-- Name: users_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_location_id_fkey FOREIGN KEY (location_id) REFERENCES locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2123 (class 2606 OID 17287)
-- Dependencies: 1596 2003 1627
-- Name: users_modified_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_modified_by_fkey FOREIGN KEY (modified_by) REFERENCES admins(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2128 (class 2606 OID 17292)
-- Dependencies: 1631 1627 2058
-- Name: users_tokens_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users_tokens
    ADD CONSTRAINT users_tokens_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2157 (class 0 OID 0)
-- Dependencies: 3
-- Name: public; Type: ACL; Schema: -; Owner: -
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2011-02-05 18:43:15 CET

--
-- PostgreSQL database dump complete
--

