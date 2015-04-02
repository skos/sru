ALTER TABLE vlans ADD COLUMN domain_suffix character varying(20) NOT NULL DEFAULT 'ds.pg.gda.pl';

ALTER TABLE computers ADD COLUMN domain_name character varying(70) DEFAULT '.ds.pg.gda.pl';

UPDATE computers c SET domain_name = c.host || '.' || (SELECT domain_suffix FROM vlans v, ipv4s i WHERE v.id = i.vlan AND i.ip = c.ipv4);
ALTER TABLE computers ALTER domain_name SET NOT NULL;

CREATE UNIQUE INDEX computers_domain_name_key
  ON computers
  USING btree
  (domain_name COLLATE pg_catalog."default", active)
  WHERE active = true;

DROP INDEX computers_host_key;

CREATE OR REPLACE FUNCTION computers_set_domain_name()
  RETURNS trigger AS
$BODY$BEGIN
IF ('INSERT' = TG_OP) THEN
	UPDATE computers c SET domain_name = c.host || '.' || (SELECT domain_suffix FROM vlans v, ipv4s i WHERE v.id = i.vlan AND i.ip = c.ipv4) WHERE c.id = NEW.id;
ELSIF ('UPDATE' = TG_OP) THEN
	IF
		NEW.host!=OLD.host
	THEN
		UPDATE computers c SET domain_name = c.host || '.' || (SELECT domain_suffix FROM vlans v, ipv4s i WHERE v.id = i.vlan AND i.ip = c.ipv4) WHERE c.id = OLD.id;
	END IF;
END IF;
return NEW;
END;$BODY$
  LANGUAGE plpgsql VOLATILE;
ALTER FUNCTION computers_set_domain_name() OWNER TO sru;
COMMENT ON FUNCTION computers_set_domain_name() IS 'aktualziacja nazwy domenowej';

CREATE TRIGGER computer_update_domain_name
  AFTER INSERT OR UPDATE
  ON computers
  FOR EACH ROW
  EXECUTE PROCEDURE computers_set_domain_name();