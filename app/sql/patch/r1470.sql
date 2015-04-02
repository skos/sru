CREATE OR REPLACE FUNCTION computers_change_location()
  RETURNS trigger AS
$BODY$BEGIN
IF
	NEW.location_id!=OLD.location_id
THEN
	UPDATE computers SET location_id = NEW.location_id, modified_by = NEW.modified_by, modified_at = NEW.modified_at WHERE master_host_id = NEW.id;
END IF;
return NEW;
END;$BODY$
  LANGUAGE plpgsql VOLATILE;
ALTER FUNCTION computers_change_location() OWNER TO sru;
COMMENT ON FUNCTION computers_change_location() IS 'aktualziacja lokalizacji maszyn wirtualnych interfejsow';

CREATE TRIGGER computer_update_location
  AFTER UPDATE
  ON computers
  FOR EACH ROW
  EXECUTE PROCEDURE computers_change_location();
