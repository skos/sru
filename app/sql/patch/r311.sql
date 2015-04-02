-- Function: penalty_users()

CREATE OR REPLACE FUNCTION penalty_users()
  RETURNS trigger AS
$BODY$BEGIN
IF ('INSERT' = TG_OP) THEN
	IF NEW.type_id<>1 THEN	-- nie ostrzezenie
		UPDATE users
			SET banned = true, bans = bans + 1
			WHERE id = NEW.user_id;
	ELSE
		UPDATE users
			SET bans = bans + 1
			WHERE id = NEW.user_id;
	END IF;
ELSIF ('UPDATE' = TG_OP) THEN
	IF (OLD.active=true AND NEW.active = false AND (SELECT COUNT(*) from computers where banned='true' and user_id = old.user_id) = 0) THEN
		UPDATE users
			SET banned = false
			WHERE users.id = old.user_id;
	END IF;
END IF;
RETURN NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE
  COST 100;
ALTER FUNCTION penalty_users() OWNER TO postgres;
COMMENT ON FUNCTION penalty_users() IS 'modyfikuje dane uzytkownika';
