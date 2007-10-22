--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

ALTER TABLE ONLY public.text DROP CONSTRAINT text_pkey;
ALTER TABLE ONLY public.text DROP CONSTRAINT text_alias_key;
ALTER TABLE public.text ALTER COLUMN id DROP DEFAULT;
DROP SEQUENCE public.text_id_seq;
DROP TABLE public.text;
DROP SCHEMA public;
--
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO postgres;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: text; Type: TABLE; Schema: public; Owner: hrynek; Tablespace: 
--

CREATE TABLE text (
    id bigint NOT NULL,
    alias pg_catalog.text NOT NULL,
    title pg_catalog.text NOT NULL,
    content pg_catalog.text NOT NULL,
    modified_at timestamp without time zone DEFAULT now() NOT NULL,
    modified_by bigint
);


ALTER TABLE public.text OWNER TO hrynek;

--
-- Name: COLUMN text.alias; Type: COMMENT; Schema: public; Owner: hrynek
--

COMMENT ON COLUMN text.alias IS '"url"';


--
-- Name: COLUMN text.title; Type: COMMENT; Schema: public; Owner: hrynek
--

COMMENT ON COLUMN text.title IS 'tytul';


--
-- Name: COLUMN text.content; Type: COMMENT; Schema: public; Owner: hrynek
--

COMMENT ON COLUMN text.content IS 'tresc glowna';


--
-- Name: COLUMN text.modified_at; Type: COMMENT; Schema: public; Owner: hrynek
--

COMMENT ON COLUMN text.modified_at IS 'data ostatniej modyfikacji';


--
-- Name: COLUMN text.modified_by; Type: COMMENT; Schema: public; Owner: hrynek
--

COMMENT ON COLUMN text.modified_by IS 'kto dokonal modyfikacji';


--
-- Name: text_id_seq; Type: SEQUENCE; Schema: public; Owner: hrynek
--

CREATE SEQUENCE text_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.text_id_seq OWNER TO hrynek;

--
-- Name: text_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: hrynek
--

ALTER SEQUENCE text_id_seq OWNED BY text.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: hrynek
--

ALTER TABLE text ALTER COLUMN id SET DEFAULT nextval('text_id_seq'::regclass);


--
-- Name: text_alias_key; Type: CONSTRAINT; Schema: public; Owner: hrynek; Tablespace: 
--

ALTER TABLE ONLY text
    ADD CONSTRAINT text_alias_key UNIQUE (alias);


--
-- Name: text_pkey; Type: CONSTRAINT; Schema: public; Owner: hrynek; Tablespace: 
--

ALTER TABLE ONLY text
    ADD CONSTRAINT text_pkey PRIMARY KEY (id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

