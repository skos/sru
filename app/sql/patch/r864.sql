ALTER TABLE computers_history ADD COLUMN carer_id bigint;
ALTER TABLE computers_history ADD COLUMN master_host_id bigint;

-- Function: computer_update()

-- DROP FUNCTION computer_update();

CREATE OR REPLACE FUNCTION computer_update()
  RETURNS trigger AS
$BODY$BEGIN
if
	OLD.host!=NEW.host OR
	OLD.mac!=NEW.mac OR
	OLD.ipv4!=NEW.ipv4 OR
	OLD.user_id!=NEW.user_id OR
	OLD.location_id!=NEW.location_id OR
	OLD.avail_to!=NEW.avail_to OR
	OLD.avail_max_to!=NEW.avail_max_to OR
	OLD.comment!=NEW.comment OR
	OLD.can_admin!=NEW.can_admin OR
	OLD.active!=NEW.active OR
	OLD.type_id!=NEW.type_id OR
	OLD.exadmin!=NEW.exadmin OR
	OLD.carer_id!=NEW.carer_id OR
	(OLD.carer_id IS NULL AND NEW.carer_id IS NOT NULL) OR
	(OLD.carer_id IS NOT NULL AND NEW.carer_id IS NULL) OR
	OLD.master_host_id!=NEW.master_host_id OR
	(OLD.master_host_id IS NULL AND NEW.master_host_id IS NOT NULL) OR
	(OLD.master_host_id IS NOT NULL AND NEW.master_host_id IS NULL)
then
	INSERT INTO computers_history (
		computer_id,
		host,
		mac,
		ipv4,
		user_id,
		location_id,
		avail_to,
		avail_max_to,
		modified_by,
		modified_at,
		comment,
		can_admin,
		active,
		type_id,
		exadmin,
		carer_id,
		master_host_id

	) VALUES (
		OLD.id,
		OLD.host,
		OLD.mac,
		OLD.ipv4,
		OLD.user_id,
		OLD.location_id,
		OLD.avail_to,
		OLD.avail_max_to,
		OLD.modified_by,
		OLD.modified_at,
		OLD.comment,
		OLD.can_admin,
		OLD.active,
		OLD.type_id,
		OLD.exadmin,
		OLD.carer_id,
		OLD.master_host_id
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE plpgsql VOLATILE;
COMMENT ON FUNCTION computer_update() IS 'archiwizacja danych komputera';