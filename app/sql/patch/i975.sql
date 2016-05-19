ALTER TABLE computers_aliases ADD COLUMN record_type SMALLINT NOT NULL DEFAULT 1;
UPDATE computers_aliases SET record_type = 2 WHERE is_cname IS FALSE;
ALTER TABLE computers_aliases ADD COLUMN value VARCHAR(127);
ALTER TABLE computers_aliases ADD COLUMN avail_to TIMESTAMP WITH TIME ZONE;
ALTER TABLE computers_aliases DROP COLUMN is_cname;

COMMENT ON COLUMN computers_aliases.record_type IS 'typ rekordu';
COMMENT ON COLUMN computers_aliases.value IS 'opcjonalna wartosc rekordu rekordu';
COMMENT ON COLUMN computers_aliases.avail_to IS 'opcjonalna data usuniecia rekordu';
