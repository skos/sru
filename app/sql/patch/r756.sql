ALTER TABLE computers ADD COLUMN last_seen timestamp without time zone;

CREATE OR REPLACE FUNCTION computers_seen_update(macaddr, timestamp) returns boolean AS '
	BEGIN
		UPDATE computers SET last_seen = $2 WHERE mac = $1 and active = true;
		RETURN true;
	END;
' LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;