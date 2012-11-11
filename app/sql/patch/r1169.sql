-- Table: countries

-- DROP TABLE countries;

CREATE TABLE vlans
(
  id bigserial NOT NULL,
  name varchar(20) NOT NULL,
  description varchar(100),
  CONSTRAINT id_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

ALTER TABLE vlans ADD CONSTRAINT vlans_name_key UNIQUE ("name");
INSERT INTO vlans VALUES (42, 'DSPG');

ALTER TABLE ipv4s
	ADD COLUMN vlan bigint NOT NULL DEFAULT 42;

ALTER TABLE ipv4s ADD CONSTRAINT users_vlan_id_fkey FOREIGN KEY (vlan) REFERENCES vlans (id) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE computers ADD COLUMN master_ip_host_id bigint;
ALTER TABLE computers ADD CONSTRAINT computers_master_ip_host_id_fkey FOREIGN KEY (master_ip_host_id) REFERENCES computers (id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE computers_history ADD COLUMN master_ip_host_id bigint;

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
	(OLD.master_host_id IS NOT NULL AND NEW.master_host_id IS NULL) OR
	OLD.master_ip_host_id!=NEW.master_ip_host_id OR
	(OLD.master_ip_host_id IS NULL AND NEW.master_ip_host_id IS NOT NULL) 
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