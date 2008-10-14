DROP INDEX computers_mac_key;

CREATE UNIQUE INDEX computers_mac_key
  ON computers
  USING btree
  (mac, active)
  WHERE active = true AND type_id != 4;

DROP INDEX computers_ipv4_key;

CREATE UNIQUE INDEX computers_ipv4_key
  ON computers
  USING btree
  (ipv4, active)
  WHERE active = true AND type_id <> 4;

INSERT INTO locations (alias, dormitory_id) values ('serw', 2);
INSERT INTO locations (alias, dormitory_id) values ('serw', 5);
