ALTER TABLE ipv4s DROP CONSTRAINT users_vlan_id_fkey;

ALTER TABLE vlans DROP CONSTRAINT id_pkey;
ALTER TABLE vlans ADD CONSTRAINT vlans_pkey PRIMARY KEY (id);

ALTER TABLE ipv4s ADD CONSTRAINT ipv4s_vlan_id_fkey FOREIGN KEY (vlan) REFERENCES vlans(id) ON UPDATE CASCADE ON DELETE RESTRICT;

CREATE TABLE switches_firmware
(
   id serial, 
   firmware character varying NOT NULL, 
   CONSTRAINT switches_firmware_pkey PRIMARY KEY (id),
   CONSTRAINT switches_firmware_firmware_key UNIQUE (firmware)
)
WITH (
  OIDS=FALSE
);

INSERT INTO switches_firmware(firmware) VALUES ('X');
ALTER TABLE switches_model ADD COLUMN firmware_id integer NOT NULL DEFAULT 1;
ALTER TABLE switches_model ADD CONSTRAINT switches_model_firmware_id_fkey FOREIGN KEY (firmware_id) REFERENCES switches_firmware (id) ON UPDATE CASCADE ON DELETE RESTRICT;
