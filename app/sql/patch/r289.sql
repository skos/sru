CREATE OR REPLACE FUNCTION penalty_computers_bans() RETURNS "trigger" AS
$BODY$BEGIN
IF ('UPDATE' = TG_OP) THEN
IF (OLD.active = true AND NEW.active = false) THEN
	 UPDATE computers_bans
		SET active = false
		WHERE penalty_id = OLD.id;
END IF;
END IF;
RETURN NEW;
END;$BODY$
LANGUAGE 'plpgsql' VOLATILE;


update penalty_templates set penalty_type_id =3 where id not in (5,6);
update penalty_templates set penalty_type_id =2 where id in (5,6);