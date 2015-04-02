ALTER TABLE fw_exceptions
ADD COLUMN waiting boolean NOT NULL DEFAULT FALSE,
ADD CHECK (NOT (active IS TRUE AND waiting IS TRUE));
COMMENT ON COLUMN fw_exceptions.waiting IS 'oczekuje na rozpatrzenie';

ALTER TABLE fw_exception_applications
DROP COLUMN token,
ADD COLUMN sspg_opinion_by bigint,
ALTER COLUMN created_at SET DEFAULT now(),
DROP CONSTRAINT fw_exception_applications_opinion_by_fkey,
ADD CONSTRAINT fw_exception_applications_skos_opinion_by_fkey FOREIGN KEY (skos_opinion_by)
      REFERENCES admins (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT,
ADD CONSTRAINT fw_exception_applications_sspg_opinion_by_fkey FOREIGN KEY (sspg_opinion_by)
      REFERENCES users (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE SET NULL;