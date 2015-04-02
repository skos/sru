-- Function: computers_set_domain_name()

-- DROP FUNCTION computers_set_domain_name();

CREATE OR REPLACE FUNCTION computers_set_domain_name()
  RETURNS trigger AS
$BODY$BEGIN
IF ('INSERT' = TG_OP) THEN
	UPDATE computers c SET domain_name = c.host || '.' || (SELECT domain_suffix FROM vlans v, ipv4s i WHERE v.id = i.vlan AND i.ip = c.ipv4) WHERE c.id = NEW.id;
ELSIF ('UPDATE' = TG_OP) THEN
	IF
		NEW.host!=OLD.host OR
		OLD.ipv4!=NEW.ipv4
	THEN
		UPDATE computers c SET domain_name = c.host || '.' || (SELECT domain_suffix FROM vlans v, ipv4s i WHERE v.id = i.vlan AND i.ip = c.ipv4) WHERE c.id = OLD.id;
	END IF;
END IF;
return NEW;
END;$BODY$
  LANGUAGE plpgsql VOLATILE;