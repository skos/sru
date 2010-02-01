-- Function: user_computers()

-- DROP FUNCTION user_computers();

CREATE OR REPLACE FUNCTION user_computers()
  RETURNS trigger AS
$BODY$BEGIN
IF ('UPDATE' = TG_OP) THEN
IF (OLD.active=true AND NEW.active=false) THEN
	UPDATE computers
		SET	active = false,
			can_admin = false,
			modified_by = new.modified_by,
			modified_at = new.modified_at,
			avail_to = new.modified_at
		WHERE user_id = NEW.id AND active = true;

END IF;
END IF;
RETURN NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE
COMMENT ON FUNCTION user_computers() IS 'zmienia dane komputerow';