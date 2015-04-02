-- Function: computer_add()

-- DROP FUNCTION computer_add();

CREATE OR REPLACE FUNCTION computer_add()
  RETURNS trigger AS
$BODY$DECLARE
	penalties_cursor CURSOR FOR
		SELECT id FROM penalties p WHERE p.user_id = NEW.user_id AND active = true AND type_id = 3;
	penalty penalties%ROWTYPE;
	computer_ban computers_bans%ROWTYPE;

BEGIN
IF ('INSERT' = TG_OP OR ('UPDATE' = TG_OP AND OLD.active = false AND NEW.active = true)) THEN
	OPEN penalties_cursor;
	LOOP
		FETCH penalties_cursor INTO penalty;
		EXIT WHEN NOT FOUND;
		SELECT id INTO computer_ban FROM computers_bans WHERE computer_id = NEW.id AND penalty_id = penalty.id;
		IF NOT FOUND THEN
			INSERT INTO computers_bans(computer_id, penalty_id, active) 
				VALUES (NEW.id, penalty.id, true);
		END IF;
	END LOOP;
	CLOSE penalties_cursor;
END IF;
RETURN NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION computer_add() IS 'naklada kare na nowy komputer, jesli uzytkownik jest zbanowany';


-- Trigger: computer_add on computers

-- DROP TRIGGER computer_add ON computers;

CREATE TRIGGER computer_add
  AFTER INSERT OR UPDATE
  ON computers
  FOR EACH ROW
  EXECUTE PROCEDURE computer_add();
