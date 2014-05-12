CREATE TABLE inventory_cards (
id bigserial NOT NULL,
modified_by bigint,
modified_at timestamp with time zone NOT NULL DEFAULT now(),
dormitory_id bigint NOT NULL,
serial_no character varying(32),
inventory_no character varying(32),
received date,
comment text,
CONSTRAINT inventory_cards_pkey PRIMARY KEY (id),
CONSTRAINT inventory_cards_modified_by_fkey FOREIGN KEY (modified_by)
REFERENCES admins (id)
ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT inventory_cards_dormitory_id_fkey FOREIGN KEY (dormitory_id)
REFERENCES dormitories (id)
ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT inventory_cards_serial_no_unique UNIQUE (serial_no)
);
CREATE INDEX inventory_cards_inventory_no_idx ON inventory_cards(inventory_no);
COMMENT ON COLUMN inventory_cards.serial_no IS 'numer seryjny';
COMMENT ON COLUMN inventory_cards.inventory_no IS 'numer inwentarzowy';
COMMENT ON COLUMN inventory_cards.received IS 'data dodania na stan';
COMMENT ON TABLE inventory_cards IS 'karty wyposazenia';

CREATE TABLE inventory_cards_history (
id bigserial NOT NULL,
inventory_card_id bigint NOT NULL,
modified_by bigint,
modified_at timestamp with time zone NOT NULL DEFAULT now(),
dormitory_id bigint NOT NULL,
serial_no character varying(32),
inventory_no character varying(32),
received date,
comment text,
CONSTRAINT inventory_cards_history_pkey PRIMARY KEY (id),
CONSTRAINT inventory_cards_history_inventory_card_id_fkey FOREIGN KEY (inventory_card_id)
REFERENCES inventory_cards (id)
ON UPDATE CASCADE ON DELETE CASCADE,
CONSTRAINT inventory_cards_history_modified_by_fkey FOREIGN KEY (modified_by)
REFERENCES admins (id)
ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT inventory_cards_history_dormitory_id_fkey FOREIGN KEY (dormitory_id)
REFERENCES dormitories (id)
ON UPDATE CASCADE ON DELETE RESTRICT
);
COMMENT ON TABLE inventory_cards_history IS 'historia kart wyposazenia';

-- Function: inventory_card_update()

-- DROP FUNCTION inventory_card_update();

CREATE OR REPLACE FUNCTION inventory_card_update()
  RETURNS trigger AS
$BODY$BEGIN
if
	NEW.dormitory_id!=OLD.dormitory_id OR
	NEW.serial_no!=OLD.serial_no OR
	NEW.inventory_no!=OLD.inventory_no OR
	(OLD.inventory_no IS NULL AND NEW.inventory_no IS NOT NULL) OR
	(OLD.inventory_no IS NOT NULL AND NEW.inventory_no IS NULL) OR
	NEW.received!=OLD.received OR
	(OLD.received IS NULL AND NEW.received IS NOT NULL) OR
	(OLD.received IS NOT NULL AND NEW.received IS NULL) OR
	NEW.comment!=OLD.comment OR
	(OLD.comment IS NOT NULL AND NEW.comment IS NULL) OR
	(OLD.comment IS NULL AND NEW.comment IS NOT NULL)
