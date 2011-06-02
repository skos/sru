ALTER TABLE computers ADD COLUMN exadmin boolean NOT NULL DEFAULT false;
ALTER TABLE computers_history ADD COLUMN exadmin boolean NOT NULL DEFAULT false;

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
	OLD.exadmin!=NEW.exadmin
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
		exadmin
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
		OLD.exadmin
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE plpgsql VOLATILE;
COMMENT ON FUNCTION computer_update() IS 'archiwizacja danych komputera';

UPDATE computers SET type_id = 21 where type_id = 2;
UPDATE computers SET type_id = 31 where type_id = 3;
UPDATE computers SET type_id = 41 where type_id = 4;

UPDATE computers_history SET type_id = 21 where type_id = 2;
UPDATE computers_history SET type_id = 31 where type_id = 3;
UPDATE computers_history SET type_id = 41 where type_id = 4;

DROP INDEX computers_mac_key;
CREATE UNIQUE INDEX computers_mac_key
  ON computers
  USING btree
  (mac, active)
  WHERE active = true AND type_id <> 42;

ALTER TABLE computers ADD COLUMN carer_id bigint;
ALTER TABLE computers ADD COLUMN master_host_id bigint;
ALTER TABLE computers ADD CONSTRAINT computers_carer_id FOREIGN KEY (carer_id) REFERENCES admins (id) ON UPDATE CASCADE ON DELETE SET NULL;
ALTER TABLE computers ADD CONSTRAINT computers_master_host_id_fkey FOREIGN KEY (master_host_id) REFERENCES computers (id) ON UPDATE CASCADE ON DELETE SET NULL;
