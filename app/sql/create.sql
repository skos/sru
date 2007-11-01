--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

ALTER TABLE ONLY public.users DROP CONSTRAINT users_modified_by_fkey;
ALTER TABLE ONLY public.users DROP CONSTRAINT users_location_id_fkey;
ALTER TABLE ONLY public.users_history DROP CONSTRAINT users_history_user_id_fkey;
ALTER TABLE ONLY public.users_history DROP CONSTRAINT users_history_modified_by_fkey;
ALTER TABLE ONLY public.users_history DROP CONSTRAINT users_history_location_id_fkey;
ALTER TABLE ONLY public.users_history DROP CONSTRAINT users_history_faculty_id_fkey;
ALTER TABLE ONLY public.users DROP CONSTRAINT users_faculty_id_fkey;
ALTER TABLE ONLY public.locations DROP CONSTRAINT locations_dormitory_id_fkey;
ALTER TABLE ONLY public.ipv4s DROP CONSTRAINT ipv4s_dormitory_id_fkey;
ALTER TABLE ONLY public.computers DROP CONSTRAINT computers_user_id_fkey;
ALTER TABLE ONLY public.computers DROP CONSTRAINT computers_modified_by_fkey;
ALTER TABLE ONLY public.computers DROP CONSTRAINT computers_location_id_fkey;
ALTER TABLE ONLY public.computers DROP CONSTRAINT computers_ipv4_fkey;
ALTER TABLE ONLY public.computers_history DROP CONSTRAINT computers_history_user_id_fkey;
ALTER TABLE ONLY public.computers_history DROP CONSTRAINT computers_history_modified_by_fkey;
ALTER TABLE ONLY public.computers_history DROP CONSTRAINT computers_history_location_id_fkey;
ALTER TABLE ONLY public.computers_history DROP CONSTRAINT computers_history_computer_id_fkey;
ALTER TABLE ONLY public.admins DROP CONSTRAINT admins_dormitory_id_fkey;
DROP TRIGGER users_update ON public.users;
DROP TRIGGER computers_update ON public.computers;
DROP INDEX public.computers_mac_key;
DROP INDEX public.computers_ipv4_key;
DROP INDEX public.computers_host_key;
ALTER TABLE ONLY public.users DROP CONSTRAINT users_pkey;
ALTER TABLE ONLY public.users DROP CONSTRAINT users_login_key;
ALTER TABLE ONLY public.users_history DROP CONSTRAINT users_history_pkey;
ALTER TABLE ONLY public.text DROP CONSTRAINT text_pkey;
ALTER TABLE ONLY public.text DROP CONSTRAINT text_alias_key;
ALTER TABLE ONLY public.locations DROP CONSTRAINT locations_pkey;
ALTER TABLE ONLY public.locations DROP CONSTRAINT locations_alias_key;
ALTER TABLE ONLY public.ipv4s DROP CONSTRAINT ipv4s_pkey;
ALTER TABLE ONLY public.faculties DROP CONSTRAINT faulties_pkey;
ALTER TABLE ONLY public.faculties DROP CONSTRAINT faulties_alias_key;
ALTER TABLE ONLY public.dormitories DROP CONSTRAINT dormitories_pkey;
ALTER TABLE ONLY public.dormitories DROP CONSTRAINT dormitories_alias_key;
ALTER TABLE ONLY public.computers DROP CONSTRAINT computers_pkey;
ALTER TABLE ONLY public.computers_history DROP CONSTRAINT computers_history_pkey;
ALTER TABLE ONLY public.admins DROP CONSTRAINT admins_pkey;
ALTER TABLE ONLY public.admins DROP CONSTRAINT admins_login_key;
ALTER TABLE public.users_history ALTER COLUMN id DROP DEFAULT;
ALTER TABLE public.users ALTER COLUMN id DROP DEFAULT;
ALTER TABLE public.text ALTER COLUMN id DROP DEFAULT;
ALTER TABLE public.locations ALTER COLUMN id DROP DEFAULT;
ALTER TABLE public.faculties ALTER COLUMN id DROP DEFAULT;
ALTER TABLE public.dormitories ALTER COLUMN id DROP DEFAULT;
ALTER TABLE public.computers_history ALTER COLUMN id DROP DEFAULT;
ALTER TABLE public.computers_history ALTER COLUMN computer_id DROP DEFAULT;
ALTER TABLE public.computers ALTER COLUMN id DROP DEFAULT;
ALTER TABLE public.admins ALTER COLUMN id DROP DEFAULT;
DROP SEQUENCE public.users_id_seq;
DROP SEQUENCE public.users_history_id_seq;
DROP TABLE public.users_history;
DROP TABLE public.users;
DROP SEQUENCE public.text_id_seq;
DROP TABLE public.text;
DROP SEQUENCE public.locations_id_seq;
DROP TABLE public.locations;
DROP TABLE public.ipv4s;
DROP SEQUENCE public.faulties_id_seq;
DROP TABLE public.faculties;
DROP SEQUENCE public.dormitories_id_seq;
DROP TABLE public.dormitories;
DROP SEQUENCE public.computers_id_seq;
DROP SEQUENCE public.computers_history_id_seq;
DROP SEQUENCE public.computers_history_computer_id_seq;
DROP TABLE public.computers_history;
DROP TABLE public.computers;
DROP SEQUENCE public.admins_id_seq;
DROP TABLE public.admins;
DROP FUNCTION public.user_update();
DROP FUNCTION public.computer_update();
DROP PROCEDURAL LANGUAGE plpgsql;
DROP SCHEMA public;
--
-- Name: public; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA public;


