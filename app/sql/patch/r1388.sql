-- wgrać TYLKO jeśli BYŁ wgrany r1375! --

DROP VIEW v_inventory_list;
ALTER TABLE computers DROP CONSTRAINT computers_device_type_id_fkey;
ALTER TABLE computers DROP CONSTRAINT computers_device_type_id_chk;
ALTER TABLE computers DROP COLUMN device_type_id;
ALTER TABLE computers_history DROP COLUMN device_type_id;
DROP TABLE devices_history;
DROP TABLE devices;
DROP TABLE device_types;


CREATE TABLE device_models (
id bigserial NOT NULL,
name character varying(32),
CONSTRAINT device_models_pkey PRIMARY KEY (id),
CONSTRAINT device_models_name_unique UNIQUE (name)
);
COMMENT ON TABLE device_models IS 'modele urzadzen';

INSERT INTO device_models (name) VALUES ('Serwer X 0000');

ALTER TABLE computers ADD COLUMN device_model_id bigint;
ALTER TABLE computers_history ADD COLUMN device_model_id bigint;

-- poprawić id aktualizującego
UPDATE computers SET device_model_id = 1, modified_at=now(), modified_by=23 WHERE type_id = 41 OR type_id = 43;

ALTER TABLE computers ADD CONSTRAINT computers_device_model_id_fkey FOREIGN KEY (device_model_id) REFERENCES device_models (id) ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE computers ADD CONSTRAINT computers_device_model_id_chk CHECK ((type_id <> 41 AND type_id <> 43) OR device_model_id IS NOT NULL);


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
	OLD.auto_deactivation!=NEW.auto_deactivation OR
	OLD.device_model_id!=NEW.device_model_id
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
		auto_deactivation,
		device_model_id
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
		OLD.auto_deactivation,
		OLD.device_model_id
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE plpgsql VOLATILE;

CREATE TABLE devices (
id bigserial NOT NULL,
device_model_id bigint NOT NULL,
inoperational boolean NOT NULL,
modified_by bigint,
modified_at timestamp with time zone NOT NULL DEFAULT now(),
location_id bigint NOT NULL,
comment text,
inventory_card_id bigint,
CONSTRAINT devices_pkey PRIMARY KEY (id),
CONSTRAINT devices_modified_by_fkey FOREIGN KEY (modified_by)
REFERENCES admins (id)
ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT devices_location_id_fkey FOREIGN KEY (location_id)
REFERENCES locations (id)
ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT devices_device_model_id_fkey FOREIGN KEY (device_model_id)
REFERENCES device_models (id)
ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT devices_inventory_card_no_fkey FOREIGN KEY (inventory_card_id)
REFERENCES inventory_cards (id)
ON UPDATE CASCADE ON DELETE RESTRICT
);
COMMENT ON TABLE devices IS 'pozostale urzadzenia i sprzety';


CREATE TABLE devices_history (
id bigserial NOT NULL,
device_id bigint NOT NULL,
device_model_id bigint NOT NULL,
inoperational boolean NOT NULL,
modified_by bigint,
modified_at timestamp with time zone NOT NULL DEFAULT now(),
location_id bigint NOT NULL,
comment text,
CONSTRAINT devices_history_pkey PRIMARY KEY (id),
CONSTRAINT devices_history_device_id_fkey FOREIGN KEY (device_id)
REFERENCES devices (id)
ON UPDATE CASCADE ON DELETE CASCADE,
CONSTRAINT devices_history_modified_by_fkey FOREIGN KEY (modified_by)
REFERENCES admins (id)
ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT devices_history_location_id_fkey FOREIGN KEY (location_id)
REFERENCES locations (id)
ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT devices_history_device_model_id_fkey FOREIGN KEY (device_model_id)
REFERENCES device_models (id)
ON UPDATE CASCADE ON DELETE RESTRICT
);
COMMENT ON TABLE devices_history IS 'historia pozostalych urzadzen i sprzetow';

-- Function: device_update()

-- DROP FUNCTION device_update();

CREATE OR REPLACE FUNCTION device_update()
  RETURNS trigger AS
$BODY$BEGIN
if
	NEW.modified_by!=OLD.modified_by OR
	NEW.modified_at!=OLD.modified_at OR
	NEW.location_id!=OLD.location_id OR
	NEW.device_model_id!=OLD.device_model_id OR
	NEW.inoperational!=OLD.inoperational OR
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
		comment
	) VALUES (
		OLD.id,
		OLD.modified_by,
		OLD.modified_at,
		OLD.location_id,
		OLD.device_model_id,
		OLD.inoperational,
		OLD.comment
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION device_update() IS 'archiwizacja pozostalych urzadzen i sprzetow';


-- Trigger: devices_update on devices

-- DROP TRIGGER devices_update on devices;

CREATE TRIGGER devices_update
  AFTER UPDATE
  ON devices
  FOR EACH ROW
  EXECUTE PROCEDURE device_update();
COMMENT ON TRIGGER devices_update ON devices IS 'kopiuje dane do historii';

CREATE VIEW v_inventory_list AS
SELECT ic.id as card_id, c.id, ic.dormitory_id, c.location_id, ic.serial_no, ic.inventory_no, ic.received, c.device_model_id, 1 as table_id FROM computers c, inventory_cards ic WHERE c.inventory_card_id=ic.id
UNION
SELECT ic.id as card_id, s.id, ic.dormitory_id, s.location_id, ic.serial_no, ic.inventory_no, ic.received, 0, 2 as table_id FROM switches s, inventory_cards ic WHERE s.inventory_card_id=ic.id
UNION
SELECT ic.id as card_id, d.id, ic.dormitory_id, d.location_id, ic.serial_no, ic.inventory_no, ic.received, d.device_model_id, 3 as table_id FROM devices d, inventory_cards ic WHERE d.inventory_card_id=ic.id;