-- Sequence: duty_hours_id_seq

-- DROP SEQUENCE duty_hours_id_seq;

CREATE SEQUENCE duty_hours_id_seq
  INCREMENT 1
  MINVALUE 1
  NO MAXVALUE
  START 1
  CACHE 1;

-- Table: duty_hours

-- DROP TABLE duty_hours;

CREATE TABLE duty_hours
(
  id bigserial NOT NULL,
  admin_id bigint NOT NULL, -- Administrator
  "day" integer NOT NULL,
  start_hour integer NOT NULL, -- Godzina rozpoczecia
  end_hour integer NOT NULL, -- Godzina zakonczenia
  active boolean NOT NULL DEFAULT true, -- Czy aktywny (nieodwolany)
  "comment" text,
  CONSTRAINT duty_hours_pkey PRIMARY KEY (id),
  CONSTRAINT duty_hours_admin_id_fkey FOREIGN KEY (admin_id)
      REFERENCES admins (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE duty_hours OWNER TO sru;
COMMENT ON COLUMN duty_hours.admin_id IS 'Administrator';
COMMENT ON COLUMN duty_hours.start_hour IS 'Godzina rozpoczecia';
COMMENT ON COLUMN duty_hours.end_hour IS 'Godzina zakonczenia';
COMMENT ON COLUMN duty_hours.active IS 'Czy aktywny (nieodwolany)';


-- Index: idx_duty_hours_admin_id

-- DROP INDEX idx_duty_hours_admin_id;

CREATE INDEX idx_duty_hours_admin_id
  ON duty_hours
  USING btree
  (admin_id);