--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA public IS 'Standard public schema';


--
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: -
--

CREATE PROCEDURAL LANGUAGE plpgsql;


--
-- Name: computer_update(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION computer_update() RETURNS "trigger"
    AS $$BEGIN
if
	OLD.host!=NEW.host OR
	OLD.mac!=NEW.mac OR
	OLD.ipv4!=NEW.ipv4 OR
	OLD.user_id!=NEW.user_id OR
	OLD.location_id!=NEW.location_id OR
	OLD.avail_to!=NEW.avail_to OR
	OLD.comment!=NEW.comment OR
	OLD.can_admin!=NEW.can_admin
then
	INSERT INTO computers_history (
		computer_id,
		host,
		mac,
		ipv4,
		user_id,
		location_id,
		avail_to,
		modified_by,
		modified_at,
		comment,
		can_admin
	) VALUES (
		OLD.id,
		OLD.host,
		OLD.mac,
		OLD.ipv4,
		OLD.user_id,
		OLD.location_id,
		OLD.avail_to,
		OLD.modified_by,
		OLD.modified_at,
		OLD.comment,
		OLD.can_admin
	);
end if;
return NEW;
END;$$
    LANGUAGE plpgsql;


--
-- Name: user_update(); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION user_update() RETURNS "trigger"
    AS $$BEGIN
if
	NEW.name!=OLD.name OR
	NEW.surname!=OLD.surname OR
	NEW.email!=OLD.email OR
	NEW.faculty_id!=OLD.faculty_id OR
	NEW.study_year_id!=OLD.study_year_id OR
	NEW.location_id!=OLD.location_id OR
	NEW.comment!=OLD.comment
then
	INSERT INTO users_history (
		user_id,
		name,
		surname,
		email,
		faculty_id,
		study_year_id,
		location_id,
		modified_by,
		modified_at,
		comment
	) VALUES (
		OLD.id,
		OLD.name,
		OLD.surname,
		OLD.email,
		OLD.faculty_id,
		OLD.study_year_id,
		OLD.location_id,
		OLD.modified_by,
		OLD.modified_at,
		OLD.comment
	);
end if;
return NEW;
END;$$
    LANGUAGE plpgsql;


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: admins; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE admins (
    id bigint NOT NULL,
    "login" character varying NOT NULL,
    "password" character(32) NOT NULL,
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
-- Name: TABLE admins; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE admins IS 'administratorzy';


--
-- Name: COLUMN admins."login"; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins."login" IS 'login';


--
-- Name: COLUMN admins."password"; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins."password" IS 'haslo zakodowane md5';


--
-- Name: COLUMN admins.last_login_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.last_login_at IS 'czas ostatniego logowania';


--
-- Name: COLUMN admins.last_login_ip; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.last_login_ip IS 'ip, z ktorego ostatnio sie logowal';


--
-- Name: COLUMN admins.name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.name IS 'nazwa ekranowa - imie-ksywka-nazwisko albo nazwa bota itp.';


--
-- Name: COLUMN admins.type_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.type_id IS 'typ administratora: lokalny, osiedlowy, centralny, bot';


--
-- Name: COLUMN admins.phone; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.phone IS 'telefon prywatny';


--
-- Name: COLUMN admins.gg; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.gg IS 'numer gadu-gadu';


--
-- Name: COLUMN admins.jid; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.jid IS 'jabber id';


--
-- Name: COLUMN admins.email; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.email IS '"oficjalny" email do administratora';


--
-- Name: COLUMN admins.created_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.created_at IS 'czas utworzenia konta';


--
-- Name: COLUMN admins.dormitory_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.dormitory_id IS 'akademik, nie dotyczy botow i centralnych';


--
-- Name: COLUMN admins.address; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.address IS 'gdzie mieszka administrator';


--
-- Name: COLUMN admins.active; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN admins.active IS 'czy konto jest aktywne?';


--
-- Name: admins_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE admins_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: admins_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE admins_id_seq OWNED BY admins.id;


--
-- Name: computers; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE computers (
    id bigint NOT NULL,
    host character varying(50) NOT NULL,
    mac macaddr NOT NULL,
    ipv4 inet NOT NULL,
    user_id bigint,
    location_id bigint NOT NULL,
    avail_to timestamp without time zone NOT NULL,
    avail_max_to timestamp without time zone NOT NULL,
    modified_by bigint,
    modified_at timestamp without time zone DEFAULT now() NOT NULL,
    "comment" pg_catalog.text DEFAULT ''::pg_catalog.text NOT NULL,
    active boolean DEFAULT true NOT NULL,
    type_id smallint DEFAULT 1 NOT NULL,
    bans smallint DEFAULT 0 NOT NULL,
    can_admin boolean DEFAULT false NOT NULL,
    banned boolean DEFAULT false NOT NULL
);


--
-- Name: TABLE computers; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE computers IS 'komputery';


--
-- Name: COLUMN computers.host; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.host IS 'nazwa hosta';


--
-- Name: COLUMN computers.mac; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.mac IS 'adres mac karty sieciowej';


--
-- Name: COLUMN computers.ipv4; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.ipv4 IS 'adres ip';


--
-- Name: COLUMN computers.user_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.user_id IS 'uzytkownik, do ktorego nalezy ten komputer';


--
-- Name: COLUMN computers.location_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.location_id IS 'pokoj';


--
-- Name: COLUMN computers.avail_to; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.avail_to IS 'do kiedy jest wazna rejestracja';


--
-- Name: COLUMN computers.avail_max_to; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.avail_max_to IS 'do kiedy mozna sobie przedluzyc rejestracje';


--
-- Name: COLUMN computers.modified_by; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.modified_by IS 'kto wprowadzil te dane';


--
-- Name: COLUMN computers.modified_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.modified_at IS 'czas powstania tej wersji';


--
-- Name: COLUMN computers."comment"; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers."comment" IS 'komentarz';


--
-- Name: COLUMN computers.active; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.active IS 'czy komputer ma wazna rejestracje';


--
-- Name: COLUMN computers.type_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.type_id IS 'typ komputera: student, administracja, organizacja, serwer itd.';


--
-- Name: COLUMN computers.bans; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.bans IS 'licznik banow';


--
-- Name: COLUMN computers.can_admin; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.can_admin IS 'komputer nalezy do administratora';


--
-- Name: COLUMN computers.banned; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers.banned IS 'czy komputer jest aktualnie zabanowany?';


--
-- Name: computers_history; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE computers_history (
    computer_id bigint NOT NULL,
    host character varying(50) NOT NULL,
    mac macaddr NOT NULL,
    ipv4 inet NOT NULL,
    user_id bigint,
    location_id bigint NOT NULL,
    avail_to timestamp without time zone NOT NULL,
    modified_by bigint,
    modified_at timestamp without time zone DEFAULT now() NOT NULL,
    "comment" pg_catalog.text NOT NULL,
    can_admin boolean DEFAULT false NOT NULL,
    id bigint NOT NULL
);


--
-- Name: TABLE computers_history; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE computers_history IS 'historia zmian danych komputerow';


--
-- Name: COLUMN computers_history.host; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.host IS 'nazwa hosta';


--
-- Name: COLUMN computers_history.mac; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.mac IS 'adres mac karty sieciowej';


--
-- Name: COLUMN computers_history.ipv4; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.ipv4 IS 'adres ip';


--
-- Name: COLUMN computers_history.user_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.user_id IS 'uzytkownik, do ktorego nalezy ten komputer';


--
-- Name: COLUMN computers_history.location_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.location_id IS 'pokoj';


--
-- Name: COLUMN computers_history.avail_to; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.avail_to IS 'do kiedy jest wazna rejestracja';


--
-- Name: COLUMN computers_history.modified_by; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.modified_by IS 'kto wprowadzil te dane';


--
-- Name: COLUMN computers_history.modified_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.modified_at IS 'czas powstania tej wersji';


--
-- Name: COLUMN computers_history."comment"; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history."comment" IS 'komentarz';


--
-- Name: COLUMN computers_history.can_admin; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN computers_history.can_admin IS 'komputer nalezy do administratora';


--
-- Name: computers_history_computer_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE computers_history_computer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: computers_history_computer_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE computers_history_computer_id_seq OWNED BY computers_history.computer_id;


--
-- Name: computers_history_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE computers_history_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: computers_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE computers_history_id_seq OWNED BY computers_history.id;


--
-- Name: computers_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE computers_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: computers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE computers_id_seq OWNED BY computers.id;


--
-- Name: dormitories; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE dormitories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    alias character varying(10) NOT NULL,
    users_count integer DEFAULT 0 NOT NULL,
    computers_count integer DEFAULT 0 NOT NULL
);


--
-- Name: TABLE dormitories; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE dormitories IS 'akademiki';


--
-- Name: COLUMN dormitories.name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN dormitories.name IS 'pelna nazwa';


--
-- Name: COLUMN dormitories.alias; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN dormitories.alias IS 'skrot, uzywany do budowy url-i';


--
-- Name: COLUMN dormitories.users_count; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN dormitories.users_count IS 'ilosc zarejestrowanych uzytkownikow';


--
-- Name: COLUMN dormitories.computers_count; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN dormitories.computers_count IS 'ilosc zarejestrowanych komputerow';


--
-- Name: dormitories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE dormitories_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: dormitories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE dormitories_id_seq OWNED BY dormitories.id;


--
-- Name: faculties; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE faculties (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    alias character varying(10) NOT NULL,
    users_count integer DEFAULT 0 NOT NULL,
    computers_count integer DEFAULT 0 NOT NULL
);


--
-- Name: TABLE faculties; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE faculties IS 'wydzialy';


--
-- Name: COLUMN faculties.name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN faculties.name IS 'nazwa wydzialu';


--
-- Name: COLUMN faculties.alias; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN faculties.alias IS 'skrot nazwy, uzywany do budowy url-i';


--
-- Name: COLUMN faculties.users_count; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN faculties.users_count IS 'ilosc zarejestrowanych uzytkownikow';


--
-- Name: COLUMN faculties.computers_count; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN faculties.computers_count IS 'ilosc zarejestrowanych komputerow';


--
-- Name: faulties_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE faulties_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: faulties_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE faulties_id_seq OWNED BY faculties.id;


--
-- Name: ipv4s; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE ipv4s (
    ip inet NOT NULL,
    dormitory_id bigint NOT NULL
);


--
-- Name: TABLE ipv4s; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE ipv4s IS 'dostepne adresy ip';


--
-- Name: COLUMN ipv4s.ip; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN ipv4s.ip IS 'adres ip';


--
-- Name: COLUMN ipv4s.dormitory_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN ipv4s.dormitory_id IS 'akademik';


--
-- Name: locations; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE locations (
    id bigint NOT NULL,
    alias character varying(10) NOT NULL,
    "comment" pg_catalog.text DEFAULT ''::pg_catalog.text NOT NULL,
    users_count integer DEFAULT 0 NOT NULL,
    computers_count integer DEFAULT 0 NOT NULL,
    dormitory_id bigint NOT NULL
);


--
-- Name: TABLE locations; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE locations IS 'pokoje';


--
-- Name: COLUMN locations.alias; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN locations.alias IS 'unikalna nazwa pokoju, uzywana do budowy url-i';


--
-- Name: COLUMN locations."comment"; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN locations."comment" IS 'komentarz do pokoju';


--
-- Name: COLUMN locations.users_count; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN locations.users_count IS 'ilosc zarejestrowanych uzytkownikow';


--
-- Name: COLUMN locations.computers_count; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN locations.computers_count IS 'ilosc zarejestrowanych komputerow';


--
-- Name: COLUMN locations.dormitory_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN locations.dormitory_id IS 'akademik, w ktorym znajduje sie pokoj';


--
-- Name: locations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE locations_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: locations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE locations_id_seq OWNED BY locations.id;


--
-- Name: text; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE text (
    id bigint NOT NULL,
    alias pg_catalog.text NOT NULL,
    title pg_catalog.text NOT NULL,
    content pg_catalog.text NOT NULL,
    modified_at timestamp without time zone DEFAULT now() NOT NULL,
    modified_by bigint
);


--
-- Name: TABLE text; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE text IS 'statyczne strony tekstowe';


--
-- Name: COLUMN text.alias; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN text.alias IS '"url"';


--
-- Name: COLUMN text.title; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN text.title IS 'tytul';


--
-- Name: COLUMN text.content; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN text.content IS 'tresc glowna';


--
-- Name: COLUMN text.modified_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN text.modified_at IS 'data ostatniej modyfikacji';


--
-- Name: COLUMN text.modified_by; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN text.modified_by IS 'kto dokonal modyfikacji';


--
-- Name: text_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE text_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: text_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE text_id_seq OWNED BY text.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE users (
    id bigint NOT NULL,
    "login" character varying NOT NULL,
    "password" character(32) NOT NULL,
    surname character varying(100) NOT NULL,
    email character varying(100) NOT NULL,
    faculty_id bigint,
    study_year_id smallint,
    location_id bigint NOT NULL,
    bans smallint DEFAULT 0 NOT NULL,
    modified_by bigint,
    modified_at timestamp without time zone DEFAULT now() NOT NULL,
    "comment" pg_catalog.text DEFAULT ''::pg_catalog.text NOT NULL,
    name character varying(100) NOT NULL
);


--
-- Name: TABLE users; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE users IS 'uzytkownicy sieci';


--
-- Name: COLUMN users."login"; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users."login" IS 'login';


--
-- Name: COLUMN users."password"; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users."password" IS 'haslo zakodowane md5';


--
-- Name: COLUMN users.surname; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.surname IS 'nazwisko';


--
-- Name: COLUMN users.email; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.email IS 'email';


--
-- Name: COLUMN users.faculty_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.faculty_id IS 'wydzial ,jezeli dotyczy';


--
-- Name: COLUMN users.study_year_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.study_year_id IS 'identyfikator roku studiow, jezeli dotyczy';


--
-- Name: COLUMN users.location_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.location_id IS 'miejsce zamieszkania';


--
-- Name: COLUMN users.bans; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.bans IS 'ilosc otrzymanych banow';


--
-- Name: COLUMN users.modified_by; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.modified_by IS 'kto wprowadzil te dane';


--
-- Name: COLUMN users.modified_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.modified_at IS 'czas powstania tej wersji';


--
-- Name: COLUMN users."comment"; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users."comment" IS 'komentarze dotyczace uzytkownika';


--
-- Name: COLUMN users.name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users.name IS 'imie';


--
-- Name: users_history; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE users_history (
    user_id bigint NOT NULL,
    name character varying(50) NOT NULL,
    surname character varying(100) NOT NULL,
    email character varying(100) NOT NULL,
    faculty_id bigint,
    study_year_id smallint,
    location_id bigint NOT NULL,
    modified_by bigint,
    modified_at timestamp without time zone NOT NULL,
    "comment" pg_catalog.text NOT NULL,
    id bigint NOT NULL
);


--
-- Name: TABLE users_history; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE users_history IS 'historia zmian danych uzytkownikow';


--
-- Name: COLUMN users_history.user_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.user_id IS 'id uzytkownika';


--
-- Name: COLUMN users_history.name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.name IS 'imie';


--
-- Name: COLUMN users_history.surname; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.surname IS 'nazwisko';


--
-- Name: COLUMN users_history.email; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.email IS 'email';


--
-- Name: COLUMN users_history.faculty_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.faculty_id IS 'wydzial';


--
-- Name: COLUMN users_history.study_year_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.study_year_id IS 'identyfikator roku studiow';


--
-- Name: COLUMN users_history.location_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.location_id IS 'miejsce zamieszkania';


--
-- Name: COLUMN users_history.modified_by; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.modified_by IS 'kto wprowadzil te dane';


--
-- Name: COLUMN users_history.modified_at; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history.modified_at IS 'czas powstania tej wersji';


--
-- Name: COLUMN users_history."comment"; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN users_history."comment" IS 'komentarz';


--
-- Name: users_history_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE users_history_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: users_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE users_history_id_seq OWNED BY users_history.id;


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE users_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE admins ALTER COLUMN id SET DEFAULT nextval('admins_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE computers ALTER COLUMN id SET DEFAULT nextval('computers_id_seq'::regclass);


--
-- Name: computer_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE computers_history ALTER COLUMN computer_id SET DEFAULT nextval('computers_history_computer_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE computers_history ALTER COLUMN id SET DEFAULT nextval('computers_history_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE dormitories ALTER COLUMN id SET DEFAULT nextval('dormitories_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE faculties ALTER COLUMN id SET DEFAULT nextval('faulties_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE locations ALTER COLUMN id SET DEFAULT nextval('locations_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE text ALTER COLUMN id SET DEFAULT nextval('text_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE users_history ALTER COLUMN id SET DEFAULT nextval('users_history_id_seq'::regclass);


--
-- Name: admins_login_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY admins
    ADD CONSTRAINT admins_login_key UNIQUE ("login", active);


--
-- Name: admins_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY admins
    ADD CONSTRAINT admins_pkey PRIMARY KEY (id);


--
-- Name: computers_history_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY computers_history
    ADD CONSTRAINT computers_history_pkey PRIMARY KEY (id);


--
-- Name: computers_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY computers
    ADD CONSTRAINT computers_pkey PRIMARY KEY (id);


--
-- Name: dormitories_alias_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY dormitories
    ADD CONSTRAINT dormitories_alias_key UNIQUE (alias);


--
-- Name: dormitories_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY dormitories
    ADD CONSTRAINT dormitories_pkey PRIMARY KEY (id);


--
-- Name: faulties_alias_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY faculties
    ADD CONSTRAINT faulties_alias_key UNIQUE (alias);


--
-- Name: faulties_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY faculties
    ADD CONSTRAINT faulties_pkey PRIMARY KEY (id);


--
-- Name: ipv4s_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY ipv4s
    ADD CONSTRAINT ipv4s_pkey PRIMARY KEY (ip);


--
-- Name: locations_alias_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY locations
    ADD CONSTRAINT locations_alias_key UNIQUE (alias, dormitory_id);


--
-- Name: locations_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY locations
    ADD CONSTRAINT locations_pkey PRIMARY KEY (id);


--
-- Name: text_alias_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY text
    ADD CONSTRAINT text_alias_key UNIQUE (alias);


--
-- Name: text_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY text
    ADD CONSTRAINT text_pkey PRIMARY KEY (id);


--
-- Name: users_history_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY users_history
    ADD CONSTRAINT users_history_pkey PRIMARY KEY (id);


--
-- Name: users_login_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_login_key UNIQUE ("login");


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: computers_host_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX computers_host_key ON computers USING btree (host, active) WHERE (active = true);


--
-- Name: computers_ipv4_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX computers_ipv4_key ON computers USING btree (ipv4, active) WHERE (active = true);


--
-- Name: computers_mac_key; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX computers_mac_key ON computers USING btree (mac, active) WHERE (active = true);


--
-- Name: computers_update; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER computers_update
    AFTER UPDATE ON computers
    FOR EACH ROW
    EXECUTE PROCEDURE computer_update();


--
-- Name: TRIGGER computers_update ON computers; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TRIGGER computers_update ON computers IS 'zapisuje historie zmian';


--
-- Name: users_update; Type: TRIGGER; Schema: public; Owner: -
--

CREATE TRIGGER users_update
    AFTER UPDATE ON users
    FOR EACH ROW
    EXECUTE PROCEDURE user_update();


--
-- Name: TRIGGER users_update ON users; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TRIGGER users_update ON users IS 'kopiuje dane do historii';


--
-- Name: admins_dormitory_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY admins
    ADD CONSTRAINT admins_dormitory_id_fkey FOREIGN KEY (dormitory_id) REFERENCES dormitories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: computers_history_computer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers_history
    ADD CONSTRAINT computers_history_computer_id_fkey FOREIGN KEY (computer_id) REFERENCES computers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: computers_history_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers_history
    ADD CONSTRAINT computers_history_location_id_fkey FOREIGN KEY (location_id) REFERENCES locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: computers_history_modified_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers_history
    ADD CONSTRAINT computers_history_modified_by_fkey FOREIGN KEY (modified_by) REFERENCES admins(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: computers_history_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers_history
    ADD CONSTRAINT computers_history_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: computers_ipv4_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers
    ADD CONSTRAINT computers_ipv4_fkey FOREIGN KEY (ipv4) REFERENCES ipv4s(ip) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: computers_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers
    ADD CONSTRAINT computers_location_id_fkey FOREIGN KEY (location_id) REFERENCES locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: computers_modified_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers
    ADD CONSTRAINT computers_modified_by_fkey FOREIGN KEY (modified_by) REFERENCES admins(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: computers_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY computers
    ADD CONSTRAINT computers_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: ipv4s_dormitory_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY ipv4s
    ADD CONSTRAINT ipv4s_dormitory_id_fkey FOREIGN KEY (dormitory_id) REFERENCES dormitories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: locations_dormitory_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY locations
    ADD CONSTRAINT locations_dormitory_id_fkey FOREIGN KEY (dormitory_id) REFERENCES dormitories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users_faculty_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_faculty_id_fkey FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users_history_faculty_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users_history
    ADD CONSTRAINT users_history_faculty_id_fkey FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users_history_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users_history
    ADD CONSTRAINT users_history_location_id_fkey FOREIGN KEY (location_id) REFERENCES locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users_history_modified_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users_history
    ADD CONSTRAINT users_history_modified_by_fkey FOREIGN KEY (modified_by) REFERENCES admins(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users_history_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users_history
    ADD CONSTRAINT users_history_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_location_id_fkey FOREIGN KEY (location_id) REFERENCES locations(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users_modified_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_modified_by_fkey FOREIGN KEY (modified_by) REFERENCES admins(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

