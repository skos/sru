-- Function: user_services()

-- DROP FUNCTION user_services();

CREATE OR REPLACE FUNCTION user_services()
  RETURNS trigger AS
$BODY$BEGIN
IF ('UPDATE' = TG_OP) THEN
IF (OLD.active=true AND NEW.active=false) THEN
	UPDATE services
		SET	active = null,
			modified_by = new.modified_by
		WHERE user_id = NEW.id AND active = true;

END IF;
END IF;
RETURN NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION user_services() IS 'zmienia dane uslug';

-- Trigger: users_services on users

-- DROP TRIGGER users_services ON users;

CREATE TRIGGER users_services
  AFTER INSERT OR UPDATE OR DELETE
  ON users
  FOR EACH ROW
  EXECUTE PROCEDURE user_services();