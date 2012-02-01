ALTER TABLE locations ADD COLUMN type_id smallint DEFAULT 1 NOT NULL; -- typ pomieszczenia
ALTER TABLE locations_history ADD COLUMN users_max smallint; -- ilu ludzi moze byc maksymalnie zameldowanych
ALTER TABLE locations_history ADD COLUMN type_id smallint DEFAULT 1 NOT NULL; -- typ pomieszczenia

-- Function: location_update()

-- DROP FUNCTION location_update();

CREATE OR REPLACE FUNCTION location_update()
  RETURNS trigger AS
$BODY$BEGIN
if
	NEW."comment"!=OLD."comment" OR
	NEW.users_max!=OLD.users_max OR
	NEW.type_id!=OLD.type_id
then
	INSERT INTO locations_history (
		location_id,
		"comment",
		users_max,
		type_id,
		modified_by,
		modified_at
	) VALUES (
		OLD.id,
		OLD."comment",
		OLD.users_max,
		OLD.type_id,
		OLD.modified_by,
		OLD.modified_at
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE plpgsql VOLATILE;
COMMENT ON FUNCTION location_update() IS 'archiwizacja informacji o lokacji';