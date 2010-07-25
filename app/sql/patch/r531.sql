-- Sequence: computers_aliases_id_seq

-- DROP SEQUENCE computers_aliases_id_seq;

CREATE SEQUENCE computers_aliases_id_seq
  INCREMENT 1
  MINVALUE 1
  NO MAXVALUE
  START 1
  CACHE 1;

-- Table: computers_aliases

-- DROP TABLE computers_aliases;

CREATE TABLE computers_aliases
(
  id bigint DEFAULT nextval('computers_aliases_id_seq'::regclass) NOT NULL,
  computer_id bigint NOT NULL, -- ktory komputer
  host character varying(50) NOT NULL, -- alias
  CONSTRAINT computers_aliases_pkey PRIMARY KEY (id),
  CONSTRAINT computers_aliases_fkey FOREIGN KEY (computer_id)
      REFERENCES computers (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE,
  CONSTRAINT computers_aliases_host_key UNIQUE (host)
)
WITH (
  OIDS=FALSE
);
COMMENT ON COLUMN computers_aliases.computer_id IS 'ktory komputer';
COMMENT ON COLUMN computers_aliases.host IS 'alias';


-- Index: fki_computers_aliases_fkey

-- DROP INDEX fki_computers_aliases_fkey;

CREATE INDEX fki_computers_aliases_fkey
  ON computers_aliases
  USING btree
  (computer_id);

-- Index: computers_ipv4_key

DROP INDEX computers_ipv4_key;

CREATE UNIQUE INDEX computers_ipv4_key
  ON computers
  USING btree
  (ipv4, active)
  WHERE active = true;