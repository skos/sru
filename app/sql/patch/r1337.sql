ALTER TABLE switches DROP CONSTRAINT switches_dormitories_fkey;
ALTER TABLE computers ALTER COLUMN location_id SET NOT NULL;
ALTER TABLE switches ALTER COLUMN location_id SET NOT NULL;
ALTER TABLE switches DROP COLUMN localization;
ALTER TABLE switches DROP COLUMN dormitory;