ALTER TABLE switches ADD COLUMN lab BOOLEAN DEFAULT false;
COMMENT ON COLUMN switches.lab IS 'czy switch labowy';