ALTER TABLE dormitories ADD COLUMN active BOOLEAN NOT NULL DEFAULT true;
CREATE INDEX dormitories_active_idx ON dormitories(active);