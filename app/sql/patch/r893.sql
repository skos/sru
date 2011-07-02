ALTER TABLE computers DROP COLUMN avail_max_to;
ALTER TABLE computers ALTER COLUMN avail_to SET DEFAULT NULL;
ALTER TABLE computers ALTER COLUMN avail_to DROP NOT NULL;
ALTER TABLE computers_history DROP COLUMN avail_max_to;
ALTER TABLE computers_history ALTER COLUMN avail_to SET DEFAULT NULL;
ALTER TABLE computers_history ALTER COLUMN avail_to DROP NOT NULL;

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
	(OLD.avail_to IS NULL AND NEW.avail_to IS NOT NULL) OR
	(OLD.avail_to IS NOT NULL AND NEW.avail_to IS NULL) OR
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
	(OLD.master_host_id IS NOT NULL AND NEW.master_host_id IS NULL) OR
	OLD.auto_deactivation!=NEW.auto_deactivation
then
	INSERT INTO computers_history (
		computer_id,
		host,
		mac,
		ipv4,
		user_id,
		location_id,
		avail_to,
		modified_by,
		modified_at,
		comment,
		can_admin,
		active,
		type_id,
		exadmin,
		carer_id,
		master_host_id,
		auto_deactivation
	) VALUES (
		OLD.id,
		OLD.host,
		OLD.mac,
		OLD.ipv4,
		OLD.user_id,
		OLD.location_id,
		OLD.avail_to,
		OLD.modified_by,
		OLD.modified_at,
		OLD.comment,
		OLD.can_admin,
		OLD.active,
		OLD.type_id,
		OLD.exadmin,
		OLD.carer_id,
		OLD.master_host_id,
		OLD.auto_deactivation
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE plpgsql VOLATILE;
COMMENT ON FUNCTION computer_update() IS 'archiwizacja danych komputera';

--konieczne jest wstawienie ID administratora/bota odpowiedzialnego za aktualizację daty ważności
update computers set avail_to = null, modified_by = , modified_at = now() where avail_to = '2011-10-01';