-- Sequence: admins_dormitories_id_seq

-- DROP SEQUENCE admins_dormitories_id_seq;

CREATE SEQUENCE admins_dormitories_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

-- Table: admins_dormitories

-- DROP TABLE admins_dormitories;

CREATE TABLE admins_dormitories
(
  id bigint DEFAULT nextval('admins_dormitories_id_seq'::regclass) NOT NULL,
  "admin" bigint,
  dormitory bigint,
  CONSTRAINT admins_dormitories_pkey PRIMARY KEY (id),
  CONSTRAINT admins_dormitories_admin_id FOREIGN KEY ("admin")
      REFERENCES admins (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT admins_dormitories_dormitory_id_fkey FOREIGN KEY (dormitory)
      REFERENCES dormitories (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT admins_dormitories_key UNIQUE (admin, dormitory)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE admins_dormitories OWNER TO postgres;
COMMENT ON TABLE admins_dormitories IS 'Przypisania adminów do wielu akademików';
