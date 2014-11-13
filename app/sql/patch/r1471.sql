CREATE OR REPLACE FUNCTION device_update()
  RETURNS trigger AS
$BODY$BEGIN
if
        NEW.modified_by!=OLD.modified_by OR
        NEW.modified_at!=OLD.modified_at OR
        NEW.location_id!=OLD.location_id OR
        NEW.device_model_id!=OLD.device_model_id OR
        NEW.comment!=OLD.comment OR
        (OLD.comment IS NOT NULL AND NEW.comment IS NULL) OR
        (OLD.comment IS NULL AND NEW.comment IS NOT NULL)
then
        INSERT INTO devices_history (
                device_id,
                modified_by,
                modified_at,
                location_id,
                device_model_id,
                comment
        ) VALUES (
                OLD.id,
                OLD.modified_by,
                OLD.modified_at,
                OLD.location_id,
                OLD.device_model_id,
                OLD.comment
        );
end if;
return NEW;
END;$BODY$
  LANGUAGE plpgsql VOLATILE;


ALTER TABLE devices RENAME COLUMN inoperational TO used;
ALTER TABLE devices_history RENAME COLUMN inoperational TO used;
UPDATE devices SET used = NOT used;
UPDATE devices_history SET used = NOT used;

ALTER TABLE devices ADD COLUMN inoperational BOOLEAN NOT NULL DEFAULT false;
ALTER TABLE devices_history ADD COLUMN inoperational BOOLEAN NOT NULL DEFAULT false;

ALTER TABLE devices ALTER COLUMN inoperational DROP DEFAULT;
ALTER TABLE devices_history ALTER COLUMN inoperational DROP DEFAULT;

CREATE OR REPLACE FUNCTION device_update()
  RETURNS trigger AS
$BODY$BEGIN
if
	NEW.modified_by!=OLD.modified_by OR
	NEW.modified_at!=OLD.modified_at OR
	NEW.location_id!=OLD.location_id OR
	NEW.device_model_id!=OLD.device_model_id OR
	NEW.inoperational!=OLD.inoperational OR
	NEW.used!=OLD.used OR
	NEW.comment!=OLD.comment OR
	(OLD.comment IS NOT NULL AND NEW.comment IS NULL) OR
	(OLD.comment IS NULL AND NEW.comment IS NOT NULL)
then
	INSERT INTO devices_history (
		device_id,
		modified_by,
		modified_at,
		location_id,
		device_model_id,
		inoperational,
		used,
		comment
	) VALUES (
		OLD.id,
		OLD.modified_by,
		OLD.modified_at,
		OLD.location_id,
		OLD.device_model_id,
		OLD.inoperational,
		OLD.used,
		OLD.comment
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE plpgsql VOLATILE;
