DROP INDEX computers_mac_key;

CREATE UNIQUE INDEX computers_mac_key
  ON computers
  USING btree
  (mac, active)
  WHERE active = true AND type_id <> 41 AND type_id <> 42 AND type_id <> 44;
