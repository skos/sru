ALTER TABLE switches ADD COLUMN location_id bigint;
ALTER TABLE switches ADD CONSTRAINT switches_location_id_fkey FOREIGN KEY (location_id) REFERENCES locations (id) ON UPDATE CASCADE ON DELETE CASCADE;