-- Table: countries

-- DROP TABLE countries;

CREATE TABLE vlans
(
  id bigserial NOT NULL,
  name varchar(20) NOT NULL,
  description varchar(100),
  CONSTRAINT id_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

ALTER TABLE vlans ADD CONSTRAINT vlans_name_key UNIQUE ("name");
INSERT INTO vlans VALUES (42, 'DSPG');

ALTER TABLE ipv4s
	ADD COLUMN vlan bigint NOT NULL DEFAULT 42;

ALTER TABLE ipv4s ADD CONSTRAINT users_vlan_id_fkey FOREIGN KEY (vlan) REFERENCES vlans (id) ON UPDATE CASCADE ON DELETE RESTRICT;