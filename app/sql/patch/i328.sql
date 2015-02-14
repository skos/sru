-- Table: campuses

-- DROP TABLE campuses;

CREATE TABLE campuses
(
  id serial NOT NULL,
  name varchar(20) NOT NULL,
  CONSTRAINT id_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

ALTER TABLE campuses ADD CONSTRAINT campuses_name_key UNIQUE ("name");
INSERT INTO campuses (name) VALUES ('Os. Traugutta');
INSERT INTO campuses (name) VALUES ('Os. Wyspiańskiego');

ALTER TABLE dormitories
	ADD COLUMN campus int NOT NULL DEFAULT 1;

ALTER TABLE dormitories ADD CONSTRAINT dormitories_campus_id_fkey FOREIGN KEY (campus) REFERENCES campuses (id) ON UPDATE CASCADE ON DELETE RESTRICT;