CREATE OR REPLACE FUNCTION remove_bans() RETURNS integer AS $$
DECLARE
	updated INT;
BEGIN
	UPDATE penalties SET active = 'false' WHERE active = 'true' and end_at < now();

	GET DIAGNOSTICS updated = ROW_COUNT;
	RETURN updated;
END;
$$ LANGUAGE plpgsql;