then
	INSERT INTO inventory_cards_history (
		inventory_card_id,
		modified_by,
		modified_at,
		dormitory_id,
		serial_no,
		inventory_no,
		received,
		comment
	) VALUES (
		OLD.id,
		OLD.modified_by,
		OLD.modified_at,
		OLD.dormitory_id,
		OLD.serial_no,
		OLD.inventory_no,
		OLD.received,
		OLD.comment
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION inventory_card_update() IS 'archiwizacja karty wyposazenia';


-- Trigger: inventory_cards_update on inventory_cards

-- DROP TRIGGER inventory_cards_update on inventory_cards;

CREATE TRIGGER inventory_cards_update
  AFTER UPDATE
  ON inventory_cards
  FOR EACH ROW
  EXECUTE PROCEDURE inventory_card_update();
COMMENT ON TRIGGER inventory_cards_update ON inventory_cards IS 'kopiuje dane do historii';

INSERT INTO inventory_cards (dormitory_id, serial_no, inventory_no, received)
SELECT l.dormitory_id, s.serial_no, s.inventory_no, s.received FROM switches s, locations l WHERE s.location_id=l.id;

ALTER TABLE switches ADD COLUMN inventory_card_id bigint;
ALTER TABLE switches ADD COLUMN modified_by bigint;
ALTER TABLE switches ADD COLUMN modified_at timestamp with time zone NOT NULL DEFAULT now();
ALTER TABLE switches ADD CONSTRAINT switches_inventory_card_no_fkey FOREIGN KEY (inventory_card_id) REFERENCES inventory_cards (id) ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE switches ADD CONSTRAINT switches_modified_by_fkey FOREIGN KEY (modified_by) REFERENCES admins (id) ON UPDATE CASCADE ON DELETE RESTRICT;

UPDATE switches s SET inventory_card_id = (SELECT id FROM inventory_cards i WHERE i.serial_no = s.serial_no);

ALTER TABLE switches ALTER COLUMN inventory_card_id SET NOT NULL;
ALTER TABLE switches DROP CONSTRAINT switches_inventory_no_unique;
ALTER TABLE switches DROP CONSTRAINT switches_serial_no_unique;
ALTER TABLE switches DROP COLUMN serial_no;
ALTER TABLE switches DROP COLUMN inventory_no;
ALTER TABLE switches DROP COLUMN received;


CREATE TABLE switches_history (
id bigserial NOT NULL,
switch_id bigint NOT NULL,
modified_by bigint,
modified_at timestamp with time zone NOT NULL DEFAULT now(),
location_id bigint NOT NULL,
model bigint NOT NULL,
inoperational boolean NOT NULL,
hierarchy_no integer,
ipv4 inet,
comment text,
CONSTRAINT switches_history_pkey PRIMARY KEY (id),
CONSTRAINT switches_history_switch_id_fkey FOREIGN KEY (switch_id)
REFERENCES switches (id)
ON UPDATE CASCADE ON DELETE CASCADE,
CONSTRAINT switches_history_modified_by_fkey FOREIGN KEY (modified_by)
REFERENCES admins (id)
ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT switches_history_location_id_fkey FOREIGN KEY (location_id)
REFERENCES locations (id)
ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT switches_history_ipv4_fkey FOREIGN KEY (ipv4)
REFERENCES ipv4s (ip)
ON UPDATE CASCADE ON DELETE RESTRICT,
CONSTRAINT switches_history_model_fkey FOREIGN KEY (model)
REFERENCES switches_model (id)
ON UPDATE CASCADE ON DELETE RESTRICT
);
COMMENT ON TABLE switches_history IS 'historia switchy';

-- Function: switch_update()

-- DROP FUNCTION switch_update();

CREATE OR REPLACE FUNCTION switch_update()
  RETURNS trigger AS
$BODY$BEGIN
if
	NEW.location_id!=OLD.location_id OR
	NEW.model!=OLD.model OR
	NEW.inoperational!=OLD.inoperational OR
	NEW.hierarchy_no!=OLD.hierarchy_no OR
	(OLD.hierarchy_no IS NULL AND NEW.hierarchy_no IS NOT NULL) OR
	(OLD.hierarchy_no IS NOT NULL AND NEW.hierarchy_no IS NULL) OR
	NEW.ipv4!=OLD.ipv4 OR
	(OLD.ipv4 IS NULL AND NEW.ipv4 IS NOT NULL) OR
	(OLD.ipv4 IS NOT NULL AND NEW.ipv4 IS NULL) OR
	NEW.comment!=OLD.comment OR
	(OLD.comment IS NOT NULL AND NEW.comment IS NULL) OR
	(OLD.comment IS NULL AND NEW.comment IS NOT NULL)
then
	INSERT INTO switches_history (
		switch_id,
		modified_by,
		modified_at,
		location_id,
		model,
		inoperational,
		hierarchy_no,
		ipv4,
		comment
	) VALUES (
		OLD.id,
		OLD.modified_by,
		OLD.modified_at,
		OLD.location_id,
		OLD.model,
		OLD.inoperational,
		OLD.hierarchy_no,
		OLD.ipv4,
		OLD.comment
	);
end if;
return NEW;
END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;
COMMENT ON FUNCTION switch_update() IS 'archiwizacja switcha';


-- Trigger: switches_update on switches

-- DROP TRIGGER switches_update on switches;

CREATE TRIGGER switches_update
  AFTER UPDATE
  ON switches
  FOR EACH ROW
  EXECUTE PROCEDURE switch_update();
COMMENT ON TRIGGER switches_update ON switches IS 'kopiuje dane do historii';

CREATE TABLE device_models (
id bigserial NOT NULL,
name character varying(32),
CONSTRAINT device_models_pkey PRIMARY KEY (id),
CONSTRAINT device_models_name_unique UNIQUE (name)
);
COMMENT ON TABLE device_models IS 'modele urzadzen';

INSERT INTO device_models (name) VALUES ('Serwer X 0000');

ALTER TABLE computers ADD COLUMN inventory_card_id bigint;
ALTER TABLE computers ADD COLUMN device_model_id bigint;
ALTER TABLE computers_history ADD COLUMN device_model_id bigint;

-- poprawić id aktualizującego
UPDATE computers SET device_model_id = 1, modified_at=now(), modified_by=23 WHERE type_id = 41 OR type_id = 43;

ALTER TABLE computers ADD CONSTRAINT computers_inventory_card_id_fkey FOREIGN KEY (inventory_card_id) REFERENCES inventory_cards (id) ON UPDATE CASCADE ON DELETE RESTRICT;
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