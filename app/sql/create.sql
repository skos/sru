--
-- PostgreSQL database dump
--

-- Started on 2008-02-13 11:51:54



SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- TOC entry 1764 (class 0 OID 0)
-- Dependencies: 5
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


--
-- TOC entry 293 (class 2612 OID 16386)
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: postgres
--

CREATE PROCEDURAL LANGUAGE plpgsql;


ALTER PROCEDURAL LANGUAGE plpgsql OWNER TO postgres;

SET search_path = public, pg_catalog;

--
-- TOC entry 18 (class 1255 OID 17801)
-- Dependencies: 293 5
-- Name: computer_update(); Type: FUNCTION; Schema: public; Owner: postgres
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
	OLD.avail_max_to!=NEW.avail_max_to OR
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
		avail_max_to,
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
		OLD.avail_max_to,
		OLD.modified_by,
		OLD.modified_at,
		OLD.comment,
		OLD.can_admin
	);
end if;
return NEW;
END;$$
    LANGUAGE plpgsql;


ALTER FUNCTION public.computer_update() OWNER TO postgres;

--
-- TOC entry 19 (class 1255 OID 17802)
-- Dependencies: 293 5
-- Name: user_update(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION user_update() RETURNS "trigger"
    AS $$BEGIN
if
	NEW.name!=OLD.name OR
	NEW.surname!=OLD.surname OR
	NEW.login!=OLD.login OR
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
		login,
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
		OLD.login,
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


ALTER FUNCTION public.user_update() OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 1298 (class 1259 OID 17803)
-- Dependencies: 1650 1651 1652 1653 1654 1655 1656 5
-- Name: admins; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE admins (
    id bigint NOT NULL,
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


ALTER TABLE public.admins OWNER TO postgres;

--
-- TOC entry 1766 (class 0 OID 0)
-- Dependencies: 1298
-- Name: TABLE admins; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE admins IS 'administratorzy';


--
-- TOC entry 1767 (class 0 OID 0)
-- Dependencies: 1298
-- Name: COLUMN admins.login; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN admins.login IS 'login';


--
-- TOC entry 1768 (class 0 OID 0)
-- Dependencies: 1298
-- Name: COLUMN admins.password; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN admins.password IS 'haslo zakodowane md5';


--
-- TOC entry 1769 (class 0 OID 0)
-- Dependencies: 1298
-- Name: COLUMN admins.last_login_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN admins.last_login_at IS 'czas ostatniego logowania';


--
-- TOC entry 1770 (class 0 OID 0)
-- Dependencies: 1298
-- Name: COLUMN admins.last_login_ip; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN admins.last_login_ip IS 'ip, z ktorego ostatnio sie logowal';


--
-- TOC entry 1771 (class 0 OID 0)
-- Dependencies: 1298
-- Name: COLUMN admins.name; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN admins.name IS 'nazwa ekranowa - imie-ksywka-nazwisko albo nazwa bota itp.';


--
-- TOC entry 1772 (class 0 OID 0)
-- Dependencies: 1298
-- Name: COLUMN admins.type_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN admins.type_id IS 'typ administratora: lokalny, osiedlowy, centralny, bot';


--
-- TOC entry 1773 (class 0 OID 0)
-- Dependencies: 1298
-- Name: COLUMN admins.phone; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN admins.phone IS 'telefon prywatny';


--
-- TOC entry 1774 (class 0 OID 0)
-- Dependencies: 1298
-- Name: COLUMN admins.gg; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN admins.gg IS 'numer gadu-gadu';


--
-- TOC entry 1775 (class 0 OID 0)
-- Dependencies: 1298
-- Name: COLUMN admins.jid; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN admins.jid IS 'jabber id';


--
-- TOC entry 1776 (class 0 OID 0)
-- Dependencies: 1298
-- Name: COLUMN admins.email; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN admins.email IS '"oficjalny" email do administratora';


--
-- TOC entry 1777 (class 0 OID 0)
-- Dependencies: 1298
-- Name: COLUMN admins.created_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN admins.created_at IS 'czas utworzenia konta';


--
-- TOC entry 1778 (class 0 OID 0)
-- Dependencies: 1298
-- Name: COLUMN admins.dormitory_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN admins.dormitory_id IS 'akademik, nie dotyczy botow i centralnych';


--
-- TOC entry 1779 (class 0 OID 0)
-- Dependencies: 1298
-- Name: COLUMN admins.address; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN admins.address IS 'gdzie mieszka administrator';


--
-- TOC entry 1780 (class 0 OID 0)
-- Dependencies: 1298
-- Name: COLUMN admins.active; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN admins.active IS 'czy konto jest aktywne?';


--
-- TOC entry 1299 (class 1259 OID 17815)
-- Dependencies: 5 1298
-- Name: admins_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE admins_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.admins_id_seq OWNER TO postgres;

--
-- TOC entry 1781 (class 0 OID 0)
-- Dependencies: 1299
-- Name: admins_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE admins_id_seq OWNED BY admins.id;


--
-- TOC entry 1782 (class 0 OID 0)
-- Dependencies: 1299
-- Name: admins_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('admins_id_seq', 11, true);


--
-- TOC entry 1319 (class 1259 OID 18043)
-- Dependencies: 5
-- Name: bans_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE bans_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.bans_id_seq OWNER TO postgres;

--
-- TOC entry 1783 (class 0 OID 0)
-- Dependencies: 1319
-- Name: bans_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('bans_id_seq', 72, true);


--
-- TOC entry 1300 (class 1259 OID 17817)
-- Dependencies: 1658 1659 1660 1661 1662 1663 1664 5
-- Name: computers; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE computers (
    id bigint NOT NULL,
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
    bans smallint DEFAULT 0 NOT NULL,
    can_admin boolean DEFAULT false NOT NULL,
    banned boolean DEFAULT false NOT NULL
);


ALTER TABLE public.computers OWNER TO postgres;

--
-- TOC entry 1784 (class 0 OID 0)
-- Dependencies: 1300
-- Name: TABLE computers; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE computers IS 'komputery';


--
-- TOC entry 1785 (class 0 OID 0)
-- Dependencies: 1300
-- Name: COLUMN computers.host; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers.host IS 'nazwa hosta';


--
-- TOC entry 1786 (class 0 OID 0)
-- Dependencies: 1300
-- Name: COLUMN computers.mac; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers.mac IS 'adres mac karty sieciowej';


--
-- TOC entry 1787 (class 0 OID 0)
-- Dependencies: 1300
-- Name: COLUMN computers.ipv4; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers.ipv4 IS 'adres ip';


--
-- TOC entry 1788 (class 0 OID 0)
-- Dependencies: 1300
-- Name: COLUMN computers.user_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers.user_id IS 'uzytkownik, do ktorego nalezy ten komputer';


--
-- TOC entry 1789 (class 0 OID 0)
-- Dependencies: 1300
-- Name: COLUMN computers.location_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers.location_id IS 'pokoj';


--
-- TOC entry 1790 (class 0 OID 0)
-- Dependencies: 1300
-- Name: COLUMN computers.avail_to; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers.avail_to IS 'do kiedy jest wazna rejestracja';


--
-- TOC entry 1791 (class 0 OID 0)
-- Dependencies: 1300
-- Name: COLUMN computers.avail_max_to; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers.avail_max_to IS 'do kiedy mozna sobie przedluzyc rejestracje';


--
-- TOC entry 1792 (class 0 OID 0)
-- Dependencies: 1300
-- Name: COLUMN computers.modified_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers.modified_by IS 'kto wprowadzil te dane';


--
-- TOC entry 1793 (class 0 OID 0)
-- Dependencies: 1300
-- Name: COLUMN computers.modified_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers.modified_at IS 'czas powstania tej wersji';


--
-- TOC entry 1794 (class 0 OID 0)
-- Dependencies: 1300
-- Name: COLUMN computers.comment; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers.comment IS 'komentarz';


--
-- TOC entry 1795 (class 0 OID 0)
-- Dependencies: 1300
-- Name: COLUMN computers.active; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers.active IS 'czy komputer ma wazna rejestracje';


--
-- TOC entry 1796 (class 0 OID 0)
-- Dependencies: 1300
-- Name: COLUMN computers.type_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers.type_id IS 'typ komputera: student, administracja, organizacja, serwer itd.';


--
-- TOC entry 1797 (class 0 OID 0)
-- Dependencies: 1300
-- Name: COLUMN computers.bans; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers.bans IS 'licznik banow';


--
-- TOC entry 1798 (class 0 OID 0)
-- Dependencies: 1300
-- Name: COLUMN computers.can_admin; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers.can_admin IS 'komputer nalezy do administratora';


--
-- TOC entry 1799 (class 0 OID 0)
-- Dependencies: 1300
-- Name: COLUMN computers.banned; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers.banned IS 'czy komputer jest aktualnie zabanowany?';


--
-- TOC entry 1301 (class 1259 OID 17829)
-- Dependencies: 1666 1667 5
-- Name: computers_history; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE computers_history (
    computer_id bigint NOT NULL,
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
    id bigint NOT NULL,
    avail_max_to timestamp without time zone NOT NULL
);


ALTER TABLE public.computers_history OWNER TO postgres;

--
-- TOC entry 1800 (class 0 OID 0)
-- Dependencies: 1301
-- Name: TABLE computers_history; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE computers_history IS 'historia zmian danych komputerow';


--
-- TOC entry 1801 (class 0 OID 0)
-- Dependencies: 1301
-- Name: COLUMN computers_history.host; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers_history.host IS 'nazwa hosta';


--
-- TOC entry 1802 (class 0 OID 0)
-- Dependencies: 1301
-- Name: COLUMN computers_history.mac; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers_history.mac IS 'adres mac karty sieciowej';


--
-- TOC entry 1803 (class 0 OID 0)
-- Dependencies: 1301
-- Name: COLUMN computers_history.ipv4; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers_history.ipv4 IS 'adres ip';


--
-- TOC entry 1804 (class 0 OID 0)
-- Dependencies: 1301
-- Name: COLUMN computers_history.user_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers_history.user_id IS 'uzytkownik, do ktorego nalezy ten komputer';


--
-- TOC entry 1805 (class 0 OID 0)
-- Dependencies: 1301
-- Name: COLUMN computers_history.location_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers_history.location_id IS 'pokoj';


--
-- TOC entry 1806 (class 0 OID 0)
-- Dependencies: 1301
-- Name: COLUMN computers_history.avail_to; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers_history.avail_to IS 'do kiedy jest wazna rejestracja';


--
-- TOC entry 1807 (class 0 OID 0)
-- Dependencies: 1301
-- Name: COLUMN computers_history.modified_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers_history.modified_by IS 'kto wprowadzil te dane';


--
-- TOC entry 1808 (class 0 OID 0)
-- Dependencies: 1301
-- Name: COLUMN computers_history.modified_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers_history.modified_at IS 'czas powstania tej wersji';


--
-- TOC entry 1809 (class 0 OID 0)
-- Dependencies: 1301
-- Name: COLUMN computers_history.comment; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers_history.comment IS 'komentarz';


--
-- TOC entry 1810 (class 0 OID 0)
-- Dependencies: 1301
-- Name: COLUMN computers_history.can_admin; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN computers_history.can_admin IS 'komputer nalezy do administratora';


--
-- TOC entry 1302 (class 1259 OID 17836)
-- Dependencies: 5 1301
-- Name: computers_history_computer_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE computers_history_computer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.computers_history_computer_id_seq OWNER TO postgres;

--
-- TOC entry 1811 (class 0 OID 0)
-- Dependencies: 1302
-- Name: computers_history_computer_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE computers_history_computer_id_seq OWNED BY computers_history.computer_id;


--
-- TOC entry 1812 (class 0 OID 0)
-- Dependencies: 1302
-- Name: computers_history_computer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('computers_history_computer_id_seq', 1, false);


--
-- TOC entry 1303 (class 1259 OID 17838)
-- Dependencies: 1301 5
-- Name: computers_history_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE computers_history_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.computers_history_id_seq OWNER TO postgres;

--
-- TOC entry 1813 (class 0 OID 0)
-- Dependencies: 1303
-- Name: computers_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE computers_history_id_seq OWNED BY computers_history.id;


--
-- TOC entry 1814 (class 0 OID 0)
-- Dependencies: 1303
-- Name: computers_history_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('computers_history_id_seq', 69, true);


--
-- TOC entry 1304 (class 1259 OID 17840)
-- Dependencies: 5 1300
-- Name: computers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE computers_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.computers_id_seq OWNER TO postgres;

--
-- TOC entry 1815 (class 0 OID 0)
-- Dependencies: 1304
-- Name: computers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE computers_id_seq OWNED BY computers.id;


--
-- TOC entry 1816 (class 0 OID 0)
-- Dependencies: 1304
-- Name: computers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('computers_id_seq', 32, true);


--
-- TOC entry 1305 (class 1259 OID 17842)
-- Dependencies: 1670 1671 5
-- Name: dormitories; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE dormitories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    alias character varying(10) NOT NULL,
    users_count integer DEFAULT 0 NOT NULL,
    computers_count integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.dormitories OWNER TO postgres;

--
-- TOC entry 1817 (class 0 OID 0)
-- Dependencies: 1305
-- Name: TABLE dormitories; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE dormitories IS 'akademiki';


--
-- TOC entry 1818 (class 0 OID 0)
-- Dependencies: 1305
-- Name: COLUMN dormitories.name; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN dormitories.name IS 'pelna nazwa';


--
-- TOC entry 1819 (class 0 OID 0)
-- Dependencies: 1305
-- Name: COLUMN dormitories.alias; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN dormitories.alias IS 'skrot, uzywany do budowy url-i';


--
-- TOC entry 1820 (class 0 OID 0)
-- Dependencies: 1305
-- Name: COLUMN dormitories.users_count; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN dormitories.users_count IS 'ilosc zarejestrowanych uzytkownikow';


--
-- TOC entry 1821 (class 0 OID 0)
-- Dependencies: 1305
-- Name: COLUMN dormitories.computers_count; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN dormitories.computers_count IS 'ilosc zarejestrowanych komputerow';


--
-- TOC entry 1306 (class 1259 OID 17846)
-- Dependencies: 5 1305
-- Name: dormitories_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE dormitories_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.dormitories_id_seq OWNER TO postgres;

--
-- TOC entry 1822 (class 0 OID 0)
-- Dependencies: 1306
-- Name: dormitories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE dormitories_id_seq OWNED BY dormitories.id;


--
-- TOC entry 1823 (class 0 OID 0)
-- Dependencies: 1306
-- Name: dormitories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('dormitories_id_seq', 13, true);


--
-- TOC entry 1307 (class 1259 OID 17848)
-- Dependencies: 1673 1674 5
-- Name: faculties; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE faculties (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    alias character varying(10) NOT NULL,
    users_count integer DEFAULT 0 NOT NULL,
    computers_count integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.faculties OWNER TO postgres;

--
-- TOC entry 1824 (class 0 OID 0)
-- Dependencies: 1307
-- Name: TABLE faculties; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE faculties IS 'wydzialy';


--
-- TOC entry 1825 (class 0 OID 0)
-- Dependencies: 1307
-- Name: COLUMN faculties.name; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN faculties.name IS 'nazwa wydzialu';


--
-- TOC entry 1826 (class 0 OID 0)
-- Dependencies: 1307
-- Name: COLUMN faculties.alias; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN faculties.alias IS 'skrot nazwy, uzywany do budowy url-i';


--
-- TOC entry 1827 (class 0 OID 0)
-- Dependencies: 1307
-- Name: COLUMN faculties.users_count; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN faculties.users_count IS 'ilosc zarejestrowanych uzytkownikow';


--
-- TOC entry 1828 (class 0 OID 0)
-- Dependencies: 1307
-- Name: COLUMN faculties.computers_count; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN faculties.computers_count IS 'ilosc zarejestrowanych komputerow';


--
-- TOC entry 1308 (class 1259 OID 17852)
-- Dependencies: 5 1307
-- Name: faulties_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE faulties_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.faulties_id_seq OWNER TO postgres;

--
-- TOC entry 1829 (class 0 OID 0)
-- Dependencies: 1308
-- Name: faulties_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE faulties_id_seq OWNED BY faculties.id;


--
-- TOC entry 1830 (class 0 OID 0)
-- Dependencies: 1308
-- Name: faulties_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('faulties_id_seq', 9, true);


--
-- TOC entry 1309 (class 1259 OID 17854)
-- Dependencies: 5
-- Name: ipv4s; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ipv4s (
    ip inet NOT NULL,
    dormitory_id bigint NOT NULL
);


ALTER TABLE public.ipv4s OWNER TO postgres;

--
-- TOC entry 1831 (class 0 OID 0)
-- Dependencies: 1309
-- Name: TABLE ipv4s; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE ipv4s IS 'dostepne adresy ip';


--
-- TOC entry 1832 (class 0 OID 0)
-- Dependencies: 1309
-- Name: COLUMN ipv4s.ip; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN ipv4s.ip IS 'adres ip';


--
-- TOC entry 1833 (class 0 OID 0)
-- Dependencies: 1309
-- Name: COLUMN ipv4s.dormitory_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN ipv4s.dormitory_id IS 'akademik';


--
-- TOC entry 1310 (class 1259 OID 17856)
-- Dependencies: 1676 1677 1678 5
-- Name: locations; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE locations (
    id bigint NOT NULL,
    alias character varying(10) NOT NULL,
    comment pg_catalog.text DEFAULT ''::pg_catalog.text NOT NULL,
    users_count integer DEFAULT 0 NOT NULL,
    computers_count integer DEFAULT 0 NOT NULL,
    dormitory_id bigint NOT NULL
);


ALTER TABLE public.locations OWNER TO postgres;

--
-- TOC entry 1834 (class 0 OID 0)
-- Dependencies: 1310
-- Name: TABLE locations; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE locations IS 'pokoje';


--
-- TOC entry 1835 (class 0 OID 0)
-- Dependencies: 1310
-- Name: COLUMN locations.alias; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN locations.alias IS 'unikalna nazwa pokoju, uzywana do budowy url-i';


--
-- TOC entry 1836 (class 0 OID 0)
-- Dependencies: 1310
-- Name: COLUMN locations.comment; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN locations.comment IS 'komentarz do pokoju';


--
-- TOC entry 1837 (class 0 OID 0)
-- Dependencies: 1310
-- Name: COLUMN locations.users_count; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN locations.users_count IS 'ilosc zarejestrowanych uzytkownikow';


--
-- TOC entry 1838 (class 0 OID 0)
-- Dependencies: 1310
-- Name: COLUMN locations.computers_count; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN locations.computers_count IS 'ilosc zarejestrowanych komputerow';


--
-- TOC entry 1839 (class 0 OID 0)
-- Dependencies: 1310
-- Name: COLUMN locations.dormitory_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN locations.dormitory_id IS 'akademik, w ktorym znajduje sie pokoj';


--
-- TOC entry 1311 (class 1259 OID 17864)
-- Dependencies: 1310 5
-- Name: locations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE locations_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.locations_id_seq OWNER TO postgres;

--
-- TOC entry 1840 (class 0 OID 0)
-- Dependencies: 1311
-- Name: locations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE locations_id_seq OWNED BY locations.id;


--
-- TOC entry 1841 (class 0 OID 0)
-- Dependencies: 1311
-- Name: locations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('locations_id_seq', 697, true);


--
-- TOC entry 1312 (class 1259 OID 17866)
-- Dependencies: 1680 5
-- Name: text; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE text (
    id bigint NOT NULL,
    alias pg_catalog.text NOT NULL,
    title pg_catalog.text NOT NULL,
    content pg_catalog.text NOT NULL,
    modified_at timestamp without time zone DEFAULT now() NOT NULL,
    modified_by bigint
);


ALTER TABLE public.text OWNER TO postgres;

--
-- TOC entry 1842 (class 0 OID 0)
-- Dependencies: 1312
-- Name: TABLE text; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE text IS 'statyczne strony tekstowe';


--
-- TOC entry 1843 (class 0 OID 0)
-- Dependencies: 1312
-- Name: COLUMN text.alias; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN text.alias IS '"url"';


--
-- TOC entry 1844 (class 0 OID 0)
-- Dependencies: 1312
-- Name: COLUMN text.title; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN text.title IS 'tytul';


--
-- TOC entry 1845 (class 0 OID 0)
-- Dependencies: 1312
-- Name: COLUMN text.content; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN text.content IS 'tresc glowna';


--
-- TOC entry 1846 (class 0 OID 0)
-- Dependencies: 1312
-- Name: COLUMN text.modified_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN text.modified_at IS 'data ostatniej modyfikacji';


--
-- TOC entry 1847 (class 0 OID 0)
-- Dependencies: 1312
-- Name: COLUMN text.modified_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN text.modified_by IS 'kto dokonal modyfikacji';


--
-- TOC entry 1313 (class 1259 OID 17872)
-- Dependencies: 1312 5
-- Name: text_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE text_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.text_id_seq OWNER TO postgres;

--
-- TOC entry 1848 (class 0 OID 0)
-- Dependencies: 1313
-- Name: text_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE text_id_seq OWNED BY text.id;


--
-- TOC entry 1849 (class 0 OID 0)
-- Dependencies: 1313
-- Name: text_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('text_id_seq', 31, true);


--
-- TOC entry 1314 (class 1259 OID 17874)
-- Dependencies: 1682 1683 1684 5
-- Name: users; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE users (
    id bigint NOT NULL,
    login character varying NOT NULL,
    password character(32) NOT NULL,
    surname character varying(100) NOT NULL,
    email character varying(100) NOT NULL,
    faculty_id bigint,
    study_year_id smallint,
    location_id bigint NOT NULL,
    bans smallint DEFAULT 0 NOT NULL,
    modified_by bigint,
    modified_at timestamp without time zone DEFAULT now() NOT NULL,
    comment pg_catalog.text DEFAULT ''::pg_catalog.text NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 1850 (class 0 OID 0)
-- Dependencies: 1314
-- Name: TABLE users; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE users IS 'uzytkownicy sieci';


--
-- TOC entry 1851 (class 0 OID 0)
-- Dependencies: 1314
-- Name: COLUMN users.login; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users.login IS 'login';


--
-- TOC entry 1852 (class 0 OID 0)
-- Dependencies: 1314
-- Name: COLUMN users.password; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users.password IS 'haslo zakodowane md5';


--
-- TOC entry 1853 (class 0 OID 0)
-- Dependencies: 1314
-- Name: COLUMN users.surname; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users.surname IS 'nazwisko';


--
-- TOC entry 1854 (class 0 OID 0)
-- Dependencies: 1314
-- Name: COLUMN users.email; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users.email IS 'email';


--
-- TOC entry 1855 (class 0 OID 0)
-- Dependencies: 1314
-- Name: COLUMN users.faculty_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users.faculty_id IS 'wydzial ,jezeli dotyczy';


--
-- TOC entry 1856 (class 0 OID 0)
-- Dependencies: 1314
-- Name: COLUMN users.study_year_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users.study_year_id IS 'identyfikator roku studiow, jezeli dotyczy';


--
-- TOC entry 1857 (class 0 OID 0)
-- Dependencies: 1314
-- Name: COLUMN users.location_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users.location_id IS 'miejsce zamieszkania';


--
-- TOC entry 1858 (class 0 OID 0)
-- Dependencies: 1314
-- Name: COLUMN users.bans; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users.bans IS 'ilosc otrzymanych banow';


--
-- TOC entry 1859 (class 0 OID 0)
-- Dependencies: 1314
-- Name: COLUMN users.modified_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users.modified_by IS 'kto wprowadzil te dane';


--
-- TOC entry 1860 (class 0 OID 0)
-- Dependencies: 1314
-- Name: COLUMN users.modified_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users.modified_at IS 'czas powstania tej wersji';


--
-- TOC entry 1861 (class 0 OID 0)
-- Dependencies: 1314
-- Name: COLUMN users.comment; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users.comment IS 'komentarze dotyczace uzytkownika';


--
-- TOC entry 1862 (class 0 OID 0)
-- Dependencies: 1314
-- Name: COLUMN users.name; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users.name IS 'imie';


--
-- TOC entry 1315 (class 1259 OID 17882)
-- Dependencies: 5
-- Name: users_history; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
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
    comment pg_catalog.text NOT NULL,
    id bigint NOT NULL,
    login character varying NOT NULL
);


ALTER TABLE public.users_history OWNER TO postgres;

--
-- TOC entry 1863 (class 0 OID 0)
-- Dependencies: 1315
-- Name: TABLE users_history; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE users_history IS 'historia zmian danych uzytkownikow';


--
-- TOC entry 1864 (class 0 OID 0)
-- Dependencies: 1315
-- Name: COLUMN users_history.user_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users_history.user_id IS 'id uzytkownika';


--
-- TOC entry 1865 (class 0 OID 0)
-- Dependencies: 1315
-- Name: COLUMN users_history.name; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users_history.name IS 'imie';


--
-- TOC entry 1866 (class 0 OID 0)
-- Dependencies: 1315
-- Name: COLUMN users_history.surname; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users_history.surname IS 'nazwisko';


--
-- TOC entry 1867 (class 0 OID 0)
-- Dependencies: 1315
-- Name: COLUMN users_history.email; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users_history.email IS 'email';


--
-- TOC entry 1868 (class 0 OID 0)
-- Dependencies: 1315
-- Name: COLUMN users_history.faculty_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users_history.faculty_id IS 'wydzial';


--
-- TOC entry 1869 (class 0 OID 0)
-- Dependencies: 1315
-- Name: COLUMN users_history.study_year_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users_history.study_year_id IS 'identyfikator roku studiow';


--
-- TOC entry 1870 (class 0 OID 0)
-- Dependencies: 1315
-- Name: COLUMN users_history.location_id; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users_history.location_id IS 'miejsce zamieszkania';


--
-- TOC entry 1871 (class 0 OID 0)
-- Dependencies: 1315
-- Name: COLUMN users_history.modified_by; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users_history.modified_by IS 'kto wprowadzil te dane';


--
-- TOC entry 1872 (class 0 OID 0)
-- Dependencies: 1315
-- Name: COLUMN users_history.modified_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users_history.modified_at IS 'czas powstania tej wersji';


--
-- TOC entry 1873 (class 0 OID 0)
-- Dependencies: 1315
-- Name: COLUMN users_history.comment; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users_history.comment IS 'komentarz';


--
-- TOC entry 1874 (class 0 OID 0)
-- Dependencies: 1315
-- Name: COLUMN users_history.login; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN users_history.login IS 'login';


--
-- TOC entry 1316 (class 1259 OID 17887)
-- Dependencies: 1315 5
-- Name: users_history_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE users_history_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.users_history_id_seq OWNER TO postgres;

--
-- TOC entry 1875 (class 0 OID 0)
-- Dependencies: 1316
-- Name: users_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE users_history_id_seq OWNED BY users_history.id;


--
-- TOC entry 1876 (class 0 OID 0)
-- Dependencies: 1316
-- Name: users_history_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('users_history_id_seq', 28, true);


--
-- TOC entry 1317 (class 1259 OID 17889)
-- Dependencies: 5 1314
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE users_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 1877 (class 0 OID 0)
-- Dependencies: 1317
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- TOC entry 1878 (class 0 OID 0)
-- Dependencies: 1317
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('users_id_seq', 41, true);


--
-- TOC entry 1318 (class 1259 OID 18031)
-- Dependencies: 1687 1688 1689 1690 1691 1692 5
-- Name: users_penalties; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE users_penalties (
    id bigint DEFAULT nextval('bans_id_seq'::regclass) NOT NULL,
    admin_id bigint NOT NULL,
    user_id bigint NOT NULL,
    type_id smallint DEFAULT 1 NOT NULL,
    start_time timestamp without time zone DEFAULT now() NOT NULL,
    end_time timestamp without time zone NOT NULL,
    active boolean DEFAULT true NOT NULL,
    comment pg_catalog.text,
    modified_by bigint,
    modified_at time without time zone DEFAULT now() NOT NULL,
    reason_id smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.users_penalties OWNER TO postgres;

--
-- TOC entry 1879 (class 0 OID 0)
-- Dependencies: 1318
-- Name: TABLE users_penalties; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE users_penalties IS 'kary nalozone na uzytkownikow';


--
-- TOC entry 1657 (class 2604 OID 17891)
-- Dependencies: 1299 1298
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE admins ALTER COLUMN id SET DEFAULT nextval('admins_id_seq'::regclass);


--
-- TOC entry 1665 (class 2604 OID 17892)
-- Dependencies: 1304 1300
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE computers ALTER COLUMN id SET DEFAULT nextval('computers_id_seq'::regclass);


--
-- TOC entry 1668 (class 2604 OID 17893)
-- Dependencies: 1302 1301
-- Name: computer_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE computers_history ALTER COLUMN computer_id SET DEFAULT nextval('computers_history_computer_id_seq'::regclass);


--
-- TOC entry 1669 (class 2604 OID 17894)
-- Dependencies: 1303 1301
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE computers_history ALTER COLUMN id SET DEFAULT nextval('computers_history_id_seq'::regclass);


--
-- TOC entry 1672 (class 2604 OID 17895)
-- Dependencies: 1306 1305
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE dormitories ALTER COLUMN id SET DEFAULT nextval('dormitories_id_seq'::regclass);


--
-- TOC entry 1675 (class 2604 OID 17896)
-- Dependencies: 1308 1307
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE faculties ALTER COLUMN id SET DEFAULT nextval('faulties_id_seq'::regclass);


--
-- TOC entry 1679 (class 2604 OID 17897)
-- Dependencies: 1311 1310
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE locations ALTER COLUMN id SET DEFAULT nextval('locations_id_seq'::regclass);


--
-- TOC entry 1681 (class 2604 OID 17898)
-- Dependencies: 1313 1312
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE text ALTER COLUMN id SET DEFAULT nextval('text_id_seq'::regclass);


--
-- TOC entry 1685 (class 2604 OID 17899)
-- Dependencies: 1317 1314
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- TOC entry 1686 (class 2604 OID 17900)
-- Dependencies: 1316 1315
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE users_history ALTER COLUMN id SET DEFAULT nextval('users_history_id_seq'::regclass);


--
-- TOC entry 1750 (class 0 OID 17803)
-- Dependencies: 1298
-- Data for Name: admins; Type: TABLE DATA; Schema: public; Owner: postgres
--

